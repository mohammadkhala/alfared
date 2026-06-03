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
        $middleware->trustProxies(
            at: '*',                          // trust all (Cloudflare IPs change)
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
