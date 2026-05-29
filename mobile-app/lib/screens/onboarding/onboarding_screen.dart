import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../l10n/app_strings.dart';
import '../../providers/locale_provider.dart';
import '../../theme/app_theme.dart';


class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key, required this.dest, required this.prefs});
  final Widget dest;
  final SharedPreferences prefs;

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final _controller = PageController();
  int _page = 0;

  List<_Slide> _slides(S s) => [
    _Slide(emoji: '🛍️', tag: s.onboardTag1, title: s.onboardTitle1, subtitle: s.onboardSub1),
    _Slide(emoji: '🚀', tag: s.onboardTag2, title: s.onboardTitle2, subtitle: s.onboardSub2),
    _Slide(emoji: '💎', tag: s.onboardTag3, title: s.onboardTitle3, subtitle: s.onboardSub3),
  ];

  void _finish() {
    widget.prefs.setBool('onboarding_seen', true);
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(builder: (_) => widget.dest),
    );
  }

  void _next(int total) {
    if (_page < total - 1) {
      _controller.nextPage(duration: const Duration(milliseconds: 400), curve: Curves.easeInOut);
    } else {
      _finish();
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final s      = context.watch<LocaleProvider>().s;
    final slides = _slides(s);

    return Scaffold(
      body: Stack(children: [
        // Blue gradient background
        Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [Color(0xFF0F2660), AppColors.blue, Color(0xFF2448A8)],
            ),
          ),
        ),
        // Decorative circles
        Positioned(top: -80, right: -80, child: _circle(280, AppColors.orange.withValues(alpha: 0.12))),
        Positioned(bottom: 220, left: -40, child: _circle(200, Colors.white.withValues(alpha: 0.04))),
        Positioned(top: 100, left: 30, child: _circle(100, AppColors.orange.withValues(alpha: 0.08))),

        Column(children: [
          // Slides
          Expanded(
            child: PageView.builder(
              controller: _controller,
              itemCount: slides.length,
              onPageChanged: (i) => setState(() => _page = i),
              itemBuilder: (_, i) => _SlideView(slide: slides[i]),
            ),
          ),

          // Bottom white panel
          Container(
            decoration: const BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
            ),
            padding: EdgeInsets.fromLTRB(24, 20, 24, MediaQuery.of(context).padding.bottom + 20),
            child: Column(mainAxisSize: MainAxisSize.min, children: [

              // ── Language picker (3 flag buttons) ────────────────
              _LanguagePicker(s: s),
              const SizedBox(height: 18),

              // Dots
              Row(mainAxisAlignment: MainAxisAlignment.center, children: List.generate(slides.length, (i) {
                final active = i == _page;
                return AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  margin: const EdgeInsets.symmetric(horizontal: 4),
                  width: active ? 24 : 8, height: 4,
                  decoration: BoxDecoration(
                    color: active ? AppColors.orange : const Color(0xFFE5E7EB),
                    borderRadius: BorderRadius.circular(2),
                  ),
                );
              })),
              const SizedBox(height: 20),

              // Main button
              SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton(
                  onPressed: () => _next(slides.length),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.orange,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: Text(
                    _page < slides.length - 1 ? s.onboardNext : s.onboardStart,
                    style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, fontFamily: 'Cairo'),
                  ),
                ),
              ),
              const SizedBox(height: 12),

              // Skip link
              GestureDetector(
                onTap: _finish,
                child: RichText(
                  text: TextSpan(
                    text: s.onboardSkipOr,
                    style: const TextStyle(fontSize: 12, color: AppColors.gray, fontFamily: 'Cairo'),
                    children: [
                      TextSpan(
                        text: s.onboardSkip,
                        style: const TextStyle(color: AppColors.blue, fontWeight: FontWeight.w700),
                      ),
                    ],
                  ),
                ),
              ),
            ]),
          ),
        ]),
      ]),
    );
  }

  Widget _circle(double size, Color color) => Container(
    width: size, height: size,
    decoration: BoxDecoration(shape: BoxShape.circle, color: color),
  );
}

// ── Slide data ────────────────────────────────────────────────────────────────

class _Slide {
  const _Slide({required this.emoji, required this.tag, required this.title, required this.subtitle});
  final String emoji;
  final String tag;
  final String title;
  final String subtitle;
}

// ── Slide widget ──────────────────────────────────────────────────────────────

class _SlideView extends StatelessWidget {
  const _SlideView({required this.slide});
  final _Slide slide;

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      bottom: false,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 32),
        child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
          // App logo
          Container(
            width: 90, height: 90,
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              boxShadow: [BoxShadow(
                color: Colors.black.withValues(alpha: 0.30),
                blurRadius: 32, offset: const Offset(0, 12),
              )],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(24),
              child: Image.asset('assets/images/logo.png', fit: BoxFit.contain),
            ),
          ),
          const SizedBox(height: 28),

          // Tag pill
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
            decoration: BoxDecoration(
              color: AppColors.orange.withValues(alpha: 0.2),
              border: Border.all(color: AppColors.orange.withValues(alpha: 0.4)),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(slide.tag,
              style: const TextStyle(
                color: Color(0xFFFFA76B), fontSize: 10,
                fontWeight: FontWeight.w800, fontFamily: 'Cairo', letterSpacing: 1,
              )),
          ),
          const SizedBox(height: 20),

          // Big emoji
          Text(slide.emoji, style: const TextStyle(fontSize: 72)),
          const SizedBox(height: 20),

          // Title
          Text(slide.title,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: Colors.white, fontSize: 26,
              fontWeight: FontWeight.w900, fontFamily: 'Cairo', height: 1.35,
            )),
          const SizedBox(height: 14),

          // Subtitle
          Text(slide.subtitle,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: Colors.white70, fontSize: 13,
              fontFamily: 'Cairo', height: 1.7,
            )),
        ]),
      ),
    );
  }
}

// ── Language Picker ───────────────────────────────────────────────────────────

class _LanguagePicker extends StatelessWidget {
  const _LanguagePicker({required this.s});
  final S s;

  static const _langs = [
    ('ar', '🇸🇦', 'العربية'),
    ('he', '🇮🇱', 'עברית'),
    ('en', '🇬🇧', 'English'),
  ];

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<LocaleProvider>();
    return Column(children: [
      Text(s.chooseLanguage,
        style: const TextStyle(
          fontSize: 11, fontWeight: FontWeight.w700,
          fontFamily: 'Cairo', color: AppColors.gray,
        )),
      const SizedBox(height: 10),
      Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: _langs.map((lang) {
          final (code, flag, name) = lang;
          final active = provider.code == code;
          return GestureDetector(
            onTap: () => provider.setLocale(code),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              margin: const EdgeInsets.symmetric(horizontal: 5),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              decoration: BoxDecoration(
                color: active ? AppColors.orange : const Color(0xFFF1F5F9),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: active ? AppColors.orange : const Color(0xFFE2E8F0),
                  width: active ? 2 : 1,
                ),
              ),
              child: Row(mainAxisSize: MainAxisSize.min, children: [
                Text(flag, style: const TextStyle(fontSize: 16)),
                const SizedBox(width: 6),
                Text(name,
                  style: TextStyle(
                    fontSize: 11,
                    fontFamily: 'Cairo',
                    fontWeight: FontWeight.w700,
                    color: active ? Colors.white : AppColors.text,
                  )),
              ]),
            ),
          );
        }).toList(),
      ),
    ]);
  }
}
