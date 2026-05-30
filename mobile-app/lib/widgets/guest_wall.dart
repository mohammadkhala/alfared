import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/cart_provider.dart';
import '../providers/locale_provider.dart';
import '../theme/app_theme.dart';
import '../screens/auth/login_screen.dart';
import '../screens/auth/register_screen.dart';

/// Full-screen guard shown when a guest tries to access a protected tab.
/// After login/register the auth state notifies listeners → parent rebuilds.
class GuestWall extends StatelessWidget {
  final IconData icon;
  const GuestWall({super.key, this.icon = Icons.lock_outline_rounded});

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 36, vertical: 48),
            child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [

              // ── Icon ──────────────────────────────────────────────
              Container(
                width: 96, height: 96,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [AppColors.blueDark, AppColors.blue],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: AppShadows.elevated,
                ),
                child: Icon(icon, color: Colors.white, size: 44),
              ),
              const SizedBox(height: 26),

              // ── Title ─────────────────────────────────────────────
              Text(
                s.guestLoginTitle,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 20, fontWeight: FontWeight.w900,
                  color: AppColors.blueDark, fontFamily: 'Cairo',
                ),
              ),
              const SizedBox(height: 10),

              // ── Subtitle ──────────────────────────────────────────
              Text(
                s.guestLoginSub,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: AppColors.gray, fontSize: 13,
                  fontFamily: 'Cairo', height: 1.8,
                ),
              ),
              const SizedBox(height: 36),

              // ── Login button ──────────────────────────────────────
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () async {
                    await Navigator.of(context).push<void>(MaterialPageRoute(
                        builder: (_) => const LoginScreen(returnAfterLogin: true)));
                    // auth state changed → parent rebuilds; also reload cart
                    if (context.mounted) {
                      await context.read<CartProvider>().load();
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 15),
                  ),
                  child: Text(
                    s.login,
                    style: const TextStyle(
                      fontFamily: 'Cairo', fontWeight: FontWeight.w900, fontSize: 15),
                  ),
                ),
              ),
              const SizedBox(height: 12),

              // ── Register button ───────────────────────────────────
              SizedBox(
                width: double.infinity,
                child: OutlinedButton(
                  onPressed: () {
                    Navigator.of(context).push<void>(MaterialPageRoute(
                        builder: (_) => const RegisterScreen()));
                  },
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    side: const BorderSide(color: AppColors.orange, width: 1.5),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14)),
                  ),
                  child: Text(
                    s.loginCreate,
                    style: const TextStyle(
                      color: AppColors.orange, fontFamily: 'Cairo',
                      fontWeight: FontWeight.w900, fontSize: 15,
                    ),
                  ),
                ),
              ),

            ]),
          ),
        ),
      ),
    );
  }
}
