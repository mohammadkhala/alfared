import 'dart:async';

import 'package:cached_network_image/cached_network_image.dart';
import 'package:carousel_slider/carousel_slider.dart' as cs;
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../models/category.dart';
import '../../models/product.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_service.dart';
import '../../services/recently_viewed_service.dart';
import '../../theme/app_theme.dart';
import '../../providers/locale_provider.dart';
import '../../utils/greeting.dart';
import '../../widgets/product_card.dart';
import '../../widgets/product_card_skeleton.dart';
import '../notifications_screen.dart';
import '../cart/cart_screen.dart';
import '../account/rewards_screen.dart';
import '../products/categories_screen.dart';
import '../products/products_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  List<AppBanner> _banners      = [];
  List<AppBanner> _offerBanners = [];
  List<Category>  _categories   = [];
  List<Product>   _featured     = [];
  List<Product>   _newArrivals  = [];
  List<Product>   _offers       = [];
  List<Product>   _recent       = [];
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final res  = await ApiService.instance.get('/home');
      final data = res as Map;
      _banners      = ((data['banners']      as List?) ?? []).map((e) => AppBanner.fromJson(e as Map<String, dynamic>)).toList();
      _categories   = ((data['categories']   as List?) ?? []).map((e) => Category.fromJson(e as Map<String, dynamic>)).toList();
      _featured     = ((data['featured']     as List?) ?? []).map((e) => Product.fromJson(e as Map<String, dynamic>)).toList();
      _newArrivals  = ((data['newArrivals']  as List?) ?? []).map((e) => Product.fromJson(e as Map<String, dynamic>)).toList();
      _offers       = ((data['offers']       as List?) ?? []).map((e) => Product.fromJson(e as Map<String, dynamic>)).toList();
      _offerBanners = ((data['offerBanners'] as List?) ?? []).map((e) => AppBanner.fromJson(e as Map<String, dynamic>)).toList();
      _recent       = await RecentlyViewedService.list();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  // Extract unique brand names from all product lists
  List<String> get _brands {
    final set = <String>{};
    for (final list in [_featured, _newArrivals, _offers]) {
      for (final p in list) {
        if (p.brandName != null && p.brandName!.isNotEmpty) set.add(p.brandName!);
      }
    }
    return set.toList();
  }

  @override
  Widget build(BuildContext context) {
    final user      = context.watch<AuthProvider>().user;
    final cartCount = context.watch<CartProvider>().count;
    final s         = context.watch<LocaleProvider>().s;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      floatingActionButton: _WhatsAppFab(),
      floatingActionButtonLocation: FloatingActionButtonLocation.startFloat,
      body: _loading
        ? Column(children: [
            _Header(name: user?.name, cartCount: cartCount, points: user?.loyaltyPoints),
            const Expanded(child: HomeBodySkeleton()),
          ])
        : RefreshIndicator(
            color: AppColors.orange,
            onRefresh: _load,
            child: CustomScrollView(slivers: [
              SliverToBoxAdapter(child: _Header(name: user?.name, cartCount: cartCount, points: user?.loyaltyPoints)),
              SliverToBoxAdapter(child: const _MarqueeBar()),
              SliverPadding(
                padding: const EdgeInsets.only(top: 16, bottom: 100),
                sliver: SliverList(
                  delegate: SliverChildListDelegate([

                    // 1) Hero carousel
                    if (_banners.isNotEmpty) _HeroCarousel(banners: _banners),
                    const SizedBox(height: 18),

                    // 2) Features strip
                    _FeaturesStrip(),
                    const SizedBox(height: 20),

                    // 3) Categories
                    _SectionHeader(
                      title: s.sectionCategories,
                      onSeeAll: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const CategoriesScreen())),
                    ),
                    const SizedBox(height: 10),
                    _CategoryStories(categories: _categories),
                    const SizedBox(height: 24),

                    // 4) Flash Sale + Offers
                    if (_offers.isNotEmpty || _offerBanners.isNotEmpty) ...[
                      _FlashSaleSection(banners: _offerBanners, offers: _offers),
                      const SizedBox(height: 24),
                    ],

                    // 5) Recently viewed
                    if (_recent.isNotEmpty) ...[
                      _SectionHeader(title: s.sectionRecentlyViewed),
                      const SizedBox(height: 10),
                      SizedBox(
                        height: 315,
                        child: ListView.separated(
                          scrollDirection: Axis.horizontal,
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                          itemCount: _recent.length,
                          separatorBuilder: (_, __) => const SizedBox(width: 10),
                          itemBuilder: (_, i) => SizedBox(width: 155,
                            child: ProductCard(product: _recent[i])),
                        ),
                      ),
                      const SizedBox(height: 24),
                    ],

                    // 6) Loyalty mid-feed card
                    if (user != null && user.loyaltyPoints > 0) ...[
                      _LoyaltyFeedCard(user: user),
                      const SizedBox(height: 24),
                    ],

                    // 7) Bestsellers grid (🔥 badge)
                    if (_featured.isNotEmpty) ...[
                      _SectionHeader(
                        title: s.sectionBestseller,
                        onSeeAll: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ProductsScreen())),
                      ),
                      const SizedBox(height: 10),
                      _ProductGrid(
                        products: _featured.take(4).toList(),
                        badgeLabel: '⭐ رائج',
                        badgeColor: const Color(0xFFEF4444),
                      ),
                      const SizedBox(height: 24),
                    ],

                    // 8) New arrivals (⚡ badge)
                    if (_newArrivals.isNotEmpty) ...[
                      _SectionHeader(title: s.sectionNewArrivals),
                      const SizedBox(height: 10),
                      SizedBox(
                        height: 315,
                        child: ListView.separated(
                          scrollDirection: Axis.horizontal,
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                          itemCount: _newArrivals.length,
                          separatorBuilder: (_, __) => const SizedBox(width: 10),
                          itemBuilder: (_, i) => SizedBox(width: 155,
                            child: ProductCard(
                              product: _newArrivals[i],
                              badgeLabel: '✨ جديد',
                              badgeColor: AppColors.blue,
                            )),
                        ),
                      ),
                      const SizedBox(height: 24),
                    ],

                    // 9) Brands carousel
                    if (_brands.length >= 3) ...[
                      _SectionHeader(title: '🏪 الماركات المتوفرة'),
                      const SizedBox(height: 10),
                      _BrandsCarousel(brands: _brands),
                      const SizedBox(height: 24),
                    ],

                    // 10) Promo CTA
                    _PromoCta(),
                    const SizedBox(height: 22),

                    // 11) Trust badges
                    _TrustBadges(),
                    const SizedBox(height: 16),
                  ]),
                ),
              ),
            ]),
          ),
    );
  }
}

