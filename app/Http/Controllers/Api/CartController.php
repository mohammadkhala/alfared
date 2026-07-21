<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Coupon;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\RoadFnArea;
use App\Models\Product;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Services\LahzaService;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * Server-side cart for authenticated users. Stored as a JSON blob in cache
 * keyed by user id so it survives across devices.
 */
class CartController extends Controller
{
    protected function key(int $userId): string
    {
        return "user_cart:{$userId}";
    }

    protected function getCart(int $userId): array
    {
        return Cache::get($this->key($userId), []);
    }

    protected function saveCart(int $userId, array $cart): void
    {
        Cache::put($this->key($userId), $cart, now()->addDays(30));
    }

    /** Helper: convert cart array → fully computed totals using session coupon/loyalty if provided */
    protected function computeTotals(array $cart, ?array $couponData = null, int $loyaltyPoints = 0, ?int $zoneId = null): array
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += (float) $item['price'] * (int) $item['qty'];
        }

        $discount = 0;
        if ($couponData) {
            $discount = (float) $couponData['discount'];
        }

        $loyaltyDiscount = $loyaltyPoints > 0 ? LoyaltyService::pointsToIls($loyaltyPoints) : 0;

        $deliveryFee = 0;
        if ($zoneId) {
            if ($zone = DeliveryZone::find($zoneId)) {
                $deliveryFee = $zone->calculateFee(max(0, $subtotal - $discount - $loyaltyDiscount));
            }
        }

        $total = max(0, $subtotal - $discount - $loyaltyDiscount + $deliveryFee);

        return [
            'subtotal'         => round($subtotal, 2),
            'discount'         => round($discount, 2),
            'loyalty_discount' => round($loyaltyDiscount, 2),
            'loyalty_points'   => $loyaltyPoints,
            'delivery_fee'     => round($deliveryFee, 2),
            'total'            => round($total, 2),
            'items_count'      => count($cart),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $cart = $this->getCart($request->user()->id);

        return response()->json([
            'items' => array_values($this->withLocalizedNames($cart)),
            'totals'=> $this->computeTotals($cart),
        ]);
    }

    /**
     * Adds name_en/name_he to each cart line so the app can show the product in
     * the user's language.
     *
     * The stored 'name' is an Arabic snapshot from when the item was added, and
     * older carts have only that — so the translations are read from the live
     * product, in one query for the whole cart, and fall back to the snapshot.
     */
    protected function withLocalizedNames(array $cart): array
    {
        $ids = collect($cart)->pluck('product_id')->filter()->unique();
        if ($ids->isEmpty()) {
            return $cart;
        }

        $products = Product::whereIn('id', $ids)->get(['id', 'name_ar', 'name_en', 'name_he'])->keyBy('id');

        foreach ($cart as $key => $item) {
            $p = $products->get($item['product_id'] ?? null);
            $cart[$key]['name']    = $p->name_ar ?? $item['name'];
            $cart[$key]['name_en'] = $p->name_en ?? null;
            $cart[$key]['name_he'] = $p->name_he ?? null;
        }

        return $cart;
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id'  => 'required|exists:products,id',
            'variant_id'  => 'nullable|integer',
            'qty'         => 'required|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($data['product_id']);
        if (! $product->is_in_stock) {
            return response()->json(['error' => 'المنتج غير متوفر حالياً'], 422);
        }
        if ($product->track_quantity && $product->stock_quantity < $data['qty']) {
            return response()->json(['error' => "الكمية المتاحة {$product->stock_quantity} فقط"], 422);
        }

        $cart = $this->getCart($request->user()->id);
        $variantId = $data['variant_id'] ?? null;
        $key  = $product->id . ($variantId ? '_' . $variantId : '');
        $price = $product->price;

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $data['qty'];
        } else {
            $cart[$key] = [
                'key'        => $key,
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name'       => $product->name_ar,
                'image'      => $product->main_image ? (str_starts_with($product->main_image, 'http') ? $product->main_image : url('storage/' . $product->main_image)) : null,
                'price'      => (float) $price,
                'qty'        => (int) $data['qty'],
            ];
        }

        $this->saveCart($request->user()->id, $cart);
        return $this->index($request);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $request->validate(['qty' => 'required|integer|min:1|max:100']);
        $cart = $this->getCart($request->user()->id);
        if (isset($cart[$key])) {
            $cart[$key]['qty'] = (int) $request->qty;
            $this->saveCart($request->user()->id, $cart);
        }
        return $this->index($request);
    }

    public function remove(Request $request, string $key): JsonResponse
    {
        $cart = $this->getCart($request->user()->id);
        unset($cart[$key]);
        $this->saveCart($request->user()->id, $cart);
        return $this->index($request);
    }

    public function clear(Request $request): JsonResponse
    {
        Cache::forget($this->key($request->user()->id));
        return response()->json(['items' => [], 'totals' => $this->computeTotals([])]);
    }

    /** Validate + apply a coupon code, return computed discount. */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        $cart = $this->getCart($request->user()->id);
        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();
        if (! $coupon || ! $coupon->isValid()) {
            return response()->json(['error' => 'كود الخصم غير صحيح أو منتهي الصلاحية'], 422);
        }
        $discount = $coupon->calculateDiscount($subtotal);
        if ($discount <= 0) {
            return response()->json(['error' => "الحد الأدنى للطلب {$coupon->min_order_amount} ₪"], 422);
        }

        return response()->json([
            'coupon' => [
                'code'     => $coupon->code,
                'id'       => $coupon->id,
                'discount' => round($discount, 2),
                'label'    => $coupon->type === 'percentage' ? "{$coupon->value}%" : "{$coupon->value} ₪",
            ],
        ]);
    }

    /** Get delivery zones list */
    public function deliveryZones(): JsonResponse
    {
        $all = DeliveryZone::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name_ar')->get();

        $format = fn ($z) => [
            'id'             => $z->id,
            'parent_id'      => $z->parent_id,
            'name'           => $z->name_ar,
            'name_en'        => $z->name_en,
            'name_he'        => $z->name_he,
            'base_fee'       => (float) ($z->base_fee ?? $z->delivery_fee ?? 0),
            'free_above'     => $z->free_above ? (float) $z->free_above : null,
            'estimated_days' => (int) ($z->estimated_days ?? 1),
        ];

        $mains = $all->whereNull('parent_id');

        // `zones` stays flat for app builds that predate the hierarchy: they
        // see only the main regions and still get a valid fee.
        // `tree` carries the two levels for newer builds.
        return response()->json([
            'zones' => $mains->map($format)->values(),
            'tree'  => $mains->map(function ($m) use ($all, $format) {
                $row = $format($m);
                $row['children'] = $all->where('parent_id', $m->id)->map($format)->values();
                return $row;
            })->values(),
        ]);
    }

    /**
     * Neighbourhoods of one city, for the checkout area picker. Loaded per
     * city on demand — all of them at once would be a heavy payload on mobile.
     */
    public function zoneAreas(Request $request): JsonResponse
    {
        $areas = RoadFnArea::where('delivery_zone_id', $request->zone_id)
            ->ordered()
            ->get(['roadfn_area_id', 'name_ar', 'name_he', 'name_en'])
            ->map(fn ($a) => ['id' => $a->roadfn_area_id, 'name' => $a->name])
            ->values();

        return response()->json(['areas' => $areas]);
    }

    /** Compute totals given a zone + optional coupon + optional loyalty */
    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'coupon_code'      => 'nullable|string',
            'loyalty_points'   => 'nullable|integer|min:0',
        ]);
        $cart = $this->getCart($request->user()->id);

        $couponData = null;
        if (! empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();
            if ($coupon && $coupon->isValid()) {
                $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);
                $d = $coupon->calculateDiscount($subtotal);
                if ($d > 0) $couponData = ['discount' => $d, 'code' => $coupon->code, 'id' => $coupon->id];
            }
        }

        $maxPoints = LoyaltyService::maxRedeemablePoints($request->user(),
            collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']) - ($couponData['discount'] ?? 0)
        );
        $points = min((int) ($data['loyalty_points'] ?? 0), $maxPoints);

        $totals = $this->computeTotals($cart, $couponData, $points, $data['delivery_zone_id'] ?? null);
        $totals['max_loyalty_points'] = $maxPoints;

        return response()->json(['totals' => $totals]);
    }

    /** POST /api/v1/checkout — place the order */
    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'phone'            => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
            'email'            => 'nullable|email',
            'city'             => 'required|string|max:100',
            'area'             => 'nullable|string|max:100',
            'address_line'     => 'required|string|max:500',
            'building'         => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:1000',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            // Must be a neighbourhood of the chosen city — otherwise the
            // shipment would be addressed to another city's area.
            'roadfn_area_id'   => [
                'required', 'string', 'max:50',
                Rule::exists('roadfn_areas', 'roadfn_area_id')
                    ->where('delivery_zone_id', $request->delivery_zone_id),
            ],
            'coupon_code'      => 'nullable|string',
            'loyalty_points'   => 'nullable|integer|min:0',
            'payment_method'   => 'nullable|in:cod,lahza',
        ], [
            'roadfn_area_id.required' => 'يرجى اختيار الحي من القائمة.',
            'roadfn_area_id.exists'   => 'الحي المختار لا يتبع المدينة المحددة.',
        ]);

        $user = $request->user();
        $cart = $this->getCart($user->id);
        if (empty($cart)) {
            return response()->json(['error' => 'السلة فارغة'], 422);
        }

        $zone = DeliveryZone::findOrFail($data['delivery_zone_id']);

        // Coupon
        $couponData = null;
        if (! empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();
            if ($coupon && $coupon->isValid()) {
                $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);
                $d = $coupon->calculateDiscount($subtotal);
                if ($d > 0) $couponData = ['discount' => $d, 'code' => $coupon->code, 'id' => $coupon->id];
            }
        }

        $subtotal        = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);
        $discount        = $couponData['discount'] ?? 0;

        // Loyalty
        $pointsRedeemed = 0;
        $loyaltyDiscount = 0;
        if (($data['loyalty_points'] ?? 0) > 0) {
            $max = LoyaltyService::maxRedeemablePoints($user, $subtotal - $discount);
            $pointsRedeemed  = min((int) $data['loyalty_points'], $max);
            $loyaltyDiscount = LoyaltyService::pointsToIls($pointsRedeemed);
        }

        $deliveryFee = $zone->calculateFee($subtotal - $discount - $loyaltyDiscount);
        $total = max(0, $subtotal - $discount - $loyaltyDiscount + $deliveryFee);

        try {
        $order = DB::transaction(function () use ($user, $data, $cart, $couponData, $subtotal, $discount, $deliveryFee, $loyaltyDiscount, $pointsRedeemed, $total, $zone) {
            $order = Order::create([
                'user_id'                  => $user->id,
                'customer_name'            => $data['first_name'] . ' ' . $data['last_name'],
                'customer_phone'           => $data['phone'],
                'customer_email'           => $data['email'] ?? $user->email,
                'city'                     => $data['city'],
                'area'                     => $data['area'] ?? null,
                'roadfn_area_id'           => $data['roadfn_area_id'],
                'address_line'             => $data['address_line'],
                'building'                 => $data['building'] ?? null,
                'delivery_notes'           => $data['notes'] ?? null,
                'delivery_zone_id'         => $zone->id,
                'subtotal'                 => $subtotal,
                'delivery_fee'             => $deliveryFee,
                'discount_amount'          => $discount,
                'loyalty_points_redeemed'  => $pointsRedeemed,
                'loyalty_discount'         => $loyaltyDiscount,
                'total'                    => $total,
                'coupon_code'              => $couponData['code'] ?? null,
                'coupon_id'                => $couponData['id']   ?? null,
                'status'                   => 'pending',
                'payment_method'           => $data['payment_method'] ?? 'cod',
            ]);

            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if ($product && $product->track_quantity && $product->stock_quantity < $item['qty']) {
                    throw new \RuntimeException("المنتج \"{$item['name']}\" لا يوجد منه كمية كافية (متبقي: {$product->stock_quantity})");
                }
                $order->items()->create([
                    'product_id'    => $item['product_id'],
                    'variant_id'    => $item['variant_id'] ?? null,
                    'product_name'  => $item['name'],
                    'product_image' => $item['image'] ? str_replace(url('storage/') . '/', '', $item['image']) : null,
                    'price'         => $item['price'],
                    'quantity'      => $item['qty'],
                    'total'         => $item['price'] * $item['qty'],
                ]);
                if ($product && $product->track_quantity) {
                    $product->decrement('stock_quantity', $item['qty']);
                }
            }

            if ($couponData) {
                Coupon::where('id', $couponData['id'])->increment('used_count');
            }
            if ($pointsRedeemed > 0) {
                LoyaltyService::redeemForOrder($user, $order, $pointsRedeemed);
            }

            // Notify admins
            $recipients = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get()
                ->filter(fn ($u) => $u->hasPermission('manage_orders'));
            if ($recipients->isNotEmpty()) {
                FilamentNotification::make()
                    ->title('🛒 طلب جديد #' . $order->order_number)
                    ->body($order->customer_name . ' • ' . number_format($order->total, 2) . ' ₪ • ' . $order->city . ' (تطبيق)')
                    ->icon('heroicon-o-shopping-cart')
                    ->iconColor('warning')
                    ->actions([
                        NotificationAction::make('view')
                            ->label('عرض الطلب')
                            ->url(url('/admin/orders/' . $order->id))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($recipients);
            }

            return $order;
        });
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        // Empty the cart
        Cache::forget($this->key($user->id));

        // Same path as the website, so the two checkouts can't drift on which
        // emails a customer gets. Falls back to the account address itself.
        \App\Services\OrderMailer::sendConfirmation($order);

        // ── Lahza online payment ──
        if (($data['payment_method'] ?? 'cod') === 'lahza') {
            try {
                $lahza       = app(LahzaService::class);
                $callbackUrl = route('checkout.lahza.callback', ['orderNumber' => $order->order_number]);
                $result      = $lahza->initialize($order, $callbackUrl);
                $order->update(['payment_ref' => $result['reference']]);

                return response()->json([
                    'order_number'      => $order->order_number,
                    'order_id'          => $order->id,
                    'total'             => (float) $order->total,
                    'payment_method'    => 'lahza',
                    'authorization_url' => $result['authorization_url'],
                    'message'           => 'أكمل الدفع عبر بوابة لحظة',
                ], 201);
            } catch (\Throwable $e) {
                // Cancel the order
                $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
                return response()->json(['error' => 'فشل تهيئة الدفع: ' . $e->getMessage()], 422);
            }
        }

        return response()->json([
            'order_number'   => $order->order_number,
            'order_id'       => $order->id,
            'total'          => (float) $order->total,
            'payment_method' => $data['payment_method'] ?? 'cod',
            'message'        => 'تم استلام طلبك بنجاح!',
        ], 201);
    }
}
