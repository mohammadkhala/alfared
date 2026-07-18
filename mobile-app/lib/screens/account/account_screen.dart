import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../l10n/app_strings.dart';
import '../../providers/auth_provider.dart';
import '../../providers/locale_provider.dart';
import '../../providers/theme_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../../theme/theme_ext.dart';
import '../../widgets/brand_icon.dart';
import '../../widgets/guest_wall.dart';
import '../notifications_screen.dart';
import '../orders/orders_screen.dart';
import 'addresses_screen.dart';
import 'loyalty_screen.dart';
import 'rewards_screen.dart';
import 'wishlist_screen.dart';

class AccountScreen extends StatefulWidget {
  const AccountScreen({super.key});

  @override
  State<AccountScreen> createState() => _AccountScreenState();
}

class _AccountScreenState extends State<AccountScreen> {
  int _ordersCount = 0;
  int _wishlistCount = 0;
  Map<String, dynamic>? _loyalty;
  String _version = '';

  @override
  void initState() {
    super.initState();
    _fetchStats();
    _loadVersion();
  }

  /// Reads the version from the build itself instead of a hard-coded string,
  /// so it stays correct after every release.
  Future<void> _loadVersion() async {
    try {
      final info = await PackageInfo.fromPlatform();
      if (mounted) {
        setState(() => _version = 'الإصدار ${info.version} (${info.buildNumber})');
      }
    } catch (_) {
      // Plugin unavailable — leave the line empty rather than showing a lie.
    }
  }

  Future<void> _fetchStats() async {
    if (!context.read<AuthProvider>().isLoggedIn) return;
    try {
      final results = await Future.wait([
        ApiService.instance.get('/orders'),
        ApiService.instance.get('/wishlist'),
        ApiService.instance.get('/loyalty'),
      ]);
      _ordersCount  = ((results[0] as Map)['total'] as num?)?.toInt() ?? ((results[0] as Map)['data'] as List).length;
      _wishlistCount = ((results[1] as Map)['items'] as List).length;
      _loyalty       = (results[2] as Map).cast<String, dynamic>();
      if (mounted) setState(() {});
    } catch (_) {}
  }

