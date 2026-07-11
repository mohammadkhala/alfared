import '../utils/image_helper.dart';

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

  /// Strips HTML tags and decodes common HTML entities so the description
  /// renders as clean plain text (API may return raw HTML from the editor).
  static String? _cleanHtml(String? html) {
    if (html == null) return null;
    var s = html
        // Block/line-break tags → newlines
        .replaceAll(RegExp(r'<\s*br\s*/?>', caseSensitive: false), '\n')
        .replaceAll(RegExp(r'</\s*(p|div|li|h[1-6])\s*>', caseSensitive: false), '\n')
        // Remove all remaining tags
        .replaceAll(RegExp(r'<[^>]+>'), '');
    // Decode common HTML entities
    const entities = {
      '&nbsp;': ' ', '&amp;': '&', '&lt;': '<', '&gt;': '>',
      '&quot;': '"', '&#39;': "'", '&apos;': "'", '&hellip;': '…',
      '&mdash;': '—', '&ndash;': '–', '&laquo;': '«', '&raquo;': '»',
    };
    entities.forEach((k, v) => s = s.replaceAll(k, v));
    // Numeric entities (e.g. &#1234; and &#x1F600;)
    s = s.replaceAllMapped(RegExp(r'&#(\d+);'),
        (m) => String.fromCharCode(int.parse(m.group(1)!)));
    s = s.replaceAllMapped(RegExp(r'&#x([0-9a-fA-F]+);'),
        (m) => String.fromCharCode(int.parse(m.group(1)!, radix: 16)));
    // Collapse 3+ newlines and trim
    s = s.replaceAll(RegExp(r'\n{3,}'), '\n\n').trim();
    return s.isEmpty ? null : s;
  }

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
    image:           ImageHelper.cleanUrl(j['image'] as String?),
    price:          (j['price']          as num?)?.toDouble() ?? 0,
    comparePrice:   (j['compare_price']  as num?)?.toDouble(),
    discountPercent: j['discount_percent'] is int ? j['discount_percent'] : null,
    isOnSale:        j['is_on_sale']     as bool? ?? false,
    inStock:         j['in_stock']       as bool? ?? true,
    ratingAvg:      (j['rating_avg']     as num?)?.toDouble() ?? 0,
    reviewsCount:   (j['reviews_count']  as num?)?.toInt() ?? 0,
    brandName:       j['brand']    is Map ? j['brand']['name']    as String? : null,
    categoryName:    j['category'] is Map ? j['category']['name'] as String? : null,
    shortDescription: _cleanHtml(j['short_description'] as String?),
    stockQuantity:   (j['stock_quantity'] as num?)?.toInt(),
    description:     _cleanHtml(j['description'] as String?),
    images:          (j['images'] as List?)?.cast<String>().map(ImageHelper.cleanUrl).whereType<String>().toList() ?? const [],
    variantGroups:   (j['variants'] as List?)
        ?.map((v) => VariantGroup.fromJson(v as Map<String, dynamic>))
        .toList() ?? const [],
    reviews:         (j['reviews']  as List?)?.cast<Map<String, dynamic>>() ?? const [],
  );
}
