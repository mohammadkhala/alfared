import 'dart:io' show Platform;
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/api_config.dart';

/// Central API client. Singleton — use [ApiService.instance].
/// Wraps Dio with a token interceptor that auto-injects the Sanctum bearer token.
class ApiService {
  ApiService._() {
    _dio = Dio(BaseOptions(
      baseUrl: ApiConfig.api,
      connectTimeout: ApiConfig.timeout,
      receiveTimeout: ApiConfig.timeout,
      headers: {
        'Accept': 'application/json',
        'X-App-Source': 'app',
        'User-Agent': '${ApiConfig.appUaToken} (${Platform.operatingSystem})',
      },
      validateStatus: (s) => s != null && s < 500,
    ));

    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: 'auth_token');
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (e, handler) {
        // Surface 401 by clearing the token automatically
        if (e.response?.statusCode == 401) {
          _storage.delete(key: 'auth_token');
        }
        return handler.next(e);
      },
    ));
  }

  static final ApiService instance = ApiService._();
  late final Dio _dio;
  final _storage = const FlutterSecureStorage();

  Dio get dio => _dio;

  Future<void> setToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }

  Future<String?> token() => _storage.read(key: 'auth_token');

  Future<void> clearToken() async {
    await _storage.delete(key: 'auth_token');
  }

  /// Returns the data part of a successful response, or throws a friendly ApiException.
  Future<dynamic> request(
    String method,
    String path, {
    Map<String, dynamic>? data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final resp = await _dio.request(
        path,
        data: data,
        queryParameters: queryParameters,
        options: Options(method: method),
      );
      if (resp.statusCode != null && resp.statusCode! >= 200 && resp.statusCode! < 300) {
        return resp.data;
      }
      final msg = _extractErrorMessage(resp.data);
      throw ApiException(msg, statusCode: resp.statusCode, raw: resp.data);
    } on DioException catch (e) {
      final msg = _extractErrorMessage(e.response?.data) ??
          (e.type == DioExceptionType.connectionTimeout
              ? 'انتهت مهلة الاتصال. تحقّق من الإنترنت.'
              : 'تعذّر الاتصال بالخادم.');
      throw ApiException(msg, statusCode: e.response?.statusCode, raw: e.response?.data);
    }
  }

  Future<dynamic> get(String path, {Map<String, dynamic>? query}) =>
      request('GET', path, queryParameters: query);

  Future<dynamic> post(String path, {Map<String, dynamic>? data}) =>
      request('POST', path, data: data);

  Future<dynamic> put(String path, {Map<String, dynamic>? data}) =>
      request('PUT', path, data: data);

  Future<dynamic> delete(String path, {Map<String, dynamic>? data}) =>
      request('DELETE', path, data: data);

  String? _extractErrorMessage(dynamic data) {
    if (data is Map) {
      if (data['message'] is String) return data['message'];
      if (data['error']   is String) return data['error'];
      if (data['errors']  is Map) {
        // Take the first validation error
        final firstField = data['errors'].values.first;
        if (firstField is List && firstField.isNotEmpty) {
          return firstField.first.toString();
        }
      }
    }
    return null;
  }
}

class ApiException implements Exception {
  final String? message;
  final int? statusCode;
  final dynamic raw;
  const ApiException(this.message, {this.statusCode, this.raw});

  @override
  String toString() => message ?? 'خطأ غير معروف';
}
