<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets a baseline of HTTP security headers on every response.
 * These mitigate XSS, clickjacking, MIME-sniffing, referrer leaks, and protocol downgrade.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking (no embedding the site in <iframe>)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Don't send referer to third-party sites in full
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features (camera, mic, geolocation, etc.)
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(self), payment=(self), usb=(), accelerometer=(), gyroscope=()'
        );

        // HSTS — force HTTPS for 1 year. Only safe when site actually serves over HTTPS.
        if ($request->secure() || app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Cross-Origin policies — modern browsers
        $response->headers->set('X-XSS-Protection', '0'); // disabled — rely on CSP instead

        // Content-Security-Policy — restrictive but practical. Adjust if you add CDNs.
        // We allow inline styles/scripts because Filament and our blade views use them.
        // For a stricter policy you'd refactor inline scripts to external files + nonces.
        if (! app()->runningUnitTests()) {
            $csp = [
                "default-src 'self'",
                "img-src 'self' data: blob: https:",
                "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://analytics.tiktok.com https://ads.tiktok.com",
                "connect-src 'self' wss: https: https://analytics.tiktok.com https://ads.tiktok.com",
                // Product videos are embedded from YouTube/Vimeo; without these
                // the browser blocks the iframe ("هذا المحتوى محظور").
                "frame-src 'self' https://www.google.com https://maps.google.com "
                    . "https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "form-action 'self' https://alfared.ps https://alfared.online https://checkout.lahza.io",
                "object-src 'none'",
            ];
            $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        }

        // Remove server fingerprinting
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
