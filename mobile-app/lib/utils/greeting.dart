/// Returns a localized greeting + emoji based on the hour of day.
class Greeting {
  static ({String text, String emoji}) now() {
    final hour = DateTime.now().hour;
    if (hour >= 5 && hour < 12)  return (text: 'صباح الخير', emoji: '☀️');
    if (hour >= 12 && hour < 17) return (text: 'مساء الخير', emoji: '🌤️');
    if (hour >= 17 && hour < 21) return (text: 'مساء النور', emoji: '🌅');
    return (text: 'مساء الخير', emoji: '🌙');
  }
}
