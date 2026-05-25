import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:fluttertoast/fluttertoast.dart';
import '../../l10n/app_strings.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/empty_state.dart';
import '../auth/login_screen.dart';
import '../checkout/checkout_screen.dart';
import '../products/products_screen.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  final _coupon = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (context.read<AuthProvider>().isLoggedIn) {
        context.read<CartProvider>().load();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final cart = context.watch<CartProvider>();
    final s    = context.watch<LocaleProvider>().s;

    if (!auth.isLoggedIn) return _LoginPrompt(s: s);

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: Column(children: [

        // ── Header ──────────────────────────────────────────────
        Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft, end: Alignment.bottomRight,
              colors: [AppColors.blueDark, AppColors.blue],
            ),
          ),
          child: SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
              child: Row(children: [
                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(
                    s.cartTitle,
                    style: const TextStyle(
                      fontSize: 20, fontWeight: FontWeight.w900,
                      fontFamily: 'Cairo', color: Colors.white,
                    ),
                  ),
                  if (cart.items.isNotEmpty)
                    Text(
                      s.itemsCount(cart.items.length),
                      style: const TextStyle(
                        color: Colors.white70, fontSize: 12,
                        fontFamily: 'Cairo',
                      ),
                    ),
                ]),
                const Spacer(),
                if (cart.items.isNotEmpty)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                    decoration: BoxDecoration(
                      color: AppColors.orange,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(mainAxisSize: MainAxisSize.min, children: [
                      const Icon(Icons.shopping_cart_outlined, color: Colors.white, size: 14),
                      const SizedBox(width: 4),
                      Text(
                        '${cart.items.length}',
                        style: const TextStyle(
                          color: Colors.white, fontSize: 13,
                          fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                        ),
                      ),
                    ]),
                  ),
              ]),
            ),
          ),
        ),

        // ── Body ─────────────────────────────────────────────────
        Expanded(
          child: cart.loading && cart.items.isEmpty
            ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
            : cart.items.isEmpty
              ? _Empty(s: s)
              : RefreshIndicator(
                  color: AppColors.orange,
                  onRefresh: cart.load,
                  child: ListView(
                    padding: const EdgeInsets.fromLTRB(14, 14, 14, 110),
                    children: [
                      ...cart.items.map((item) => _CartItemCard(
                        item: item,
                        s: s,
                        onIncrement: () => cart.updateQty(item['key'], (item['qty'] as int) + 1),
                        onDecrement: () async {
                          final newQty = (item['qty'] as int) - 1;
                          if (newQty < 1) {
                            await cart.remove(item['key']);
                          } else {
                            await cart.updateQty(item['key'], newQty);
                          }
                        },
                        onRemove: () => cart.remove(item['key']),
                        onSaveForLater: () async {
                          try {
                            await ApiService.instance.post('/wishlist/toggle',
                                data: {'product_id': item['product_id']});
                            await cart.remove(item['key']);
                            Fluttertoast.showToast(
                              msg: s.movedToWishlist,
                              backgroundColor: AppColors.success,
                              textColor: Colors.white,
                            );
                          } catch (e) {
                            Fluttertoast.showToast(
                              msg: e.toString(),
                              backgroundColor: AppColors.danger,
                              textColor: Colors.white,
                            );
                          }
                        },
                      )),
                      const SizedBox(height: 6),
                      _CouponBox(controller: _coupon, s: s, onApply: () {}),
                      const SizedBox(height: 10),
                      _SummaryBox(totals: cart.totals, s: s),
                    ],
                  ),
                ),
        ),

        // ── Sticky checkout button ───────────────────────────────
        if (cart.items.isNotEmpty)
          Container(
            padding: EdgeInsets.fromLTRB(14, 12, 14, MediaQuery.of(context).padding.bottom + 14),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [BoxShadow(
                color: Colors.black.withValues(alpha: 0.06),
                blurRadius: 20, offset: const Offset(0, -3),
              )],
            ),
            child: Column(children: [
              // Total preview
              Padding(
                padding: const EdgeInsets.only(bottom: 10),
                child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                  Text(
                    s.total,
                    style: const TextStyle(
                      fontSize: 13, color: AppColors.gray,
                      fontFamily: 'Cairo', fontWeight: FontWeight.w600,
                    ),
                  ),
                  Text(
                    '${((cart.totals['total'] as num?)?.toDouble() ?? 0).toStringAsFixed(0)} ₪',
                    style: const TextStyle(
                      fontSize: 18, fontWeight: FontWeight.w900,
                      color: AppColors.blueDark, fontFamily: 'Cairo',
                    ),
                  ),
                ]),
              ),
              GestureDetector(
                onTap: () => Navigator.of(context).push(
                    MaterialPageRoute(builder: (_) => const CheckoutScreen())),
                child: Container(
                  height: 54,
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      begin: Alignment.centerRight, end: Alignment.centerLeft,
                      colors: [AppColors.blue, AppColors.blueDark],
                    ),
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [BoxShadow(
                      color: AppColors.blue.withValues(alpha: 0.35),
                      blurRadius: 20, offset: const Offset(0, 8),
                    )],
                  ),
                  alignment: Alignment.center,
                  child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                    Text(
                      s.checkout,
                      style: const TextStyle(
                        color: Colors.white, fontSize: 15,
                        fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                      ),
                    ),
                    const SizedBox(width: 8),
                    const Icon(Icons.arrow_back, color: Colors.white, size: 18),
                  ]),
                ),
              ),
            ]),
          ),
      ]),
    );
  }
}