// ═══════════════════════════════════════════════
// HEADER
// ═══════════════════════════════════════════════
class _Header extends StatelessWidget {
  const _Header({this.name, required this.cartCount, this.points});
  final String? name;
  final int cartCount;
  final int? points;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft, end: Alignment.bottomRight,
          colors: [AppColors.blueDark, AppColors.blue],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(28), bottomRight: Radius.circular(28),
        ),
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 18),
          child: Column(children: [
            Row(children: [
              // Avatar + greeting
              Container(
                width: 42, height: 42,
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.white.withValues(alpha: 0.25), width: 1.5),
                ),
                alignment: Alignment.center,
                child: Text(
                  name != null && name!.isNotEmpty ? name![0].toUpperCase() : '👤',
                  style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w900, fontFamily: 'Cairo'),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Builder(builder: (_) {
                    final g = Greeting.now();
                    return Text('${g.text} ${g.emoji}',
                      style: const TextStyle(color: Colors.white60, fontSize: 11, fontFamily: 'Cairo'));
                  }),
                  const SizedBox(height: 2),
                  Row(children: [
                    Flexible(
                      child: Text(name ?? 'مرحباً',
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(color: Colors.white, fontSize: 15, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
                    ),
                    if (points != null && points! > 0) ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: AppColors.orange.withValues(alpha: 0.9),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text('⭐ $points',
                          style: const TextStyle(
                            color: Colors.white, fontSize: 10,
                            fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                          )),
                      ),
                    ],
                  ]),
                ]),
              ),
              // Notification bell (with pulse when there's a count)
              _PulsingBell(
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const NotificationsScreen())),
              ),
              const SizedBox(width: 8),
              _HeaderIcon(
                icon: Icons.language_rounded,
                onTap: () => LocaleProvider.showPicker(context),
              ),
              const SizedBox(width: 8),
              _HeaderIcon(
                icon: Icons.shopping_cart_outlined,
                badge: cartCount > 0 ? '$cartCount' : null,
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const CartScreen())),
              ),
            ]),
            const SizedBox(height: 14),
            // Search bar
            GestureDetector(
              onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ProductsScreen())),
              child: Container(
                padding: const EdgeInsets.fromLTRB(14, 10, 10, 10),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.white.withValues(alpha: 0.15)),
                ),
                child: Row(children: [
                  const Icon(Icons.search, color: Colors.white70, size: 18),
                  const SizedBox(width: 8),
                  Expanded(child: Text(
                    context.watch<LocaleProvider>().s.searchHint,
                    style: const TextStyle(color: Colors.white54, fontSize: 12, fontFamily: 'Cairo'),
                  )),
                  Container(
                    width: 30, height: 30,
                    decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(9)),
                    child: const Icon(Icons.tune, color: Colors.white, size: 14),
                  ),
                ]),
              ),
            ),
          ]),
        ),
      ),
    );
  }
}