  Future<void> _showDeleteAccountDialog(BuildContext context, S s) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text(s.deleteAccountTitle,
          style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w900,
              fontSize: 16, color: AppColors.danger)),
        content: Text(s.deleteAccountMsg,
          style: const TextStyle(fontFamily: 'Cairo', fontSize: 13, color: AppColors.text, height: 1.6)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text(s.cancel, style: const TextStyle(fontFamily: 'Cairo', color: AppColors.gray)),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.danger,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            child: Text(s.deleteAccountConfirm,
              style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800, fontSize: 12)),
          ),
        ],
      ),
    );
    if (confirmed != true || !mounted) return;

    try {
      await ApiService.instance.delete('/auth/account');
      if (!mounted) return;
      await context.read<AuthProvider>().logout();
      Fluttertoast.showToast(
        msg: s.deleteAccountDone,
        backgroundColor: AppColors.success,
        textColor: Colors.white,
      );
    } catch (_) {
      Fluttertoast.showToast(
        msg: s.deleteAccountFail,
        backgroundColor: AppColors.danger,
        textColor: Colors.white,
      );
    }
  }

  void _showEditProfile(BuildContext context, user) {
    final s         = context.read<LocaleProvider>().s;
    final nameCtrl  = TextEditingController(text: user.name);
    final emailCtrl = TextEditingController(text: user.email);
    final phoneCtrl = TextEditingController(text: user.phone ?? '');
    final formKey   = GlobalKey<FormState>();
    bool saving = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => StatefulBuilder(
        builder: (ctx, setModal) => Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Container(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 28),
            decoration: const BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
            ),
            child: Form(
              key: formKey,
              child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
                Row(children: [
                  Text(s.editProfile, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.blueDark)),
                  const Spacer(),
                  GestureDetector(onTap: () => Navigator.pop(ctx), child: const Icon(Icons.close, color: AppColors.gray)),
                ]),
                const SizedBox(height: 16),
                TextFormField(
                  controller: nameCtrl,
                  decoration: InputDecoration(labelText: s.fullName, prefixIcon: const Icon(Icons.person_outline)),
                  validator: (v) => (v == null || v.trim().isEmpty) ? s.nameRequired : null,
                ),
                const SizedBox(height: 10),
                TextFormField(
                  controller: emailCtrl,
                  keyboardType: TextInputType.emailAddress,
                  decoration: InputDecoration(labelText: s.emailField, prefixIcon: const Icon(Icons.email_outlined)),
                  validator: (v) => (v == null || !v.contains('@')) ? s.emailInvalid : null,
                ),
                const SizedBox(height: 10),
                TextFormField(
                  controller: phoneCtrl,
                  keyboardType: TextInputType.phone,
                  decoration: InputDecoration(labelText: s.phoneOptional, prefixIcon: const Icon(Icons.phone_outlined)),
                ),
                const SizedBox(height: 20),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: saving ? null : () async {
                      if (!formKey.currentState!.validate()) return;
                      setModal(() => saving = true);
                      try {
                        await ApiService.instance.put('/auth/profile', data: {
                          'name':  nameCtrl.text.trim(),
                          'email': emailCtrl.text.trim(),
                          if (phoneCtrl.text.trim().isNotEmpty) 'phone': phoneCtrl.text.trim(),
                        });
                        await context.read<AuthProvider>().refreshMe();
                        if (ctx.mounted) Navigator.pop(ctx);
                        Fluttertoast.showToast(msg: s.changesSaved, backgroundColor: AppColors.success, textColor: Colors.white);
                      } catch (e) {
                        Fluttertoast.showToast(msg: s.saveFailed, backgroundColor: AppColors.danger, textColor: Colors.white);
                      } finally {
                        if (ctx.mounted) setModal(() => saving = false);
                      }
                    },
                    child: saving
                      ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                      : Text(s.saveChanges, style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800)),
                  ),
                ),
              ]),
            ),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final s    = context.watch<LocaleProvider>().s;

    if (!auth.isLoggedIn) return const GuestWall(icon: Icons.person_outline_rounded);

    final u = auth.user!;
    final balance = _loyalty?['balance'] as int? ?? u.loyaltyPoints;
    final valueIls = (_loyalty?['value_ils'] as num?)?.toDouble() ?? (balance / 20);

    // Tier data from user profile
    final tierEmoji    = u.tierEmoji;
    final tierLabel    = u.tierLabel;
    final tierProgress = u.tierProgress;
    final nextLabel    = u.nextTierLabel;
    final nextEmoji    = u.nextTierEmoji;
    final amountToNext = u.amountToNext;
    final totalSpent   = u.totalSpent;
    final nextMin      = u.nextTierMin;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: RefreshIndicator(
        color: AppColors.orange,
        onRefresh: () async { await Future.wait([auth.refreshMe(), _fetchStats()]); },
        child: ListView(padding: const EdgeInsets.only(bottom: 100), children: [
          // ── Hero ──
          Container(
            padding: EdgeInsets.fromLTRB(16, MediaQuery.of(context).padding.top + 16, 16, 20),
            decoration: const BoxDecoration(
              gradient: LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: [AppColors.blueDark, AppColors.blue]),
              borderRadius: BorderRadius.only(bottomLeft: Radius.circular(28), bottomRight: Radius.circular(28)),
            ),
            child: Column(children: [
              Row(children: [
                Container(
                  width: 58, height: 58,
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(18),
                    boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.25), blurRadius: 16, offset: const Offset(0, 6))]),
                  alignment: Alignment.center,
                  child: Text(u.name.isNotEmpty ? u.name[0].toUpperCase() : 'A',
                    style: const TextStyle(color: AppColors.orange, fontSize: 26, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
                ),
                const SizedBox(width: 12),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(u.name, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
                  Text(u.email, style: const TextStyle(color: Colors.white60, fontSize: 11, fontFamily: 'Cairo')),
                  const SizedBox(height: 4),
                  _TierBadge(tierKey: u.tierKey, emoji: tierEmoji, label: tierLabel),
                ])),
                GestureDetector(
                  onTap: () => _showEditProfile(context, u),
                  child: Container(
                    width: 32, height: 32,
                    decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.edit_outlined, color: Colors.white, size: 14),
                  ),
                ),
              ]),
              const SizedBox(height: 16),
              Container(
                decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(16)),
                child: Row(children: [
                  _StatCell(value: '$_ordersCount',   label: s.ordersCountLabel),
                  _StatDivider(),
                  _StatCell(value: '$_wishlistCount', label: s.wishlistLabel),
                  _StatDivider(),
                  _StatCell(value: '$balance',        label: s.pointsShortLabel),
                ]),
              ),
            ]),
          ),

          // ── Tier progress card ──
          _TierProgressCard(
            tierEmoji: tierEmoji,
            tierLabel: tierLabel,
            tierKey: u.tierKey,
            tierProgress: tierProgress,
            totalSpent: totalSpent,
            nextLabel: nextLabel,
            nextEmoji: nextEmoji,
            nextMin: nextMin,
            amountToNext: amountToNext,
            s: s,
          ),

          // ── Loyalty card ──
          Container(
            margin: const EdgeInsets.fromLTRB(14, 10, 14, 0),
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              gradient: const LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: [AppColors.orange, AppColors.orangeDark]),
              borderRadius: BorderRadius.circular(AppRadius.xl),
            ),
            child: Row(children: [
              const Text('🏆', style: TextStyle(fontSize: 22)),
              const SizedBox(width: 10),
              Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(s.loyaltyPointsCard, style: const TextStyle(color: Colors.white70, fontSize: 10, fontFamily: 'Cairo')),
                Text('$balance ${s.pointsShortLabel}', style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
              ]),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                decoration: BoxDecoration(color: Colors.black.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(10)),
                child: Text('= ${valueIls.toStringAsFixed(0)} ₪', style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
              ),
            ]),
          ),

          // ── Menu groups ──
          const SizedBox(height: 12),
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 14),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.xl), boxShadow: AppShadows.card),
            child: Column(children: [
              _MenuItem(icon: '📦', iconBg: AppColors.blueLight, label: s.myOrders, badge: _ordersCount > 0 ? '$_ordersCount' : null,
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const OrdersScreen()))),
              _MenuDivider(),
              _MenuItem(icon: '❤️', iconBg: AppColors.orangeLight, label: s.myWishlist,
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const WishlistScreen()))),
              _MenuDivider(),
              _MenuItem(icon: '⭐', iconBg: AppColors.successLight, label: s.loyalty,
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const LoyaltyScreen()))),
              _MenuDivider(),
              _MenuItem(icon: '🎁', iconBg: AppColors.orangeLight, label: s.dailyRewards,
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const RewardsScreen()))),
              _MenuDivider(),
              _MenuItem(icon: '📍', iconBg: AppColors.blueLight, label: s.myAddresses,
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const AddressesScreen()))),
              _MenuDivider(),
              _MenuItem(icon: '🔔', iconBg: AppColors.dangerLight, label: s.notifications,
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const NotificationsScreen()))),
              _MenuDivider(),
              _DarkModeToggle(s: s),
            ]),
          ),

          // ── Social media ──
          const SizedBox(height: 12),
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 14),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.xl), boxShadow: AppShadows.card),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(s.followUs, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, fontFamily: 'Cairo', color: AppColors.text)),
              const SizedBox(height: 12),
              Builder(builder: (context) {
                const wa = Color(0xFF25D366);
                const ig = Color(0xFFE1306C);
                const fb = Color(0xFF1877F2);
                // TikTok's black is invisible on a dark page — use the text colour.
                final tt = context.isDark ? AppColors.darkText : Colors.black87;

                return Row(children: [
                  _SocialBtn(
                    icon: const BrandIcon.whatsapp(size: 22, color: wa),
                    label: 'WhatsApp', color: wa, url: 'https://wa.me/970598191312'),
                  const SizedBox(width: 10),
                  _SocialBtn(
                    icon: const BrandIcon.instagram(size: 22, color: ig),
                    label: 'Instagram', color: ig, url: 'https://instagram.com/alfared.shop'),
                  const SizedBox(width: 10),
                  _SocialBtn(
                    icon: const BrandIcon.facebook(size: 22, color: fb),
                    label: 'Facebook', color: fb, url: 'https://facebook.com/alfared.shop'),
                  const SizedBox(width: 10),
                  _SocialBtn(
                    icon: BrandIcon.tiktok(size: 22, color: tt),
                    label: 'TikTok', color: tt, url: 'https://tiktok.com/@alfared.shop'),
                ]);
              }),
            ]),
          ),

          // ── Legal ──
          const SizedBox(height: 12),
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 14),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.xl), boxShadow: AppShadows.card),
            child: Column(children: [
              _MenuItem(
                icon: '🔒',
                iconBg: const Color(0xFFE8F0FF),
                label: 'سياسة الخصوصية',
                onTap: () async {
                  final url = Uri.parse('https://alfared.ps/privacy-policy');
                  await launchUrl(url, mode: LaunchMode.externalApplication);
                },
              ),
              _MenuDivider(),
              _MenuItem(
                icon: '📜',
                iconBg: const Color(0xFFE8F0FF),
                label: 'شروط الاستخدام',
                onTap: () async {
                  final url = Uri.parse('https://alfared.ps/terms');
                  await launchUrl(url, mode: LaunchMode.externalApplication);
                },
              ),
            ]),
          ),

          // ── Support / Logout ──
          const SizedBox(height: 12),
          Container(
            margin: const EdgeInsets.symmetric(horizontal: 14),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(AppRadius.xl), boxShadow: AppShadows.card),
            child: Column(children: [
              _MenuItem(icon: '💬', iconBg: AppColors.successLight, label: s.contactWhatsapp, onTap: () async {
                final url = Uri.parse('https://wa.me/970598191312');
                await launchUrl(url, mode: LaunchMode.externalApplication);
              }),
              _MenuDivider(),
              _MenuItem(icon: '🚪', iconBg: AppColors.dangerLight, label: s.logout, labelColor: AppColors.danger,
                onTap: () async { await context.read<AuthProvider>().logout(); }),
              _MenuDivider(),
              _MenuItem(
                icon: '🗑️', iconBg: AppColors.dangerLight, label: s.deleteAccount, labelColor: AppColors.danger,
                onTap: () => _showDeleteAccountDialog(context, s),
              ),
            ]),
          ),

          const SizedBox(height: 14),

          // ── Developer credit ────────────────────────────────
          Center(
            child: GestureDetector(
              onTap: () => launchUrl(
                Uri.parse('https://wa.me/972594513978'),
                mode: LaunchMode.externalApplication,
              ),
              behavior: HitTestBehavior.opaque,
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 18),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppColors.border),
                ),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  const Text('صُنع بواسطة ',
                    style: TextStyle(color: AppColors.gray, fontFamily: 'Cairo', fontSize: 12)),
                  const Text('mohammad khallaf',
                    style: TextStyle(
                      color: AppColors.orange, fontFamily: 'Cairo',
                      fontSize: 13, fontWeight: FontWeight.w900,
                    )),
                  const SizedBox(width: 6),
                  Icon(Icons.chat_bubble, size: 13, color: AppColors.success.withValues(alpha: 0.9)),
                ]),
              ),
            ),
          ),

          const SizedBox(height: 10),
          // Real build number, so it never drifts from pubspec like the old
          // hard-coded string did.
          Center(child: Text(_version,
            style: const TextStyle(color: AppColors.grayLight, fontFamily: 'Cairo', fontSize: 11))),
          const SizedBox(height: 10),
        ]),
      ),
    );
  }
}

