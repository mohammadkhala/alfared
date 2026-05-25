import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/order.dart';
import '../../providers/locale_provider.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import 'order_detail_screen.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  List<AppOrder> _orders = [];
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final res = await ApiService.instance.get('/orders');
      _orders = (res['data'] as List)
          .map((e) => AppOrder.fromJson(e as Map<String, dynamic>))
          .toList();
    } catch (_) {}
    if (mounted) setState(() => _loading = false);
  }

  Color _statusColor(String status) => switch (status) {
    'pending'    => Colors.amber.shade700,
    'confirmed'  => AppColors.blue,
    'processing' => Colors.deepPurple,
    'shipped'    => AppColors.success,
    'delivered'  => AppColors.success,
    'cancelled'  => AppColors.danger,
    _            => AppColors.gray,
  };

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      appBar: AppBar(title: Text(s.myOrders)),
      body: _loading
        ? const Center(child: CircularProgressIndicator(color: AppColors.orange))
        : _orders.isEmpty
          ? Center(
              child: Text(
                s.noOrders,
                style: const TextStyle(
                  color: AppColors.gray, fontFamily: 'Cairo', fontSize: 14,
                ),
              ),
            )
          : RefreshIndicator(
              color: AppColors.orange,
              onRefresh: _load,
              child: ListView.separated(
                padding: const EdgeInsets.all(14),
                itemCount: _orders.length,
                separatorBuilder: (_, __) => const SizedBox(height: 12),
                itemBuilder: (_, i) {
                  final o = _orders[i];
                  final color = _statusColor(o.status);
                  return GestureDetector(
                    onTap: () => Navigator.of(context).push(MaterialPageRoute(
                      builder: (_) => OrderDetailScreen(orderNumber: o.orderNumber),
                    )),
                    child: Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(AppRadius.lg),
                        boxShadow: AppShadows.card,
                      ),
                      child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
                        Row(children: [
                          // Order icon
                          Container(
                            width: 44, height: 44,
                            decoration: BoxDecoration(
                              color: color.withValues(alpha: 0.12),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            alignment: Alignment.center,
                            child: Icon(Icons.receipt_long_outlined, color: color, size: 22),
                          ),
                          const SizedBox(width: 12),
                          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text(
                              '#${o.orderNumber}',
                              style: const TextStyle(
                                fontWeight: FontWeight.w900, color: AppColors.blueDark,
                                fontFamily: 'Cairo', fontSize: 14,
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              o.createdAtHuman ?? '',
                              style: const TextStyle(
                                color: AppColors.grayLight, fontSize: 11, fontFamily: 'Cairo',
                              ),
                            ),
                          ])),
                          // Status badge
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                            decoration: BoxDecoration(
                              color: color.withValues(alpha: 0.12),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Text(
                              o.statusLabel,
                              style: TextStyle(
                                color: color, fontSize: 11,
                                fontWeight: FontWeight.w800, fontFamily: 'Cairo',
                              ),
                            ),
                          ),
                        ]),
                        const Padding(
                          padding: EdgeInsets.symmetric(vertical: 10),
                          child: Divider(height: 1),
                        ),
                        Row(children: [
                          Text(
                            '${o.itemsCount} ${s.productsLabel.replaceAll('🛍️ ', '')}',
                            style: const TextStyle(
                              color: AppColors.gray, fontFamily: 'Cairo', fontSize: 12,
                            ),
                          ),
                          const Spacer(),
                          Text(
                            '${o.total.toStringAsFixed(2)} ₪',
                            style: const TextStyle(
                              fontSize: 16, fontWeight: FontWeight.w900,
                              color: AppColors.orange, fontFamily: 'Cairo',
                            ),
                          ),
                        ]),
                      ]),
                    ),
                  );
                },
              ),
            ),
    );
  }
}
