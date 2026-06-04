import 'dart:convert';

class ImageHelper {
  static void _log(String message, Map<String, dynamic> data, String hypothesisId) {
    // #region agent log
    print('[DEBUG-f6fb39] ${jsonEncode({'msg': message, 'data': data, 'hyp': hypothesisId})}');
    // #endregion
  }

  static String? cleanUrl(String? url) {
    if (url == null) return null;

    // #region agent log
    if (url.contains('storage') || url.contains('http')) {
      _log('cleanUrl_input', {'url': url}, 'A');
    }
    // #endregion

    // Fix for backend bug where it prepends storage path to absolute URLs
    if (url.contains('/storage/http')) {
      final index = url.indexOf('/storage/http');
      final cleaned = url.substring(index + 9);
      // #region agent log
      _log('cleanUrl_fixed', {'original': url, 'cleaned': cleaned}, 'A');
      // #endregion
      return cleaned;
    }

    // Check for storage/ without leading slash
    if (url.contains('storage/http') && !url.contains('/storage/http')) {
      final index = url.indexOf('storage/http');
      final cleaned = url.substring(index + 8);
      // #region agent log
      _log('cleanUrl_fixed_no_slash', {'original': url, 'cleaned': cleaned}, 'C');
      // #endregion
      return cleaned;
    }

    return url;
  }
}