class _HeaderIcon extends StatelessWidget {
  const _HeaderIcon({required this.icon, this.badge, required this.onTap});
  final IconData icon;
  final String? badge;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Stack(clipBehavior: Clip.none, children: [
        Container(
          width: 38, height: 38,
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.12),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.white.withValues(alpha: 0.15)),
          ),
          alignment: Alignment.center,
          child: Icon(icon, color: Colors.white, size: 18),
        ),
        if (badge != null)
          Positioned(top: -5, right: -5, child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1),
            constraints: const BoxConstraints(minWidth: 16),
            decoration: BoxDecoration(
              color: AppColors.orange,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: AppColors.blueDark, width: 2),
            ),
            alignment: Alignment.center,
            child: Text(badge!, style: const TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
          )),
      ]),
    );
  }
}

// ── Pulsing bell with animation ──────────────────────────────────────────────
class _PulsingBell extends StatefulWidget {
  const _PulsingBell({required this.onTap});
  final VoidCallback onTap;

  @override
  State<_PulsingBell> createState() => _PulsingBellState();
}

class _PulsingBellState extends State<_PulsingBell> with SingleTickerProviderStateMixin {
  late AnimationController _ctrl;
  late Animation<double> _scale;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(vsync: this, duration: const Duration(milliseconds: 800))
      ..repeat(reverse: true);
    _scale = Tween<double>(begin: 0.85, end: 1.15).animate(
      CurvedAnimation(parent: _ctrl, curve: Curves.easeInOut));
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: widget.onTap,
      child: Stack(clipBehavior: Clip.none, children: [
        Container(
          width: 38, height: 38,
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.12),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.white.withValues(alpha: 0.15)),
          ),
          alignment: Alignment.center,
          child: const Icon(Icons.notifications_outlined, color: Colors.white, size: 18),
        ),
        // Pulsing dot
        Positioned(
          top: 5, right: 5,
          child: ScaleTransition(
            scale: _scale,
            child: Container(
              width: 8, height: 8,
              decoration: BoxDecoration(
                color: AppColors.orange,
                shape: BoxShape.circle,
                border: Border.all(color: AppColors.blueDark, width: 1.5),
                boxShadow: [BoxShadow(
                  color: AppColors.orange.withValues(alpha: 0.6),
                  blurRadius: 4,
                )],
              ),
            ),
          ),
        ),
      ]),
    );
  }
}

// ═══════════════════════════════════════════════
// MARQUEE BAR
// ═══════════════════════════════════════════════
class _MarqueeBar extends StatefulWidget {
  const _MarqueeBar();

  @override
  State<_MarqueeBar> createState() => _MarqueeBarState();
}

class _MarqueeBarState extends State<_MarqueeBar> with SingleTickerProviderStateMixin {
  late AnimationController _ctrl;

  static const _text =
      '   🛵 توصيل لجميع مناطق فلسطين والداخل   •   🏆 أكثر من 10,000 عميل سعيد   •   🎁 اكسب نقاط مع كل طلب   •   💳 أسعار الجملة للمحلات والمتاجر   •   🗂️ +5000 منتج متوفر   •   ';

