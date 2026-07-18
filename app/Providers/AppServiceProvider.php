<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Use custom Arabic pagination view ──
        Paginator::defaultView('vendor.pagination.tailwind');

        // ── Force HTTPS in production ──
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // ── Log every failed login attempt (admin + storefront) ──
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Failed::class,
            function (\Illuminate\Auth\Events\Failed $event) {
                $req = request();
                $email = (string) ($event->credentials['email'] ?? '');
                $isAdmin = str_starts_with($req->path(), 'admin');
                \App\Models\FailedLogin::record($req, $email, $isAdmin ? 'admin' : 'storefront');
            }
        );

        // ── Lockout event log ──
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Lockout::class,
            function () {
                $req = request();
                \App\Models\FailedLogin::record($req, $req->input('email'), 'admin', 'rate_limited');
            }
        );

        // ── Invalidate home cache when any of these models change ──
        // Must cover every key HomeController caches, otherwise the storefront
        // keeps serving stale data (e.g. an old price) until the TTL expires.
        $bust = function () {
            foreach (['ar', 'he', 'en'] as $loc) {
                foreach ([
                    'hero','categories','featured','new','bestsellers','brands',
                    'sale','offer_banners','promo_banners',
                ] as $key) {
                    Cache::forget("home:{$loc}:{$key}");
                }
            }
        };

        // Extra bust for banners: also clear API cache keys
        $bustBanner = function () use ($bust) {
            $bust();
            Cache::forget('api:home');
            Cache::forget('api:offers:banners');
            foreach (['hero', 'offers', 'popup'] as $pos) {
                Cache::forget("api:banners:{$pos}");
            }
        };

        $bustCategory = function () use ($bust) {
            $bust();
            Cache::forget('api:categories');
            Cache::forget('api:home');
        };

        $bustProduct = function () use ($bust) {
            $bust();
            Cache::forget('api:home');
            Cache::forget('api:offers:products');
        };

        $bustBrand = function () use ($bust) {
            $bust();
            Cache::forget('api:home');
        };

        Product::saved($bustProduct);
        Product::deleted($bustProduct);
        Banner::saved($bustBanner);
        Banner::deleted($bustBanner);
        Category::saved($bustCategory);
        Category::deleted($bustCategory);
        Brand::saved($bustBrand);
        Brand::deleted($bustBrand);
    }
}
