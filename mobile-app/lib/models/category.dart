class Category {
  final int    id;
  final String name;
  final String? nameEn;
  final String? nameHe;
  final String slug;
  final String? image;
  final String? icon;

  Category({
    required this.id,
    required this.name,
    this.nameEn,
    this.nameHe,
    required this.slug,
    this.image,
    this.icon,
  });

  String nameFor(String locale) {
    if (locale == 'en') return (nameEn?.isNotEmpty == true) ? nameEn! : name;
    if (locale == 'he') return (nameHe?.isNotEmpty == true) ? nameHe! : name;
    return name;
  }

  factory Category.fromJson(Map<String, dynamic> j) => Category(
    id:     j['id']      as int,
    name:   j['name']    as String? ?? '',
    nameEn: j['name_en'] as String?,
    nameHe: j['name_he'] as String?,
    slug:   j['slug']    as String? ?? '',
    image:  j['image']   as String?,
    icon:   j['icon']    as String?,
  );
}

class AppBanner {
  final int    id;
  final String? title;
  final String? subtitle;
  final String? image;
  final String? link;
  final String? badge;
  final String? button;

  AppBanner({
    required this.id,
    this.title,
    this.subtitle,
    this.image,
    this.link,
    this.badge,
    this.button,
  });

  factory AppBanner.fromJson(Map<String, dynamic> j) => AppBanner(
    id:       j['id'] as int,
    title:    j['title']    as String?,
    subtitle: j['subtitle'] as String?,
    image:    j['image']    as String?,
    link:     j['link']     as String?,
    badge:    j['badge']    as String?,
    button:   j['button']   as String?,
  );
}
