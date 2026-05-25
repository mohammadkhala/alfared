import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../l10n/app_strings.dart';

class LocaleProvider extends ChangeNotifier {
  static const _key = 'app_locale';
  static const supported = ['ar', 'he', 'en'];

  String _code = 'ar';

  String get code => _code;
  Locale get locale => Locale(_code);
  S get s => AppStrings.from(_code);

  Future<void> bootstrap() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getString(_key);
    if (saved != null && supported.contains(saved)) {
      _code = saved;
      notifyListeners();
    }
  }

  Future<void> setLocale(String code) async {
    if (!supported.contains(code) || code == _code) return;
    _code = code;
    notifyListeners();
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_key, code);
  }

  /// Open a language-picker bottom sheet.
  static Future<void> showPicker(BuildContext context) {
    final provider = context.read<LocaleProvider>();
    return showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (_) => _LanguageSheet(provider: provider),
    );
  }
}

// ── Language picker sheet ────────────────────────────────────────────────

class _LanguageSheet extends StatelessWidget {
  const _LanguageSheet({required this.provider});
  final LocaleProvider provider;

  static const _langs = [
    ('ar', '🇸🇦', 'العربية'),
    ('he', '🇮🇱', 'עברית'),
    ('en', '🇬🇧', 'English'),
  ];

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Container(
          width: 40, height: 4,
          decoration: BoxDecoration(color: const Color(0xFFE2E8F0), borderRadius: BorderRadius.circular(2)),
        ),
        const SizedBox(height: 16),
        const Text('اختر اللغة / Choose Language',
          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
        const SizedBox(height: 16),
        ..._langs.map((lang) {
          final (code, flag, name) = lang;
          final active = provider.code == code;
          return GestureDetector(
            onTap: () {
              provider.setLocale(code);
              Navigator.of(context).pop();
            },
            child: Container(
              margin: const EdgeInsets.only(bottom: 10),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: BoxDecoration(
                color: active ? const Color(0xFFFFF3E8) : const Color(0xFFF8FAFC),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: active ? const Color(0xFFE8711A) : const Color(0xFFE2E8F0),
                  width: active ? 2 : 1,
                ),
              ),
              child: Row(children: [
                Text(flag, style: const TextStyle(fontSize: 24)),
                const SizedBox(width: 14),
                Text(name, style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: active ? const Color(0xFFE8711A) : const Color(0xFF1A202C),
                )),
                const Spacer(),
                if (active)
                  const Icon(Icons.check_circle_rounded, color: Color(0xFFE8711A), size: 22),
              ]),
            ),
          );
        }),
      ]),
    );
  }
}
