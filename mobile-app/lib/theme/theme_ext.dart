import 'package:flutter/material.dart';
import 'app_theme.dart';

/// Theme-aware surface colours.
///
/// Screens used to hard-code `Colors.white` cards and dark text, which meant
/// dark mode rendered white panels with dark text on a dark page. These
/// getters resolve against the active brightness instead.
extension AppSurfaces on BuildContext {
  bool get isDark => Theme.of(this).brightness == Brightness.dark;

  /// Page background.
  Color get bg => isDark ? AppColors.darkBg : AppColors.grayBg;

  /// Cards, sheets, list rows.
  Color get card => isDark ? AppColors.darkCard : Colors.white;

  /// Primary body text.
  Color get text => isDark ? AppColors.darkText : AppColors.text;

  /// Secondary/muted text.
  Color get muted => isDark ? AppColors.darkGray : AppColors.gray;

  /// Hairlines and outlines.
  Color get line => isDark ? AppColors.darkBorder : AppColors.border;

  /// Tinted fills (icon chips, subtle blocks) that must stay legible on dark.
  Color tint(Color base) =>
      isDark ? base.withValues(alpha: 0.22) : base.withValues(alpha: 0.12);
}
