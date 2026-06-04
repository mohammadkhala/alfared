import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../../l10n/app_strings.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../account/addresses_screen.dart';
import 'lahza_webview_screen.dart';
import 'success_screen.dart';

class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({super.key});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _form        = GlobalKey<FormState>();
  final _firstName   = TextEditingController();
  final _lastName    = TextEditingController();
  final _phone       = TextEditingController();
  final _email       = TextEditingController();
  final _city        = TextEditingController();
  final _area        = TextEditingController();
  final _address     = TextEditingController();
  final _building    = TextEditingController();
  final _notes       = TextEditingController();
  final _coupon      = TextEditingController();
  final _loyalty     = TextEditingController(text: '0');
  String _phonePrefix = '+970';
  String _payment    = 'cod';

  List<Map<String, dynamic>> _zones = [];
  int? _zoneId;
  Map<String, dynamic> _totals = {};
  bool _loading = false;
  bool _placing = false;

  @override
  void initState() {
    super.initState();
    _bootstrap();
  }

  Future<void> _bootstrap() async {
    setState(() => _loading = true);
    try {
      final zonesRes = await ApiService.instance.get('/delivery-zones');
      _zones = ((zonesRes as Map)['zones'] as List).cast<Map<String, dynamic>>();
      final user = context.read<AuthProvider>().user;
      if (user != null) {
        final nameParts = (user.name ?? '').trim().split(' ');
        _firstName.text = nameParts.isNotEmpty ? nameParts.first : '';
        _lastName.text  = nameParts.length > 1 ? nameParts.sublist(1).join(' ') : '';
        _email.text     = user.email;
        if (user.phone != null) {
          final p = user.phone!;
          if (p.startsWith('+972'))      { _phonePrefix = '+972'; _phone.text = p.substring(4); }
          else if (p.startsWith('+970')) { _phonePrefix = '+970'; _phone.text = p.substring(4); }
        }
      }
    } catch (e) { Fluttertoast.showToast(msg: e.toString()); }
    if (mounted) setState(() => _loading = false);
    _preview();
  }

  Future<void> _preview() async {
    try {
      final res = await ApiService.instance.post('/cart/preview', data: {
        if (_zoneId != null)                          'delivery_zone_id': _zoneId,
        if (_coupon.text.isNotEmpty)                  'coupon_code':      _coupon.text.trim(),
        if (int.tryParse(_loyalty.text) != null)      'loyalty_points':   int.parse(_loyalty.text),
      });
      setState(() => _totals = (res['totals'] as Map).cast<String, dynamic>());
    } catch (_) {}
  }

  Future<void> _placeOrder(S s) async {
    if (!_form.currentState!.validate()) return;
    if (_zoneId == null) {
      Fluttertoast.showToast(msg: s.selectZoneMsg);
      return;
    }
    setState(() => _placing = true);
    try {
      final res = await ApiService.instance.post('/checkout', data: {
        'first_name':       _firstName.text.trim(),
        'last_name':        _lastName.text.trim(),
        'phone':            _phonePrefix + _phone.text.replaceAll(RegExp(r'\D'), ''),
        'email':            _email.text.trim(),
        'city':             _city.text.trim(),
        'area':             _area.text.trim(),
        'address_line':     _address.text.trim(),
        'building':         _building.text.trim(),
        'notes':            _notes.text.trim(),
        'delivery_zone_id': _zoneId,
        'coupon_code':      _coupon.text.trim(),
        'loyalty_points':   int.tryParse(_loyalty.text) ?? 0,
        'payment_method':   _payment,
      });

      await context.read<CartProvider>().load();
      if (!mounted) return;

      final orderNumber      = (res as Map)['order_number'] as String;
      final paymentMethod    = (res['payment_method'] as String?) ?? 'cod';
      final authorizationUrl = res['authorization_url'] as String?;

      // ── Lahza: open WebView for payment ──
      if (paymentMethod == 'lahza' && authorizationUrl != null) {
        await Navigator.of(context).push(
          MaterialPageRoute(
            builder: (_) => LahzaWebViewScreen(
              authorizationUrl: authorizationUrl,
              orderNumber: orderNumber,
              callbackBaseUrl: 'https://alfared.ps',
            ),
          ),
        );
        return; // WebView handles navigation to SuccessScreen
      }

      // ── COD: go directly to success ──
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => SuccessScreen(orderNumber: orderNumber)));
    } catch (e) {
      Fluttertoast.showToast(
        msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    } finally {
      if (mounted) setState(() => _placing = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;
    if (_loading) return const Scaffold(body: Center(child: CircularProgressIndicator(color: AppColors.orange)));

    final maxPoints = (_totals['max_loyalty_points'] as num?)?.toInt() ?? 0;

    // Light input theme: white background, black text, clear labels
    final inputTheme = Theme.of(context).inputDecorationTheme.copyWith(
      filled: true,
      fillColor: Colors.white,
      labelStyle: const TextStyle(
        color: Color(0xFF374151), fontFamily: 'Cairo',
        fontSize: 13, fontWeight: FontWeight.w600,
      ),
      floatingLabelStyle: const TextStyle(
        color: Color(0xFF1B3B8C), fontFamily: 'Cairo',
        fontSize: 13, fontWeight: FontWeight.w700,
      ),
      floatingLabelBehavior: FloatingLabelBehavior.always,
      contentPadding: const EdgeInsets.fromLTRB(16, 22, 16, 14),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFFD1D5DB), width: 1.4),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFFD1D5DB), width: 1.4),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.orange, width: 2),
      ),
    );

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(s.checkout),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1B3B8C),
        elevation: 0,
        surfaceTintColor: Colors.transparent,
      ),
      body: Theme(
        data: Theme.of(context).copyWith(
          inputDecorationTheme: inputTheme,
          textTheme: Theme.of(context).textTheme.apply(
            bodyColor: Colors.black87,
            displayColor: Colors.black87,
          ),
        ),
        child: Form(
        key: _form,
        child: ListView(padding: const EdgeInsets.all(16), children: [

          // ── Personal info ──────────────────────────────────────
          _section(s.personalInfo, [
            Row(children: [
              Expanded(child: TextFormField(
                controller: _firstName,
                decoration: InputDecoration(labelText: '${s.firstName} *'),
                validator: (v) => v == null || v.isEmpty ? s.required : null,
              )),
              const SizedBox(width: 10),
              Expanded(child: TextFormField(
                controller: _lastName,
                decoration: InputDecoration(labelText: '${s.lastName} *'),
                validator: (v) => v == null || v.isEmpty ? s.required : null,
              )),
            ]),
            const SizedBox(height: 10),
            Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
              SizedBox(
                width: 120,
                child: DropdownButtonFormField<String>(
                  value: _phonePrefix,
                  decoration: const InputDecoration(
                    contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 14)),
                  items: const [
                    DropdownMenuItem(value: '+970',
                        child: Text('🇵🇸 +970', style: TextStyle(fontFamily: 'Cairo', fontSize: 13))),
                    DropdownMenuItem(value: '+972',
                        child: Text('🇮🇱 +972', style: TextStyle(fontFamily: 'Cairo', fontSize: 13))),
                  ],
                  onChanged: (v) => setState(() => _phonePrefix = v ?? '+970'),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(child: TextFormField(
                controller: _phone,
                keyboardType: TextInputType.phone,
                decoration: InputDecoration(
                    labelText: '${s.phoneNumber} *', hintText: '5XX XXX XXX'),
                validator: (v) {
                  final d = (v ?? '').replaceAll(RegExp(r'\D'), '');
                  return (d.length < 8 || d.length > 10) ? s.error : null;
                },
              )),
            ]),
            const SizedBox(height: 10),
            TextFormField(
              controller: _email,
              keyboardType: TextInputType.emailAddress,
              decoration: InputDecoration(labelText: s.emailOptional),
            ),
          ]),

          // ── Delivery address ──────────────────────────────────
          _section(s.deliveryAddress, [
            // Saved address picker
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: () async {
                  final a = await Navigator.of(context).push<Map<String, dynamic>>(
                    MaterialPageRoute(builder: (_) => const AddressesScreen(selectMode: true)));
                  if (a == null) return;
                  setState(() {
                    // Safe int cast — MySQL may return IDs as String on some hosts
                    final zid = a['delivery_zone_id'];
                    _zoneId = zid is int
                        ? zid
                        : int.tryParse(zid?.toString() ?? '');

                    _city.text     = a['city']?.toString() ?? '';
                    _area.text     = a['area']?.toString() ?? '';
                    _address.text  = a['address_line']?.toString() ?? '';
                    _building.text = a['building']?.toString() ?? '';
                    _notes.text    = a['notes']?.toString() ?? '';
                    final p = a['phone']?.toString() ?? '';
                    if (p.startsWith('+972'))      { _phonePrefix = '+972'; _phone.text = p.substring(4); }
                    else if (p.startsWith('+970')) { _phonePrefix = '+970'; _phone.text = p.substring(4); }
                  });
                  _preview();
                },
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: AppColors.orange),
                  padding: const EdgeInsets.symmetric(vertical: 10),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                icon: const Icon(Icons.bookmark_border_rounded, color: AppColors.orange, size: 18),
                label: Text(s.pickSavedAddress,
                    style: const TextStyle(
                        color: AppColors.orange, fontFamily: 'Cairo', fontWeight: FontWeight.w800)),
              ),
            ),
            const SizedBox(height: 12),

            DropdownButtonFormField<int>(
              value: _zoneId,
              isExpanded: true,
              decoration: InputDecoration(labelText: '${s.deliveryZone} *'),
              items: _zones.map((z) => DropdownMenuItem<int>(
                value: z['id'] as int,
                child: Text(
                  '${z['name']} — ${(z['base_fee'] as num) > 0 ? '${z['base_fee']} ₪' : s.freeDelivery}',
                  style: const TextStyle(fontFamily: 'Cairo'),
                ),
              )).toList(),
              onChanged: (v) { setState(() => _zoneId = v); _preview(); },
            ),
            const SizedBox(height: 10),
            TextFormField(
              controller: _city,
              decoration: InputDecoration(labelText: '${s.city} *'),
              validator: (v) => v == null || v.isEmpty ? s.required : null,
            ),
            const SizedBox(height: 10),
            TextFormField(
              controller: _address,
              decoration: InputDecoration(labelText: '${s.addressDetail} *'),
              validator: (v) => v == null || v.isEmpty ? s.required : null,
            ),
            const SizedBox(height: 10),
            Row(children: [
              Expanded(child: TextFormField(
                  controller: _area, decoration: InputDecoration(labelText: s.neighborhood))),
              const SizedBox(width: 10),
              Expanded(child: TextFormField(
                  controller: _building, decoration: InputDecoration(labelText: s.building))),
            ]),
            const SizedBox(height: 10),
            TextFormField(
              controller: _notes, maxLines: 2,
              decoration: InputDecoration(labelText: s.deliveryNotes),
            ),
          ]),

          // ── Coupon & points ────────────────────────────────────
          _section(s.couponAndPoints, [
            Row(children: [
              Expanded(child: TextFormField(
                  controller: _coupon,
                  decoration: InputDecoration(labelText: s.couponCode))),
              const SizedBox(width: 10),
              ElevatedButton(
                onPressed: _preview,
                style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.blue, foregroundColor: Colors.white),
                child: Text(s.apply),
              ),
            ]),
            if (maxPoints > 0) ...[
              const SizedBox(height: 12),
              TextFormField(
                controller: _loyalty,
                keyboardType: TextInputType.number,
                onChanged: (_) => _preview(),
                decoration: InputDecoration(
                  labelText: s.loyaltyPoints,
                  helperText: s.loyaltyMaxHint(maxPoints),
                  prefixIcon: const Icon(Icons.star_rounded, color: AppColors.orange),
                ),
              ),
            ],
          ]),

          // ── Payment method ─────────────────────────────────────
          _section(s.paymentMethod, [
            // COD
            RadioListTile<String>(
              value: 'cod',
              groupValue: _payment,
              onChanged: (v) => setState(() => _payment = v!),
              title: Row(children: [
                const Text('💵 ', style: TextStyle(fontSize: 18)),
                Text(s.cod,
                    style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700)),
              ]),
              subtitle: const Text('ادفع عند استلام الطلب',
                  style: TextStyle(fontFamily: 'Cairo', fontSize: 11, color: Colors.grey)),
              activeColor: AppColors.orange,
              dense: true,
            ),

            // Lahza card payment — temporarily hidden until server API issue is resolved
          ]),

          // ── Totals ─────────────────────────────────────────────
          Container(
            margin: const EdgeInsets.symmetric(vertical: 14),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: AppColors.border),
            ),
            child: Column(children: [
              _totalsRow(s.subtotal, _totals['subtotal'], s: s),
              if ((_totals['discount'] as num?) != null && (_totals['discount'] as num) > 0)
                _totalsRow(s.discount, _totals['discount'],
                    s: s, color: AppColors.success, prefix: '− '),
              if ((_totals['loyalty_discount'] as num?) != null &&
                  (_totals['loyalty_discount'] as num) > 0)
                _totalsRow(s.loyaltyDiscount, _totals['loyalty_discount'],
                    s: s, color: AppColors.success, prefix: '− '),
              _totalsRow(s.deliveryFee, _totals['delivery_fee'], s: s),
              const Divider(height: 20),
              _totalsRow(s.total, _totals['total'], s: s, grand: true),
            ]),
          ),

          // ── Place order ────────────────────────────────────────
          ElevatedButton(
            onPressed: _placing ? null : () => _placeOrder(s),
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
            ),
            child: _placing
              ? const SizedBox(width: 22, height: 22,
                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
              : Text(s.placeOrder,
                  style: const TextStyle(fontFamily: 'Cairo', fontSize: 16, fontWeight: FontWeight.w800)),
          ),
          const SizedBox(height: 24),
        ]),
      ),
      ),
    );
  }

  Widget _section(String title, List<Widget> children) => Container(
    margin: const EdgeInsets.only(bottom: 14),
    padding: const EdgeInsets.fromLTRB(16, 16, 16, 18),
    decoration: BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(16),
      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 12, offset: const Offset(0, 3))],
    ),
    child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      Text(title, style: const TextStyle(
        fontSize: 13, fontWeight: FontWeight.w900,
        color: Color(0xFF1B3B8C), fontFamily: 'Cairo', letterSpacing: 0.2,
      )),
      const SizedBox(height: 4),
      const Divider(height: 16, color: Color(0xFFF3F4F6)),
      ...children,
    ]),
  );

  Widget _totalsRow(String label, dynamic value,
      {required S s, Color? color, String prefix = '', bool grand = false}) {
    final v = (value as num?)?.toDouble() ?? 0;
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(children: [
        Text(label, style: TextStyle(
          color: color ?? AppColors.gray, fontFamily: 'Cairo',
          fontSize: grand ? 16 : 13,
          fontWeight: grand ? FontWeight.w900 : FontWeight.w500,
        )),
        const Spacer(),
        Text('$prefix${v.toStringAsFixed(2)} ₪', style: TextStyle(
          color: color ?? (grand ? AppColors.orange : AppColors.text),
          fontWeight: FontWeight.w900, fontFamily: 'Cairo',
          fontSize: grand ? 20 : 13,
        )),
      ]),
    );
  }
}
