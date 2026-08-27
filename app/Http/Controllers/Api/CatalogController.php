<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\IncrementProductViews;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    /**
     * GET /api/v1/home — single aggregated payload for the home screen.
     * Replaces 5 separate calls with one cached response.
     */
    public function home(): JsonResponse
    {
        $data = Cache::remember('api:home', 300, function () {
            $banners = Banner::active()
                ->where('position', 'hero')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($b) => [
                    'id'       => $b->id,
                    'title'    => $b->title_ar,
                    'subtitle' => $b->subtitle_ar,
                    'image'    => $b->image ? (str_starts_with($b->image, 'http') ? $b->image : url('storage/' . $b->image)) : null,
                    'link'     => $b->link,
                    'badge'    => $b->badge_text,
                    'button'   => $b->button_text_ar,
                ])->all();

            $categories = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => $this->formatCategory($c))
                ->all();

            $featured = Product::active()
                ->with(['category:id,name_ar,slug', 'brand:id,name,slug'])
                ->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END')
                ->orderByDesc('sales_count')
                ->limit(8)
                ->get()
                ->map(fn ($p) => $this->formatProduct($p))
                ->all();

            $newArrivals = Product::active()
                ->with(['category:id,name_ar,slug', 'brand:id,name,slug'])
                ->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get()
                ->map(fn ($p) => $this->formatProduct($p))
                ->all();

            $offers = Product::active()
                ->whereColumn('compare_price', '>', 'price')
                ->with(['category:id,name_ar,slug', 'brand:id,name,slug'])
                ->orderByDesc('sales_count')
                ->limit(12)
                ->get()
                ->map(fn ($p) => $this->formatProduct($p))
                ->all();

            $offerBanners = Banner::active()
                ->where('position', 'offers')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($b) => [
                    'id'       => $b->id,
                    'title'    => $b->title_ar,
                    'subtitle' => $b->subtitle_ar,
                    'image'    => $b->image ? (str_starts_with($b->image, 'http') ? $b->image : url('storage/' . $b->image)) : null,
                    'link'     => $b->link,
                    'badge'    => $b->badge_text,
                    'button'   => $b->button_text_ar,
                ])->all();

            return compact('banners', 'categories', 'featured', 'newArrivals', 'offers', 'offerBanners');
        });

        return response()->json($data);
    }

    /** GET /api/v1/categories — flat list (top-level + 1 level deep). */
    public function categories(): JsonResponse
    {
        $categories = Cache::remember('api:categories', 600, fn () =>
            Category::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => $this->formatCategory($c))
        );
        return response()->json(['categories' => $categories]);
    }

    /** GET /api/v1/banners?position=hero */
    public function banners(Request $request): JsonResponse
    {
        $position = $request->query('position', 'hero');
        $banners = Cache::remember("api:banners:{$position}", 600, fn () =>
            Banner::active()
                ->where('position', $position)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($b) => [
                    'id'        => $b->id,
                    'title'     => $b->title_ar,
                    'subtitle'  => $b->subtitle_ar,
                    'image'     => $b->image ? (str_starts_with($b->image, 'http') ? $b->image : url('storage/' . $b->image)) : null,
                    'link'      => $b->link,
                    'badge'     => $b->badge_text,
                    'button'    => $b->button_text_ar,
                ])
        );
        return response()->json(['banners' => $banners]);
    }

    /** GET /api/v1/products?category=slug&brand=slug&q=text&sort=newest&page=1 */
    public function products(Request $request): JsonResponse
    {
        $query = Product::active()->with(['category:id,name_ar,name_en,name_he,slug', 'brand:id,name,slug']);

        if ($slug = $request->query('category')) {
            $cat = Category::where('slug', $slug)->first();
            if ($cat) $query->where('category_id', $cat->id);
        }

        if ($brand = $request->query('brand')) {
            $b = Brand::where('slug', $brand)->first();
            if ($b) $query->where('brand_id', $b->id);
        }

        if ($q = trim((string) $request->query('q'))) {
            $query->where(fn ($x) => $x
                ->where('name_ar',  'like', "%{$q}%")
                ->orWhere('name_en','like', "%{$q}%")
                ->orWhere('name_he','like', "%{$q}%")
                ->orWhere('sku',    'like', "%{$q}%"));
        }

        if ($min = (float) $request->query('price_min', 0)) {
            $query->where('price', '>=', $min);
        }
        if ($max = (float) $request->query('price_max', 0)) {
            $query->where('price', '<=', $max);
        }
        if ($request->boolean('on_sale')) {
            $query->whereColumn('compare_price', '>', 'price');
        }

        // Always show in-stock products first
        $query->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END');

        switch ($request->query('sort', 'newest')) {
            case 'price_asc':    $query->orderBy('price', 'asc'); break;
            case 'price_desc':   $query->orderBy('price', 'desc'); break;
            case 'rating':       $query->orderBy('rating_avg', 'desc'); break;
            case 'bestseller':   $query->orderBy('sales_count', 'desc'); break;
            default:             $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20)->through(fn($p) => $this->formatProduct($p));
        return response()->json($products);
    }

    /** GET /api/v1/offers — on-sale products + offers banners for the home page. */
    public function offers(): JsonResponse
    {
        $products = Cache::remember('api:offers:products', 300, fn () =>
            Product::active()
                ->whereColumn('compare_price', '>', 'price')
                ->with(['category:id,name_ar,slug', 'brand:id,name,slug'])
                ->orderByDesc('sales_count')
                ->limit(20)
                ->get()
                ->map(fn ($p) => $this->formatProduct($p))
                ->all()
        );

        $banners = Cache::remember('api:offers:banners', 300, fn () =>
            Banner::active()
                ->where('position', 'offers')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($b) => [
                    'id'        => $b->id,
                    'title'     => $b->title_ar,
                    'subtitle'  => $b->subtitle_ar,
                    'image'     => $b->image ? (str_starts_with($b->image, 'http') ? $b->image : url('storage/' . $b->image)) : null,
                    'link'      => $b->link,
                    'badge'     => $b->badge_text,
                    'button'    => $b->button_text_ar,
                ])
                ->all()
        );

        return response()->json(['products' => $products, 'banners' => $banners]);
    }

    /** GET /api/v1/products/{slug} */
    public function show(string $slug): JsonResponse
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category:id,name_ar,name_en,name_he,slug', 'brand:id,name,slug', 'images', 'variants', 'reviews.user:id,name'])
            ->firstOrFail();

        IncrementProductViews::dispatch($product->id);

        return response()->json([
            'product' => array_merge(
                $this->formatProduct($product),
                [
                    'description'      => strip_tags($product->description_ar ?? ''),
                    'description_html' => $product->description_ar,
                    'images'           => collect(array_filter([
                        // Always include main_image as first element
                        $product->main_image ? (str_starts_with($product->main_image, 'http') ? $product->main_image : url('storage/' . $product->main_image)) : null,
                    ]))->merge(
                        $product->images->map(fn($i) => str_starts_with($i->image_url, 'http') ? $i->image_url : url('storage/' . $i->image_url))
                    )->unique()->values()->all(),
                    // The app can play an embed in a webview or a file in a
                    // native player, so it needs to know which it got.
                    'video_url'    => $product->embedUrl() ?? $product->videoUrl(),
                    'video_type'   => $product->hasVideo()
                        ? ($product->embedUrl() ? 'embed' : 'file')
                        : null,
                    'variants'     => $product->variants
                        ->where('is_active', true)
                        ->groupBy('type')
                        ->map(fn ($items, $type) => [
                            'type'   => $type,
                            'label'  => $this->variantTypeLabel($type),
                            'values' => $items->map(fn ($v) => [
                                'id'             => $v->id,
                                'value'          => $v->value ?? '',
                                'value_en'       => $v->value_en ?? '',
                                'color_code'     => $v->color_code,
                                'price_modifier' => (float) $v->price_modifier,
                                'stock'          => (int) ($v->stock_quantity ?? 0),
                            ])->values()->all(),
                        ])
                        ->values()
                        ->all(),
                    'reviews'      => $product->reviews->map(fn($r) => [
                        'id'      => $r->id,
                        'rating'  => (int) $r->rating,
                        'comment' => $r->comment,
                        'name'    => $r->user?->name ?? $r->reviewer_name,
                        'date'    => $r->created_at?->diffForHumans(),
                    ])->all(),
                ]
            ),
        ]);
    }

    private function variantTypeLabel(string $type): string
    {
        return match($type) {
            'color'  => 'اللون',
            'size'   => 'المقاس',
            'weight' => 'الوزن',
            'volume' => 'الحجم',
            'style'  => 'الطراز',
            default  => $type,
        };
    }

    protected function formatCategory(Category $c): array
    {
        return [
            'id'       => $c->id,
            'name'     => $c->name_ar,
            'name_en'  => $c->name_en,
            'name_he'  => $c->name_he,
            'slug'     => $c->slug,
            'icon'     => $c->icon ?? null,
            'image'    => $c->image ? (str_starts_with($c->image, 'http') ? $c->image : url('storage/' . $c->image)) : null,
        ];
    }

    protected function formatProduct(Product $p): array
    {
        return [
            'id'              => $p->id,
            'name'            => $p->name_ar,
            'name_en'         => $p->name_en,
            'name_he'         => $p->name_he,
            'slug'            => $p->slug,
            'image'           => $p->main_image ? (str_starts_with($p->main_image, 'http') ? $p->main_image : url('storage/' . $p->main_image)) : null,
            'price'           => (float) $p->price,
            'compare_price'   => $p->compare_price ? (float) $p->compare_price : null,
            'discount_percent'=> $p->discount_percent,
            'is_on_sale'      => $p->is_on_sale,
            'in_stock'        => $p->is_in_stock,
            'rating_avg'      => (float) ($p->rating_avg ?? 0),
            'reviews_count'   => (int) ($p->reviews_count ?? 0),
            'category'        => $p->category ? ['id'=>$p->category->id, 'name'=>$p->category->name_ar, 'slug'=>$p->category->slug] : null,
            'brand'           => $p->brand ? ['id'=>$p->brand->id, 'name'=>$p->brand->name, 'slug'=>$p->brand->slug] : null,
            'short_description' => $p->short_description,
            'short_description_en' => $p->short_description_en,
            'short_description_he' => $p->short_description_he,
            'stock_quantity'    => (int) $p->stock_quantity,
            'low_stock'         => $p->is_low_stock,
        ];
    }

    /**
     * GET /api/v1/app-config
     * Returns store-level feature flags and settings for the mobile app.
     * Cached for 60 s — cleared automatically when settings are saved.
     */
    public function appConfig(): JsonResponse
    {
        $data = Cache::remember('api:app_config', 60, function () {
            $s = Setting::all()->pluck('value', 'key');

            return [
                'cod_enabled'           => ($s->get('cod_enabled', '1') === '1'),
                'online_payment_enabled'=> ($s->get('online_payment_enabled', '0') === '1'),
                'loyalty_enabled'       => ($s->get('loyalty_enabled', '1') === '1'),
                // When false the app skips the WhatsApp OTP step on signup —
                // lets us disable verification instantly if the sender number
                // gets banned, without shipping a new build.
                'phone_verification_enabled' => ($s->get('phone_verification_enabled', '0') === '1'),
                'min_order_amount'      => (float) ($s->get('min_order_amount', '0')),
                'free_shipping_above'   => (float) ($s->get('free_shipping_above', '0')),
                'store_phone'           => $s->get('store_phone', ''),
                'social_whatsapp'       => $s->get('social_whatsapp', ''),
                // Store logo, so the app's splash/branding follows the website
                // without needing a new build. The launcher icon can't come
                // from here — the OS bakes it into the installed package.
                'store_logo'            => \App\Support\SiteBranding::logo(),
            ];
        });

        return response()->json(['data' => $data]);
    }
}
