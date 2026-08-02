<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\FlashSale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $ttl = 300;
        $loc = app()->getLocale();

        $heroBanners = Cache::remember("home:{$loc}:hero", $ttl, fn () =>
            Banner::active()->where('position', 'hero')->orderBy('sort_order')->get()
        );

        $categories = Cache::remember("home:{$loc}:categories", $ttl, fn () =>
            Category::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get()
        );

        $featuredProducts = Cache::remember("home:{$loc}:featured", $ttl, fn () =>
            Product::active()->featured()
                ->with(['category:id,name_ar,name_en,name_he,slug', 'brand:id,name,slug'])
                ->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END')
                ->limit(8)->get()
        );

        // "New arrivals" = the most recently added products, so anything just
        // added shows up on its own. The is_new flag is no longer required
        // (easy to forget); it only boosts a product to the front when set.
        $newProducts = Cache::remember("home:{$loc}:new", $ttl, fn () =>
            Product::active()
                ->with(['category:id,name_ar,name_en,name_he,slug', 'brand:id,name,slug'])
                ->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END')
                ->orderByDesc('is_new')
                ->orderByDesc('created_at')
                ->limit(8)->get()
        );

        $bestSellers = Cache::remember("home:{$loc}:bestsellers", $ttl, fn () =>
            Product::active()
                ->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END')
                ->orderBy('sales_count', 'desc')
                ->with(['category:id,name_ar,name_en,name_he,slug', 'brand:id,name,slug'])
                ->limit(8)->get()
        );

        // ─── On-sale products (have compare_price > price) ───────────────
        $saleProducts = Cache::remember("home:{$loc}:sale", $ttl, fn () =>
            Product::active()
                ->whereColumn('compare_price', '>', 'price')
                ->with(['category:id,name_ar,name_en,name_he,slug', 'brand:id,name,slug'])
                ->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END')
                ->orderBy('sales_count', 'desc')
                ->limit(8)->get()
        );

        // ─── Active flash sale products ───────────────────────────────────
        $flashSale     = $this->activeFlashSale();
        $flashProducts = $flashSale
            ? $this->flashSaleProducts($flashSale)
            : collect();

        // Merge: flash-sale products first (they override regular sale products
        // for the same product_id), then fill up to 8 with normal sale products.
        $offerProducts = $flashProducts
            ->concat($saleProducts->whereNotIn('id', $flashProducts->pluck('id')))
            ->take(8)
            ->values();

        $offerBanners = Cache::remember("home:{$loc}:offer_banners", $ttl, fn () =>
            Banner::active()->where('position', 'offers')->orderBy('sort_order')->get()
        );

        // Dual promo banners (managed from admin → لوحة الإعلانات → موضع "ترويجي")
        $promoBanners = Cache::remember("home:{$loc}:promo_banners", $ttl, fn () =>
            Banner::active()->where('position', 'promo')->orderBy('sort_order')->limit(2)->get()
        );

        $brands = Cache::remember("home:{$loc}:brands", $ttl, fn () =>
            Brand::where('is_active', true)->orderBy('sort_order')->limit(12)->get()
        );

        // The three cards floating over the hero image: real products, so they
        // carry a real photo, price and link instead of hardcoded brand names
        // and made-up prices. Prefer on-sale items, fall back to featured.
        $heroProducts = Cache::remember("home:{$loc}:hero_cards", $ttl, function () {
            // "On sale" is compare_price > price — is_on_sale is a computed
            // accessor, not a column, so it can't be queried directly.
            $onSale = Product::active()
                ->whereColumn('compare_price', '>', 'price')
                ->whereNotNull('main_image')->where('stock_quantity', '>', 0)
                ->inRandomOrder()->limit(3)->get();

            if ($onSale->count() === 3) {
                return $onSale;
            }

            // Top up from featured so there are always three.
            return $onSale->merge(
                Product::active()->featured()
                    ->whereNotNull('main_image')->where('stock_quantity', '>', 0)
                    ->whereNotIn('id', $onSale->pluck('id'))
                    ->limit(3 - $onSale->count())->get()
            );
        });

        return view('home', compact(
            'heroBanners', 'categories', 'featuredProducts',
            'newProducts', 'bestSellers', 'brands', 'heroProducts',
            'offerProducts', 'offerBanners', 'promoBanners', 'flashSale'
        ));
    }

    // ── All Categories Page ───────────────────────────────────────────────────

    public function categories()
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        return view('categories.index', compact('categories'));
    }

    // ── Flash sale helpers ────────────────────────────────────────────────────

    private function activeFlashSale(): ?FlashSale
    {
        return FlashSale::where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')  ->orWhere('ends_at',   '>=', now()))
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Build a Collection of Product models with price overridden by sale_price.
     * We clone each product so the original DB record is untouched.
     */
    private function flashSaleProducts(FlashSale $sale): Collection
    {
        return $sale->saleProducts()
            ->with(['product' => fn($q) =>
                $q->active()->inStock()
                  ->with(['category:id,name_ar,name_en,name_he,slug', 'brand:id,name,slug'])
            ])
            ->get()
            ->filter(fn($sp) => $sp->product !== null)
            ->map(function ($sp) {
                // Clone product and override price fields for display
                $p = clone $sp->product;
                $p->compare_price = $p->price;         // original → compare
                $p->price         = $sp->sale_price;   // flash price → current
                $p->setAttribute('flash_qty_left',
                    $sp->quantity_limit !== null
                        ? max(0, $sp->quantity_limit - $sp->sold_count)
                        : null
                );
                return $p;
            });
    }
}
