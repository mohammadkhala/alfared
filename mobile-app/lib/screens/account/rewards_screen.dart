import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../l10n/app_strings.dart';
import '../../providers/auth_provider.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';

class RewardsScreen extends StatefulWidget {
  const RewardsScreen({super.key});
  @override
  State<RewardsScreen> createState() => _RewardsScreenState();
}

class _RewardsScreenState extends State<RewardsScreen> {
  Map<String, dynamic>? _checkin;
  Map<String, dynamic>? _referral;
  bool _loading = true;
  bool _claiming = false;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final results = await Future.wait([
        ApiService.instance.get('/checkin'),
        ApiService.instance.get('/referral'),
      ]);
      _checkin  = (results[0] as Map).cast<String, dynamic>();
      _referral = (results[1] as Map).cast<String, dynamic>();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _claimCheckin(S s) async {
    setState(() => _claiming = true);
    try {
      final res   = await ApiService.instance.post('/checkin');
      final m     = (res as Map);
      final pts   = m['reward_points'] as int;
      final bonus = m['bonus_points']  as int? ?? 0;
      Fluttertoast.showToast(
        msg: bonus > 0
          ? '🎉 +$pts ${s.pointUnit} (+$bonus ${s.pointUnit} bonus)'
          : '✨ +$pts ${s.pointUnit}',
        backgroundColor: AppColors.success,
        textColor: Colors.white,
        toastLength: Toast.LENGTH_LONG,
      );
      await context.read<AuthProvider>().refreshMe();
      await _load();
    } catch (e) {
      Fluttertoast.showToast(
          msg: e.toString(), backgroundColor: AppColors.danger, textColor: Colors.white);
    } finally {
      if (mounted) setState(() => _claiming = false);
    }
  }

  void _copyCode(S s) {
    final code = _referral?['referral_code'] as String?;
    if (code == null) return;
    Clipboard.setData(ClipboardData(text: code));
    Fluttertoast.showToast(
        msg: s.codeCopied, backgroundColor: AppColors.success, textColor: Colors.white);
  }

  Future<void> _share(S s) async {
    final code = _referral?['referral_code'] as String?;
    if (code == null) return;
    final text = '${s.inviteMsg((_referral?['bonus_points'] as int?) ?? 50)}\n\n${s.appTitle}: $code 🎁';
    final url  = Uri.parse('https://wa.me/?text=${Uri.encodeComponent(text)}');
    await launchUrl(url, mode: LaunchMode.externalApplication);
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    if (_loading) {
      return Scaffold(
        appBar: AppBar(title: Text(s.rewards)),
        body: const Center(child: CircularProgressIndicator(color: AppColors.orange)),
      );
    }

    final done    = _checkin?['already_done'] == true;
    final streak  = _checkin?['streak']       as int? ?? 0;
    final pts     = _checkin?['reward_points'] as int? ?? 5;
    final code    = _referral?['referral_code']  as String? ?? '';
    final bonus   = _referral?['bonus_points']   as int? ?? 50;
    final invited = _referral?['invited_count']  as int? ?? 0;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      appBar: AppBar(title: Text(s.dailyRewards)),
      body: RefreshIndicator(
        color: AppColors.orange,
        onRefresh: _load,
        child: ListView(padding: const EdgeInsets.all(14), children: [

          // ── Daily check-in card ────────────────────────────────
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topLeft, end: Alignment.bottomRight,
                colors: [AppColors.blueDark, AppColors.blue],
              ),
              borderRadius: BorderRadius.circular(22),
            ),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Row(children: [
                const Text('🌟', style: TextStyle(fontSize: 28)),
                const SizedBox(width: 10),
                Text(
                  s.dailyCheckin,
                  style: const TextStyle(
                    color: Colors.white, fontSize: 17,
                    fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                  ),
                ),
              ]),
              const SizedBox(height: 8),
              Text(
                done ? s.checkinDone : s.checkinMsg(pts),
                style: const TextStyle(
                  color: Colors.white70, fontSize: 13, fontFamily: 'Cairo', height: 1.5,
                ),
              ),
              const SizedBox(height: 18),

              // Streak boxes (7 days)
              Row(children: List.generate(7, (i) {
                final filled  = i < (streak % 7 == 0 && streak > 0 ? 7 : streak % 7);
                final isBonus = i == 6;
                return Expanded(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 2),
                    child: Container(
                      height: 40,
                      decoration: BoxDecoration(
                        color: filled
                          ? (isBonus ? AppColors.orange : Colors.white)
                          : Colors.white.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(9),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        isBonus ? '🎁' : '${i + 1}',
                        style: TextStyle(
                          color: filled
                            ? (isBonus ? Colors.white : AppColors.blue)
                            : Colors.white,
                          fontWeight: FontWeight.w900,
                          fontFamily: 'Cairo', fontSize: 14,
                        ),
                      ),
                    ),
                  ),
                );
              })),
              const SizedBox(height: 12),
              Text(
                s.currentStreak(streak),
                style: const TextStyle(
                  color: Colors.white70, fontSize: 12, fontFamily: 'Cairo',
                ),
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: done || _claiming ? null : () => _claimCheckin(s),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: done ? Colors.white24 : AppColors.orange,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 13),
                  ),
                  child: _claiming
                    ? const SizedBox(width: 22, height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : Text(
                        done ? s.claimedCheckin : s.claimCheckin,
                        style: const TextStyle(
                          fontFamily: 'Cairo', fontWeight: FontWeight.w800, fontSize: 14,
                        ),
                      ),
                ),
              ),
            ]),
          ),

          const SizedBox(height: 14),

          // ── Referral card ──────────────────────────────────────
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topLeft, end: Alignment.bottomRight,
                colors: [AppColors.orange, AppColors.orangeDark],
              ),
              borderRadius: BorderRadius.circular(22),
            ),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Row(children: [
                const Text('🎁', style: TextStyle(fontSize: 28)),
                const SizedBox(width: 10),
                Expanded(
                  child: Text(
                    s.inviteFriend,
                    style: const TextStyle(
                      color: Colors.white, fontSize: 17,
                      fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                    ),
                  ),
                ),
              ]),
              const SizedBox(height: 8),
              Text(
                s.inviteMsg(bonus),
                style: const TextStyle(
                  color: Colors.white70, fontSize: 13, fontFamily: 'Cairo', height: 1.5,
                ),
              ),
              const SizedBox(height: 18),

              // Referral code box
              GestureDetector(
                onTap: () => _copyCode(s),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Row(children: [
                    Expanded(
                      child: Text(
                        code,
                        style: const TextStyle(
                          color: AppColors.orangeDark, fontSize: 22,
                          fontWeight: FontWeight.w900, fontFamily: 'Cairo', letterSpacing: 4,
                        ),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppColors.orangeLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.copy_rounded, color: AppColors.orangeDark, size: 18),
                    ),
                  ]),
                ),
              ),
              const SizedBox(height: 14),

              Row(children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () => _share(s),
                    icon: const Icon(Icons.share_rounded, size: 18),
                    label: Text(s.share,
                        style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: AppColors.orangeDark,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
                  decoration: BoxDecoration(
                    color: Colors.black.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Column(children: [
                    Text(
                      '$invited',
                      style: const TextStyle(
                        color: Colors.white, fontSize: 20,
                        fontWeight: FontWeight.w900, fontFamily: 'Cairo',
                      ),
                    ),
                    Text(
                      s.invitation,
                      style: const TextStyle(color: Colors.white70, fontSize: 10, fontFamily: 'Cairo'),
                    ),
                  ]),
                ),
              ]),
            ]),
          ),
        ]),
      ),
    );
  }
}
