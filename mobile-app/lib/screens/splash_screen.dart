import 'dart:async';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../l10n/app_strings.dart';
import '../providers/auth_provider.dart';
import '../providers/locale_provider.dart';
import '../providers/cart_provider.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'home/main_navigation.dart';
import 'onboarding/onboarding_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});
  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  late AnimationController _ctrl;

  /// Store logo URL cached from a previous run, so the splash can show the
  /// website's current logo without waiting on the network at launch.
  static const _logoPrefKey = 'store_logo_url';
  String? _remoteLogo;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(vsync: this, duration: const Duration(milliseconds: 700))..forward();
    _boot();
  }

  /// Refreshes the cached logo URL for the next launch. Never blocks startup.
  Future<void> _refreshLogo(SharedPreferences prefs) async {
    try {
      final cfg = await ApiService.instance
          .get('/app-config')
          .timeout(const Duration(seconds: 8));
      final url = ((cfg as Map)['data'] as Map?)?['store_logo'] as String?;
      if (url != null && url.isNotEmpty) {
        await prefs.setString(_logoPrefKey, url);
      }
    } catch (_) {
      // Offline or config unavailable — keep whatever is cached.
    }
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  Future<void> _boot() async {
    final auth = context.read<AuthProvider>();
    final cart = context.read<CartProvider>();

    // Show the logo cached from the last run straight away, then refresh it in
    // the background for next launch — never block startup on the network.
    unawaited(() async {
      try {
        final prefs = await SharedPreferences.getInstance();
        final cached = prefs.getString(_logoPrefKey);
        if (cached != null && cached.isNotEmpty && mounted) {
          setState(() => _remoteLogo = cached);
        }
        await _refreshLogo(prefs);
      } catch (_) {}
    }());

    // Restore the session in the background — this must NEVER block navigation,
    // otherwise a slow/failed network call would freeze the splash forever.
    // The store is fully usable for guests, and logged-in state updates via
    // providers once bootstrap finishes.
    unawaited(() async {
      try {
        await auth.bootstrap();
        if (auth.isLoggedIn) await cart.load();
      } catch (_) {}
    }());

    // Fixed, network-independent splash duration.
    await Future.delayed(const Duration(milliseconds: 1600));
    if (!mounted) return;

    final prefs = await SharedPreferences.getInstance();
    if (!mounted) return;
    final onboardingSeen = prefs.getBool('onboarding_seen') ?? false;
    const dest = MainNavigation(); // guests go straight to the store
    if (!onboardingSeen) {
      Navigator.of(context).pushReplacement(MaterialPageRoute(
        builder: (_) => OnboardingScreen(dest: dest, prefs: prefs),
      ));
    } else {
      Navigator.of(context).pushReplacement(MaterialPageRoute(builder: (_) => dest));
    }
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;
    return Scaffold(
      body: Stack(children: [
        // Background gradient
        Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter, end: Alignment.bottomCenter,
              colors: [Color(0xFF0F2660), AppColors.blue, Color(0xFF2448A8)],
            ),
          ),
        ),

        // Decorative circles
        Positioned(top: -80, right: -80, child: _circle(280, AppColors.orange.withValues(alpha: 0.12))),
        Positioned(bottom: -40, left: -40, child: _circle(200, Colors.white.withValues(alpha: 0.04))),
        Positioned(bottom: 100, right: 24, child: _circle(100, AppColors.orange.withValues(alpha: 0.08))),

        // Content
        SafeArea(
          child: Center(
            child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
              ScaleTransition(
                scale: CurvedAnimation(parent: _ctrl, curve: Curves.easeOutBack),
                child: Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(28),
                    boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.35), blurRadius: 32, offset: const Offset(0, 16))],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(28),
                    child: _remoteLogo == null
                        // No cached remote logo yet — show the bundled one.
                        ? Image.asset(
                            'assets/images/logo.png',
                            width: 140, height: 140, fit: BoxFit.contain,
                          )
                        : CachedNetworkImage(
                            imageUrl: _remoteLogo!,
                            width: 140, height: 140, fit: BoxFit.contain,
                            placeholder: (_, __) => Image.asset(
                              'assets/images/logo.png',
                              width: 140, height: 140, fit: BoxFit.contain,
                            ),
                            errorWidget: (_, __, ___) => Image.asset(
                              'assets/images/logo.png',
                              width: 140, height: 140, fit: BoxFit.contain,
                            ),
                          ),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              FadeTransition(
                opacity: _ctrl,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
                  decoration: BoxDecoration(
                    color: AppColors.orange.withValues(alpha: 0.2),
                    border: Border.all(color: AppColors.orange.withValues(alpha: 0.4)),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(s.splashTagline1,
                    style: const TextStyle(color: Color(0xFFFFA76B), fontSize: 11, fontWeight: FontWeight.w800, fontFamily: 'Cairo', letterSpacing: 1)),
                ),
              ),
              const SizedBox(height: 14),

              Text(s.brandTagline,
                textAlign: TextAlign.center,
                style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
              const SizedBox(height: 10),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 28),
                child: Column(children: [
                  Text(
                    s.splashChina,
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.white, fontSize: 13, fontFamily: 'Cairo', height: 1.8, fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    s.splashProducts,
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.white70, fontSize: 11, fontFamily: 'Cairo', height: 1.7),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    s.splashDelivery,
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Color(0xFFFFA76B), fontSize: 11, fontFamily: 'Cairo', height: 1.7, fontWeight: FontWeight.w700),
                  ),
                ]),
              ),
              const SizedBox(height: 44),

              // Loader
              SizedBox(
                width: 30, height: 30,
                child: CircularProgressIndicator(
                  color: AppColors.orange, strokeWidth: 3,
                  backgroundColor: Colors.white.withValues(alpha: 0.1),
                ),
              ),
            ]),
          ),
        ),
      ]),
    );
  }

  Widget _circle(double size, Color color) => Container(
    width: size, height: size,
    decoration: BoxDecoration(shape: BoxShape.circle, color: color),
  );
}
