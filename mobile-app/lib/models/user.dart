class AppUser {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final int loyaltyPoints;

  // ── Tier fields ──
  final double totalSpent;
  final String tierKey;
  final String tierLabel;
  final String tierEmoji;
  final String? nextTierLabel;
  final String? nextTierEmoji;
  final double? nextTierMin;
  final double amountToNext;
  final double tierProgress;

  AppUser({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.loyaltyPoints = 0,
    this.totalSpent = 0,
    this.tierKey = 'new',
    this.tierLabel = 'جديد',
    this.tierEmoji = '🎯',
    this.nextTierLabel,
    this.nextTierEmoji,
    this.nextTierMin,
    this.amountToNext = 200,
    this.tierProgress = 0,
  });

  factory AppUser.fromJson(Map<String, dynamic> json) => AppUser(
    id:             json['id']             as int,
    name:           json['name']           as String? ?? '',
    email:          json['email']          as String? ?? '',
    phone:          json['phone']          as String?,
    loyaltyPoints: (json['loyalty_points'] as num?)?.toInt() ?? 0,
    totalSpent:    (json['total_spent']    as num?)?.toDouble() ?? 0,
    tierKey:        json['tier_key']       as String? ?? 'new',
    tierLabel:      json['tier_label']     as String? ?? 'جديد',
    tierEmoji:      json['tier_emoji']     as String? ?? '🎯',
    nextTierLabel:  json['next_tier_label'] as String?,
    nextTierEmoji:  json['next_tier_emoji'] as String?,
    nextTierMin:   (json['next_tier_min']  as num?)?.toDouble(),
    amountToNext:  (json['amount_to_next'] as num?)?.toDouble() ?? 0,
    tierProgress:  (json['tier_progress']  as num?)?.toDouble() ?? 0,
  );

  bool get isVip => tierKey == 'vip';
}