  static const _style = TextStyle(
    color: Colors.white, fontSize: 11,
    fontFamily: 'Cairo', fontWeight: FontWeight.w700,
  );

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 28),
    )..repeat();
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 32,
      clipBehavior: Clip.hardEdge,
      decoration: const BoxDecoration(color: AppColors.orange),
      child: LayoutBuilder(builder: (ctx, _) {
        final tp = TextPainter(
          text: const TextSpan(text: _text, style: _style),
          textDirection: TextDirection.ltr,
        )..layout(maxWidth: double.infinity);
        final tw = tp.width;

        return AnimatedBuilder(
          animation: _ctrl,
          builder: (_, __) {
            final dx = (_ctrl.value * tw) % tw;
            return Stack(clipBehavior: Clip.none, children: [
              Positioned(
                left: -dx, top: 0, bottom: 0,
                child: Align(
                  alignment: Alignment.center,
                  child: Text(_text + _text, style: _style, softWrap: false, maxLines: 1),
                ),
              ),
            ]);
          },
        );
      }),
    );
  }
}

// ═══════════════════════════════════════════════
// HERO CAROUSEL
// ═══════════════════════════════════════════════
class _HeroCarousel extends StatefulWidget {
  const _HeroCarousel({required this.banners});
  final List<AppBanner> banners;

  @override
  State<_HeroCarousel> createState() => _HeroCarouselState();
}

class _HeroCarouselState extends State<_HeroCarousel> {
  int _idx = 0;

  @override
  Widget build(BuildContext context) {
    return Column(children: [
      cs.CarouselSlider(
        options: cs.CarouselOptions(
          height: 190,
          autoPlay: widget.banners.length > 1,
          autoPlayInterval: const Duration(seconds: 5),
          enlargeCenterPage: true,
          viewportFraction: 0.93,
          onPageChanged: (i, _) => setState(() => _idx = i),
        ),
        items: widget.banners.map((b) => _HeroBannerCard(banner: b)).toList(),
      ),
      if (widget.banners.length > 1) ...[
        const SizedBox(height: 10),
        Row(mainAxisAlignment: MainAxisAlignment.center, children: List.generate(widget.banners.length, (i) {
          final active = i == _idx;
          return AnimatedContainer(
            duration: const Duration(milliseconds: 250),
            margin: const EdgeInsets.symmetric(horizontal: 3),
            width: active ? 22 : 7, height: 7,
            decoration: BoxDecoration(
              color: active ? AppColors.orange : AppColors.border,
              borderRadius: BorderRadius.circular(4),
            ),
          );
        })),
      ],
    ]);
  }
}

class _HeroBannerCard extends StatelessWidget {
  const _HeroBannerCard({required this.banner});
  final AppBanner banner;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 4),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        gradient: const LinearGradient(colors: [AppColors.blueDark, AppColors.blue]),
      ),
      clipBehavior: Clip.antiAlias,
      child: Stack(children: [
        if (banner.image != null)
          Positioned.fill(child: CachedNetworkImage(imageUrl: banner.image!, fit: BoxFit.cover)),
        Positioned.fill(child: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.bottomCenter, end: Alignment.topCenter,
              colors: [Colors.black.withValues(alpha: 0.72), Colors.transparent],
            ),
          ),
        )),
        Padding(
          padding: const EdgeInsets.all(16),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, mainAxisAlignment: MainAxisAlignment.end, children: [
            if (banner.badge != null)
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(20)),
                child: Text(banner.badge!, style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
              ),
            const SizedBox(height: 8),
            if (banner.title != null)
              Text(banner.title!, style: const TextStyle(color: Colors.white, fontSize: 19, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
          ]),
        ),
      ]),
    );
  }
}

