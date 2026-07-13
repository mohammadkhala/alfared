import 'package:cached_network_image/cached_network_image.dart';
import 'package:carousel_slider/carousel_slider.dart' as cs;
import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../models/product.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_service.dart';
import '../cart/cart_screen.dart';
import '../../services/recently_viewed_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/animated_heart.dart';
import '../auth/login_screen.dart';

class ProductDetailScreen extends StatefulWidget {
  const ProductDetailScreen({super.key, required this.slug});
  final String slug;

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  Product? _product;
  bool _loading = true;
  int _qty = 1;
  // type → selected VariantValue.id
  final Map<String, int> _selectedVariants = {};
  bool _favorite = false;
  int _galleryIndex = 0;
  bool _reviewSubmitted = false;
  bool _cartAdding = false;
  bool _cartAdded  = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final res = await ApiService.instance.get('/products/${widget.slug}');
      _product = Product.fromJson((res as Map)['product'] as Map<String, dynamic>);
      // Track in recently viewed (fire-and-forget)
      if (_product != null) RecentlyViewedService.track(_product!);
    } catch (e) {
      Fluttertoast.showToast(msg: e.toString());
    }
    if (mounted) setState(() => _loading = false);
  }

  // Sum of price modifiers for all selected variants
  double get _variantPriceModifier {
    if (_product == null) return 0;
    double modifier = 0;
    for (final group in _product!.variantGroups) {
      final selId = _selectedVariants[group.type];
      if (selId != null) {
        final val = group.values.where((v) => v.id == selId).firstOrNull;
        if (val != null) modifier += val.priceModifier;
      }
    }
    return modifier;
  }

  // First selected variant ID (for API backward compat)
  int? get _primaryVariantId => _selectedVariants.values.firstOrNull;

  bool get _allGroupsSelected {
    if (_product == null) return true;
    return _product!.variantGroups.every((g) => _selectedVariants.containsKey(g.type));
  }

  Future<void> _addToCart() async {
    if (!context.read<AuthProvider>().isLoggedIn) {
      Navigator.of(context).push(MaterialPageRoute(builder: (_) => const LoginScreen()));
      return;
    }
    if (_product!.hasVariants && !_allGroupsSelected) {
      Fluttertoast.showToast(msg: 'يرجى اختيار جميع الخيارات');
      return;
    }
    setState(() => _cartAdding = true);
    try {
      await context.read<CartProvider>().add(_product!.id, qty: _qty, variantId: _primaryVariantId);
      if (!mounted) return;
      setState(() { _cartAdding = false; _cartAdded = true; });
      await Future.delayed(const Duration(milliseconds: 1800));
      if (mounted) setState(() => _cartAdded = false);
    } catch (e) {
      if (mounted) setState(() => _cartAdding = false);
      Fluttertoast.showToast(msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    }
  }

  Future<void> _submitReview() async {
    if (!context.read<AuthProvider>().isLoggedIn) {
      Navigator.of(context).push(MaterialPageRoute(builder: (_) => const LoginScreen()));
      return;
    }
    int selectedRating = 5;
    final commentCtrl = TextEditingController();
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setDlg) => AlertDialog(
          title: const Text('أضف تقييمك', style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900)),
          content: Column(mainAxisSize: MainAxisSize.min, children: [
            Row(mainAxisAlignment: MainAxisAlignment.center, children: List.generate(5, (i) {
              return GestureDetector(
                onTap: () => setDlg(() => selectedRating = i + 1),
                child: Icon(i < selectedRating ? Icons.star : Icons.star_border,
                  color: AppColors.amber, size: 32),
              );
            })),
            const SizedBox(height: 12),
            TextField(
              controller: commentCtrl,
              maxLines: 3,
              decoration: const InputDecoration(
                labelText: 'تعليقك (اختياري)',
                labelStyle: TextStyle(fontFamily: 'Cairo'),
                border: OutlineInputBorder(),
              ),
            ),
          ]),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('إلغاء', style: TextStyle(fontFamily: 'Cairo'))),
            ElevatedButton(onPressed: () => Navigator.pop(ctx, true),
              child: const Text('إرسال', style: TextStyle(fontFamily: 'Cairo'))),
          ],
        ),
      ),
    );
    if (confirmed != true) return;
    try {
      await ApiService.instance.post('/products/${_product!.slug}/reviews', data: {
        'rating':  selectedRating,
        'comment': commentCtrl.text.trim().isEmpty ? null : commentCtrl.text.trim(),
      });
      setState(() => _reviewSubmitted = true);
      Fluttertoast.showToast(msg: 'شكراً! سيظهر تقييمك بعد المراجعة.', backgroundColor: AppColors.success, textColor: Colors.white);
    } catch (e) {
      Fluttertoast.showToast(msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    }
  }

  Future<void> _toggleFavorite() async {
    if (!context.read<AuthProvider>().isLoggedIn) {
      Navigator.of(context).push(MaterialPageRoute(builder: (_) => const LoginScreen()));
      return;
    }
    try {
      final res = await ApiService.instance.post('/wishlist/toggle', data: {'product_id': _product!.id});
      setState(() => _favorite = (res as Map)['added'] == true);
    } catch (e) {
      Fluttertoast.showToast(msg: e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator(color: AppColors.orange)));
    }
    if (_product == null) {
      return Scaffold(appBar: AppBar(), body: const Center(child: Text('المنتج غير موجود', style: TextStyle(fontFamily: 'Cairo'))));
    }

    final p = _product!;
    final images = p.images.isEmpty && p.image != null ? [p.image!] : p.images;
    final saveAmount = p.comparePrice != null ? (p.comparePrice! - p.price) : 0;

    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        bottom: false,
        child: Column(children: [
          // ── Top action bar (transparent over image) ──
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
            child: Row(children: [
              _CircleIconButton(icon: Icons.arrow_back, onTap: () => Navigator.of(context).pop()),
              const Spacer(),
              _CircleIconButton(icon: Icons.share_outlined, onTap: () async {
                final p = _product;
                if (p == null) return;
                final text = '${p.name}\n💰 السعر: ${p.price.toStringAsFixed(0)} ₪\n\nمتجر شركة ابناء الفريد 🛍️';
                final url = Uri.parse('https://wa.me/?text=${Uri.encodeComponent(text)}');
                await launchUrl(url, mode: LaunchMode.externalApplication);
              }),
              const SizedBox(width: 8),
              _CartIconButton(),
            ]),
          ),

          // ── Image gallery with peach gradient bg ──
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 16),
            height: 260,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topRight, end: Alignment.bottomLeft,
                colors: [Color(0xFFFFF5EE), Color(0xFFFFE8D4)],
              ),
              borderRadius: BorderRadius.circular(22),
            ),
            child: Stack(children: [
              if (images.isEmpty)
                const Center(child: Icon(Icons.image_not_supported, color: AppColors.gray, size: 48))
              else
                cs.CarouselSlider(
                  options: cs.CarouselOptions(
                    viewportFraction: 1, height: 260,
                    autoPlay: images.length > 1,
                    onPageChanged: (i, _) => setState(() => _galleryIndex = i),
                  ),
                  items: images.asMap().entries.map((entry) {
                    final i = entry.key;
                    final u = entry.value;
                    final img = CachedNetworkImage(
                      imageUrl: u,
                      fit: BoxFit.contain,
                      fadeInDuration: const Duration(milliseconds: 200),
                      placeholder: (_, __) => const Center(
                        child: SizedBox(width: 26, height: 26,
                          child: CircularProgressIndicator(strokeWidth: 2.4, color: AppColors.orange)),
                      ),
                      errorWidget: (_, __, ___) => const Center(
                        child: Icon(Icons.image_not_supported_outlined, color: AppColors.grayLight, size: 40)),
                    );
                    return Padding(
                      padding: const EdgeInsets.all(24),
                      child: i == 0
                        ? Hero(tag: 'product-image-${p.id}', child: img)
                        : img,
                    );
                  }).toList(),
                ),
              if (images.length > 1)
                Positioned(
                  bottom: 12, left: 0, right: 0,
                  child: Row(mainAxisAlignment: MainAxisAlignment.center, children: List.generate(images.length, (i) {
                    final active = i == _galleryIndex;
                    return AnimatedContainer(
                      duration: const Duration(milliseconds: 250),
                      margin: const EdgeInsets.symmetric(horizontal: 3),
                      width: active ? 20 : 6, height: 6,
                      decoration: BoxDecoration(
                        color: active ? AppColors.blue : AppColors.blue.withValues(alpha: 0.2),
                        borderRadius: BorderRadius.circular(3),
                      ),
                    );
                  })),
                ),
            ]),
          ),

          // ── Info ──
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 16),
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Row(children: [
                  if (p.brandName != null)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                      decoration: BoxDecoration(color: AppColors.orangeLight, borderRadius: BorderRadius.circular(8)),
                      child: Text(p.brandName!.toUpperCase(),
                        style: const TextStyle(color: AppColors.orange, fontSize: 9, fontWeight: FontWeight.w800, fontFamily: 'Cairo', letterSpacing: 0.5)),
                    ),
                  const Spacer(),
                  AnimatedHeart(isFavorite: _favorite, onToggle: _toggleFavorite),
                ]),
                const SizedBox(height: 8),
                Text(p.name, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w900, fontFamily: 'Cairo', height: 1.3, color: AppColors.text)),
                const SizedBox(height: 8),

                // Rating
                Row(children: [
                  ...List.generate(5, (i) => Icon(
                    i < p.ratingAvg.round() ? Icons.star : Icons.star_border,
                    size: 14, color: AppColors.amber,
                  )),
                  const SizedBox(width: 6),
                  Text(p.ratingAvg.toStringAsFixed(1), style: const TextStyle(fontSize: 11, color: AppColors.gray, fontFamily: 'Cairo', fontWeight: FontWeight.w700)),
                  const SizedBox(width: 10),
                  Container(width: 1, height: 12, color: AppColors.border),
                  const SizedBox(width: 10),
                  Text('${p.reviewsCount}+ مبيعة', style: const TextStyle(fontSize: 11, color: AppColors.gray, fontFamily: 'Cairo')),
                ]),
                const SizedBox(height: 14),

                // Price section
                Row(crossAxisAlignment: CrossAxisAlignment.end, children: [
                  Text('${p.price.toStringAsFixed(0)} ₪',
                    style: const TextStyle(color: AppColors.blue, fontSize: 24, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
                  const SizedBox(width: 10),
                  if (p.comparePrice != null) ...[
                    Padding(
                      padding: const EdgeInsets.only(bottom: 4),
                      child: Text('${p.comparePrice!.toStringAsFixed(0)} ₪',
                        style: const TextStyle(color: AppColors.grayLight, fontSize: 13, fontFamily: 'Cairo', decoration: TextDecoration.lineThrough)),
                    ),
                    const SizedBox(width: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(color: AppColors.successLight, borderRadius: BorderRadius.circular(8)),
                      child: Text('وفّر ${saveAmount.toStringAsFixed(0)} ₪',
                        style: const TextStyle(color: AppColors.success, fontSize: 10, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
                    ),
                  ],
                ]),
                const SizedBox(height: 18),

                // Variants — grouped by type
                if (p.hasVariants) ...[
                  ...p.variantGroups.map((group) => _VariantGroupWidget(
                    group: group,
                    selectedId: _selectedVariants[group.type],
                    onSelect: (id) => setState(() => _selectedVariants[group.type] = id),
                  )),
                  const SizedBox(height: 4),
                ],

                // Quantity row
                Row(children: [
                  const Text('الكمية', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.text)),
                  const Spacer(),
                  _QtyButton(icon: Icons.remove, onTap: () => setState(() => _qty = (_qty - 1).clamp(1, 99))),
                  Padding(padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Text('$_qty', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, fontFamily: 'Cairo'))),
                  _QtyButton(icon: Icons.add, onTap: () => setState(() => _qty = (_qty + 1).clamp(1, 99))),
                ]),
                const SizedBox(height: 20),

                if (p.description != null) ...[
                  const Text('الوصف', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.text)),
                  const SizedBox(height: 10),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: AppColors.grayBg,
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Text(p.description!,
                      style: const TextStyle(fontSize: 13, height: 1.9, fontFamily: 'Cairo', color: AppColors.text)),
                  ),
                  const SizedBox(height: 18),
                ],

                // Reviews section
                Row(children: [
                  const Text('تقييمات العملاء', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.text)),
                  const Spacer(),
                  if (!_reviewSubmitted && context.read<AuthProvider>().isLoggedIn)
                    GestureDetector(
                      onTap: _submitReview,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                        decoration: BoxDecoration(color: AppColors.orangeLight, borderRadius: BorderRadius.circular(8)),
                        child: const Text('+ أضف تقييم', style: TextStyle(color: AppColors.orange, fontFamily: 'Cairo', fontSize: 11, fontWeight: FontWeight.w800)),
                      ),
                    ),
                ]),
                const SizedBox(height: 8),
                if (p.reviews.isEmpty)
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 8),
                    child: Text('لا توجد تقييمات بعد. كن أول من يقيّم!',
                      style: TextStyle(color: AppColors.gray, fontFamily: 'Cairo', fontSize: 12)),
                  )
                else
                  ...p.reviews.map((r) => Container(
                    margin: const EdgeInsets.only(bottom: 10),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(color: AppColors.grayBg, borderRadius: BorderRadius.circular(12)),
                    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Row(children: [
                        Text(r['name']?.toString() ?? '—', style: const TextStyle(fontWeight: FontWeight.w800, fontFamily: 'Cairo', fontSize: 12)),
                        const Spacer(),
                        Row(children: List.generate(5, (i) => Icon(
                          i < ((r['rating'] as num?)?.toInt() ?? 0) ? Icons.star : Icons.star_border,
                          size: 12, color: AppColors.amber,
                        ))),
                      ]),
                      const SizedBox(height: 6),
                      Text(r['comment']?.toString() ?? '', style: const TextStyle(fontFamily: 'Cairo', fontSize: 11)),
                    ]),
                  )),

                const SizedBox(height: 12),
              ]),
            ),
          ),

          // ── Sticky action bar ──
          Container(
            padding: EdgeInsets.fromLTRB(16, 12, 16, MediaQuery.of(context).padding.bottom + 12),
            decoration: BoxDecoration(
              color: Colors.white,
              border: Border(top: BorderSide(color: AppColors.grayBg, width: 1)),
            ),
            child: Row(children: [
              Container(
                width: 50, height: 50,
                decoration: BoxDecoration(color: AppColors.grayBg, borderRadius: BorderRadius.circular(14)),
                child: Center(child: AnimatedHeart(isFavorite: _favorite, onToggle: _toggleFavorite, size: 22)),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: GestureDetector(
                  onTap: (p.inStock && !_cartAdding && !_cartAdded) ? _addToCart : null,
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 350),
                    height: 50,
                    decoration: BoxDecoration(
                      gradient: !p.inStock || _cartAdded
                        ? null
                        : const LinearGradient(begin: Alignment.centerRight, end: Alignment.centerLeft, colors: [AppColors.orange, AppColors.orangeDark]),
                      color: !p.inStock
                        ? AppColors.gray
                        : _cartAdded
                          ? AppColors.success
                          : null,
                      borderRadius: BorderRadius.circular(14),
                      boxShadow: [BoxShadow(
                        color: (_cartAdded ? AppColors.success : AppColors.orange).withValues(alpha: p.inStock ? 0.35 : 0),
                        blurRadius: 12, offset: const Offset(0, 4),
                      )],
                    ),
                    alignment: Alignment.center,
                    child: _cartAdding
                      ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white))
                      : Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                          Icon(
                            _cartAdded ? Icons.check_circle_rounded : Icons.shopping_cart_outlined,
                            color: Colors.white, size: 18,
                          ),
                          const SizedBox(width: 8),
                          Text(
                            !p.inStock
                              ? 'نفد المخزون'
                              : _cartAdded
                                ? 'تمت الإضافة للسلة ✓'
                                : 'أضف للسلة — ${((p.price + _variantPriceModifier) * _qty).toStringAsFixed(0)} ₪',
                            style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w800, fontFamily: 'Cairo'),
                          ),
                        ]),
                  ),
                ),
              ),
            ]),
          ),
        ]),
      ),
    );
  }
}

