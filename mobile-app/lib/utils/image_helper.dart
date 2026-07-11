class ImageHelper {
  /// Normalizes image URLs coming from the backend.
  /// Fixes a backend bug where an absolute URL gets the storage path prepended
  /// (e.g. ".../storage/https://..." → "https://...").
  static String? cleanUrl(String? url) {
    if (url == null) return null;

    // "/storage/http..." → strip everything up to the real URL
    if (url.contains('/storage/http')) {
      final index = url.indexOf('/storage/http');
      return url.substring(index + 9);
    }

    // "storage/http..." without a leading slash
    if (url.contains('storage/http')) {
      final index = url.indexOf('storage/http');
      return url.substring(index + 8);
    }

    return url;
  }
}
