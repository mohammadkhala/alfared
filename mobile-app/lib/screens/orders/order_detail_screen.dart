import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../l10n/app_strings.dart';
import '../../models/order.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';

class OrderDetailScreen extends StatefulWidget {
  const OrderDetailScreen({super.key, required this.orderNumber});
  final String orderNumber;

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  AppOrder? _order;
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  String? _debugError;

  Future<void> _load() async {
    try {
      // Try authenticated endpoint first (for logged-in users)
      final res = await ApiService.instance.get('/orders/${widget.orderNumber}');
      _order = AppOrder.fromJson((res as Map)['order'] as Map<String, dynamic>);
    } catch (e1) {
      // Fallback to public track endpoint (works without auth / from notifications)
      try {
        final res = await ApiService.instance.get('/orders/${widget.orderNumber}/track');
        _order = AppOrder.fromJson((res as Map)['order'] as Map<String, dynamic>);
      } catch (e2) {
        _debugError = 'Auth: $e1\n\nTrack: $e2';
      }
    }
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _requestReturn(S s) async {
    final reasonCtrl = TextEditingController();
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(s.requestReturn, style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900)),
        content: Column(mainAxisSize: MainAxisSize.min, children: [
          Text(s.returnReason, style: const TextStyle(fontFamily: 'Cairo', fontSize: 13)),
          const SizedBox(height: 10),
          TextField(
            controller: reasonCtrl,
            maxLines: 3,
            decoration: InputDecoration(
              hintText: s.returnHint,
              hintStyle: const TextStyle(fontFamily: 'Cairo'),
              border: const OutlineInputBorder(),
            ),
          ),
        ]),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: Text(s.cancel, style: const TextStyle(fontFamily: 'Cairo'))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger),
            onPressed: () => Navigator.pop(ctx, true),
            child: Text(s.returnSend, style: const TextStyle(fontFamily: 'Cairo', color: Colors.white)),
          ),
        ],
      ),
    );
    if (confirmed != true) return;
    if (reasonCtrl.text.trim().isEmpty) {
      Fluttertoast.showToast(msg: s.returnReasonEmpty);
      return;
    }
    try {
      await ApiService.instance.post('/orders/${widget.orderNumber}/return-request', data: {'reason': reasonCtrl.text.trim()});
      Fluttertoast.showToast(msg: s.returnSuccess, backgroundColor: AppColors.success, textColor: Colors.white);
      _load();
    } catch (e) {
      Fluttertoast.showToast(msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    }
  }

  static const _steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;
    if (_loading) return const Scaffold(body: Center(child: CircularProgressIndicator(color: AppColors.orange)));
    if (_order == null) return Scaffold(
      appBar: AppBar(),
      body: Center(child: Padding(
        padding: const EdgeInsets.all(16),
        child: SelectableText(
          _debugError ?? s.orderNotFound,
          style: const TextStyle(fontFamily: 'Cairo', fontSize: 12, color: Colors.red),
          textAlign: TextAlign.center,
        ),
      )),
    );

    final o = _order!;
    final idx = _steps.indexOf(o.status);
    final cancelled = o.status == 'cancelled';

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: Column(children: [
        // Header
        Container(
          color: Colors.white,
          padding: EdgeInsets.fromLTRB(16, MediaQuery.of(context).padding.top + 8, 16, 14),
          child: Column(children: [
            Row(children: [
              GestureDetector(
                onTap: () => Navigator.of(context).pop(),
                child: Container(
                  width: 32, height: 32,
                  decoration: BoxDecoration(color: AppColors.grayBg, borderRadius: BorderRadius.circular(10)),
                  child: const Icon(Icons.arrow_back, size: 16),
                ),
              ),
              const SizedBox(width: 10),
              Text(s.trackOrder, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
            ]),
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(color: AppColors.blueLight, borderRadius: BorderRadius.circular(14)),
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text('📦 ${s.orderNumber}: #${o.orderNumber}',
                  style: const TextStyle(fontSize: 11, color: AppColors.blue, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
                const SizedBox(height: 4),
                Text('${o.createdAt?.toString().split(' ').first ?? ''} • ${s.totalLabel}: ${o.total.toStringAsFixed(0)} ₪',
                  style: const TextStyle(fontSize: 10, color: AppColors.gray, fontFamily: 'Cairo')),
              ]),
            ),
          ]),
        ),

        // Body
        Expanded(
          child: RefreshIndicator(
            color: AppColors.orange,
            onRefresh: _load,
            child: ListView(padding: const EdgeInsets.all(14), children: [
              // Timeline
              if (cancelled)
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(color: AppColors.dangerLight, borderRadius: BorderRadius.circular(12)),
                  child: Text(s.orderCancelled, textAlign: TextAlign.center,
                    style: const TextStyle(color: AppColors.danger, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
                )
              else
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.xl), boxShadow: AppShadows.card),
                  child: Column(children: [
                    _TimelineRow(icon: '✓', title: s.orderConfirmed, sub: o.createdAtHuman ?? '', state: idx >= 1 ? _TS.done : (idx == 0 ? _TS.active : _TS.pending), isLast: false),
                    _TimelineRow(icon: '📦', title: s.orderProcessing, state: idx >= 2 ? _TS.done : (idx == 1 ? _TS.active : _TS.pending), isLast: false),
                    _TimelineRow(icon: '🚚', title: s.orderShipped, state: idx >= 3 ? _TS.done : (idx == 2 ? _TS.active : _TS.pending), isLast: false),
                    _TimelineRow(icon: '🏠', title: s.orderDelivered, state: idx >= 4 ? _TS.done : (idx == 3 ? _TS.active : _TS.pending), isLast: true),
                  ]),
                ),

              const SizedBox(height: 14),

              // Driver card (only when shipped)
              if (o.status == 'shipped')
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.lg), boxShadow: AppShadows.card),
                  child: Row(children: [
                    Container(
                      width: 44, height: 44,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(colors: [AppColors.blue, AppColors.blueDark]),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      alignment: Alignment.center,
                      child: const Text('🚗', style: TextStyle(fontSize: 22)),
                    ),
                    const SizedBox(width: 12),
                    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(s.driver, style: const TextStyle(color: AppColors.gray, fontSize: 10, fontFamily: 'Cairo')),
                      Text(s.deliveryTeam, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800, fontFamily: 'Cairo', color: AppColors.text)),
                    ])),
                    GestureDetector(
                      onTap: () => launchUrl(Uri.parse('tel:+970598191312')),
                      child: Container(
                        width: 36, height: 36,
                        decoration: BoxDecoration(color: AppColors.blueLight, borderRadius: BorderRadius.circular(12)),
                        child: const Icon(Icons.phone, color: AppColors.blue, size: 18),
                      ),
                    ),
                    const SizedBox(width: 8),
                    GestureDetector(
                      onTap: () => launchUrl(
                        Uri.parse('https://wa.me/970598191312?text=' + Uri.encodeComponent('استفسار عن طلب #${o.orderNumber}')),
                        mode: LaunchMode.externalApplication),
                      child: Container(
                        width: 36, height: 36,
                        decoration: BoxDecoration(color: AppColors.successLight, borderRadius: BorderRadius.circular(12)),
                        child: const Icon(Icons.chat_outlined, color: Color(0xFF25D366), size: 18),
                      ),
                    ),
                  ]),
                ),

              const SizedBox(height: 14),

              // Items list
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.lg), boxShadow: AppShadows.card),
                child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
                  Text(s.productsLabel, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.text)),
                  const SizedBox(height: 10),
                  ...o.items.map((it) => Padding(
                    padding: const EdgeInsets.symmetric(vertical: 6),
                    child: Row(children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(10),
                        child: SizedBox(width: 50, height: 50, child: it.productImage != null
                          ? CachedNetworkImage(imageUrl: it.productImage!, fit: BoxFit.cover)
                          : Container(color: AppColors.grayBg, child: const Icon(Icons.image_not_supported))),
                      ),
                      const SizedBox(width: 10),
                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text(it.productName, style: const TextStyle(fontWeight: FontWeight.w800, fontFamily: 'Cairo', fontSize: 12, color: AppColors.text)),
                        Text('${it.price.toStringAsFixed(0)} ₪ × ${it.quantity}',
                          style: const TextStyle(color: AppColors.gray, fontSize: 11, fontFamily: 'Cairo')),
                      ])),
                      Text('${it.total.toStringAsFixed(0)} ₪',
                        style: const TextStyle(color: AppColors.blue, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
                    ]),
                  )),
                ]),
              ),

              const SizedBox(height: 14),

              // Totals
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.lg), boxShadow: AppShadows.card),
                child: Column(children: [
                  _row(s.subtotalLabel, '${o.subtotal.toStringAsFixed(0)} ₪'),
                  if (o.discountAmount > 0) _row(s.discountLabel, '− ${o.discountAmount.toStringAsFixed(0)} ₪', valueColor: AppColors.success),
                  if (o.loyaltyDiscount > 0) _row(s.loyaltyLabel, '− ${o.loyaltyDiscount.toStringAsFixed(0)} ₪', valueColor: AppColors.success),
                  _row(s.deliveryFee, o.deliveryFee == 0 ? s.freeDelivery : '${o.deliveryFee.toStringAsFixed(0)} ₪',
                    valueColor: o.deliveryFee == 0 ? AppColors.success : null),
                  const Padding(padding: EdgeInsets.symmetric(vertical: 8), child: Divider()),
                  Row(children: [
                    Text(s.totalLabel, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.text)),
                    const Spacer(),
                    Text('${o.total.toStringAsFixed(0)} ₪',
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppColors.blue, fontFamily: 'Cairo')),
                  ]),
                ]),
              ),

              const SizedBox(height: 14),

              // Return request button
              if (o.status == 'delivered' && o.returnRequestedAt == null)
                SizedBox(
                  width: double.infinity,
                  child: OutlinedButton.icon(
                    onPressed: () => _requestReturn(s),
                    icon: const Icon(Icons.assignment_return_outlined, color: AppColors.danger, size: 18),
                    label: Text(s.requestReturn, style: const TextStyle(color: AppColors.danger, fontFamily: 'Cairo', fontWeight: FontWeight.w800)),
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(color: AppColors.danger),
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                ),

              // Return request status badge
              if (o.returnRequestedAt != null)
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(color: AppColors.dangerLight, borderRadius: BorderRadius.circular(12)),
                  child: Row(children: [
                    const Icon(Icons.assignment_return_outlined, color: AppColors.danger, size: 18),
                    const SizedBox(width: 8),
                    Expanded(child: Text(s.returnRequested,
                      style: const TextStyle(color: AppColors.danger, fontFamily: 'Cairo', fontWeight: FontWeight.w800, fontSize: 12))),
                  ]),
                ),

              const SizedBox(height: 20),
            ]),
          ),
        ),
      ]),
    );
  }

  Widget _row(String label, String value, {Color? valueColor}) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 5),
    child: Row(children: [
      Text(label, style: const TextStyle(color: AppColors.text, fontSize: 12, fontFamily: 'Cairo', fontWeight: FontWeight.w600)),
      const Spacer(),
      Text(value, style: TextStyle(color: valueColor ?? AppColors.blueDark, fontSize: 12, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
    ]),
  );
}

