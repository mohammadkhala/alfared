import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/api_service.dart';

/// Server-backed cart with local SharedPreferences cache.
/// On startup/offline: shows cached data immediately.
/// On mutation: updates server then refreshes local cache.
class CartProvider extends ChangeNotifier {
  List<Map<String, dynamic>> _items = [];
  Map<String, dynamic> _totals = {};
  bool _loading = false;

  static const _kCartItems  = 'cart_items_cache';
  static const _kCartTotals = 'cart_totals_cache';

  List<Map<String, dynamic>> get items => _items;
  Map<String, dynamic> get totals      => _totals;
  bool get loading                     => _loading;
  int get count                        => _items.fold<int>(0, (s, it) => s + (it['qty'] as int? ?? 0));
  double get subtotal                  => (_totals['subtotal']  as num?)?.toDouble() ?? 0;
  double get total                     => (_totals['total']     as num?)?.toDouble() ?? 0;

  /// Load from cache instantly, then refresh from server.
  Future<void> load() async {
    await _restoreFromCache();
    _loading = true; notifyListeners();
    try {
      final res = await ApiService.instance.get('/cart');
      _items  = ((res as Map)['items']  as List).cast<Map<String, dynamic>>();
      _totals = (res['totals'] as Map).cast<String, dynamic>();
      await _persistToCache();
    } catch (_) {
      // Keep showing cached data on network failure
    } finally {
      _loading = false; notifyListeners();
    }
  }

  Future<void> add(int productId, {int qty = 1, int? variantId}) async {
    final res = await ApiService.instance.post('/cart/add', data: {
      'product_id': productId,
      'qty':        qty,
      if (variantId != null) 'variant_id': variantId,
    });
    _items  = ((res as Map)['items']  as List).cast<Map<String, dynamic>>();
    _totals = (res['totals'] as Map).cast<String, dynamic>();
    await _persistToCache();
    notifyListeners();
  }

  Future<void> updateQty(String key, int qty) async {
    final res = await ApiService.instance.put('/cart/$key', data: {'qty': qty});
    _items  = ((res as Map)['items']  as List).cast<Map<String, dynamic>>();
    _totals = (res['totals'] as Map).cast<String, dynamic>();
    await _persistToCache();
    notifyListeners();
  }

  Future<void> remove(String key) async {
    final res = await ApiService.instance.delete('/cart/$key');
    _items  = ((res as Map)['items']  as List).cast<Map<String, dynamic>>();
    _totals = (res['totals'] as Map).cast<String, dynamic>();
    await _persistToCache();
    notifyListeners();
  }

  Future<void> clear() async {
    await ApiService.instance.delete('/cart');
    _items = [];
    _totals = {};
    await _clearCache();
    notifyListeners();
  }

  Future<void> _restoreFromCache() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final itemsJson  = prefs.getString(_kCartItems);
      final totalsJson = prefs.getString(_kCartTotals);
      if (itemsJson != null) {
        _items  = (jsonDecode(itemsJson) as List).cast<Map<String, dynamic>>();
      }
      if (totalsJson != null) {
        _totals = (jsonDecode(totalsJson) as Map).cast<String, dynamic>();
      }
      if (itemsJson != null || totalsJson != null) notifyListeners();
    } catch (_) {}
  }

  Future<void> _persistToCache() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_kCartItems,  jsonEncode(_items));
      await prefs.setString(_kCartTotals, jsonEncode(_totals));
    } catch (_) {}
  }

  Future<void> _clearCache() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_kCartItems);
      await prefs.remove(_kCartTotals);
    } catch (_) {}
  }
}
