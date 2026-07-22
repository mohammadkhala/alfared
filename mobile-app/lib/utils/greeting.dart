import '../l10n/app_strings.dart';

/// Returns a localized greeting + emoji based on the hour of day.
class Greeting {
  static ({String text, String emoji}) now(S s) {
    final hour = DateTime.now().hour;
    if (hour >= 5 && hour < 12)  return (text: s.greetingMorning, emoji: '☀️');
    if (hour >= 12 && hour < 17) return (text: s.greetingAfternoon, emoji: '🌤️');
    if (hour >= 17 && hour < 21) return (text: s.greetingEvening, emoji: '🌅');
    return (text: s.greetingNight, emoji: '🌙');
  }
}
