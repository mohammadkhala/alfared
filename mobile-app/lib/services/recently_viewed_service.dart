import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/product.dart';

/// Persists last-viewed product summaries locally (no backend round-trip).
/// Capped at [maxItems] items, newest first.
class RecentlyViewedService {
  static const _key = 'recently_viewed';
  static const int maxItems = 12;

  static Future<void> track(Product p) async {
    final prefs = await SharedPreferences.getInstance();
    final List<Map<String, dynamic>> list = _read(prefs);

    // Remove existing then prepend
    list.removeWhere((e) => e['id'] == p.id);
    list.insert(0, {
      'id':            p.id,
      'name':          p.name,
      'slug':          p.slug,
      'image':         p.image,
      'price':         p.price,
      'compare_price': p.comparePrice,
      'discount_percent': p.discountPercent,
      'is_on_sale':    p.isOnSale,
      'in_stock':      p.inStock,
      'brand':         p.brandName != null ? {'name': p.brandName} : null,
      'category':      p.categoryName != null ? {'name': p.categoryName} : null,
      'rating_avg':    p.ratingAvg,
      'reviews_count': p.reviewsCount,
    });

    if (list.length > maxItems) list.removeRange(maxItems, list.length);
    await prefs.setString(_key, jsonEncode(list));
  }

  static Future<List<Product>> list() async {
    final prefs = await SharedPreferences.getInstance();
    return _read(prefs).map((m) => Product.fromJson(m)).toList();
  }

  static Future<void> clear() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_key);
  }

  static List<Map<String, dynamic>> _read(SharedPreferences p) {
    try {
      final raw = p.getString(_key);
      if (raw == null) return [];
      final list = jsonDecode(raw) as List;
      return list.cast<Map<String, dynamic>>();
    } catch (_) {
      return [];
    }
  }
}