// ─── Cart item card ────────────────────────────────────────────────────────────
class _CartItemCard extends StatelessWidget {
  const _CartItemCard({
    required this.item,
    required this.s,
    required this.onIncrement,
    required this.onDecrement,
    required this.onRemove,
    required this.onSaveForLater,
  });
  final Map<String, dynamic> item;
  final S s;
  final VoidCallback onIncrement;
  final VoidCallback onDecrement;
  final VoidCallback onRemove;
  final VoidCallback onSaveForLater;

  @override
  Widget build(BuildContext context) {
    return Dismissible(
      key: ValueKey(item['key']),
      direction: DismissDirection.endToStart,
      background: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.symmetric(horizontal: 24),
        alignment: Alignment.centerLeft,
        decoration: BoxDecoration(
          color: AppColors.danger,
          borderRadius: BorderRadius.circular(AppRadius.lg),
        ),
        child: const Icon(Icons.delete_outline, color: Colors.white, size: 26),
      ),
      onDismissed: (_) => onRemove(),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(AppRadius.lg),
          boxShadow: AppShadows.card,
        ),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          // Image
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: SizedBox(
              width: 76, height: 76,
              child: item['image'] != null
                ? CachedNetworkImage(imageUrl: item['image'], fit: BoxFit.cover)
                : Container(
                    color: AppColors.grayBg,
                    child: const Icon(Icons.image_not_supported, color: AppColors.gray),
                  ),
            ),
          ),
          const SizedBox(width: 14),
          // Info
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Expanded(
                child: Text(
                  item['name']?.toString() ?? '',
                  maxLines: 2, overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 13, fontWeight: FontWeight.w800,
                    fontFamily: 'Cairo', color: AppColors.text, height: 1.4,
                  ),
                ),
              ),
              const SizedBox(width: 6),
              // Save for later (wishlist)
              GestureDetector(
                onTap: onSaveForLater,
                child: Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: AppColors.orangeLight,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(Icons.favorite_border_rounded,
                      color: AppColors.orange, size: 16),
                ),
              ),
            ]),
            const SizedBox(height: 10),
            Row(children: [
              // Price
              Text(
                '${(item['price'] as num).toStringAsFixed(0)} ₪',
                style: const TextStyle(
                  color: AppColors.blue, fontSize: 16,
                  fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                ),
              ),
              const Spacer(),
              // Qty controls
              Container(
                decoration: BoxDecoration(
                  color: AppColors.grayBg,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  GestureDetector(
                    onTap: onDecrement,
                    child: Container(
                      width: 34, height: 34,
                      alignment: Alignment.center,
                      child: const Text('−', style: TextStyle(
                        color: AppColors.blue, fontSize: 18,
                        fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                      )),
                    ),
                  ),
                  SizedBox(
                    width: 32,
                    child: Text(
                      '${item['qty']}',
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 14, fontWeight: FontWeight.w900,
                        fontFamily: 'Cairo', color: AppColors.text,
                      ),
                    ),
                  ),
                  GestureDetector(
                    onTap: onIncrement,
                    child: Container(
                      width: 34, height: 34,
                      alignment: Alignment.center,
                      child: const Text('+', style: TextStyle(
                        color: AppColors.blue, fontSize: 18,
                        fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                      )),
                    ),
                  ),
                ]),
              ),
            ]),
          ])),
        ]),
      ),
    );
  }
}