class _StatCell extends StatelessWidget {
  const _StatCell({required this.value, required this.label});
  final String value;
  final String label;
  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 12),
        child: Column(children: [
          Text(value, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
          Text(label, style: const TextStyle(color: Colors.white60, fontSize: 9, fontFamily: 'Cairo')),
        ]),
      ),
    );
  }
}

class _StatDivider extends StatelessWidget {
  @override
  Widget build(BuildContext context) => Container(width: 1, height: 28, color: Colors.white.withValues(alpha: 0.12));
}

class _MenuItem extends StatelessWidget {
  const _MenuItem({required this.icon, required this.iconBg, required this.label, this.badge, this.labelColor, required this.onTap});
  final String icon;
  final Color iconBg;
  final String label;
  final String? badge;
  final Color? labelColor;
  final VoidCallback onTap;
  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
        child: Row(children: [
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(color: iconBg, borderRadius: BorderRadius.circular(12)),
            alignment: Alignment.center,
            child: Text(icon, style: const TextStyle(fontSize: 16)),
          ),
          const SizedBox(width: 12),
          Expanded(child: Text(label, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, fontFamily: 'Cairo', color: labelColor ?? AppColors.text))),
          if (badge != null)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
              decoration: BoxDecoration(color: AppColors.orange, borderRadius: BorderRadius.circular(10)),
              child: Text(badge!, style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
            ),
          const SizedBox(width: 6),
          const Icon(Icons.chevron_left, color: AppColors.border, size: 18),
        ]),
      ),
    );
  }
}

