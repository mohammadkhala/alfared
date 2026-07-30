<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\CloudflareRealIp::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TrackVisits::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);
        $middleware->api(prepend: [
            \App\Http\Middleware\CloudflareRealIp::class,
        ]);

        // Exclude Lahza webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhooks/lahza',
        ]);

        // ── Trust Cloudflare proxies so $request->ip() returns real client IP ──
        // Cloudflare sends real IP via CF-Connecting-IP header.
        // Cloudflare IPv4 + IPv6 ranges (updated 2025)
        $cloudflareIps = [
            '173.245.48.0/20','103.21.244.0/22','103.22.200.0/22','103.31.4.0/22',
            '141.101.64.0/18','108.162.192.0/18','190.93.240.0/20','188.114.96.0/20',
            '197.234.240.0/22','198.41.128.0/17','162.158.0.0/15','104.16.0.0/13',
            '104.24.0.0/14','172.64.0.0/13','131.0.72.0/22',
            '2400:cb00::/32','2606:4700::/32','2803:f800::/32','2405:b500::/32',
            '2405:8100::/32','2a06:98c0::/29','2c0f:f248::/32',
        ];
        $middleware->trustProxies(
            at: $cloudflareIps,
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