// ─── Coupon box ────────────────────────────────────────────────────────────────
class _CouponBox extends StatelessWidget {
  const _CouponBox({required this.controller, required this.s, required this.onApply});
  final TextEditingController controller;
  final S s;
  final VoidCallback onApply;
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(AppRadius.lg),
        boxShadow: AppShadows.card,
      ),
      child: Row(children: [
        Expanded(
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            decoration: BoxDecoration(
              color: AppColors.grayBg,
              borderRadius: BorderRadius.circular(10),
            ),
            child: TextField(
              controller: controller,
              style: const TextStyle(fontFamily: 'Cairo', fontSize: 13),
              decoration: InputDecoration(
                hintText: s.couponHint,
                border: InputBorder.none,
                contentPadding: const EdgeInsets.symmetric(vertical: 10),
                hintStyle: const TextStyle(fontFamily: 'Cairo', fontSize: 13, color: AppColors.gray),
              ),
            ),
          ),
        ),
        const SizedBox(width: 10),
        GestureDetector(
          onTap: onApply,
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: AppColors.blue,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              s.apply,
              style: const TextStyle(
                color: Colors.white, fontSize: 12,
                fontWeight: FontWeight.w800, fontFamily: 'Cairo',
              ),
            ),
          ),
        ),
      ]),
    );
  }
}

// ─── Summary box ───────────────────────────────────────────────────────────────
class _SummaryBox extends StatelessWidget {
  const _SummaryBox({required this.totals, required this.s});
  final Map<String, dynamic> totals;
  final S s;
  @override
  Widget build(BuildContext context) {
    final subtotal = (totals['subtotal'] as num?)?.toDouble() ?? 0;
    final delivery = (totals['delivery_fee'] as num?)?.toDouble() ?? 0;
    final discount = (totals['discount'] as num?)?.toDouble() ?? 0;
    final total    = (totals['total'] as num?)?.toDouble() ?? subtotal;
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(AppRadius.lg),
        boxShadow: AppShadows.card,
      ),
      child: Column(children: [
        _line(s.subtotal, '${subtotal.toStringAsFixed(0)} ₪', s: s),
        _line(
          s.shipping,
          delivery == 0 ? s.shippingTbd : '${delivery.toStringAsFixed(0)} ₪',
          s: s,
          valueColor: delivery == 0 ? AppColors.gray : null,
        ),
        if (discount > 0)
          _line('− ${s.discount}', '${discount.toStringAsFixed(0)} ₪',
              s: s, valueColor: AppColors.success),
        const Padding(
          padding: EdgeInsets.symmetric(vertical: 10),
          child: _DashedDivider(),
        ),
        Row(children: [
          Text(
            s.total,
            style: const TextStyle(
              fontSize: 14, fontWeight: FontWeight.w900,
              fontFamily: 'Cairo', color: AppColors.text,
            ),
          ),
          const Spacer(),
          Text(
            '${total.toStringAsFixed(0)} ₪',
            style: const TextStyle(
              fontSize: 20, fontWeight: FontWeight.w900,
              color: AppColors.blue, fontFamily: 'Cairo',
            ),
          ),
        ]),
      ]),
    );
  }

  Widget _line(String label, String value, {required S s, Color? valueColor}) =>
      Padding(
        padding: const EdgeInsets.symmetric(vertical: 5),
        child: Row(children: [
          Text(label, style: const TextStyle(
            color: AppColors.gray, fontSize: 13, fontFamily: 'Cairo',
          )),
          const Spacer(),
          Text(value, style: TextStyle(
            color: valueColor ?? AppColors.text,
            fontSize: 13, fontWeight: FontWeight.w700, fontFamily: 'Cairo',
          )),
        ]),
      );
}

class _DashedDivider extends StatelessWidget {
  const _DashedDivider();
  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(builder: (_, c) {
      const dashWidth = 4.0;
      const dashSpace = 4.0;
      final count = (c.constrainWidth() / (dashWidth + dashSpace)).floor();
      return Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: List.generate(count, (_) =>
          Container(width: dashWidth, height: 1, color: AppColors.border)),
      );
    });
  }
}

// ─── Empty ─────────────────────────────────────────────────────────────────────
class _Empty extends StatelessWidget {
  const _Empty({required this.s});
  final S s;
  @override
  Widget build(BuildContext context) {
    return EmptyState(
      icon: Icons.shopping_cart_outlined,
      title: s.emptyCartTitle,
      subtitle: s.emptyCartSub,
      ctaLabel: s.browseCta,
      onCta: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const ProductsScreen())),
    );
  }
}

// ─── Login prompt ──────────────────────────────────────────────────────────────
class _LoginPrompt extends StatelessWidget {
  const _LoginPrompt({required this.s});
  final S s;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(40),
          child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
            Container(
              width: 90, height: 90,
              decoration: BoxDecoration(color: AppColors.blueLight, shape: BoxShape.circle),
              child: const Icon(Icons.lock_outline_rounded, size: 44, color: AppColors.blue),
            ),
            const SizedBox(height: 18),
            Text(
              s.loginToViewCart,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 17, fontWeight: FontWeight.w800,
                color: AppColors.blue, fontFamily: 'Cairo',
              ),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.of(context).push(
                    MaterialPageRoute(builder: (_) => const LoginScreen())),
                child: Text(s.login),
              ),
            ),
          ]),
        ),
      ),
    );
  }
}
