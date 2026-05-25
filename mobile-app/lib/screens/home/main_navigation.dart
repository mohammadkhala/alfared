import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/cart_provider.dart';
import '../../providers/locale_provider.dart';
import '../../theme/app_theme.dart';
import '../account/account_screen.dart';
import '../account/wishlist_screen.dart';
import '../cart/cart_screen.dart';
import '../products/products_screen.dart';
import 'home_screen.dart';

class MainNavigation extends StatefulWidget {
  const MainNavigation({super.key});

  @override
  State<MainNavigation> createState() => _MainNavigationState();
}

class _MainNavigationState extends State<MainNavigation> {
  int _index = 0;

  static const _screenBuilders = [
    HomeScreen.new,
    ProductsScreen.new,
    CartScreen.new,
    WishlistScreen.new,
    AccountScreen.new,
  ];

  @override
  Widget build(BuildContext context) {
    final cartCount = context.watch<CartProvider>().count;
    final localeCode = context.watch<LocaleProvider>().code;

    return Scaffold(
      extendBody: true,
      body: IndexedStack(
        key: ValueKey('nav-$localeCode'),
        index: _index,
        children: _screenBuilders.map((b) => b()).toList(),
      ),
      bottomNavigationBar: _BottomNav(
        currentIndex: _index,
        cartCount: cartCount,
        onTap: (i) => setState(() => _index = i),
      ),
    );
  }
}

// ── Tab definition ────────────────────────────────────────────────────────────

class _TabDef {
  const _TabDef({
    required this.activeIcon,
    required this.outlineIcon,
    required this.color,
    required this.label,
  });
  final IconData activeIcon;
  final IconData outlineIcon;
  final Color color;
  final String label;
}

// ── Bottom nav bar ────────────────────────────────────────────────────────────

class _BottomNav extends StatelessWidget {
  const _BottomNav({
    required this.currentIndex,
    required this.cartCount,
    required this.onTap,
  });
  final int currentIndex;
  final int cartCount;
  final ValueChanged<int> onTap;

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    final tabs = [
      _TabDef(
        activeIcon: Icons.storefront_rounded,
        outlineIcon: Icons.storefront_outlined,
        color: AppColors.orange,
        label: s.navHome,
      ),
      _TabDef(
        activeIcon: Icons.grid_view_rounded,
        outlineIcon: Icons.grid_view_outlined,
        color: const Color(0xFF1B3B8C),
        label: s.navSearch,
      ),
      // index 2 = center cart button
      _TabDef(
        activeIcon: Icons.bookmark_rounded,
        outlineIcon: Icons.bookmark_border_rounded,
        color: const Color(0xFFEC4899),
        label: s.navWishlist,
      ),
      _TabDef(
        activeIcon: Icons.account_circle_rounded,
        outlineIcon: Icons.account_circle_outlined,
        color: const Color(0xFF1B3B8C),
        label: s.navAccount,
      ),
    ];

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: const BorderRadius.only(
          topLeft: Radius.circular(28),
          topRight: Radius.circular(28),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.10),
            blurRadius: 24,
            spreadRadius: -2,
            offset: const Offset(0, -6),
          ),
        ],
      ),
      child: SafeArea(
        top: false,
        child: SizedBox(
          height: 70,
          child: Row(children: [
            _NavItem(tab: tabs[0], active: currentIndex == 0, onTap: () => onTap(0)),
            _NavItem(tab: tabs[1], active: currentIndex == 1, onTap: () => onTap(1)),
            _CenterCart(active: currentIndex == 2, count: cartCount, onTap: () => onTap(2)),
            _NavItem(tab: tabs[2], active: currentIndex == 3, onTap: () => onTap(3)),
            _NavItem(tab: tabs[3], active: currentIndex == 4, onTap: () => onTap(4)),
          ]),
        ),
      ),
    );
  }
}

// ── Nav item ──────────────────────────────────────────────────────────────────

class _NavItem extends StatelessWidget {
  const _NavItem({required this.tab, required this.active, required this.onTap});
  final _TabDef tab;
  final bool active;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap,
        behavior: HitTestBehavior.opaque,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              curve: Curves.easeOutCubic,
              width: active ? 48 : 40,
              height: 34,
              decoration: BoxDecoration(
                color: active
                    ? tab.color.withValues(alpha: 0.13)
                    : Colors.transparent,
                borderRadius: BorderRadius.circular(12),
              ),
              alignment: Alignment.center,
              child: AnimatedSwitcher(
                duration: const Duration(milliseconds: 180),
                child: Icon(
                  active ? tab.activeIcon : tab.outlineIcon,
                  key: ValueKey(active),
                  color: active
                      ? tab.color
                      : tab.color.withValues(alpha: 0.40),
                  size: active ? 22 : 21,
                ),
              ),
            ),
            const SizedBox(height: 3),
            AnimatedDefaultTextStyle(
              duration: const Duration(milliseconds: 180),
              style: TextStyle(
                color: active
                    ? tab.color
                    : tab.color.withValues(alpha: 0.45),
                fontSize: 9,
                fontWeight: active ? FontWeight.w900 : FontWeight.w600,
                fontFamily: 'Cairo',
              ),
              child: Text(tab.label),
            ),
          ],
        ),
      ),
    );
  }
}

// ── Center cart button ────────────────────────────────────────────────────────

class _CenterCart extends StatefulWidget {
  const _CenterCart({required this.active, required this.count, required this.onTap});
  final bool active;
  final int count;
  final VoidCallback onTap;
  @override
  State<_CenterCart> createState() => _CenterCartState();
}

class _CenterCartState extends State<_CenterCart> {
  int _lastCount = 0;

  @override
  void initState() {
    super.initState();
    _lastCount = widget.count;
  }

  @override
  void didUpdateWidget(_CenterCart oldWidget) {
    super.didUpdateWidget(oldWidget);
    _lastCount = oldWidget.count;
  }

  @override
  Widget build(BuildContext context) {
    final bouncing = widget.count > _lastCount;
    return Expanded(
      child: Center(
        child: GestureDetector(
          onTap: widget.onTap,
          child: Transform.translate(
            offset: const Offset(0, -16),
            child: Container(
              width: 58, height: 58,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [AppColors.orange, AppColors.orangeDark],
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: AppShadows.elevated,
                border: Border.all(color: Colors.white, width: 3),
              ),
              child: Stack(
                clipBehavior: Clip.none,
                children: [
                  const Center(
                    child: Icon(Icons.shopping_bag_rounded, color: Colors.white, size: 26),
                  ),
                  if (widget.count > 0)
                    Positioned(
                      top: -4, right: -4,
                      child: TweenAnimationBuilder<double>(
                        key: ValueKey(widget.count),
                        tween: Tween(begin: bouncing ? 0.6 : 1.0, end: 1.0),
                        duration: const Duration(milliseconds: 380),
                        curve: Curves.elasticOut,
                        builder: (_, scale, child) => Transform.scale(scale: scale, child: child),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: AppColors.orange, width: 1.5),
                          ),
                          constraints: const BoxConstraints(minWidth: 18),
                          child: Text(
                            '${widget.count}',
                            textAlign: TextAlign.center,
                            style: const TextStyle(
                              color: AppColors.orange, fontSize: 10,
                              fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                            ),
                          ),
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
