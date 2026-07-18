import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Light/dark preference.
///
/// Defaults to light instead of following the device: opening dark on a
/// dark phone surprised users who never chose a dark theme.
class ThemeProvider extends ChangeNotifier {
  static const _key = 'theme_mode';

  ThemeMode _mode = ThemeMode.light;

  ThemeMode get mode => _mode;

  /// True when dark is actually showing.
  ///
  /// This used to be reachable while [_mode] was [ThemeMode.system]: on a dark
  /// device the screen rendered dark but this returned false, so the switch
  /// showed "light" and the first tap appeared to do nothing. Only light/dark
  /// are stored now, so the flag always matches what is on screen.
  bool get isDark => _mode == ThemeMode.dark;

  Future<void> bootstrap() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      // Anything other than an explicit "dark" — including the "system" value
      // written by earlier builds — resolves to light.
      _mode = prefs.getString(_key) == 'dark' ? ThemeMode.dark : ThemeMode.light;
    } catch (_) {
      _mode = ThemeMode.light;
    }
    notifyListeners();
  }

  Future<void> setMode(ThemeMode m) async {
    _mode = m == ThemeMode.dark ? ThemeMode.dark : ThemeMode.light;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_key, isDark ? 'dark' : 'light');
    } catch (_) {
      // Not persisted — the in-memory choice still applies this session.
    }
  }

  Future<void> toggle() async =>
      setMode(isDark ? ThemeMode.light : ThemeMode.dark);
}
