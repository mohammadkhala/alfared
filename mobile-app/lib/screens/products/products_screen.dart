import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/category.dart';
import '../../models/product.dart';
import '../../providers/cart_provider.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../../theme/theme_ext.dart';
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
  List<Category> _categories = [];
  /// null = "الكل". Starts on whatever category the screen was opened with.
  String? _activeCategory;
  String _sort = 'newest';
  int _page = 1;
  int _lastPage = 1;
  bool _loading = false;
  bool _loadingMore = false;

  @override
  void initState() {
    super.initState();
    _search.text = widget.searchQuery ?? '';
    _activeCategory = widget.categorySlug;
    _scroll.addListener(_onScroll);
    _load(reset: true);
    _loadCategories();
  }

  Future<void> _loadCategories() async {
    try {
      final res = await ApiService.instance.get('/categories');
      final raw = (res is Map ? (res['data'] ?? res['categories']) : res) as List?;
      if (raw != null && mounted) {
        setState(() => _categories =
            raw.map((e) => Category.fromJson(e as Map<String, dynamic>)).toList());
      }
    } catch (_) {
      // Filter row simply stays hidden if categories can't be fetched.
    }
  }

  Future<void> _pickCategory(String? slug) async {
    if (slug == _activeCategory) return;
    setState(() => _activeCategory = slug);
    _scroll.hasClients ? _scroll.jumpTo(0) : null;
    await _load(reset: true);
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
        if (_activeCategory != null) 'category': _activeCategory!,
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
      backgroundColor: context.bg,
      appBar: AppBar(
        title: Text(widget.categoryName ?? context.watch<LocaleProvider>().s.storeTitle),
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
              hintText: context.watch<LocaleProvider>().s.searchHint,
              prefixIcon: const Icon(Icons.search, color: AppColors.gray),
              suffixIcon: _search.text.isEmpty ? null : IconButton(
                icon: const Icon(Icons.close, color: AppColors.gray),
                onPressed: () { _search.clear(); _load(reset: true); },
              ),
            ),
          ),
        ),

        // category filter
        if (_categories.isNotEmpty)
          SizedBox(
            height: 42,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 12),
              children: [
                _categoryChip(context.watch<LocaleProvider>().s.all, null),
                ..._categories.map((c) => _categoryChip(
                      c.nameFor(context.watch<LocaleProvider>().code), c.slug)),
              ],
            ),
          ),

        // sort chips
        SizedBox(
          height: 44,
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 12),
            children: [
              _sortChip(context.watch<LocaleProvider>().s.sortNewest, 'newest'),
              _sortChip(context.watch<LocaleProvider>().s.sortPriceAsc, 'price_asc'),
              _sortChip(context.watch<LocaleProvider>().s.sortPriceDesc, 'price_desc'),
              _sortChip(context.watch<LocaleProvider>().s.sortRating, 'rating'),
              _sortChip(context.watch<LocaleProvider>().s.sortBestseller, 'bestseller'),
            ],
          ),
        ),

        // grid
        Expanded(
          child: _items.isEmpty && _loading
            ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
            : _items.isEmpty
              ? Center(child: Text(context.watch<LocaleProvider>().s.noProducts, style: const TextStyle(color: AppColors.gray, fontFamily: 'Cairo')))
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

  /// Filters the grid by category. Blue to set it apart from the orange sort row.
  Widget _categoryChip(String label, String? slug) {
    final active = _activeCategory == slug;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
      child: GestureDetector(
        onTap: () => _pickCategory(slug),
        behavior: HitTestBehavior.opaque,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 180),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: active ? AppColors.blue : context.card,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: active ? AppColors.blue : context.line),
          ),
          alignment: Alignment.center,
          child: Text(label,
            style: TextStyle(
              color: active ? Colors.white : context.text,
              fontFamily: 'Cairo',
              fontWeight: active ? FontWeight.w900 : FontWeight.w700,
              fontSize: 12,
            )),
        ),
      ),
    );
  }

  Widget _sortChip(String label, String value) {
    final active = _sort == value;
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 4),
      child: ChoiceChip(
        label: Text(label, style: TextStyle(color: active ? Colors.white : context.text, fontFamily: 'Cairo', fontWeight: FontWeight.w700, fontSize: 12)),
        selected: active,
        onSelected: (_) => _changeSort(value),
        selectedColor: AppColors.orange,
        backgroundColor: context.card,
        side: BorderSide(color: active ? AppColors.orange : context.line),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
    );
  }
}
