import 'dart:io';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import '../models/user.dart';
import '../services/api_service.dart' show ApiService, ApiException;

class AuthProvider extends ChangeNotifier {
  AppUser? _user;
  bool _loading = false;

  AppUser? get user    => _user;
  bool get isLoggedIn  => _user != null;
  bool get loading     => _loading;

  /// Called on app startup. If a token exists, fetch /auth/me. Otherwise no-op.
  Future<void> bootstrap() async {
    final t = await ApiService.instance.token();
    if (t == null || t.isEmpty) return;
    try {
      final res = await ApiService.instance.get('/auth/me');
      _user = AppUser.fromJson((res as Map)['user'] as Map<String, dynamic>);
      notifyListeners();
    } on ApiException catch (e) {
      // Only invalidate on explicit auth rejection — not on network errors
      if (e.statusCode == 401 || e.statusCode == 403) {
        await ApiService.instance.clearToken();
      }
      // Network / server errors: keep token, user stays logged in next open
    } catch (_) {
      // Unexpected error — do NOT clear token to avoid spurious logouts
    }
  }

  Future<void> login(String phone, String password, {String? fcmToken, String? deviceType}) async {
    _loading = true; notifyListeners();
    try {
      final res = await ApiService.instance.post('/auth/login', data: {
        'phone':    phone.trim(),
        'password': password,
      });
      final m = res as Map<String, dynamic>;
      await ApiService.instance.setToken(m['token'] as String);
      _user = AppUser.fromJson(m['user'] as Map<String, dynamic>);
      // Push FCM token to backend after login — fire & forget, never blocks login
      _syncFcmToken();
    } finally {
      _loading = false; notifyListeners();
    }
  }

  /// Sends the current FCM token to the backend. Called after login/register.
  /// Runs in the background — never throws or blocks the caller.
  Future<void> _syncFcmToken() async {
    try {
      final token = await FirebaseMessaging.instance
          .getToken()
          .timeout(const Duration(seconds: 8));
      if (token == null) return;
      await ApiService.instance.put('/auth/fcm-token', data: {
        'fcm_token':   token,
        'device_type': Platform.isIOS ? 'ios' : 'android',
      });
    } catch (_) {}
  }

  Future<void> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    String? fcmToken,
    String? deviceType,
  }) async {
    _loading = true; notifyListeners();
    try {
      final res = await ApiService.instance.post('/auth/register', data: {
        'name':                  name,
        'email':                 email.trim(),
        'phone':                 phone,
        'password':              password,
        'password_confirmation': password,
      });
      final m = res as Map<String, dynamic>;
      await ApiService.instance.setToken(m['token'] as String);
      _user = AppUser.fromJson(m['user'] as Map<String, dynamic>);
      // Push FCM token after register — fire & forget
      _syncFcmToken();
    } finally {
      _loading = false; notifyListeners();
    }
  }

  Future<void> logout() async {
    try {
      await ApiService.instance.post('/auth/logout');
    } catch (_) {}
    await ApiService.instance.clearToken();
    _user = null;
    notifyListeners();
  }

  Future<void> refreshMe() async {
    try {
      final res = await ApiService.instance.get('/auth/me');
      _user = AppUser.fromJson((res as Map)['user'] as Map<String, dynamic>);
      notifyListeners();
    } catch (_) {}
  }
}
