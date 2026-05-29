import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../../l10n/app_strings.dart';
import '../../providers/auth_provider.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart' show ApiService, ApiException;
import '../../theme/app_theme.dart';
import 'otp_screen.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _form  = GlobalKey<FormState>();
  final _name  = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _pass  = TextEditingController();
  String _prefix   = '+970';
  bool   _showPass = false;
  bool   _sending  = false;

  String get _fullPhone {
    var num = _phone.text.replaceAll(RegExp(r'\D'), '');
    if (num.startsWith('0')) num = num.substring(1);
    return '$_prefix$num';
  }

  @override
  void dispose() {
    _name.dispose(); _email.dispose();
    _phone.dispose(); _pass.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _sending = true);

    // ── Step 1: Check availability ──────────────────────────────
    try {
      await ApiService.instance.post('/auth/check-availability', data: {
        'phone': _fullPhone,
        'email': _email.text.trim(),
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _sending = false);
      final s2 = context.read<LocaleProvider>().s;
      final msg = (e.message != null && e.message!.isNotEmpty)
          ? e.message!
          : s2.registerDuplicate;
      Fluttertoast.showToast(
        msg: msg, backgroundColor: AppColors.danger, textColor: Colors.white,
        toastLength: Toast.LENGTH_LONG,
      );
      return;
    } catch (_) {
      // Network error — continue
    }

    // ── Step 2: Send OTP via WhatsApp ───────────────────────────
    try {
      await ApiService.instance.post('/auth/send-otp', data: {
        'phone': _fullPhone,
      });

      if (!mounted) return;
      setState(() => _sending = false);

      Navigator.of(context).push(MaterialPageRoute(
        builder: (_) => OTPScreen(
          phone:    _fullPhone,
          name:     _name.text.trim(),
          email:    _email.text.trim(),
          password: _pass.text,
        ),
      ));
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _sending = false);
      final s2 = context.read<LocaleProvider>().s;
      Fluttertoast.showToast(
        msg: e.message ?? s2.registerOtpFailed,
        backgroundColor: AppColors.danger, textColor: Colors.white,
        toastLength: Toast.LENGTH_LONG,
      );
    } catch (e) {
      if (!mounted) return;
      setState(() => _sending = false);
      Fluttertoast.showToast(
        msg: 'Error: $e',
        backgroundColor: AppColors.danger, textColor: Colors.white,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final s           = context.watch<LocaleProvider>().s;
    final authLoading = context.watch<AuthProvider>().loading;
    final busy        = _sending || authLoading;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      appBar: AppBar(title: Text(s.registerTitle)),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
          child: Form(
            key: _form,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // ── Logo ────────────────────────────────────────
                Center(
                  child: Container(
                    width: 88, height: 88,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(22),
                      boxShadow: [BoxShadow(
                        color: AppColors.blueDark.withValues(alpha: 0.12),
                        blurRadius: 18, offset: const Offset(0, 6),
                      )],
                    ),
                    padding: const EdgeInsets.all(10),
                    child: Image.asset('assets/images/logo.png', fit: BoxFit.contain),
                  ),
                ),
                const SizedBox(height: 12),
                Center(
                  child: Text(s.registerWelcome,
                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900,
                        color: AppColors.blue, fontFamily: 'Cairo')),
                ),
                const SizedBox(height: 24),

                // ── Form card ───────────────────────────────────
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: AppShadows.card,
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [

                      _Label(s.registerNameLbl),
                      const SizedBox(height: 8),
                      TextFormField(
                        controller: _name,
                        textCapitalization: TextCapitalization.words,
                        style: _inputStyle,
                        decoration: _dec(hint: s.registerNameHint, icon: Icons.person_outline_rounded),
                        validator: (v) => (v == null || v.trim().length < 2) ? s.registerNameErr : null,
                      ),
                      const SizedBox(height: 16),

                      _Label(s.registerEmailLbl),
                      const SizedBox(height: 8),
                      TextFormField(
                        controller: _email,
                        keyboardType: TextInputType.emailAddress,
                        textDirection: TextDirection.ltr,
                        style: _inputStyle,
                        decoration: _dec(hint: 'example@email.com', icon: Icons.email_outlined),
                        validator: (v) => (v == null || !v.contains('@')) ? s.registerEmailErr : null,
                      ),
                      const SizedBox(height: 16),

                      _Label(s.loginPhoneLabel),
                      const SizedBox(height: 8),
                      Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Container(
                          height: 52,
                          decoration: BoxDecoration(
                            color: AppColors.grayBg,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: AppColors.border, width: 1.2),
                          ),
                          padding: const EdgeInsets.symmetric(horizontal: 8),
                          child: DropdownButtonHideUnderline(
                            child: DropdownButton<String>(
                              value: _prefix,
                              icon: const Icon(Icons.keyboard_arrow_down_rounded, size: 18, color: AppColors.gray),
                              items: const [
                                DropdownMenuItem(value: '+970',
                                  child: Text('🇵🇸  +970', style: TextStyle(fontFamily: 'Cairo', fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.blueDark))),
                                DropdownMenuItem(value: '+972',
                                  child: Text('🇮🇱  +972', style: TextStyle(fontFamily: 'Cairo', fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.blueDark))),
                              ],
                              onChanged: (v) => setState(() => _prefix = v ?? '+970'),
                            ),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: TextFormField(
                            controller: _phone,
                            keyboardType: TextInputType.phone,
                            inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                            textDirection: TextDirection.ltr,
                            style: _inputStyle.copyWith(letterSpacing: 1.5),
                            decoration: InputDecoration(
                              filled: true, fillColor: AppColors.grayBg,
                              hintText: s.loginPhoneHint,
                              hintStyle: const TextStyle(color: AppColors.border, letterSpacing: 1.5, fontSize: 14),
                              prefixIcon: const Icon(Icons.phone_outlined, size: 18, color: AppColors.gray),
                              helperText: s.loginPhoneHelper,
                              helperStyle: const TextStyle(fontFamily: 'Cairo', fontSize: 10, color: AppColors.grayLight),
                              border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
                              enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
                              focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.orange, width: 1.8)),
                            ),
                            validator: (v) {
                              var d = (v ?? '').replaceAll(RegExp(r'\D'), '');
                              if (d.startsWith('0')) d = d.substring(1);
                              if (d.length != 9) return s.loginPhoneErr;
                              return null;
                            },
                          ),
                        ),
                      ]),
                      const SizedBox(height: 16),

                      _Label(s.registerPassLbl),
                      const SizedBox(height: 8),
                      TextFormField(
                        controller: _pass,
                        obscureText: !_showPass,
                        style: _inputStyle,
                        decoration: InputDecoration(
                          filled: true, fillColor: AppColors.grayBg,
                          hintText: '••••••••',
                          hintStyle: const TextStyle(color: AppColors.grayLight, fontSize: 16),
                          prefixIcon: const Icon(Icons.lock_outline, size: 18, color: AppColors.gray),
                          suffixIcon: IconButton(
                            icon: Icon(_showPass ? Icons.visibility_off_outlined : Icons.visibility_outlined, size: 18, color: AppColors.gray),
                            onPressed: () => setState(() => _showPass = !_showPass),
                          ),
                          helperText: s.registerPassHelp,
                          helperStyle: const TextStyle(fontFamily: 'Cairo', fontSize: 10, color: AppColors.grayLight),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.orange, width: 1.8)),
                        ),
                        validator: (v) => (v == null || v.length < 8) ? s.registerPassErr : null,
                      ),
                      const SizedBox(height: 24),

                      ElevatedButton(
                        onPressed: busy ? null : _submit,
                        style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 15)),
                        child: busy
                          ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                          : Text(s.registerBtn,
                              style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900, fontSize: 15)),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 14),

                // ── WhatsApp note ───────────────────────────────
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFE7F5EC),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFF25D366).withValues(alpha: 0.4)),
                  ),
                  child: Row(children: [
                    const Text('💬', style: TextStyle(fontSize: 18)),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        s.registerWaNote,
                        style: const TextStyle(fontFamily: 'Cairo', fontSize: 11,
                            color: Color(0xFF1A6B35), height: 1.5),
                      ),
                    ),
                  ]),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

const _inputStyle = TextStyle(fontFamily: 'Cairo', fontSize: 14, color: AppColors.text);

InputDecoration _dec({required String hint, required IconData icon}) => InputDecoration(
  filled: true, fillColor: AppColors.grayBg,
  hintText: hint,
  hintStyle: const TextStyle(color: AppColors.border, fontFamily: 'Cairo', fontSize: 13),
  prefixIcon: Icon(icon, size: 18, color: AppColors.gray),
  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
  enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
  focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.orange, width: 1.8)),
);

class _Label extends StatelessWidget {
  final String text;
  const _Label(this.text);
  @override
  Widget build(BuildContext context) => Text(text,
    style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.blueDark));
}
