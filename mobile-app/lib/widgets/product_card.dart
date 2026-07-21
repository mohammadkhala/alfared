import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import '../models/product.dart';
import '../providers/auth_provider.dart';
import '../providers/cart_provider.dart';
import '../providers/locale_provider.dart';
import '../screens/auth/login_screen.dart';
import '../screens/products/product_detail_screen.dart';
import '../theme/app_theme.dart';
import '../theme/theme_ext.dart';

class ProductCard extends StatefulWidget {
  const ProductCard({super.key, required this.product, this.badgeLabel, this.badgeColor});
  final Product product;
  final String? badgeLabel;
  final Color? badgeColor;

  @override
  State<ProductCard> createState() => _ProductCardState();
}

class _ProductCardState extends State<ProductCard> with SingleTickerProviderStateMixin {
  bool _adding = false;
  bool _added  = false;
  late AnimationController _scaleCtrl;
  late Animation<double> _scale;

  @override
  void initState() {
    super.initState();
    _scaleCtrl = AnimationController(vsync: this, duration: const Duration(milliseconds: 180));
    _scale = Tween<double>(begin: 1.0, end: 0.93).animate(
      CurvedAnimation(parent: _scaleCtrl, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _scaleCtrl.dispose();
    super.dispose();
  }

  Future<void> _quickAdd() async {
    if (!context.read<AuthProvider>().isLoggedIn) {
      Navigator.of(context).push(MaterialPageRoute(builder: (_) => const LoginScreen()));
      return;
    }
    // Scale press animation
    await _scaleCtrl.forward();
    await _scaleCtrl.reverse();

    setState(() => _adding = true);
    try {
      await context.read<CartProvider>().add(widget.product.id, qty: 1);
      if (!mounted) return;
      setState(() { _adding = false; _added = true; });
      await Future.delayed(const Duration(milliseconds: 1500));
      if (mounted) setState(() => _added = false);
    } catch (e) {
      if (!mounted) return;
      setState(() => _adding = false);
      Fluttertoast.showToast(msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    }
  }

  @override
  Widget build(BuildContext context) {
    final locale = context.watch<LocaleProvider>().code;
    final s = context.watch<LocaleProvider>().s;
    final p = widget.product;
    final displayName = p.nameFor(locale);

    return GestureDetector(
      onTap: () => Navigator.of(context).push(
        MaterialPageRoute(builder: (_) => ProductDetailScreen(slug: p.slug)),
      ),
      child: Container(
        decoration: BoxDecoration(
          color: context.card,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: context.line, width: 0.8),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: context.isDark ? 0.28 : 0.06),
              blurRadius: 14,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          mainAxisSize: MainAxisSize.min,
          children: [
            // ── Image area ──────────────────────────────────────────
            AspectRatio(
              aspectRatio: 1.05,
              child: Stack(children: [
                Positioned.fill(
                  // Product shots are mostly on white, so keep a light plate
                  // even in dark mode — just muted so it isn't glaring.
                  child: Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topRight,
                        end: Alignment.bottomLeft,
                        colors: context.isDark
                            ? const [Color(0xFFE8E6EF), Color(0xFFDCD9E6)]
                            : const [Color(0xFFFFF8F0), Color(0xFFF5F0FF)],
                      ),
                    ),
                  ),
                ),
                Positioned.fill(
                  child: Hero(
                    tag: 'product-image-${p.id}',
                    child: p.image != null
                        ? CachedNetworkImage(
                            imageUrl: p.image!,
                            fit: BoxFit.contain,
                            fadeInDuration: const Duration(milliseconds: 200),
                            placeholder: (_, __) => const _ImagePlaceholder(),
                            errorWidget: (_, __, ___) => const _ImagePlaceholder(),
                          )
                        : const _ImagePlaceholder(),
                  ),
                ),

                if (!p.inStock)
                  Positioned.fill(
                    child: Container(
                      color: Colors.white.withValues(alpha: 0.75),
                      alignment: Alignment.center,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppColors.gray.withValues(alpha: 0.9),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(s.outOfStock,
                          style: const TextStyle(
                            color: Colors.white, fontWeight: FontWeight.w800,
                            fontFamily: 'Cairo', fontSize: 11,
                          )),
                      ),
                    ),
                  ),

                if (p.isOnSale && p.discountPercent != null)
                  Positioned(
                    top: 10, right: 10,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 4),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFFE8711A), Color(0xFFC85E10)],
                        ),
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [BoxShadow(
                          color: AppColors.orange.withValues(alpha: 0.4),
                          blurRadius: 8, offset: const Offset(0, 2),
                        )],
                      ),
                      child: Text('−${p.discountPercent}%',
                        style: const TextStyle(
                          color: Colors.white, fontSize: 10,
                          fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                        )),
                    ),
                  ),

                Positioned(
                  top: 10, left: 10,
                  child: Container(
                    width: 30, height: 30,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(10),
                      boxShadow: [BoxShadow(
                        color: Colors.black.withValues(alpha: 0.1),
                        blurRadius: 8, offset: const Offset(0, 2),
                      )],
                    ),
                    alignment: Alignment.center,
                    child: const Icon(Icons.favorite_border_rounded,
                      color: Color(0xFFEF4444), size: 16),
                  ),
                ),

                if (p.inStock &&
                    p.stockQuantity != null &&
                    p.stockQuantity! > 0 &&
                    p.stockQuantity! <= 5)
                  Positioned(
                    bottom: 8, left: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFEF2F2),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFFECACA)),
                      ),
                      child: Text('⚠ ${p.stockQuantity}',
                        style: const TextStyle(
                          color: Color(0xFFDC2626), fontSize: 9,
                          fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                        )),
                    ),
                  ),

                // Category / section badge (🔥 Hot, ⚡ New, etc.)
                if (widget.badgeLabel != null)
                  Positioned(
                    bottom: 8, right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                      decoration: BoxDecoration(
                        color: (widget.badgeColor ?? AppColors.orange).withValues(alpha: 0.95),
                        borderRadius: BorderRadius.circular(8),
                        boxShadow: [BoxShadow(
                          color: (widget.badgeColor ?? AppColors.orange).withValues(alpha: 0.35),
                          blurRadius: 6, offset: const Offset(0, 2),
                        )],
                      ),
                      child: Text(widget.badgeLabel!,
                        style: const TextStyle(
                          color: Colors.white, fontSize: 9,
                          fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                        )),
                    ),
                  ),
              ]),
            ),

            // Stock progress bar (shows when ≤ 15 units remaining)
            if (p.inStock && p.stockQuantity != null && p.stockQuantity! <= 15)
              LinearProgressIndicator(
                value: (p.stockQuantity! / 15).clamp(0.0, 1.0),
                minHeight: 3,
                backgroundColor: const Color(0xFFF1F5F9),
                valueColor: AlwaysStoppedAnimation<Color>(
                  p.stockQuantity! <= 5  ? const Color(0xFFEF4444) :
                  p.stockQuantity! <= 10 ? AppColors.amber :
                  AppColors.success,
                ),
              ),

            // ── Info area ────────────────────────────────────────────
            Padding(
              padding: const EdgeInsets.fromLTRB(10, 6, 10, 8),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (p.brandName != null)
                    Padding(
                      padding: const EdgeInsets.only(bottom: 3),
                      child: Text(p.brandName!.toUpperCase(),
                        maxLines: 1, overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 9, color: AppColors.orange,
                          fontFamily: 'Cairo', fontWeight: FontWeight.w800,
                          letterSpacing: 0.5,
                        )),
                    ),

                  Text(displayName,
                    maxLines: 2, overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      fontSize: 12.5, fontWeight: FontWeight.w700,
                      fontFamily: 'Cairo', height: 1.3,
                      color: context.text,
                    )),
                  const SizedBox(height: 5),

                  Row(children: [
                    ...List.generate(5, (i) => Icon(
                      i < p.ratingAvg.round()
                          ? Icons.star_rounded
                          : Icons.star_outline_rounded,
                      size: 11, color: AppColors.amber,
                    )),
                    if (p.reviewsCount > 0) ...[
                      const SizedBox(width: 3),
                      Text('(${p.reviewsCount})',
                        style: const TextStyle(
                          color: AppColors.gray, fontSize: 9, fontFamily: 'Cairo')),
                    ],
                  ]),
                  const SizedBox(height: 8),

                  Row(crossAxisAlignment: CrossAxisAlignment.end, children: [
                    Text('${p.price.toStringAsFixed(0)} ₪',
                      style: const TextStyle(
                        color: AppColors.blue, fontSize: 15,
                        fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                      )),
                    if (p.comparePrice != null) ...[
                      const SizedBox(width: 5),
                      Padding(
                        padding: const EdgeInsets.only(bottom: 1),
                        child: Text('${p.comparePrice!.toStringAsFixed(0)} ₪',
                          style: const TextStyle(
                            color: AppColors.grayLight, fontSize: 10,
                            fontFamily: 'Cairo',
                            decoration: TextDecoration.lineThrough,
                          )),
                      ),
                    ],
                  ]),

                  const SizedBox(height: 8),

                  // Add to cart button with animation
                  if (p.inStock)
                    ScaleTransition(
                      scale: _scale,
                      child: GestureDetector(
                        onTap: (_adding || _added) ? null : _quickAdd,
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 300),
                          height: 36,
                          decoration: BoxDecoration(
                            gradient: _added
                              ? null
                              : const LinearGradient(
                                  begin: Alignment.centerRight,
                                  end: Alignment.centerLeft,
                                  colors: [Color(0xFFE8711A), Color(0xFFC85E10)],
                                ),
                            color: _added ? AppColors.success : null,
                            borderRadius: BorderRadius.circular(10),
                            boxShadow: [BoxShadow(
                              color: (_added ? AppColors.success : AppColors.orange).withValues(alpha: 0.30),
                              blurRadius: 8, offset: const Offset(0, 3),
                            )],
                          ),
                          child: Center(
                            child: _adding
                              ? const SizedBox(
                                  width: 16, height: 16,
                                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                )
                              : Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(
                                      _added ? Icons.check_circle_outline_rounded : Icons.add_shopping_cart_rounded,
                                      color: Colors.white, size: 15,
                                    ),
                                    const SizedBox(width: 6),
                                    Text(
                                      _added ? s.addedToCartShort : s.addToCart,
                                      style: const TextStyle(
                                        color: Colors.white, fontSize: 11,
                                        fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                                      )),
                                  ],
                                ),
                          ),
                        ),
                      ),
                    )
                  else
                    Container(
                      height: 36,
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      alignment: Alignment.center,
                      child: Text(s.outOfStock,
                        style: const TextStyle(
                          color: AppColors.grayLight, fontSize: 11,
                          fontWeight: FontWeight.w700, fontFamily: 'Cairo',
                        )),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ── Placeholder ──────────────────────────────────────────────────────────────

class _ImagePlaceholder extends StatelessWidget {
  const _ImagePlaceholder();
  @override
  Widget build(BuildContext context) {
    return Container(
      color: const Color(0xFFF8F9FA),
      child: Center(
        child: Icon(Icons.image_outlined, color: Colors.grey.shade300, size: 48),
      ),
    );
  }
}