// ═══════════ VARIANT GROUP ═══════════

class _VariantGroupWidget extends StatelessWidget {
  const _VariantGroupWidget({
    required this.group,
    required this.selectedId,
    required this.onSelect,
  });
  final VariantGroup group;
  final int? selectedId;
  final ValueChanged<int> onSelect;

  @override
  Widget build(BuildContext context) {
    final isColor = group.values.any((v) => v.isColor);
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        // Label + selected value name
        Row(children: [
          Text(group.label,
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
          if (selectedId != null) ...[
            const SizedBox(width: 6),
            Text(
              ': ${group.values.where((v) => v.id == selectedId).firstOrNull?.value ?? ''}',
              style: const TextStyle(fontSize: 11, color: AppColors.orange, fontFamily: 'Cairo', fontWeight: FontWeight.w700),
            ),
          ],
        ]),
        const SizedBox(height: 8),
        isColor
            ? _ColorSwatches(values: group.values, selectedId: selectedId, onSelect: onSelect)
            : _OptionChips(values: group.values, selectedId: selectedId, onSelect: onSelect),
      ]),
    );
  }
}

class _ColorSwatches extends StatelessWidget {
  const _ColorSwatches({required this.values, required this.selectedId, required this.onSelect});
  final List<VariantValue> values;
  final int? selectedId;
  final ValueChanged<int> onSelect;

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: 10, runSpacing: 10,
      children: values.map((v) {
        final active = selectedId == v.id;
        Color? bgColor;
        if (v.colorCode != null) {
          try {
            final hex = v.colorCode!.replaceFirst('#', '');
            bgColor = Color(int.parse('FF$hex', radix: 16));
          } catch (_) {}
        }
        return GestureDetector(
          onTap: v.inStock ? () => onSelect(v.id) : null,
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 180),
            width: 38, height: 38,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: bgColor ?? AppColors.grayBg,
              border: Border.all(
                color: active ? AppColors.orange : Colors.transparent,
                width: 2.5,
              ),
              boxShadow: active ? [BoxShadow(color: AppColors.orange.withValues(alpha: 0.35), blurRadius: 8, spreadRadius: 1)] : null,
            ),
            child: v.inStock
              ? (active ? const Icon(Icons.check, color: Colors.white, size: 16) : null)
              : Center(child: Container(
                  width: 30, height: 2,
                  decoration: BoxDecoration(color: AppColors.grayLight, borderRadius: BorderRadius.circular(1)),
                  transform: Matrix4.rotationZ(0.785),
                  transformAlignment: Alignment.center,
                )),
          ),
        );
      }).toList(),
    );
  }
}

