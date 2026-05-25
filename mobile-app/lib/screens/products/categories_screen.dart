import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import '../../models/category.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import 'products_screen.dart';

class CategoriesScreen extends StatefulWidget {
  const CategoriesScreen({super.key});

  @override
  State<CategoriesScreen> createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  List<Category> _categories = [];
  bool _loading = true;

  static const _emojis = ['💄', '🧴', '💆', '🌹', '💅', '🧖', '✨', '🎀', '🛍️', '👜'];
  static const _gradients = [
    [Color(0xFFFFE5D9), Color(0xFFFFD0BB)],   // peach
    [Color(0xFFE5EAFF), Color(0xFFD0DAFF)],   // blue
    [Color(0xFFFFE9F5), Color(0xFFFFD0EA)],   // pink
    [Color(0xFFE9FFE9), Color(0xFFD0FFD0)],   // green
    [Color(0xFFFFF9D9), Color(0xFFFFEC9F)],   // yellow
    [Color(0xFFF0E5FF), Color(0xFFE0D0FF)],   // purple
  ];

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final res = await ApiService.instance.get('/categories');
      _categories = ((res as Map)['categories'] as List).map((e) => Category.fromJson(e)).toList();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.grayBg,
      appBar: AppBar(title: const Text('الأقسام')),
      body: _loading
        ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
        : RefreshIndicator(
            color: AppColors.orange,
            onRefresh: _load,
            child: GridView.builder(
              padding: const EdgeInsets.all(14),
              itemCount: _categories.length,
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
                childAspectRatio: 0.95,
              ),
              itemBuilder: (_, i) {
                final c = _categories[i];
                final colors = _gradients[i % _gradients.length];
                return GestureDetector(
                  onTap: () => Navigator.of(context).push(MaterialPageRoute(
                    builder: (_) => ProductsScreen(categorySlug: c.slug, categoryName: c.name))),
                  child: Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(begin: Alignment.topRight, end: Alignment.bottomLeft, colors: colors),
                      borderRadius: BorderRadius.circular(AppRadius.xl),
                      boxShadow: AppShadows.card,
                    ),
                    child: Stack(children: [
                      // Decorative blob
                      Positioned(
                        top: -30, right: -30,
                        child: Container(
                          width: 90, height: 90,
                          decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.4), shape: BoxShape.circle),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            // Top: emoji or image
                            Container(
                              width: 56, height: 56,
                              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16),
                                boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 8)]),
                              alignment: Alignment.center,
                              child: c.image != null
                                ? ClipRRect(borderRadius: BorderRadius.circular(14),
                                    child: CachedNetworkImage(imageUrl: c.image!, fit: BoxFit.cover, width: 50, height: 50))
                                : Text(_emojis[i % _emojis.length], style: const TextStyle(fontSize: 26)),
                            ),
                            // Bottom: name + arrow
                            Row(children: [
                              Expanded(child: Text(c.name,
                                style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w900, color: AppColors.blueDark, fontFamily: 'Cairo'),
                                maxLines: 2, overflow: TextOverflow.ellipsis,
                              )),
                              Container(
                                width: 28, height: 28,
                                decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(10)),
                                alignment: Alignment.center,
                                child: const Icon(Icons.arrow_back, size: 16, color: Colors.white),
                              ),
                            ]),
                          ],
                        ),
                      ),
                    ]),
                  ),
                );
              },
            ),
          ),
    );
  }
}