class _MenuDivider extends StatelessWidget {
  @override
  Widget build(BuildContext context) => Container(height: 1, color: const Color(0xFFF9FAFB));
}

class _DarkModeToggle extends StatelessWidget {
  const _DarkModeToggle({required this.s});
  final S s;
  @override
  Widget build(BuildContext context) {
    final theme = context.watch<ThemeProvider>();
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
      child: Row(children: [
        Container(
          width: 36, height: 36,
          decoration: BoxDecoration(color: AppColors.blueLight, borderRadius: BorderRadius.circular(12)),
          alignment: Alignment.center,
          child: Text(theme.isDark ? '🌙' : '☀️', style: const TextStyle(fontSize: 16)),
        ),
        const SizedBox(width: 12),
        Expanded(child: Text(s.darkMode, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800, fontFamily: 'Cairo'))),
        Switch(
          value: theme.isDark,
          activeColor: AppColors.orange,
          onChanged: (_) => theme.toggle(),
        ),
      ]),
    );
  }
}

// ── Tier badge ────────────────────────────────────────────────────────────────

class _TierBadge extends StatelessWidget {
  const _TierBadge({required this.tierKey, required this.emoji, required this.label});
  final String tierKey;
  final String emoji;
  final String label;

  Color get _color => switch (tierKey) {
    'bronze' => const Color(0xFFCD7F32),
    'silver' => const Color(0xFF64B5F6),
    'gold'   => const Color(0xFFFFB300),
    'vip'    => AppColors.orange,
    _        => Colors.white24,
  };

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
      decoration: BoxDecoration(color: _color, borderRadius: BorderRadius.circular(20)),
      child: Text('$emoji $label',
        style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
    );
  }
}

