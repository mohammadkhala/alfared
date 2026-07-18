import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/locale_provider.dart';
import '../../theme/app_theme.dart';
import '../../theme/theme_ext.dart';
import 'order_detail_screen.dart';

/// Look up a single order by its number, for someone who doesn't want to go
/// through "my orders" — an order placed on the website, or one being checked
/// on behalf of somebody else.
///
/// OrderDetailScreen already falls back to the public /track endpoint when the
/// authenticated fetch fails, so this screen only has to collect the number.
class TrackOrderScreen extends StatefulWidget {
  const TrackOrderScreen({super.key});

  @override
  State<TrackOrderScreen> createState() => _TrackOrderScreenState();
}

class _TrackOrderScreenState extends State<TrackOrderScreen> {
  final _controller = TextEditingController();
  String? _error;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _submit() {
    final number = _controller.text.trim();
    if (number.isEmpty) {
      setState(() => _error = context.read<LocaleProvider>().s.trackEnterNumber);
      return;
    }
    setState(() => _error = null);
    Navigator.of(context).push(
      MaterialPageRoute(builder: (_) => OrderDetailScreen(orderNumber: number)),
    );
  }

  @override
  Widget build(BuildContext context) {
    final s = context.watch<LocaleProvider>().s;

    return Scaffold(
      backgroundColor: context.bg,
      appBar: AppBar(title: Text(s.trackOrder)),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(20, 28, 20, 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Center(
              child: Container(
                width: 68,
                height: 68,
                decoration: BoxDecoration(
                  color: AppColors.blueLight,
                  borderRadius: BorderRadius.circular(20),
                ),
                alignment: Alignment.center,
                child: const Text('📦', style: TextStyle(fontSize: 30)),
              ),
            ),
            const SizedBox(height: 18),
            Text(
              s.trackIntro,
              textAlign: TextAlign.center,
              style: TextStyle(fontFamily: 'Cairo', fontSize: 13, height: 1.7, color: context.muted),
            ),
            const SizedBox(height: 26),
            Text(
              s.trackNumberLabel,
              style: TextStyle(fontFamily: 'Cairo', fontSize: 14, fontWeight: FontWeight.w700, color: context.text),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: _controller,
              textInputAction: TextInputAction.go,
              onSubmitted: (_) => _submit(),
              style: TextStyle(fontFamily: 'Cairo', color: context.text),
              decoration: InputDecoration(
                hintText: 'ORD-2026-0001',
                hintStyle: TextStyle(fontFamily: 'Cairo', color: context.muted),
                filled: true,
                fillColor: context.card,
                errorText: _error,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
            const SizedBox(height: 22),
            SizedBox(
              height: 50,
              child: ElevatedButton(
                onPressed: _submit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.blue,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text(
                  s.trackSubmit,
                  style: const TextStyle(fontFamily: 'Cairo', fontSize: 15, fontWeight: FontWeight.w900),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