class _OptionChips extends StatelessWidget {
  const _OptionChips({required this.values, required this.selectedId, required this.onSelect});
  final List<VariantValue> values;
  final int? selectedId;
  final ValueChanged<int> onSelect;

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: 8, runSpacing: 8,
      children: values.map((v) {
        final active = selectedId == v.id;
        return GestureDetector(
          onTap: v.inStock ? () => onSelect(v.id) : null,
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 180),
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: active ? AppColors.orange : (v.inStock ? Colors.white : AppColors.grayBg),
              border: Border.all(
                color: active ? AppColors.orange : (v.inStock ? AppColors.border : AppColors.grayBg),
                width: 1.5,
              ),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              Text(v.value,
                style: TextStyle(
                  color: active ? Colors.white : (v.inStock ? AppColors.text : AppColors.grayLight),
                  fontFamily: 'Cairo',
                  fontWeight: FontWeight.w700,
                  fontSize: 12,
                  decoration: v.inStock ? null : TextDecoration.lineThrough,
                )),
              if (v.priceModifier != 0)
                Text(
                  '${v.priceModifier > 0 ? '+' : ''}${v.priceModifier.toStringAsFixed(0)} ₪',
                  style: TextStyle(
                    fontSize: 9,
                    fontFamily: 'Cairo',
                    color: active ? Colors.white70 : AppColors.orange,
                    fontWeight: FontWeight.w600,
                  ),
                ),
            ]),
          ),
        );
      }).toList(),
    );
  }
}