// ═══════════════════════════════════════════════
// FEATURES STRIP
// ═══════════════════════════════════════════════
class _FeaturesStrip extends StatelessWidget {
  static const _icons   = ['🛵', '🔒', '💳'];
  static const _bgColors = [0xFFEEF2FF, 0xFFECFDF5, 0xFFFFF3E8];

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;
    final titles = [s.featureDelivery, s.featureOriginal, s.featureSecurePay];

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14),
      child: Row(
        children: List.generate(3, (i) => Expanded(
          child: Padding(
            padding: EdgeInsets.only(right: i == 0 ? 0 : 6, left: i == 2 ? 0 : 6),
            child: Container(
              padding: const EdgeInsets.symmetric(vertical: 12),
              decoration: BoxDecoration(
                color: Color(_bgColors[i]),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [BoxShadow(
                  color: Colors.black.withValues(alpha: 0.04),
                  blurRadius: 8, offset: const Offset(0, 2),
                )],
              ),
              child: Column(children: [
                Text(_icons[i], style: const TextStyle(fontSize: 26)),
                const SizedBox(height: 6),
                Text(titles[i],
                  textAlign: TextAlign.center, maxLines: 2, overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 10, fontWeight: FontWeight.w800,
                    fontFamily: 'Cairo', color: AppColors.text,
                  )),
              ]),
            ),
          ),
        )),
      ),
    );
  }
}

// ═══════════════════════════════════════════════
// SECTION HEADER
// ═══════════════════════════════════════════════
class _SectionHeader extends StatelessWidget {
  const _SectionHeader({required this.title, this.onSeeAll});
  final String title;
  final VoidCallback? onSeeAll;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14),
      child: Row(children: [
        // Accent bar
        Container(width: 4, height: 18, decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(2))),
        const SizedBox(width: 8),
        Text(title, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.blueDark)),
        const Spacer(),
        if (onSeeAll != null)
          GestureDetector(
            onTap: onSeeAll,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: AppColors.orangeLight,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                context.watch<LocaleProvider>().s.seeAll,
                style: const TextStyle(color: AppColors.orange, fontSize: 11, fontWeight: FontWeight.w800, fontFamily: 'Cairo'),
              ),
            ),
          ),
      ]),
    );
  }
}

// ═══════════════════════════════════════════════
// CATEGORY STORIES — colorful gradients
// ═══════════════════════════════════════════════
class _CategoryStories extends StatelessWidget {
  const _CategoryStories({required this.categories});
  final List<Category> categories;

  static const _emojis = ['🧴', '🧼', '🪥', '🌿', '🧃', '🥤', '🧹', '🛒'];

  // Per-index gradient pairs
  static const _gradients = [
    [Color(0xFFFF6B6B), Color(0xFFFF8E53)], // 0 Orange-Red
    [Color(0xFF4ECDC4), Color(0xFF2196F3)], // 1 Teal-Blue
    [Color(0xFF9B59B6), Color(0xFF8E44AD)], // 2 Purple
    [Color(0xFF2ECC71), Color(0xFF27AE60)], // 3 Green
    [Color(0xFFF39C12), Color(0xFFE67E22)], // 4 Amber
    [Color(0xFFE74C3C), Color(0xFFC0392B)], // 5 Red
    [Color(0xFF1ABC9C), Color(0xFF16A085)], // 6 Teal
    [Color(0xFF3498DB), Color(0xFF2980B9)], // 7 Blue
    [Color(0xFFE91E63), Color(0xFFC2185B)], // 8 Pink
    [Color(0xFF607D8B), Color(0xFF455A64)], // 9 Blue Grey
  ];

  @override
  Widget build(BuildContext context) {
    final locale = context.watch<LocaleProvider>().code;

    return SizedBox(
      height: 96,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 14),
        itemCount: categories.length,
        separatorBuilder: (_, __) => const SizedBox(width: 12),
        itemBuilder: (_, i) {
          final c = categories[i];
          final label = c.nameFor(locale);
          final grad = _gradients[i % _gradients.length];

          return GestureDetector(
            onTap: () => Navigator.of(context).push(MaterialPageRoute(
              builder: (_) => ProductsScreen(categorySlug: c.slug, categoryName: label))),
            child: Column(children: [
              Container(
                width: 64, height: 64,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(20),
                  gradient: LinearGradient(
                    begin: Alignment.topLeft, end: Alignment.bottomRight,
                    colors: grad,
                  ),
                  boxShadow: [BoxShadow(
                    color: grad[0].withValues(alpha: 0.40),
                    blurRadius: 10, offset: const Offset(0, 4),
                  )],
                ),
                padding: const EdgeInsets.all(2),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(18),
                  child: c.image != null
                    ? CachedNetworkImage(imageUrl: c.image!, fit: BoxFit.cover, width: 60, height: 60)
                    : Center(child: Text(_emojis[i % _emojis.length], style: const TextStyle(fontSize: 28))),
                ),
              ),
              const SizedBox(height: 6),
              SizedBox(
                width: 72,
                child: Text(label,
                  textAlign: TextAlign.center, maxLines: 1, overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontSize: 10, fontFamily: 'Cairo', fontWeight: FontWeight.w800,
                    color: i == 0 ? grad[0] : AppColors.text,
                  )),
              ),
            ]),
          );
        },
      ),
    );
  }
}

