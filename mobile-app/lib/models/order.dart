import '../utils/image_helper.dart';

class AppOrder {
  final String orderNumber;
  final String status;
  final String statusLabel;
  final double subtotal;
  final double deliveryFee;
  final double discountAmount;
  final double loyaltyDiscount;
  final double total;
  final DateTime? createdAt;
  final String? createdAtHuman;
  final int itemsCount;

  // Detail-only
  final String? customerName;
  final String? customerPhone;
  final String? city;
  final String? area;
  final String? addressLine;
  final String? building;
  final String? paymentMethod;
  final List<OrderItem> items;
  final DateTime? returnRequestedAt;

  AppOrder({
    required this.orderNumber,
    required this.status,
    required this.statusLabel,
    required this.subtotal,
    required this.deliveryFee,
    required this.discountAmount,
    required this.loyaltyDiscount,
    required this.total,
    this.createdAt,
    this.createdAtHuman,
    this.itemsCount = 0,
    this.customerName,
    this.customerPhone,
    this.city,
    this.area,
    this.addressLine,
    this.building,
    this.paymentMethod,
    this.items = const [],
    this.returnRequestedAt,
  });

  factory AppOrder.fromJson(Map<String, dynamic> j) => AppOrder(
    orderNumber:    j['order_number']    as String? ?? '',
    status:         j['status']          as String? ?? 'pending',
    statusLabel:    j['status_label']    as String? ?? '',
    subtotal:      (j['subtotal']        as num?)?.toDouble() ?? 0,
    deliveryFee:   (j['delivery_fee']    as num?)?.toDouble() ?? 0,
    discountAmount:(j['discount_amount'] as num?)?.toDouble() ?? 0,
    loyaltyDiscount:(j['loyalty_discount'] as num?)?.toDouble() ?? 0,
    total:         (j['total']           as num?)?.toDouble() ?? 0,
    createdAt:      j['created_at'] != null ? DateTime.tryParse(j['created_at']) : null,
    createdAtHuman: j['created_at_human'] as String?,
    itemsCount:    (j['items_count']    as num?)?.toInt() ?? 0,
    customerName:   j['customer_name']  as String?,
    customerPhone:  j['customer_phone'] as String?,
    city:           j['city']           as String?,
    area:           j['area']           as String?,
    addressLine:    j['address_line']   as String?,
    building:       j['building']       as String?,
    paymentMethod:      j['payment_method']       as String?,
    returnRequestedAt:  j['return_requested_at'] != null
        ? DateTime.tryParse(j['return_requested_at']) : null,
    items: ((j['items'] as List?) ?? const [])
        .map((e) => OrderItem.fromJson(e as Map<String, dynamic>))
        .toList(),
  );
}

class OrderItem {
  final int id;
  final int productId;
  final String productName;
  final String? productImage;
  final double price;
  final int quantity;
  final double total;
  final String? variantInfo;

  OrderItem({
    required this.id,
    required this.productId,
    required this.productName,
    this.productImage,
    required this.price,
    required this.quantity,
    required this.total,
    this.variantInfo,
  });

  factory OrderItem.fromJson(Map<String, dynamic> j) => OrderItem(
    id:           _toInt(j['id']),
    productId:    _toInt(j['product_id']),
    productName:  j['product_name'] as String? ?? '',
    productImage: ImageHelper.cleanUrl(j['product_image'] as String?),
    price:       (j['price']        as num?)?.toDouble() ?? (double.tryParse(j['price'].toString()) ?? 0),
    quantity:    (j['quantity']     as num?)?.toInt() ?? 1,
    total:       (j['total']        as num?)?.toDouble() ?? (double.tryParse(j['total'].toString()) ?? 0),
    variantInfo:  j['variant_info'] as String?,
  );

  static int _toInt(dynamic v) {
    if (v is int) return v;
    if (v is num) return v.toInt();
    return int.tryParse(v?.toString() ?? '0') ?? 0;
  }
}
