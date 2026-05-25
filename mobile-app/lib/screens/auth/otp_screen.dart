import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_service.dart' show ApiService, ApiException;
import '../../theme/app_theme.dart';
import '../home/main_navigation.dart';

class OTPScreen extends StatefulWidget {
  final String phone;
  final String name;
  final String email;
  final String password;

  const OTPScreen({
    super.key,
    required this.phone,
    required this.name,
    required this.email,
    required this.password,
  });

  @override
  State<OTPScreen> createState() => _OTPScreenState();
}

class _OTPScreenState extends State<OTPScreen> {
  final List<TextEditingController> _ctrl  = List.generate(6, (_) => TextEditingController());
  final List<FocusNode>             _focus = List.generate(6, (_) => FocusNode());

  bool _loading = false;
  int  _seconds = 60;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _startTimer();
    WidgetsBinding.instance.addPostFrameCallback((_) => _focus[0].requestFocus());
  }

  void _startTimer() {
    _seconds = 60;
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 1), (t) {
      if (!mounted) { t.cancel(); return; }
      if (_seconds == 0) { t.cancel(); return; }
      setState(() => _seconds--);
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    for (final c in _ctrl)  c.dispose();
    for (final f in _focus) f.dispose();
    super.dispose();
  }

  String get _otp => _ctrl.map((c) => c.text).join();

  void _onChange(int i, String v) {
    if (v.length == 1) {
      if (i < 5) _focus[i + 1].requestFocus();
      else { _focus[i].unfocus(); _verify(); }
    }
  }

  void _onKey(int i, KeyEvent e) {
    if (e is KeyDownEvent &&
        e.logicalKey == LogicalKeyboardKey.backspace &&
        _ctrl[i].text.isEmpty && i > 0) {
      _focus[i - 1].requestFocus();
    }
  }

  Future<void> _verify() async {
    if (_otp.length != 6) {
      Fluttertoast.showToast(
        msg: 'أدخل الرمز المكوّن من 6 أرقام',
        backgroundColor: AppColors.danger, textColor: Colors.white,
      );
      return;
    }
    setState(() => _loading = true);
    try {
      // Step 1: verify OTP
      await ApiService.instance.post('/auth/verify-otp', data: {
        'phone': widget.phone,
        'code':  _otp,
      });

      if (!mounted) return;

      // Step 2: register on backend
      await context.read<AuthProvider>().register(
        name:     widget.name,
        email:    widget.email,
        phone:    widget.phone,
        password: widget.password,
      );
      if (!mounted) return;
      await context.read<CartProvider>().load();

      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const MainNavigation()),
        (_) => false,
      );
      Fluttertoast.showToast(
        msg: 'مرحباً بك في أبناء الفريد! 🎉',
        backgroundColor: AppColors.success, textColor: Colors.white,
      );
    } on ApiException catch (e) {
      setState(() => _loading = false);
      Fluttertoast.showToast(
        msg: e.message ?? 'الرمز غير صحيح أو منتهي الصلاحية',
        backgroundColor: AppColors.danger, textColor: Colors.white,
        toastLength: Toast.LENGTH_LONG,
      );
    } catch (e) {
      setState(() => _loading = false);
      Fluttertoast.showToast(
        msg: e.toString(),
        backgroundColor: AppColors.danger, textColor: Colors.white,
      );
    }
  }

  Future<void> _resend() async {
    if (_seconds > 0 || _loading) return;
    setState(() => _loading = true);
    try {
      await ApiService.instance.post('/auth/send-otp', data: {'phone': widget.phone});
      if (!mounted) return;
      setState(() { _loading = false; for (final c in _ctrl) c.clear(); });
      _startTimer();
      _focus[0].requestFocus();
      Fluttertoast.showToast(
        msg: '✅ تم إرسال رمز جديد عبر واتساب',
        backgroundColor: AppColors.success, textColor: Colors.white,
      );
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _loading = false);
      Fluttertoast.showToast(
        msg: e.message ?? 'فشل إعادة الإرسال',
        backgroundColor: AppColors.danger, textColor: Colors.white,
      );
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.grayBg,
      appBar: AppBar(
        title: const Text('التحقق من الرقم'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_rounded),
          onPressed: () => Navigator.of(context).pop(),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 16),

              // ── WhatsApp icon ───────────────────────────────────
              Container(
                width: 88, height: 88,
                decoration: BoxDecoration(
                  color: const Color(0xFFE7F5EC),
                  borderRadius: BorderRadius.circular(26),
                  boxShadow: [BoxShadow(
                    color: const Color(0xFF25D366).withValues(alpha: 0.2),
                    blurRadius: 20, offset: const Offset(0, 8),
                  )],
                ),
                child: const Center(
                  child: Text('💬', style: TextStyle(fontSize: 44)),
                ),
              ),
              const SizedBox(height: 22),

              const Text('أدخل رمز التحقق',
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900,
                    color: AppColors.blueDark, fontFamily: 'Cairo'),
              ),
              const SizedBox(height: 8),
              Text(
                'تم إرسال رمز مكوّن من 6 أرقام\nعبر واتساب إلى\n${widget.phone}',
                style: const TextStyle(fontSize: 13, color: AppColors.gray,
                    fontFamily: 'Cairo', height: 1.7),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 36),

              // ── OTP boxes ─────────────────────────────────────
              Directionality(
                textDirection: TextDirection.ltr,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(6, _buildBox),
                ),
              ),
              const SizedBox(height: 36),

              // ── Verify button ─────────────────────────────────
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _loading ? null : _verify,
                  style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 15)),
                  child: _loading
                    ? const SizedBox(width: 22, height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white))
                    : const Text('تحقق وأنشئ الحساب',
                        style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900, fontSize: 15)),
                ),
              ),
              const SizedBox(height: 20),

              // ── Resend ─────────────────────────────────────────
              Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                const Text('لم تستلم الرمز؟ ',
                  style: TextStyle(color: AppColors.gray, fontFamily: 'Cairo', fontSize: 13)),
                TextButton(
                  onPressed: _seconds == 0 && !_loading ? _resend : null,
                  child: Text(
                    _seconds > 0 ? 'إعادة الإرسال (${_seconds}s)' : 'إعادة الإرسال',
                    style: TextStyle(
                      color: _seconds == 0 ? AppColors.orange : AppColors.grayLight,
                      fontWeight: FontWeight.w900, fontFamily: 'Cairo', fontSize: 13,
                    ),
                  ),
                ),
              ]),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBox(int i) {
    return Container(
      width: 46, height: 58,
      margin: const EdgeInsets.symmetric(horizontal: 4),
      child: KeyboardListener(
        focusNode: FocusNode(),
        onKeyEvent: (e) => _onKey(i, e),
        child: TextFormField(
          controller: _ctrl[i],
          focusNode: _focus[i],
          keyboardType: TextInputType.number,
          textAlign: TextAlign.center,
          maxLength: 1,
          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
          style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900,
              color: AppColors.blueDark, fontFamily: 'Cairo'),
          decoration: InputDecoration(
            counterText: '',
            filled: true, fillColor: Colors.white,
            contentPadding: EdgeInsets.zero,
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
                borderSide: const BorderSide(color: AppColors.border, width: 1.5)),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
                borderSide: const BorderSide(color: AppColors.border, width: 1.5)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
                borderSide: const BorderSide(color: AppColors.orange, width: 2.2)),
          ),
          onChanged: (v) => _onChange(i, v),
        ),
      ),
    );
  }
}
