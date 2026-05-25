import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../l10n/app_strings.dart';
import '../models/order.dart';
import '../providers/locale_provider.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'orders/order_detail_screen.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  List<AppOrder> _orders = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final res = await ApiService.instance.get('/orders');
      _orders = ((res as Map)['data'] as List)
          .map((e) => AppOrder.fromJson(e as Map<String, dynamic>))
          .toList();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  IconData _iconFor(String status) => switch (status) {
    'pending'    => Icons.access_time_rounded,
    'confirmed'  => Icons.check_circle_outline,
    'processing' => Icons.inventory_2_outlined,
    'shipped'    => Icons.local_shipping_outlined,
    'delivered'  => Icons.home_outlined,
    'cancelled'  => Icons.cancel_outlined,
    _            => Icons.notifications_outlined,
  };

  Color _colorFor(String status) => switch (status) {
    'pending'    => AppColors.amber,
    'confirmed'  => AppColors.blue,
    'processing' => AppColors.orange,
    'shipped'    => AppColors.success,
    'delivered'  => AppColors.success,
    'cancelled'  => AppColors.danger,
    _            => AppColors.gray,
  };

  String _titleFor(String status, S s) => switch (status) {
    'pending'    => s.notifPending,
    'confirmed'  => s.notifConfirmed,
    'processing' => s.notifProcessing,
    'shipped'    => s.notifShipped,
    'delivered'  => s.notifDelivered,
    'cancelled'  => s.notifCancelled,
    _            => s.notifUpdate,
  };

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;
    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: Column(children: [
        // ── Header ──────────────────────────────────────────────
        Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft, end: Alignment.bottomRight,
              colors: [AppColors.blueDark, AppColors.blue],
            ),
          ),
          padding: EdgeInsets.fromLTRB(16, MediaQuery.of(context).padding.top + 10, 16, 16),
          child: Row(children: [
            GestureDetector(
              onTap: () => Navigator.of(context).pop(),
              child: Container(
                width: 36, height: 36,
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.arrow_back, size: 18, color: Colors.white),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                s.notifications,
                style: const TextStyle(
                  fontSize: 18, fontWeight: FontWeight.w900,
                  fontFamily: 'Cairo', color: Colors.white,
                ),
              ),
            ),
            if (_orders.isNotEmpty)
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                decoration: BoxDecoration(
                  color: AppColors.orange,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  '${_orders.length}',
                  style: const TextStyle(
                    color: Colors.white, fontSize: 13,
                    fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                  ),
                ),
              ),
          ]),
        ),

        // ── Body ─────────────────────────────────────────────────
        Expanded(
          child: _loading
            ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
            : _orders.isEmpty
              ? _Empty(s: s)
              : RefreshIndicator(
                  color: AppColors.orange,
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(14, 14, 14, 30),
                    itemCount: _orders.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 10),
                    itemBuilder: (_, i) {
                      final o = _orders[i];
                      final color = _colorFor(o.status);
                      return GestureDetector(
                        onTap: () => Navigator.of(context).push(MaterialPageRoute(
                          builder: (_) => OrderDetailScreen(orderNumber: o.orderNumber),
                        )),
                        child: Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(AppRadius.lg),
                            boxShadow: AppShadows.card,
                          ),
                          child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            // Status icon
                            Container(
                              width: 48, height: 48,
                              decoration: BoxDecoration(
                                color: color.withValues(alpha: 0.12),
                                borderRadius: BorderRadius.circular(14),
                              ),
                              alignment: Alignment.center,
                              child: Icon(_iconFor(o.status), color: color, size: 24),
                            ),
                            const SizedBox(width: 14),
                            // Text info
                            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Text(
                                _titleFor(o.status, s),
                                style: TextStyle(
                                  fontSize: 14, fontWeight: FontWeight.w900,
                                  fontFamily: 'Cairo', color: AppColors.text,
                                  height: 1.3,
                                ),
                              ),
                              const SizedBox(height: 5),
                              Row(children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: color.withValues(alpha: 0.1),
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  child: Text(
                                    '#${o.orderNumber}',
                                    style: TextStyle(
                                      fontSize: 12, color: color,
                                      fontFamily: 'Cairo', fontWeight: FontWeight.w800,
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  '${o.total.toStringAsFixed(0)} ₪',
                                  style: const TextStyle(
                                    fontSize: 13, color: AppColors.blueDark,
                                    fontFamily: 'Cairo', fontWeight: FontWeight.w900,
                                  ),
                                ),
                              ]),
                              const SizedBox(height: 5),
                              Text(
                                o.createdAtHuman ?? '',
                                style: const TextStyle(
                                  fontSize: 11, color: AppColors.grayLight,
                                  fontFamily: 'Cairo',
                                ),
                              ),
                            ])),
                            const Icon(Icons.chevron_left, color: AppColors.border, size: 20),
                          ]),
                        ),
                      );
                    },
                  ),
                ),
        ),
      ]),
    );
  }
}

// ── Empty state ───────────────────────────────────────────────────────────────
class _Empty extends StatelessWidget {
  const _Empty({required this.s});
  final S s;
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(40),
        child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
          Container(
            width: 100, height: 100,
            decoration: BoxDecoration(color: AppColors.blueLight, borderRadius: BorderRadius.circular(28)),
            child: const Icon(Icons.notifications_none_rounded, size: 52, color: AppColors.blue),
          ),
          const SizedBox(height: 20),
          Text(
            s.noNotifications,
            style: const TextStyle(
              fontSize: 18, fontWeight: FontWeight.w900,
              color: AppColors.blueDark, fontFamily: 'Cairo',
            ),
          ),
          const SizedBox(height: 8),
          Text(
            s.noNotifSub,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: AppColors.gray, fontFamily: 'Cairo',
              fontSize: 13, height: 1.6,
            ),
          ),
        ]),
      ),
    );
  }
}
