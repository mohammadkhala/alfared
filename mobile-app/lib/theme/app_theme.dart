import 'package:flutter/material.dart';

class AppColors {
  static const Color blue       = Color(0xFF1B3B8C);
  static const Color blueDark   = Color(0xFF0F2660);
  static const Color blueLight  = Color(0xFFEEF2FF);
  static const Color orange     = Color(0xFFE8711A);
  static const Color orangeDark = Color(0xFFC85E10);
  static const Color orangeLight= Color(0xFFFFF3E8);
  static const Color text       = Color(0xFF1A1A2E);
  static const Color gray       = Color(0xFF6B7280);
  static const Color grayLight  = Color(0xFF9CA3AF);
  static const Color grayBg     = Color(0xFFF5F6FA);
  static const Color border     = Color(0xFFE5E7EB);
  static const Color success    = Color(0xFF10B981);
  static const Color successLight = Color(0xFFECFDF5);
  static const Color danger     = Color(0xFFEF4444);
  static const Color dangerLight  = Color(0xFFFEF2F2);
  static const Color amber      = Color(0xFFFFC107);

  // ── Dark mode tokens ──
  static const Color darkBg     = Color(0xFF0F172A);
  static const Color darkCard   = Color(0xFF1E293B);
  static const Color darkText   = Color(0xFFE2E8F0);
  static const Color darkBorder = Color(0xFF334155);
  static const Color darkGray   = Color(0xFF94A3B8);
}

class AppRadius {
  static const double xs = 8;
  static const double sm = 10;
  static const double md = 14;
  static const double lg = 16;
  static const double xl = 18;
  static const double xxl = 22;
}

class AppShadows {
  static List<BoxShadow> card = [
    BoxShadow(color: const Color(0xFF000000).withValues(alpha: 0.08), blurRadius: 20, offset: const Offset(0, 4)),
  ];
  static List<BoxShadow> elevated = [
    BoxShadow(color: const Color(0xFFE8711A).withValues(alpha: 0.35), blurRadius: 24, offset: const Offset(0, 8)),
  ];
}

class AppTheme {
  static ThemeData light() {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      primaryColor: AppColors.blue,
      scaffoldBackgroundColor: AppColors.grayBg,
      fontFamily: 'Cairo',
      colorScheme: const ColorScheme.light(
        primary: AppColors.blue, secondary: AppColors.orange,
        surface: Colors.white, error: AppColors.danger,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: Colors.white, foregroundColor: AppColors.blue, elevation: 0, centerTitle: true,
        titleTextStyle: TextStyle(color: AppColors.blue, fontFamily: 'Cairo', fontSize: 17, fontWeight: FontWeight.w900),
        iconTheme: IconThemeData(color: AppColors.blue),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.orange, foregroundColor: Colors.white, elevation: 0,
          textStyle: const TextStyle(fontFamily: 'Cairo', fontSize: 15, fontWeight: FontWeight.w800),
          padding: const EdgeInsets.symmetric(vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true, fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.border, width: 1.2)),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.orange, width: 1.8)),
        labelStyle: const TextStyle(color: AppColors.gray, fontFamily: 'Cairo'),
      ),
      cardTheme: CardThemeData(color: Colors.white, elevation: 0, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
      textTheme: const TextTheme(
        headlineLarge:  TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900, color: AppColors.blue),
        headlineMedium: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800, color: AppColors.blue),
        titleLarge:     TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800),
        bodyLarge:      TextStyle(fontFamily: 'Cairo', fontSize: 14, color: AppColors.text),
        bodyMedium:     TextStyle(fontFamily: 'Cairo', fontSize: 13, color: AppColors.text),
        labelLarge:     TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700),
      ),
    );
  }

  static ThemeData dark() {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.dark,
      primaryColor: AppColors.orange,
      scaffoldBackgroundColor: AppColors.darkBg,
      fontFamily: 'Cairo',
      colorScheme: const ColorScheme.dark(
        primary: AppColors.orange, secondary: AppColors.orange,
        surface: AppColors.darkCard, error: AppColors.danger,
        onSurface: AppColors.darkText,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: AppColors.darkCard, foregroundColor: AppColors.darkText, elevation: 0, centerTitle: true,
        titleTextStyle: TextStyle(color: AppColors.darkText, fontFamily: 'Cairo', fontSize: 17, fontWeight: FontWeight.w900),
        iconTheme: IconThemeData(color: AppColors.darkText),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.orange, foregroundColor: Colors.white, elevation: 0,
          textStyle: const TextStyle(fontFamily: 'Cairo', fontSize: 15, fontWeight: FontWeight.w800),
          padding: const EdgeInsets.symmetric(vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true, fillColor: AppColors.darkCard,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.darkBorder, width: 1.2)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.darkBorder, width: 1.2)),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.orange, width: 1.8)),
        labelStyle: const TextStyle(color: AppColors.darkGray, fontFamily: 'Cairo'),
      ),
      cardTheme: const CardThemeData(color: AppColors.darkCard, elevation: 0),
      textTheme: const TextTheme(
        headlineLarge:  TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900, color: AppColors.darkText),
        headlineMedium: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800, color: AppColors.darkText),
        titleLarge:     TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800, color: AppColors.darkText),
        bodyLarge:      TextStyle(fontFamily: 'Cairo', fontSize: 14, color: AppColors.darkText),
        bodyMedium:     TextStyle(fontFamily: 'Cairo', fontSize: 13, color: AppColors.darkText),
        labelLarge:     TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700, color: AppColors.darkText),
      ),
    );
  }
}
