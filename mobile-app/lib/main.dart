import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:provider/provider.dart';

import 'providers/auth_provider.dart';
import 'providers/cart_provider.dart';
import 'providers/locale_provider.dart';
import 'providers/theme_provider.dart';
import 'screens/splash_screen.dart';
import 'services/fcm_service.dart';
import 'theme/app_theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  try {
    await Firebase.initializeApp();
    await FcmService.init();
  } catch (_) {}
  runApp(const AlfaredApp());
}

class AlfaredApp extends StatelessWidget {
  const AlfaredApp({super.key});
  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => CartProvider()),
        ChangeNotifierProvider(create: (_) => ThemeProvider()..bootstrap()),
        ChangeNotifierProvider(create: (_) => LocaleProvider()..bootstrap()),
      ],
      child: Consumer2<ThemeProvider, LocaleProvider>(
        builder: (_, theme, localeProvider, __) => MaterialApp(
          title: 'أبناء الفريد',
          debugShowCheckedModeBanner: false,
          theme: AppTheme.light(),
          darkTheme: AppTheme.dark(),
          themeMode: theme.mode,
          locale: localeProvider.locale,
          supportedLocales: const [Locale('ar'), Locale('he'), Locale('en')],
          localizationsDelegates: const [
            GlobalMaterialLocalizations.delegate,
            GlobalWidgetsLocalizations.delegate,
            GlobalCupertinoLocalizations.delegate,
          ],
          builder: (context, child) => Directionality(
            textDirection: localeProvider.code == 'he'
                ? TextDirection.rtl
                : localeProvider.code == 'en'
                    ? TextDirection.ltr
                    : TextDirection.rtl,
            child: child!,
          ),
          home: const SplashScreen(),
        ),
      ),
    );
  }
}
