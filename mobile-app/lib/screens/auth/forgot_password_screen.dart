import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_service.dart' show ApiService, ApiException;
import '../../theme/app_theme.dart';
import '../home/main_navigation.dart';

/// Password recovery: request a WhatsApp code, then set a new password.
class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _phone   = TextEditingController();
  final _email   = TextEditingController();
  final _code    = TextEditingController();
  final _pass    = TextEditingController();
  final _confirm = TextEditingController();

  String _prefix = '+970';
  bool _useEmail = false;
  bool _codeSent = false;
  bool _busy     = false;
  bool _obscure  = true;

  String get _fullPhone {
    final num = _phone.text.replaceAll(RegExp(r'\D'), '');
    return '$_prefix${num.startsWith('0') ? num.substring(1) : num}';
  }

  /// What the user typed — shown back to them on the code step.
  String get _label => _useEmail ? _email.text.trim() : _fullPhone;

  /// Identifies the account on both requests; the backend never returns the
  /// account's phone, so we keep sending whatever the user identified with.
  Map<String, dynamic> get _identity =>
      _useEmail ? {'email': _email.text.trim()} : {'phone': _fullPhone};

  @override
  void dispose() {
    _phone.dispose(); _email.dispose(); _code.dispose();
    _pass.dispose(); _confirm.dispose();
    super.dispose();
  }

  void _toast(String msg, {bool error = true}) {
    Fluttertoast.showToast(
      msg: msg,
      backgroundColor: error ? AppColors.danger : AppColors.success,
      textColor: Colors.white,
      toastLength: Toast.LENGTH_LONG,
    );
  }

  Future<void> _sendCode() async {
    if (_useEmail) {
      final e = _email.text.trim();
      if (e.isEmpty || !e.contains('@')) {
        _toast('أدخل بريداً إلكترونياً صحيحاً');
        return;
      }
    } else if (_phone.text.trim().isEmpty) {
      _toast('أدخل رقم هاتفك');
      return;
    }

    setState(() => _busy = true);
    try {
      await ApiService.instance.post('/auth/forgot-password', data: {
        ..._identity,
        'method': _useEmail ? 'email' : 'whatsapp',
      });
      if (!mounted) return;
      setState(() { _busy = false; _codeSent = true; });
      _toast('إذا كان الحساب مسجّلاً فستصلك رسالة بالرمز', error: false);
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _busy = false);
      _toast(e.message ?? 'تعذّر إرسال الرمز، حاول مجدداً');
    } catch (_) {
      if (!mounted) return;
      setState(() => _busy = false);
      _toast('تعذّر الاتصال بالخادم');
    }
  }

  Future<void> _resetPassword() async {
    if (_code.text.trim().length != 6) {
      _toast('أدخل الرمز المكوّن من 6 أرقام');
      return;
    }
    if (_pass.text.length < 8) {
      _toast('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
      return;
    }
    if (_pass.text != _confirm.text) {
      _toast('كلمة المرور وتأكيدها غير متطابقتين');
      return;
    }

    setState(() => _busy = true);
    try {
      final res = await ApiService.instance.post('/auth/reset-password', data: {
        ..._identity,
        'code':     _code.text.trim(),
        'password': _pass.text,
      });

      // Backend returns a fresh token — adopt the new session directly.
      final map = res as Map;
      final token = map['token'] as String?;
      if (token != null) {
        await ApiService.instance.setToken(token);
        if (!mounted) return;
        await context.read<AuthProvider>().bootstrap();
        if (!mounted) return;
        await context.read<CartProvider>().load();
      }
      if (!mounted) return;

      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const MainNavigation()),
        (_) => false,
      );
      _toast('تم تغيير كلمة المرور بنجاح ✓', error: false);
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _busy = false);
      _toast(e.message ?? 'الرمز غير صحيح أو منتهي الصلاحية');
    } catch (_) {
      if (!mounted) return;
      setState(() => _busy = false);
      _toast('تعذّر الاتصال بالخادم');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.grayBg,
      appBar: AppBar(
        title: const Text('استعادة كلمة المرور',
          style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900, fontSize: 16)),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
            const SizedBox(height: 12),

            Center(
              child: Container(
                width: 72, height: 72,
                decoration: BoxDecoration(
                  color: _codeSent ? AppColors.successLight : AppColors.blueLight,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Icon(
                  _codeSent
                      ? (_useEmail ? Icons.mark_email_read_outlined : Icons.chat_bubble_outline)
                      : Icons.lock_reset,
                  size: 34,
                  color: _codeSent ? AppColors.success : AppColors.blue,
                ),
              ),
            ),
            const SizedBox(height: 16),
            Text(
              _codeSent ? 'أدخل الرمز وكلمة المرور الجديدة' : 'استعادة كلمة المرور',
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontFamily: 'Cairo', fontWeight: FontWeight.w900,
                fontSize: 16, color: AppColors.text,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              _codeSent
                  ? (_useEmail
                      ? 'إذا كان الحساب مسجّلاً فستصلك رسالة على $_label'
                      : 'إذا كان الحساب مسجّلاً فستصلك رسالة واتساب على $_label')
                  : 'اختر طريقة استلام رمز التحقق',
              textAlign: TextAlign.center,
              style: const TextStyle(fontFamily: 'Cairo', fontSize: 12, color: AppColors.gray, height: 1.6),
            ),
            const SizedBox(height: 24),

            if (!_codeSent) ...[
              // ── Delivery channel ──
              Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: AppColors.grayBg,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.border),
                ),
                child: Row(children: [
                  Expanded(child: _methodTab('واتساب', !_useEmail, () => setState(() => _useEmail = false))),
                  Expanded(child: _methodTab('البريد الإلكتروني', _useEmail, () => setState(() => _useEmail = true))),
                ]),
              ),
              const SizedBox(height: 20),
            ],

            if (!_codeSent && _useEmail) ...[
              _fieldLabel('البريد الإلكتروني'),
              TextField(
                controller: _email,
                keyboardType: TextInputType.emailAddress,
                textDirection: TextDirection.ltr,
                style: const TextStyle(fontFamily: 'Cairo'),
                decoration: const InputDecoration(hintText: 'name@example.com'),
              ),
              const SizedBox(height: 24),
              _primaryButton('إرسال الرمز', _sendCode),
            ] else if (!_codeSent) ...[
              _fieldLabel('رقم الهاتف'),
              Row(children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    border: Border.all(color: AppColors.border, width: 1.2),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: DropdownButton<String>(
                    value: _prefix,
                    underline: const SizedBox(),
                    items: const [
                      DropdownMenuItem(value: '+970', child: Text('🇵🇸  +970', style: TextStyle(fontFamily: 'Cairo', fontSize: 13))),
                      DropdownMenuItem(value: '+972', child: Text('🇮🇱  +972', style: TextStyle(fontFamily: 'Cairo', fontSize: 13))),
                    ],
                    onChanged: (v) => setState(() => _prefix = v ?? '+970'),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: TextField(
                    controller: _phone,
                    keyboardType: TextInputType.phone,
                    inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(10)],
                    textDirection: TextDirection.ltr,
                    decoration: const InputDecoration(hintText: '5XXXXXXXX'),
                    style: const TextStyle(fontFamily: 'Cairo'),
                  ),
                ),
              ]),
              const SizedBox(height: 24),
              _primaryButton('إرسال الرمز', _sendCode),
            ] else ...[
              _fieldLabel('رمز التحقق'),
              TextField(
                controller: _code,
                keyboardType: TextInputType.number,
                inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(6)],
                textAlign: TextAlign.center,
                textDirection: TextDirection.ltr,
                style: const TextStyle(fontFamily: 'Cairo', fontSize: 22, fontWeight: FontWeight.w900, letterSpacing: 8),
                decoration: const InputDecoration(hintText: '——————'),
              ),
              const SizedBox(height: 16),

              _fieldLabel('كلمة المرور الجديدة'),
              TextField(
                controller: _pass,
                obscureText: _obscure,
                style: const TextStyle(fontFamily: 'Cairo'),
                decoration: InputDecoration(
                  hintText: '8 أحرف على الأقل',
                  suffixIcon: IconButton(
                    icon: Icon(_obscure ? Icons.visibility_off : Icons.visibility, size: 20),
                    onPressed: () => setState(() => _obscure = !_obscure),
                  ),
                ),
              ),
              const SizedBox(height: 16),

              _fieldLabel('تأكيد كلمة المرور'),
              TextField(
                controller: _confirm,
                obscureText: _obscure,
                style: const TextStyle(fontFamily: 'Cairo'),
                decoration: const InputDecoration(hintText: 'أعد كتابتها'),
              ),
              const SizedBox(height: 24),

              _primaryButton('تغيير كلمة المرور', _resetPassword),
              const SizedBox(height: 8),
              TextButton(
                onPressed: _busy ? null : () => setState(() => _codeSent = false),
                child: const Text('لم يصلك الرمز؟ إعادة المحاولة',
                  style: TextStyle(fontFamily: 'Cairo', fontSize: 13, color: AppColors.blue)),
              ),
            ],
          ]),
        ),
      ),
    );
  }

  /// Segmented control option for choosing the delivery channel.
  Widget _methodTab(String text, bool active, VoidCallback onTap) => GestureDetector(
    onTap: _busy ? null : onTap,
    behavior: HitTestBehavior.opaque,
    child: AnimatedContainer(
      duration: const Duration(milliseconds: 180),
      padding: const EdgeInsets.symmetric(vertical: 10),
      decoration: BoxDecoration(
        color: active ? Colors.white : Colors.transparent,
        borderRadius: BorderRadius.circular(9),
        boxShadow: active
            ? [BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 6, offset: const Offset(0, 2))]
            : null,
      ),
      alignment: Alignment.center,
      child: Text(text,
        style: TextStyle(
          fontFamily: 'Cairo', fontSize: 13, fontWeight: FontWeight.w800,
          color: active ? AppColors.blue : AppColors.gray,
        )),
    ),
  );

  Widget _fieldLabel(String text) => Padding(
    padding: const EdgeInsets.only(bottom: 8),
    child: Text(text,
      style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800, fontSize: 13, color: AppColors.text)),
  );

  Widget _primaryButton(String label, VoidCallback onTap) => SizedBox(
    height: 50,
    child: ElevatedButton(
      onPressed: _busy ? null : onTap,
      child: _busy
          ? const SizedBox(width: 22, height: 22,
              child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white))
          : Text(label,
              style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900, fontSize: 15)),
    ),
  );
}
