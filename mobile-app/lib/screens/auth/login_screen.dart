import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../theme/app_theme.dart';
import '../home/main_navigation.dart';
import 'register_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _form   = GlobalKey<FormState>();
  final _phone  = TextEditingController();
  final _pass   = TextEditingController();
  String _prefix   = '+970';
  bool _showPass   = false;

  // Build full phone number: strip leading zero + prepend prefix
  String get _fullPhone {
    var num = _phone.text.replaceAll(RegExp(r'\D'), '');
    if (num.startsWith('0')) num = num.substring(1);
    return '$_prefix$num';
  }

  Future<void> _submit() async {
    if (!_form.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    try {
      await auth.login(_fullPhone, _pass.text);
      if (!mounted) return;
      await context.read<CartProvider>().load();
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const MainNavigation()));
    } catch (e) {
      Fluttertoast.showToast(
        msg: e.toString(),
        backgroundColor: AppColors.danger,
        textColor: Colors.white,
      );
    }
  }

  @override
  void dispose() {
    _phone.dispose();
    _pass.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
          child: Form(
            key: _form,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [

                // ── Logo ────────────────────────────────────────────────
                const SizedBox(height: 8),
                Center(
                  child: Container(
                    width: 100, height: 100,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(26),
                      boxShadow: [BoxShadow(
                        color: AppColors.blueDark.withValues(alpha: 0.14),
                        blurRadius: 24, offset: const Offset(0, 8),
                      )],
                    ),
                    padding: const EdgeInsets.all(12),
                    child: Image.asset('assets/images/logo.png', fit: BoxFit.contain),
                  ),
                ),
                const SizedBox(height: 14),
                const Center(child: Text(
                  'شركة ابناء الفريد التجارية',
                  style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: AppColors.orange, fontFamily: 'Cairo'),
                )),
                const SizedBox(height: 20),

                // ── Title ────────────────────────────────────────────────
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: AppShadows.card,
                  ),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text('مرحباً بعودتك! 👋',
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: AppColors.blueDark, fontFamily: 'Cairo')),
                    const SizedBox(height: 4),
                    const Text('سجّل دخولك برقم هاتفك',
                      style: TextStyle(color: AppColors.gray, fontFamily: 'Cairo', fontSize: 13)),
                    const SizedBox(height: 22),

                    // ── Phone field ──────────────────────────────────────
                    const Text('رقم الهاتف',
                      style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700,
                          fontSize: 13, color: AppColors.blueDark)),
                    const SizedBox(height: 8),
                    Row(crossAxisAlignment: CrossAxisAlignment.start, children: [

                      // Prefix dropdown
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
                              DropdownMenuItem(
                                value: '+970',
                                child: Padding(
                                  padding: EdgeInsets.only(left: 2),
                                  child: Text('🇵🇸  +970',
                                    style: TextStyle(fontFamily: 'Cairo', fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.blueDark)),
                                ),
                              ),
                              DropdownMenuItem(
                                value: '+972',
                                child: Padding(
                                  padding: EdgeInsets.only(left: 2),
                                  child: Text('🇮🇱  +972',
                                    style: TextStyle(fontFamily: 'Cairo', fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.blueDark)),
                                ),
                              ),
                            ],
                            onChanged: (v) => setState(() => _prefix = v ?? '+970'),
                          ),
                        ),
                      ),

                      const SizedBox(width: 10),

                      // Number input
                      Expanded(
                        child: TextFormField(
                          controller: _phone,
                          keyboardType: TextInputType.phone,
                          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                          textDirection: TextDirection.ltr,
                          style: const TextStyle(
                            fontFamily: 'Cairo', fontSize: 15, letterSpacing: 1.5,
                            color: AppColors.text,
                          ),
                          decoration: InputDecoration(
                            filled: true,
                            fillColor: AppColors.grayBg,
                            hintText: 'XXXXXXXXX',
                            hintStyle: const TextStyle(color: AppColors.border, letterSpacing: 1.5, fontSize: 14),
                            prefixIcon: const Icon(Icons.phone_outlined, size: 18, color: AppColors.gray),
                            helperText: '9 أرقام (بدون الصفر)',
                            helperStyle: const TextStyle(fontFamily: 'Cairo', fontSize: 10, color: AppColors.grayLight),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: const BorderSide(color: AppColors.border, width: 1.2),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: const BorderSide(color: AppColors.border, width: 1.2),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: const BorderSide(color: AppColors.orange, width: 1.8),
                            ),
                          ),
                          validator: (v) {
                            var d = (v ?? '').replaceAll(RegExp(r'\D'), '');
                            if (d.startsWith('0')) d = d.substring(1);
                            if (d.length != 9) return 'أدخل 9 أرقام صحيحة';
                            return null;
                          },
                        ),
                      ),
                    ]),
                    const SizedBox(height: 16),

                    // ── Password ─────────────────────────────────────────
                    const Text('كلمة المرور',
                      style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700,
                          fontSize: 13, color: AppColors.blueDark)),
                    const SizedBox(height: 8),
                    TextFormField(
                      controller: _pass,
                      obscureText: !_showPass,
                      style: const TextStyle(
                        fontFamily: 'Cairo', fontSize: 15, color: AppColors.text,
                      ),
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: AppColors.grayBg,
                        hintText: '••••••••',
                        hintStyle: const TextStyle(color: AppColors.grayLight, fontSize: 16),
                        prefixIcon: const Icon(Icons.lock_outline, size: 18, color: AppColors.gray),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _showPass ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                            size: 18, color: AppColors.gray,
                          ),
                          onPressed: () => setState(() => _showPass = !_showPass),
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppColors.border, width: 1.2),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppColors.border, width: 1.2),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppColors.orange, width: 1.8),
                        ),
                      ),
                      validator: (v) => (v == null || v.length < 6) ? '6 أحرف على الأقل' : null,
                    ),
                    const SizedBox(height: 24),

                    // ── Login button ─────────────────────────────────────
                    ElevatedButton(
                      onPressed: auth.loading ? null : _submit,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 15),
                      ),
                      child: auth.loading
                        ? const SizedBox(width: 22, height: 22,
                            child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Text('تسجيل الدخول',
                            style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900, fontSize: 15)),
                    ),
                  ]),
                ),
                const SizedBox(height: 16),

                // ── Register link ────────────────────────────────────────
                Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Text('ليس لديك حساب؟ ',
                    style: TextStyle(color: AppColors.gray, fontFamily: 'Cairo', fontSize: 13)),
                  TextButton(
                    onPressed: () => Navigator.of(context).push(
                      MaterialPageRoute(builder: (_) => const RegisterScreen())),
                    child: const Text('إنشاء حساب',
                      style: TextStyle(color: AppColors.orange, fontWeight: FontWeight.w900,
                          fontFamily: 'Cairo', fontSize: 13)),
                  ),
                ]),

                // ── Phone preview ────────────────────────────────────────
                if (_phone.text.isNotEmpty)
                  Center(
                    child: AnimatedOpacity(
                      opacity: _phone.text.isNotEmpty ? 1 : 0,
                      duration: const Duration(milliseconds: 200),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppColors.blueLight,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          _fullPhone,
                          style: const TextStyle(
                            color: AppColors.blue, fontFamily: 'Cairo',
                            fontWeight: FontWeight.w700, fontSize: 13, letterSpacing: 1,
                          ),
                          textDirection: TextDirection.ltr,
                        ),
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
