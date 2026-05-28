import 'dart:io';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'api_service.dart';

/// Wraps Firebase Messaging — request permission, subscribe, and forward
/// the device token to our Laravel backend whenever it changes.
class FcmService {
  static final FlutterLocalNotificationsPlugin _local = FlutterLocalNotificationsPlugin();
  static bool _initialized = false;

  /// Call once on app startup AFTER Firebase.initializeApp().
  static Future<void> init() async {
    if (_initialized) return;
    _initialized = true;

    try {
      // Initialize local notifications (for in-app foreground display)
      const initSettings = InitializationSettings(
        android: AndroidInitializationSettings('@mipmap/ic_launcher'),
        iOS: DarwinInitializationSettings(),
      );
      await _local.initialize(initSettings);

      // Request permission (iOS) / register (Android)
      await FirebaseMessaging.instance.requestPermission(
        alert: true, badge: true, sound: true,
      );

      // Foreground messages — show as a local notification
      FirebaseMessaging.onMessage.listen(_handleForeground);

      // Listen for token refresh and push to backend
      FirebaseMessaging.instance.onTokenRefresh.listen(_sendTokenToBackend);

      // Initial token
      final token = await FirebaseMessaging.instance.getToken();
      if (token != null) {
        await _sendTokenToBackend(token);
      }
    } catch (e) {
      // Firebase may not be configured yet — non-fatal
      debugPrint('FCM init skipped: $e');
    }
  }

  static Future<void> _sendTokenToBackend(String token) async {
    try {
      await ApiService.instance.put('/auth/fcm-token', data: {
        'fcm_token':   token,
        'device_type': Platform.isIOS ? 'ios' : 'android',
      });
    } catch (_) {/* user might not be logged in yet */}
  }

  static void _handleForeground(RemoteMessage msg) {
    final n     = msg.notification;
    final title = n?.title ?? msg.data['title'] ?? '';
    final body  = n?.body  ?? msg.data['body']  ?? '';
    if (title.isEmpty && body.isEmpty) return;

    _local.show(
      msg.hashCode,
      title,
      body,
      const NotificationDetails(
        android: AndroidNotificationDetails(
          'orders_channel',
          'إشعارات الطلبات',
          channelDescription: 'تحديثات حالة الطلبات',
          importance: Importance.max,
          priority: Priority.high,
          playSound: true,
        ),
        iOS: DarwinNotificationDetails(),
      ),
    );
  }
}
