import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
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

  @override
  void initState() {
    super.initState();

    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0xFF080810))
      ..setNavigationDelegate(NavigationDelegate(
        onPageStarted: (_) => setState(() => _loading = true),
        onPageFinished: (_) => setState(() => _loading = false),
        onNavigationRequest: (request) {
          final url = request.url;

          // Detect success callback
          if (url.contains('${widget.callbackBaseUrl}/checkout/lahza/callback') ||
              url.contains('checkout/lahza/callback')) {
            if (!_handled) {
              _handled = true;
              _onPaymentSuccess();
            }
            return NavigationDecision.prevent;
          }

          // Detect failure
          if (url.contains('checkout/failed')) {
            if (!_handled) {
              _handled = true;
              _onPaymentFailed();
            }
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

  void _onPaymentSuccess() {
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