// ═══════════════════════════════════════════════
// FLASH SALE SECTION (countdown + offers)
// ═══════════════════════════════════════════════
class _FlashSaleSection extends StatefulWidget {
  const _FlashSaleSection({required this.banners, required this.offers});
  final List<AppBanner> banners;
  final List<Product> offers;

  @override
  State<_FlashSaleSection> createState() => _FlashSaleSectionState();
}

class _FlashSaleSectionState extends State<_FlashSaleSection> {
  late Timer _timer;
  late Duration _remaining;

  @override
  void initState() {
    super.initState();
    _remaining = _calcRemaining();
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (mounted) setState(() => _remaining = _calcRemaining());
    });
  }

  @override
  void dispose() { _timer.cancel(); super.dispose(); }

  Duration _calcRemaining() {
    final now      = DateTime.now();
    final midnight = DateTime(now.year, now.month, now.day + 1);
    return midnight.difference(now);
  }

  String _pad(int n) => n.toString().padLeft(2, '0');

  @override
  Widget build(BuildContext context) {
    final h  = _pad(_remaining.inHours);
    final m  = _pad(_remaining.inMinutes.remainder(60));
    final sc = _pad(_remaining.inSeconds.remainder(60));

    return Column(children: [
      // Flash sale banner with countdown
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 14),
        child: Container(
          padding: const EdgeInsets.fromLTRB(16, 14, 14, 14),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              begin: Alignment.centerRight, end: Alignment.centerLeft,
              colors: [Color(0xFFC85E10), AppColors.orange],
            ),
            borderRadius: BorderRadius.circular(20),
            boxShadow: [BoxShadow(
              color: AppColors.orange.withValues(alpha: 0.3),
              blurRadius: 12, offset: const Offset(0, 4),
            )],
          ),
          child: Row(children: [
            const Text('⚡', style: TextStyle(fontSize: 36)),
            const SizedBox(width: 12),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('عروض اليوم فقط!',
                style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
              const Text('لا تفوّت الفرصة — الأسعار تتغير غداً',
                style: TextStyle(color: Colors.white70, fontSize: 11, fontFamily: 'Cairo')),
            ])),
            const SizedBox(width: 12),
            // Countdown timer
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.black.withValues(alpha: 0.25),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(children: [
                const Text('ينتهي خلال',
                  style: TextStyle(color: Colors.white60, fontSize: 8, fontFamily: 'Cairo')),
                const SizedBox(height: 4),
                Row(children: [
                  _TimeUnit(value: h, label: 'س'),
                  const _TimeSep(),
                  _TimeUnit(value: m, label: 'د'),
                  const _TimeSep(),
                  _TimeUnit(value: sc, label: 'ث'),
                ]),
              ]),
            ),
          ]),
        ),
      ),

      if (widget.offers.isNotEmpty) ...[
        const SizedBox(height: 14),
        SizedBox(
          height: 310,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 14),
            itemCount: widget.offers.length,
            separatorBuilder: (_, __) => const SizedBox(width: 10),
            itemBuilder: (_, i) => SizedBox(
              width: 145,
              child: ProductCard(
                product: widget.offers[i],
                badgeLabel: '💥 عرض',
                badgeColor: const Color(0xFFC85E10),
              ),
            ),
          ),
        ),
      ],
    ]);
  }
}

class _TimeUnit extends StatelessWidget {
  const _TimeUnit({required this.value, required this.label});
  final String value;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Column(children: [
      Container(
        width: 28, height: 26,
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.2),
          borderRadius: BorderRadius.circular(6),
        ),
        alignment: Alignment.center,
        child: Text(value, style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
      ),
      const SizedBox(height: 2),
      Text(label, style: const TextStyle(color: Colors.white60, fontSize: 8, fontFamily: 'Cairo')),
    ]);
  }
}