enum _TS { done, active, pending }

class _TimelineRow extends StatelessWidget {
  const _TimelineRow({required this.icon, required this.title, this.sub, required this.state, required this.isLast});
  final String icon;
  final String title;
  final String? sub;
  final _TS state;
  final bool isLast;

  @override
  Widget build(BuildContext context) {
    final color = switch (state) { _TS.done => AppColors.success, _TS.active => AppColors.orange, _TS.pending => const Color(0xFFE5E7EB) };
    final textColor = switch (state) { _TS.done => AppColors.text, _TS.active => AppColors.orange, _TS.pending => AppColors.gray };

    return IntrinsicHeight(
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Column(children: [
          Container(
            width: 28, height: 28,
            decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(8)),
            alignment: Alignment.center,
            child: state == _TS.done
              ? const Icon(Icons.check, color: Colors.white, size: 16)
              : Text(icon, style: const TextStyle(fontSize: 13)),
          ),
          if (!isLast) Expanded(child: Container(width: 2, color: state == _TS.done ? AppColors.success : const Color(0xFFE5E7EB))),
        ]),
        const SizedBox(width: 12),
        Expanded(child: Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(title, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: textColor, fontFamily: 'Cairo')),
            if (sub != null && sub!.isNotEmpty)
              Padding(padding: const EdgeInsets.only(top: 2), child: Text(sub!, style: const TextStyle(fontSize: 10, color: AppColors.gray, fontFamily: 'Cairo'))),
          ]),
        )),
      ]),
    );
  }
}
