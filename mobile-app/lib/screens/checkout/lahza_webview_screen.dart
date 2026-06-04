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
  bool _loading = true;
  bool _handled = false;
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

          // Prevent loading our callback URL inside the WebView
          // (server handles it, then redirects to success/failed)
          if (request.url.contains('checkout/lahza/callback')) {
            return NavigationDecision.prevent;
          }
          if (request.url.contains('checkout/failed') ||
              request.url.contains('checkout/payment-failed')) {
            return NavigationDecision.prevent;
          }

          return NavigationDecision.navigate;
        },
        onWebResourceError: (error) {
          debugPrint('WebView error: ${error.description}');
        },
      ))
      ..loadRequest(Uri.parse(widget.authorizationUrl));
  }

  /// Called on every URL change — covers both navigation requests and page finishes.
  void _checkUrl(String url) {
    if (_handled) return;

    // Success paths
    if (url.contains('checkout/lahza/callback') ||
        url.contains('checkout/success')) {
      _handled = true;
      _pollTimer?.cancel();
      _navigateToSuccess();
      return;
    }

    // Failure paths
    if (url.contains('checkout/failed') ||
        url.contains('checkout/payment-failed')) {
      _handled = true;
      _pollTimer?.cancel();
      _onPaymentFailed();
      return;
    }

    // Lahza shows "Please close this web page to continue" when done.
    // At this point the browser won't redirect — poll backend for order status.
    if (!url.contains('lahza.io') && !url.contains('alfared')) {
      // Still on Lahza's domain — do nothing yet
      return;
    }

    // We're on some unknown URL after payment — start polling if not already
    _startPolling();
  }

  /// After Lahza finishes, it sometimes shows a completion page without redirect.
  /// Poll our order-track API for up to 30 s to detect payment_status = paid/failed.
  void _startPolling() {
    if (_pollTimer != null || _handled) return;
    int attempts = 0;
    _pollTimer = Timer.periodic(const Duration(seconds: 3), (t) async {
      attempts++;
      if (_handled || !mounted) { t.cancel(); return; }
      if (attempts > 10) {
        t.cancel();
        // Timed out — assume success and let user verify on success screen
        _handled = true;
        _navigateToSuccess();
        return;
      }
      try {
        final res = await ApiService.instance
            .get('/orders/${widget.orderNumber}/track');
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

  void _navigateToSuccess() {
    if (!mounted) return;
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
        builder: (_) => SuccessScreen(orderNumber: widget.orderNumber),
      ),
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
        title: const Text(
          '💳 الدفع عبر لحظة',
          style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700),
        ),
        leading: IconButton(
          icon: const Icon(Icons.close),
          onPressed: () {
            showDialog(
              context: context,
              builder: (ctx) => AlertDialog(
                title: const Text('إلغاء الدفع؟',
                    style: TextStyle(fontFamily: 'Cairo', fontWeight: FontWeight.w700)),
                content: const Text('هل تريد إلغاء عملية الدفع والعودة؟',
                    style: TextStyle(fontFamily: 'Cairo')),
                actions: [
                  TextButton(
                    onPressed: () => Navigator.pop(ctx),
                    child: const Text('لا', style: TextStyle(fontFamily: 'Cairo')),
                  ),
                  TextButton(
                    onPressed: () {
                      Navigator.pop(ctx);
                      Navigator.pop(context, {'success': false});
                    },
                    child: const Text('نعم، إلغاء',
                        style: TextStyle(fontFamily: 'Cairo', color: Colors.red)),
                  ),
                ],
              ),
            );
          },
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
                    Text(
                      'جاري تحميل بوابة الدفع...',
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        color: Colors.white70,
                        fontSize: 14,
                      ),
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }
}