class _TimeSep extends StatelessWidget {
  const _TimeSep();
  @override
  Widget build(BuildContext context) {
    return const Padding(
      padding: EdgeInsets.only(bottom: 8, left: 2, right: 2),
      child: Text(':', style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.w900)),
    );
  }
}

// ═══════════════════════════════════════════════
// LOYALTY MID-FEED CARD
// ═══════════════════════════════════════════════
class _LoyaltyFeedCard extends StatelessWidget {
  const _LoyaltyFeedCard({required this.user});
  final dynamic user; // AppUser

  @override
  Widget build(BuildContext context) {
    final pts      = user.loyaltyPoints as int;
    final progress = (user.tierProgress as double).clamp(0.0, 1.0);
    final tier     = user.tierLabel  as String;
    final emoji    = user.tierEmoji  as String;
    final nextTier = user.nextTierLabel as String?;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            begin: Alignment.topLeft, end: Alignment.bottomRight,
            colors: [AppColors.blueDark, AppColors.blue],
          ),
          borderRadius: BorderRadius.circular(20),
          boxShadow: [BoxShadow(
            color: AppColors.blue.withValues(alpha: 0.25),
            blurRadius: 12, offset: const Offset(0, 4),
          )],
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Text(emoji, style: const TextStyle(fontSize: 28)),
            const SizedBox(width: 10),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('نقاط الولاء الخاصة بك',
                style: TextStyle(color: Colors.white70, fontSize: 11, fontFamily: 'Cairo')),
              Row(children: [
                Text('$pts', style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
                const SizedBox(width: 6),
                Text('نقطة  •  $tier', style: const TextStyle(color: Colors.white60, fontSize: 11, fontFamily: 'Cairo')),
              ]),
            ])),
            // Value chip
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
              decoration: BoxDecoration(
                color: AppColors.orange,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Text('= ${(pts / 20).toStringAsFixed(0)} ₪',
                style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
            ),
          ]),
          const SizedBox(height: 14),
          // Progress bar toward next tier
          ClipRRect(
            borderRadius: BorderRadius.circular(6),
            child: LinearProgressIndicator(
              value: progress,
              minHeight: 7,
              backgroundColor: Colors.white.withValues(alpha: 0.15),
              valueColor: const AlwaysStoppedAnimation<Color>(AppColors.orange),
            ),
          ),
          if (nextTier != null) ...[
            const SizedBox(height: 6),
            Text('${(progress * 100).round()}% نحو مستوى $nextTier',
              style: const TextStyle(color: Colors.white60, fontSize: 10, fontFamily: 'Cairo')),
          ],
        ]),
      ),
    );
  }
}

// ═══════════════════════════════════════════════
// PRODUCT GRID
// ═══════════════════════════════════════════════
class _ProductGrid extends StatelessWidget {
  const _ProductGrid({required this.products, this.badgeLabel, this.badgeColor});
  final List<Product> products;
  final String? badgeLabel;
  final Color? badgeColor;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14),
      child: GridView.builder(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: products.length,
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2, crossAxisSpacing: 10, mainAxisSpacing: 10, childAspectRatio: 0.54,
        ),
        itemBuilder: (_, i) => ProductCard(
          product: products[i],
          badgeLabel: badgeLabel,
          badgeColor: badgeColor,
        ),
      ),
    );
  }
}

// ═══════════════════════════════════════════════
// BRANDS CAROUSEL
// ═══════════════════════════════════════════════
class _BrandsCarousel extends StatelessWidget {
  const _BrandsCarousel({required this.brands});
  final List<String> brands;

  static const _colors = [
    Color(0xFFEEF2FF), Color(0xFFECFDF5), Color(0xFFFFF3E8),
    Color(0xFFFDF4FF), Color(0xFFF0FDF4), Color(0xFFFEF3C7),
  ];

  static const _textColors = [
    Color(0xFF4338CA), Color(0xFF065F46), Color(0xFF92400E),
    Color(0xFF7E22CE), Color(0xFF14532D), Color(0xFF92400E),
  ];

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 54,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 14),
        itemCount: brands.length,
        separatorBuilder: (_, __) => const SizedBox(width: 10),
        itemBuilder: (_, i) {
          final bg    = _colors[i % _colors.length];
          final color = _textColors[i % _textColors.length];
          return Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: bg,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: color.withValues(alpha: 0.2)),
            ),
            alignment: Alignment.center,
            child: Text(
              brands[i],
              style: TextStyle(
                fontFamily: 'Cairo', fontWeight: FontWeight.w800,
                fontSize: 12, color: color,
              ),
            ),
          );
        },
      ),
    );
  }
}