// ── Tier progress card ────────────────────────────────────────────────────────

class _TierProgressCard extends StatelessWidget {
  const _TierProgressCard({
    required this.tierEmoji,
    required this.tierLabel,
    required this.tierKey,
    required this.tierProgress,
    required this.totalSpent,
    required this.nextLabel,
    required this.nextEmoji,
    required this.nextMin,
    required this.amountToNext,
    required this.s,
  });

  final String tierEmoji;
  final String tierLabel;
  final String tierKey;
  final double tierProgress;
  final double totalSpent;
  final String? nextLabel;
  final String? nextEmoji;
  final double? nextMin;
  final double amountToNext;
  final S s;

  Color get _barColor => switch (tierKey) {
    'bronze' => const Color(0xFFCD7F32),
    'silver' => const Color(0xFF64B5F6),
    'gold'   => const Color(0xFFFFB300),
    'vip'    => AppColors.orange,
    _        => AppColors.blue,
  };

  @override
  Widget build(BuildContext context) {
    final isMax = nextLabel == null;
    return Container(
      margin: const EdgeInsets.fromLTRB(14, 12, 14, 0),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(AppRadius.xl),
        boxShadow: AppShadows.card,
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Text(tierEmoji, style: const TextStyle(fontSize: 22)),
          const SizedBox(width: 8),
          Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(s.currentLevel, style: const TextStyle(color: AppColors.gray, fontSize: 10, fontFamily: 'Cairo')),
            Text(tierLabel,
              style: TextStyle(color: _barColor, fontSize: 15, fontWeight: FontWeight.w900, fontFamily: 'Cairo')),
          ]),
          const Spacer(),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: _barColor.withValues(alpha: 0.10),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text('${totalSpent.toStringAsFixed(0)} ₪ ${s.spendingLabel}',
              style: TextStyle(color: _barColor, fontSize: 10, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
          ),
        ]),
        const SizedBox(height: 12),
        // Progress bar
        Stack(children: [
          Container(
            height: 8,
            decoration: BoxDecoration(
              color: const Color(0xFFF1F5F9),
              borderRadius: BorderRadius.circular(6),
            ),
          ),
          FractionallySizedBox(
            widthFactor: isMax ? 1.0 : tierProgress.clamp(0.0, 1.0),
            child: Container(
              height: 8,
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: [_barColor.withValues(alpha: 0.7), _barColor]),
                borderRadius: BorderRadius.circular(6),
              ),
            ),
          ),
        ]),
        const SizedBox(height: 8),
        if (isMax)
          Row(children: [
            Text('$tierEmoji ${s.topTierMsg}',
              style: TextStyle(color: _barColor, fontSize: 10, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
          ])
        else
          Row(children: [
            Text(s.remainingAmount(amountToNext.toStringAsFixed(0)),
              style: const TextStyle(color: AppColors.gray, fontSize: 9, fontFamily: 'Cairo')),
            const SizedBox(width: 4),
            Text(s.toReachTier(nextEmoji ?? '', nextLabel ?? ''),
              style: TextStyle(color: _barColor, fontSize: 9, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
            const Spacer(),
            Text('${(tierProgress * 100).round()}%',
              style: const TextStyle(color: AppColors.gray, fontSize: 9, fontFamily: 'Cairo')),
          ]),
        // Tier roadmap
        const SizedBox(height: 12),
        const Divider(height: 1, color: Color(0xFFF1F5F9)),
        const SizedBox(height: 10),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          _TierStep(emoji: '🎯', label: s.newTierLabel,    min: '0',    active: tierKey == 'new'),
          _TierArrow(),
          _TierStep(emoji: '🥉', label: s.bronzeTierLabel, min: '200',  active: tierKey == 'bronze'),
          _TierArrow(),
          _TierStep(emoji: '🥈', label: s.silverTierLabel, min: '500',  active: tierKey == 'silver'),
          _TierArrow(),
          _TierStep(emoji: '🥇', label: s.goldTierLabel,   min: '1000', active: tierKey == 'gold'),
          _TierArrow(),
          _TierStep(emoji: '💎', label: 'VIP',             min: '2000', active: tierKey == 'vip'),
        ]),
      ]),
    );
  }
}

