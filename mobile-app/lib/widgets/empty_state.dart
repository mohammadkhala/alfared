import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class EmptyState extends StatelessWidget {
  const EmptyState({
    super.key,
    required this.icon,
    required this.title,
    this.subtitle,
    this.ctaLabel,
    this.onCta,
    this.iconColor,
  });

  final IconData icon;
  final String title;
  final String? subtitle;
  final String? ctaLabel;
  final VoidCallback? onCta;
  final Color? iconColor;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            TweenAnimationBuilder<double>(
              duration: const Duration(milliseconds: 600),
              tween: Tween(begin: 0, end: 1),
              curve: Curves.easeOutBack,
              builder: (_, value, child) => Transform.scale(scale: value, child: child),
              child: Container(
                width: 120, height: 120,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft, end: Alignment.bottomRight,
                    colors: [
                      (iconColor ?? AppColors.blue).withValues(alpha: 0.10),
                      (iconColor ?? AppColors.blue).withValues(alpha: 0.04),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(36),
                ),
                child: Icon(icon, size: 56, color: iconColor ?? AppColors.blue),
              ),
            ),
            const SizedBox(height: 22),
            Text(title,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppColors.blue, fontFamily: 'Cairo')),
            if (subtitle != null) ...[
              const SizedBox(height: 8),
              Text(subtitle!,
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.gray, fontFamily: 'Cairo', height: 1.6, fontSize: 13)),
            ],
            if (ctaLabel != null && onCta != null) ...[
              const SizedBox(height: 22),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: onCta,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: iconColor ?? AppColors.orange,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                  ),
                  child: Text(ctaLabel!, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
