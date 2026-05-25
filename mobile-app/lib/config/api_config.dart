/// API configuration. Switch baseUrl for production deploy.
class ApiConfig {
  /// For local dev:
  ///   - Android emulator → http://10.0.2.2:8001
  ///   - iOS simulator    → http://127.0.0.1:8001
  ///   - Real device      → use your computer's LAN IP, e.g. http://192.168.1.X:8001
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://192.168.1.6:8001',
  );

  static const String apiPrefix = '/api/v1';
  static String get api => '$baseUrl$apiPrefix';

  /// Custom UA header so the backend tracker can classify visits as the app.
  static const String appUaToken = 'AlfaredApp/1.0';

  static const Duration timeout = Duration(seconds: 30);
}
