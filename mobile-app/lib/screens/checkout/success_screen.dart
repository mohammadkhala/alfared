import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../l10n/app_strings.dart';
import '../../providers/locale_provider.dart';
import '../../theme/app_theme.dart';
import '../home/main_navigation.dart';
import '../orders/order_detail_screen.dart';

class SuccessScreen extends StatelessWidget {
  const SuccessScreen({super.key, required this.orderNumber});
  final String orderNumber;

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      backgroundColor: AppColors.grayBg,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
            // Success icon
            Container(
              width: 100, height: 100,
              decoration: const BoxDecoration(color: AppColors.success, shape: BoxShape.circle),
              child: const Icon(Icons.check_rounded, color: Colors.white, size: 56),
            ),
            const SizedBox(height: 24),

            // Title
            Text(
              s.orderSuccessTitle,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 22, fontWeight: FontWeight.w900,
                color: AppColors.blueDark, fontFamily: 'Cairo', height: 1.4,
              ),
            ),
            const SizedBox(height: 10),

            // Subtitle
            Text(
              s.orderSuccessSub,
              textAlign: TextAlign.center,
              style: const TextStyle(
                color: AppColors.gray, fontFamily: 'Cairo',
                fontSize: 13, height: 1.6,
              ),
            ),
            const SizedBox(height: 32),

            // Order number card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(18),
                border: Border.all(color: AppColors.blue.withValues(alpha: 0.2)),
                boxShadow: AppShadows.card,
              ),
              child: Column(children: [
                Text(
                  s.orderNumber,
                  style: const TextStyle(
                    color: AppColors.gray, fontFamily: 'Cairo', fontSize: 13,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  orderNumber,
                  style: const TextStyle(
                    fontSize: 28, fontWeight: FontWeight.w900,
                    color: AppColors.blue, fontFamily: 'Cairo', letterSpacing: 3,
                  ),
                ),
              ]),
            ),
            const SizedBox(height: 28),

            // Track order button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () => Navigator.of(context).pushReplacement(
                  MaterialPageRoute(
                    builder: (_) => OrderDetailScreen(orderNumber: orderNumber),
                  ),
                ),
                icon: const Icon(Icons.local_shipping_outlined),
                label: Text(s.trackOrder,
                    style: const TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w800)),
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                ),
              ),
            ),
            const SizedBox(height: 12),

            // Back to home
            SizedBox(
              width: double.infinity,
              child: OutlinedButton(
                onPressed: () => Navigator.of(context).pushAndRemoveUntil(
                  MaterialPageRoute(builder: (_) => const MainNavigation()),
                  (_) => false,
                ),
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: AppColors.blue),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text(
                  s.backToHome,
                  style: const TextStyle(
                    color: AppColors.blue, fontFamily: 'Cairo',
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ),
          ]),
        ),
      ),
    );
  }
}
