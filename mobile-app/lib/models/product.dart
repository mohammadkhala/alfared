class VariantValue {
  final int id;
  final String value;
  final String? valueEn;
  final String? colorCode;
  final double priceModifier;
  final int stock;

  const VariantValue({
    required this.id,
    required this.value,
    this.valueEn,
    this.colorCode,
    this.priceModifier = 0,
    this.stock = 0,
  });

  bool get isColor => colorCode != null && colorCode!.isNotEmpty;
  bool get inStock => stock > 0;

  factory VariantValue.fromJson(Map<String, dynamic> j) => VariantValue(
    id:            j['id'] as int,
    value:         j['value'] as String? ?? '',
    valueEn:       j['value_en'] as String?,
    colorCode:     j['color_code'] as String?,
    priceModifier: (j['price_modifier'] as num?)?.toDouble() ?? 0,
    stock:         (j['stock'] as num?)?.toInt() ?? 0,
  );
}

class VariantGroup {
  final String type;
  final String label;
  final List<VariantValue> values;

  const VariantGroup({
    required this.type,
    required this.label,
    required this.values,
  });

  factory VariantGroup.fromJson(Map<String, dynamic> j) => VariantGroup(
    type:   j['type']  as String? ?? '',
    label:  j['label'] as String? ?? '',
    values: (j['values'] as List?)
        ?.map((v) => VariantValue.fromJson(v as Map<String, dynamic>))
        .toList() ?? [],
  );
}

class Product {
  final int    id;
  final String name;     // Arabic (primary)
  final String? nameEn;  // English
  final String? nameHe;  // Hebrew
  final String slug;
  final String? image;
  final double price;
  final double? comparePrice;
  final int?    discountPercent;
  final bool    isOnSale;
  final bool    inStock;
  final double  ratingAvg;
  final int     reviewsCount;
  final String? brandName;
  final String? categoryName;
  final String? shortDescription;
  final int? stockQuantity;

  // Detail-only
  final String? description;
  final List<String> images;
  final List<VariantGroup> variantGroups;
  final List<Map<String, dynamic>> reviews;

  const Product({
    required this.id,
    required this.name,
    this.nameEn,
    this.nameHe,
    required this.slug,
    this.image,
    required this.price,
    this.comparePrice,
    this.discountPercent,
    this.isOnSale = false,
    this.inStock = true,
    this.ratingAvg = 0,
    this.reviewsCount = 0,
    this.brandName,
    this.categoryName,
    this.shortDescription,
    this.stockQuantity,
    this.description,
    this.images = const [],
    this.variantGroups = const [],
    this.reviews = const [],
  });

  bool get hasVariants => variantGroups.isNotEmpty;

  /// Returns the best available name for the given locale code.
  String nameFor(String locale) {
    if (locale == 'en') return (nameEn?.isNotEmpty == true) ? nameEn! : name;
    if (locale == 'he') return (nameHe?.isNotEmpty == true) ? nameHe! : name;
    return name;
  }

  factory Product.fromJson(Map<String, dynamic> j) => Product(
    id:              j['id']             as int,
    name:            j['name']           as String? ?? '',
    nameEn:          j['name_en']        as String?,
    nameHe:          j['name_he']        as String?,
    slug:            j['slug']           as String? ?? '',
    image:           j['image']          as String?,
    price:          (j['price']          as num?)?.toDouble() ?? 0,
    comparePrice:   (j['compare_price']  as num?)?.toDouble(),
    discountPercent: j['discount_percent'] is int ? j['discount_percent'] : null,
    isOnSale:        j['is_on_sale']     as bool? ?? false,
    inStock:         j['in_stock']       as bool? ?? true,
    ratingAvg:      (j['rating_avg']     as num?)?.toDouble() ?? 0,
    reviewsCount:   (j['reviews_count']  as num?)?.toInt() ?? 0,
    brandName:       j['brand']    is Map ? j['brand']['name']    as String? : null,
    categoryName:    j['category'] is Map ? j['category']['name'] as String? : null,
    shortDescription: j['short_description'] as String?,
    stockQuantity:   (j['stock_quantity'] as num?)?.toInt(),
    description:     j['description'] as String?,
    images:          (j['images'] as List?)?.cast<String>() ?? const [],
    variantGroups:   (j['variants'] as List?)
        ?.map((v) => VariantGroup.fromJson(v as Map<String, dynamic>))
        .toList() ?? const [],
    reviews:         (j['reviews']  as List?)?.cast<Map<String, dynamic>>() ?? const [],
  );
}
