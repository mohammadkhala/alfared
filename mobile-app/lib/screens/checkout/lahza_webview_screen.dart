import 'dart:async';
import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import 'success_screen.dart';

class LahzaWebViewScreen extends StatefulWidget {
  final String authorizationUrl;
  final String orderNumber;
  final String callbackBaseUrl; // https://alfared.ps

  const LahzaWebViewScreen({
    super.key,
    required this.authorizationUrl,
    required this.orderNumber,
    required this.callbackBaseUrl,
  });

  @override
  State<LahzaWebViewScreen> createState() => _LahzaWebViewScreenState();
}

class _LahzaWebViewScreenState extends State<LahzaWebViewScreen> {
  late final WebViewController _controller;
  bool _loading  = true;
  bool _handled  = false;
  bool _checking = false;
  Timer? _pollTimer;

  @override
  void initState() {
    super.initState();

    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0xFF080810))
      ..setNavigationDelegate(NavigationDelegate(
        onPageStarted: (_) => setState(() => _loading = true),
        onPageFinished: (url) {
          setState(() => _loading = false);
          _checkUrl(url);
        },
        onNavigationRequest: (request) {
          _checkUrl(request.url);
          if (request.url.contains('checkout/lahza/callback') ||
              request.url.contains('checkout/failed') ||
              request.url.contains('checkout/payment-failed')) {
            return NavigationDecision.prevent;
          }
          return NavigationDecision.navigate;
        },
        onWebResourceError: (e) => debugPrint('WebView error: ${e.description}'),
      ))
      ..loadRequest(Uri.parse(widget.authorizationUrl));

    // Start polling immediately — Lahza may never redirect the browser.
    // Poll every 3 s for up to 2 min, starting after 4 s.
    Future.delayed(const Duration(seconds: 4), _startPolling);
  }

  void _checkUrl(String url) {
    if (_handled) return;
    if (url.contains('checkout/lahza/callback') ||
        url.contains('checkout/success')) {
      _handled = true;
      _pollTimer?.cancel();
      _navigateToSuccess();
    } else if (url.contains('checkout/failed') ||
               url.contains('checkout/payment-failed')) {
      _handled = true;
      _pollTimer?.cancel();
      _onPaymentFailed();
    }
  }

  void _startPolling() {
    if (_pollTimer != null || _handled) return;
    int attempts = 0;
    _pollTimer = Timer.periodic(const Duration(seconds: 3), (t) async {
      attempts++;
      if (_handled || !mounted) { t.cancel(); return; }
      if (attempts > 40) { t.cancel(); return; } // stop after 2 min
      try {
        final res    = await ApiService.instance.get('/orders/${widget.orderNumber}/track');
        final status = (res as Map)['payment_status'] as String? ?? '';
        if (status == 'paid') {
          t.cancel();
          _handled = true;
          if (mounted) _navigateToSuccess();
        } else if (status == 'failed') {
          t.cancel();
          _handled = true;
          if (mounted) _onPaymentFailed();
        }
      } catch (_) {}
    });
  }

  /// Called when user taps X — actively verify with Lahza before showing cancel.
  Future<void> _handleClosePressed() async {
    if (_handled) return;
    setState(() => _checking = true);
    try {
      // Call verify-payment which queries Lahza API directly and updates our DB
      final res    = await ApiService.instance
          .post('/orders/${widget.orderNumber}/verify-payment');
      final status = (res as Map)['payment_status'] as String? ?? '';
      if (status == 'paid') {
        _handled = true;
        _pollTimer?.cancel();
        _navigateToSuccess();
        return;
      }
    } catch (_) {}
    if (!mounted) return;
    setState(() => _checking = false);

    // Payment not confirmed yet — ask if they really want to cancel
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('إلغاء الدفع؟',
            style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700)),
        content: const Text(
            'إذا أتممت الدفع بالفعل فالطلب سيُسجَّل تلقائياً. هل تريد العودة؟',
            style: TextStyle(fontFamily: 'Cairo')),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('لا، انتظر', style: TextStyle(fontFamily: 'Cairo')),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              Navigator.pop(context, {'success': false});
            },
            child: const Text('نعم، ارجع',
                style: TextStyle(fontFamily: 'Cairo', color: Colors.red)),
          ),
        ],
      ),
    );
  }

  void _navigateToSuccess() {
    if (!mounted) return;
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
          builder: (_) => SuccessScreen(orderNumber: widget.orderNumber)),
    );
  }

  void _onPaymentFailed() {
    if (!mounted) return;
    Navigator.of(context).pop({'success': false});
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('فشلت عملية الدفع. يمكنك المحاولة مجدداً.',
            style: TextStyle(fontFamily: 'Cairo')),
        backgroundColor: AppColors.danger,
      ),
    );
  }

  @override
  void dispose() {
    _pollTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF080810),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0B1A3B),
        foregroundColor: Colors.white,
        title: const Text('💳 الدفع عبر لحظة',
            style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700)),
        leading: _checking
            ? const Padding(
                padding: EdgeInsets.all(14),
                child: SizedBox(
                  width: 20, height: 20,
                  child: CircularProgressIndicator(
                      strokeWidth: 2, color: Colors.white),
                ),
              )
            : IconButton(
                icon: const Icon(Icons.close),
                onPressed: _handleClosePressed,
              ),
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_loading)
            Container(
              color: const Color(0xFF080810),
              child: const Center(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    CircularProgressIndicator(color: AppColors.orange),
                    SizedBox(height: 16),
                    Text('جاري تحميل بوابة الدفع...',
                        style: TextStyle(
                            fontFamily: 'Cairo',
                            color: Colors.white70,
                            fontSize: 14)),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }
}
