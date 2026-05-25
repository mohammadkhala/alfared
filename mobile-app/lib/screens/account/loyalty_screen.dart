import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';

class LoyaltyScreen extends StatefulWidget {
  const LoyaltyScreen({super.key});

  @override
  State<LoyaltyScreen> createState() => _LoyaltyScreenState();
}

class _LoyaltyScreenState extends State<LoyaltyScreen> {
  Map<String, dynamic>? _data;
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      _data = (await ApiService.instance.get('/loyalty') as Map).cast<String, dynamic>();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      appBar: AppBar(title: Text(s.loyalty)),
      backgroundColor: AppColors.grayBg,
      body: _loading
        ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
        : _data == null
          ? Center(child: Text(s.loadError, style: const TextStyle(fontFamily: 'Cairo')))
          : RefreshIndicator(
              color: AppColors.orange,
              onRefresh: _load,
              child: ListView(padding: const EdgeInsets.all(16), children: [

                // ── Balance card ──
                Container(
                  padding: const EdgeInsets.all(28),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(colors: [AppColors.blueDark, AppColors.blue]),
                    borderRadius: BorderRadius.circular(22),
                  ),
                  child: Column(children: [
                    Text(
                      s.yourBalance,
                      style: const TextStyle(
                        color: Colors.white70, fontFamily: 'Cairo', fontSize: 14,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      '${_data!['balance']}',
                      style: const TextStyle(
                        fontSize: 56, fontWeight: FontWeight.w900,
                        color: Colors.white, fontFamily: 'Cairo',
                      ),
                    ),
                    Text(
                      s.pointUnit,
                      style: const TextStyle(
                        color: Colors.white70, fontFamily: 'Cairo', fontSize: 14,
                      ),
                    ),
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: Text(
                        s.equivalentIls((_data!['value_ils'] as num).toStringAsFixed(2)),
                        style: const TextStyle(
                          color: Color(0xFFFCD34D), fontFamily: 'Cairo',
                          fontWeight: FontWeight.w800, fontSize: 15,
                        ),
                      ),
                    ),
                  ]),
                ),
                const SizedBox(height: 16),

                // ── How to earn ──
                _card(
                  icon: '💰',
                  title: s.howToEarn,
                  body: s.earnExplain(
                    _data!['config']['earn_amount_per_point'].toString(),
                  ),
                ),

                // ── How to redeem ──
                _card(
                  icon: '🎁',
                  title: s.howToRedeem,
                  body: s.redeemExplain(
                    _data!['config']['points_per_redeem_ils'].toString(),
                    _data!['config']['min_redeem_points'].toString(),
                  ),
                ),

                const SizedBox(height: 6),

                // ── History header ──
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  child: Text(
                    s.pointsHistory,
                    style: const TextStyle(
                      fontWeight: FontWeight.w900, color: AppColors.blue,
                      fontFamily: 'Cairo', fontSize: 16,
                    ),
                  ),
                ),

                // ── History list ──
                ...(_data!['history'] as List).map((tx) {
                  final pts     = (tx['points'] as num).toInt();
                  final positive = pts >= 0;
                  return Container(
                    margin: const EdgeInsets.only(bottom: 10),
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(AppRadius.lg),
                      boxShadow: AppShadows.card,
                    ),
                    child: Row(children: [
                      Container(
                        width: 42, height: 42,
                        decoration: BoxDecoration(
                          color: positive
                              ? AppColors.successLight
                              : AppColors.dangerLight,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        alignment: Alignment.center,
                        child: Icon(
                          positive ? Icons.add_circle_outline : Icons.remove_circle_outline,
                          color: positive ? AppColors.success : AppColors.danger,
                          size: 22,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text(
                          tx['action_label'] as String? ?? '',
                          style: const TextStyle(
                            fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                            fontSize: 13, color: AppColors.text,
                          ),
                        ),
                        const SizedBox(height: 3),
                        Text(
                          tx['date_human'] as String? ?? '',
                          style: const TextStyle(
                            color: AppColors.grayLight, fontSize: 11, fontFamily: 'Cairo',
                          ),
                        ),
                      ])),
                      Text(
                        '${positive ? '+' : ''}$pts',
                        style: TextStyle(
                          color: positive ? AppColors.success : AppColors.danger,
                          fontWeight: FontWeight.w900, fontFamily: 'Cairo', fontSize: 16,
                        ),
                      ),
                    ]),
                  );
                }),
              ]),
            ),
    );
  }

  Widget _card({required String icon, required String title, required String body}) =>
      Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(AppRadius.lg),
          boxShadow: AppShadows.card,
        ),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(icon, style: const TextStyle(fontSize: 24)),
          const SizedBox(width: 12),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(title, style: const TextStyle(
              fontWeight: FontWeight.w900, color: AppColors.blue,
              fontFamily: 'Cairo', fontSize: 13,
            )),
            const SizedBox(height: 6),
            Text(body, style: const TextStyle(
              height: 1.7, fontFamily: 'Cairo', fontSize: 12, color: AppColors.text,
            )),
          ])),
        ]),
      );
}