class _CartIconButton extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final count = context.watch<CartProvider>().count;
    return GestureDetector(
      onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const CartScreen())),
      child: Stack(clipBehavior: Clip.none, children: [
        Container(
          width: 36, height: 36,
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.08), blurRadius: 12, offset: const Offset(0, 2))],
          ),
          child: const Icon(Icons.shopping_cart_outlined, color: AppColors.text, size: 18),
        ),
        if (count > 0)
          Positioned(top: -4, right: -4,
            child: Container(
              padding: const EdgeInsets.all(3),
              constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
              decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.white, width: 1.5)),
              child: Text('$count', textAlign: TextAlign.center,
                style: const TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
            )),
      ]),
    );
  }
}

class _CircleIconButton extends StatelessWidget {
  const _CircleIconButton({required this.icon, required this.onTap});
  final IconData icon;
  final VoidCallback onTap;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 36, height: 36,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.08), blurRadius: 12, offset: const Offset(0, 2))],
        ),
        child: Icon(icon, color: AppColors.text, size: 18),
      ),
    );
  }
}

class _QtyButton extends StatelessWidget {
  const _QtyButton({required this.icon, required this.onTap});
  final IconData icon;
  final VoidCallback onTap;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 32, height: 32,
        decoration: BoxDecoration(color: AppColors.grayBg, borderRadius: BorderRadius.circular(10)),
        alignment: Alignment.center,
        child: Icon(icon, color: AppColors.blue, size: 18),
      ),
    );
  }
}
