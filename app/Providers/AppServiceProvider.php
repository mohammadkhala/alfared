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
        $bust = function () {
            foreach (['ar', 'he', 'en'] as $loc) {
                foreach (['hero','categories','featured','new','bestsellers','brands'] as $key) {
                    Cache::forget("home:{$loc}:{$key}");
                }
            }
        };

        Product::saved($bust);
        Product::deleted($bust);
        Banner::saved($bust);
        Banner::deleted($bust);
        Category::saved($bust);
        Category::deleted($bust);
        Brand::saved($bust);
        Brand::deleted($bust);
    }
}