class _TierStep extends StatelessWidget {
  const _TierStep({required this.emoji, required this.label, required this.min, required this.active});
  final String emoji;
  final String label;
  final String min;
  final bool active;

  @override
  Widget build(BuildContext context) {
    return Column(children: [
      Text(emoji, style: TextStyle(fontSize: active ? 20 : 16)),
      Text(label,
        style: TextStyle(
          fontSize: 8,
          fontFamily: 'Cairo',
          fontWeight: active ? FontWeight.w900 : FontWeight.w600,
          color: active ? AppColors.blue : AppColors.gray,
        )),
      Text('$min ₪',
        style: const TextStyle(fontSize: 7, fontFamily: 'Cairo', color: AppColors.grayLight)),
    ]);
  }
}

class _TierArrow extends StatelessWidget {
  @override
  Widget build(BuildContext context) => const Icon(Icons.chevron_right_rounded, size: 14, color: AppColors.grayLight);
}

class _SocialBtn extends StatelessWidget {
  const _SocialBtn({required this.icon, required this.label, required this.color, required this.url});
  final Widget icon;
  final String label;
  final Color color;
  final String url;

  @override
  Widget build(BuildContext context) {
    // On dark backgrounds a 10% brand tint is nearly invisible, so lift it.
    final fill   = color.withValues(alpha: context.isDark ? 0.20 : 0.10);
    final stroke = color.withValues(alpha: context.isDark ? 0.45 : 0.25);

    return Expanded(
      child: GestureDetector(
        onTap: () async {
          final uri = Uri.parse(url);
          await launchUrl(uri, mode: LaunchMode.externalApplication);
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: fill,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: stroke),
          ),
          child: Column(children: [
            icon,
            const SizedBox(height: 6),
            Text(label, style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, fontFamily: 'Cairo', color: color)),
          ]),
        ),
      ),
    );
  }
}