// ═══════════════════════════════════════════════
// PROMO CTA
// ═══════════════════════════════════════════════
class _PromoCta extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;
    return GestureDetector(
      onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RewardsScreen())),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 14),
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            begin: Alignment.topRight, end: Alignment.bottomLeft,
            colors: [AppColors.blueDark, AppColors.blue],
          ),
          borderRadius: BorderRadius.circular(22),
          boxShadow: [BoxShadow(
            color: AppColors.blue.withValues(alpha: 0.3),
            blurRadius: 14, offset: const Offset(0, 5),
          )],
        ),
        child: Stack(children: [
          Positioned(top: -20, right: -20, child: Container(
            width: 100, height: 100,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.06),
              shape: BoxShape.circle,
            ),
          )),
          Positioned(bottom: -30, left: -10, child: Container(
            width: 80, height: 80,
            decoration: BoxDecoration(
              color: AppColors.orange.withValues(alpha: 0.12),
              shape: BoxShape.circle,
            ),
          )),
          Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('🏆', style: TextStyle(fontSize: 32)),
            const SizedBox(height: 8),
            Text(s.promoCta1, style: const TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
            const SizedBox(height: 4),
            Text(s.promoCta2, style: const TextStyle(color: Colors.white70, fontSize: 12, fontFamily: 'Cairo', height: 1.6)),
            const SizedBox(height: 14),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
              decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(12)),
              child: Text(s.promoCtaBtn,
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontFamily: 'Cairo', fontSize: 13)),
            ),
          ]),
        ]),
      ),
    );
  }
}

// ═══════════════════════════════════════════════
// TRUST BADGES
// ═══════════════════════════════════════════════
class _TrustBadges extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 14),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: AppShadows.card,
      ),
      child: Column(children: [
        Text(s.whyUs,
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, color: AppColors.blueDark, fontFamily: 'Cairo')),
        const SizedBox(height: 14),
        Row(children: [
          Expanded(child: _Badge(icon: '🏆', title: '+5,000', sub: s.unitProduct)),
          Expanded(child: _Badge(icon: '😊', title: '+10K',   sub: s.unitCustomer)),
          Expanded(child: _Badge(icon: '🏅', title: '4.9/5',  sub: s.unitRating)),
        ]),
      ]),
    );
  }
}

class _Badge extends StatelessWidget {
  const _Badge({required this.icon, required this.title, required this.sub});
  final String icon;
  final String title;
  final String sub;

  @override
  Widget build(BuildContext context) {
    return Column(children: [
      Container(
        width: 48, height: 48,
        decoration: BoxDecoration(
          color: AppColors.blueLight,
          borderRadius: BorderRadius.circular(14),
        ),
        alignment: Alignment.center,
        child: Text(icon, style: const TextStyle(fontSize: 24)),
      ),
      const SizedBox(height: 6),
      Text(title, style: const TextStyle(fontWeight: FontWeight.w900, color: AppColors.blueDark, fontFamily: 'Cairo', fontSize: 14)),
      Text(sub, style: const TextStyle(color: AppColors.gray, fontSize: 10, fontFamily: 'Cairo')),
    ]);
  }
}

// ═══════════════════════════════════════════════
// WHATSAPP FAB
// ═══════════════════════════════════════════════
class _WhatsAppFab extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () async {
        final url = Uri.parse('https://wa.me/970598191312');
        await launchUrl(url, mode: LaunchMode.externalApplication);
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        decoration: BoxDecoration(
          color: const Color(0xFF25D366),
          borderRadius: BorderRadius.circular(30),
          boxShadow: [BoxShadow(
            color: const Color(0xFF25D366).withValues(alpha: 0.40),
            blurRadius: 12, offset: const Offset(0, 4),
          )],
        ),
        child: const Row(mainAxisSize: MainAxisSize.min, children: [
          Text('💬', style: TextStyle(fontSize: 18)),
          SizedBox(width: 6),
          Text('واتساب', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
        ]),
      ),
    );
  }
}
