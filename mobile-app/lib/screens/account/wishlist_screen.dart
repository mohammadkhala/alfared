import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../products/product_detail_screen.dart';

class WishlistScreen extends StatefulWidget {
  const WishlistScreen({super.key});

  @override
  State<WishlistScreen> createState() => _WishlistScreenState();
}

class _WishlistScreenState extends State<WishlistScreen> {
  List<Map<String, dynamic>> _items = [];
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final res = await ApiService.instance.get('/wishlist');
      _items = ((res as Map)['items'] as List).cast<Map<String, dynamic>>();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _remove(int productId) async {
    try {
      await ApiService.instance.post('/wishlist/toggle', data: {'product_id': productId});
      setState(() => _items.removeWhere((i) => i['id'] == productId));
    } catch (e) { Fluttertoast.showToast(msg: e.toString()); }
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      appBar: AppBar(title: Text(s.myWishlist)),
      body: _loading
        ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
        : _items.isEmpty
          ? Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.favorite_border_rounded, size: 52, color: AppColors.border),
                  const SizedBox(height: 14),
                  Text(
                    s.noWishlistItems,
                    style: const TextStyle(
                      color: AppColors.gray, fontFamily: 'Cairo',
                      fontSize: 15, fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            )
          : RefreshIndicator(
              color: AppColors.orange,
              onRefresh: _load,
              child: ListView.separated(
                padding: const EdgeInsets.all(14),
                itemCount: _items.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (_, i) {
                  final p = _items[i];
                  return Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(AppRadius.lg),
                      boxShadow: AppShadows.card,
                    ),
                    child: Row(children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(10),
                        child: SizedBox(
                          width: 74, height: 74,
                          child: p['image'] != null
                            ? CachedNetworkImage(imageUrl: p['image'], fit: BoxFit.cover)
                            : Container(
                                color: AppColors.grayBg,
                                child: const Icon(Icons.image_not_supported, color: AppColors.border)),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: GestureDetector(
                          onTap: () => Navigator.of(context).push(MaterialPageRoute(
                            builder: (_) => ProductDetailScreen(slug: p['slug'] as String))),
                          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text(
                              p['name'] as String,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                                fontSize: 13, color: AppColors.text,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              '${(p['price'] as num).toStringAsFixed(2)} ₪',
                              style: const TextStyle(
                                color: AppColors.orange, fontWeight: FontWeight.w900,
                                fontFamily: 'Cairo', fontSize: 15,
                              ),
                            ),
                          ]),
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.delete_outline_rounded, color: AppColors.danger, size: 22),
                        onPressed: () => _remove(p['id'] as int),
                      ),
                    ]),
                  );
                },
              ),
            ),
    );
  }
}
