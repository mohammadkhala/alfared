import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/product.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/product_card.dart';
import '../cart/cart_screen.dart';

class ProductsScreen extends StatefulWidget {
  const ProductsScreen({super.key, this.categorySlug, this.categoryName, this.searchQuery});
  final String? categorySlug;
  final String? categoryName;
  final String? searchQuery;

  @override
  State<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  final _search = TextEditingController();
  final _scroll = ScrollController();
  final List<Product> _items = [];
  String _sort = 'newest';
  int _page = 1;
  int _lastPage = 1;
  bool _loading = false;
  bool _loadingMore = false;

  @override
  void initState() {
    super.initState();
    _search.text = widget.searchQuery ?? '';
    _scroll.addListener(_onScroll);
    _load(reset: true);
  }

  void _onScroll() {
    if (_scroll.position.pixels > _scroll.position.maxScrollExtent - 240 && !_loadingMore && _page < _lastPage) {
      _loadMore();
    }
  }

  Future<void> _load({bool reset = false}) async {
    if (reset) {
      _items.clear();
      _page = 1;
    }
    setState(() => _loading = true);
    try {
      final res = await ApiService.instance.get('/products', query: {
        if (widget.categorySlug != null) 'category': widget.categorySlug!,
        if (_search.text.trim().isNotEmpty) 'q': _search.text.trim(),
        'sort': _sort,
        'page': _page,
      });
      final list = (res['data'] as List).map((e) => Product.fromJson(e)).toList();
      _items.addAll(list);
      _lastPage = (res['last_page'] as num?)?.toInt() ?? 1;
    } catch (_) {/* keep what we have */}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _loadMore() async {
    setState(() => _loadingMore = true);
    _page++;
    await _load();
    if (mounted) setState(() => _loadingMore = false);
  }

  Future<void> _changeSort(String s) async {
    if (s == _sort) return;
    setState(() => _sort = s);
    await _load(reset: true);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.categoryName ?? 'المتجر'),
        actions: [
          Builder(builder: (ctx) {
            final count = ctx.watch<CartProvider>().count;
            return Stack(clipBehavior: Clip.none, children: [
              IconButton(
                icon: const Icon(Icons.shopping_cart_outlined),
                onPressed: () => Navigator.of(ctx).push(MaterialPageRoute(builder: (_) => const CartScreen())),
              ),
              if (count > 0)
                Positioned(top: 8, right: 6,
                  child: Container(
                    padding: const EdgeInsets.all(3),
                    constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                    decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(8)),
                    child: Text('$count', textAlign: TextAlign.center,
                      style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
                  )),
            ]);
          }),
        ],
      ),
      body: Column(children: [
        // search bar
        Padding(
          padding: const EdgeInsets.fromLTRB(14, 12, 14, 6),
          child: TextField(
            controller: _search,
            onSubmitted: (_) => _load(reset: true),
            decoration: InputDecoration(
              hintText: 'ابحث عن منتج...',
              prefixIcon: const Icon(Icons.search, color: AppColors.gray),
              suffixIcon: _search.text.isEmpty ? null : IconButton(
                icon: const Icon(Icons.close, color: AppColors.gray),
                onPressed: () { _search.clear(); _load(reset: true); },
              ),
            ),
          ),
        ),

        // sort chips
        SizedBox(
          height: 44,
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 12),
            children: [
              _sortChip('الأحدث',          'newest'),
              _sortChip('السعر ↑',         'price_asc'),
              _sortChip('السعر ↓',         'price_desc'),
              _sortChip('الأعلى تقييماً',   'rating'),
              _sortChip('الأكثر مبيعاً',    'bestseller'),
            ],
          ),
        ),

        // grid
        Expanded(
          child: _items.isEmpty && _loading
            ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
            : _items.isEmpty
              ? const Center(child: Text('لا توجد منتجات', style: TextStyle(color: AppColors.gray, fontFamily: 'Cairo')))
              : RefreshIndicator(
                  color: AppColors.orange,
                  onRefresh: () => _load(reset: true),
                  child: GridView.builder(
                    controller: _scroll,
                    padding: const EdgeInsets.all(14),
                    itemCount: _items.length + (_loadingMore ? 1 : 0),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                      childAspectRatio: 0.54,
                    ),
                    itemBuilder: (_, i) {
                      if (i >= _items.length) {
                        return const Center(child: CircularProgressIndicator(color: AppColors.orange));
                      }
                      return ProductCard(product: _items[i]);
                    },
                  ),
                ),
        ),
      ]),
    );
  }

  Widget _sortChip(String label, String value) {
    final active = _sort == value;
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 4),
      child: ChoiceChip(
        label: Text(label, style: TextStyle(color: active ? Colors.white : AppColors.text, fontFamily: 'Cairo', fontWeight: FontWeight.w700, fontSize: 12)),
        selected: active,
        onSelected: (_) => _changeSort(value),
        selectedColor: AppColors.orange,
        backgroundColor: Colors.white,
        side: BorderSide(color: active ? AppColors.orange : AppColors.border),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
    );
  }
}
