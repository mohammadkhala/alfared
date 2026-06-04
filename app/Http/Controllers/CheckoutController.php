<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DeliveryZone;
use App\Models\Coupon;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Services\LahzaService;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        $zones    = DeliveryZone::where('is_active', true)->get();
        $coupon   = session('coupon');
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $discount = $coupon ? $coupon['discount'] : 0;

        // ── Loyalty ──
        $loyaltyCfg     = LoyaltyService::config();
        $loyaltyBalance = (int) (auth()->user()->loyalty_points ?? 0);
        $loyaltyMax     = auth()->check() ? LoyaltyService::maxRedeemablePoints(auth()->user(), $subtotal - $discount) : 0;

        // ── Payment methods from admin settings ──
        $codEnabled            = Setting::get('cod_enabled', '1') === '1';
        $onlinePaymentEnabled  = Setting::get('online_payment_enabled', '0') === '1';

        return view('checkout.index', compact(
            'cart', 'zones', 'coupon', 'subtotal', 'discount',
            'loyaltyCfg', 'loyaltyBalance', 'loyaltyMax',
            'codEnabled', 'onlinePaymentEnabled'
        ));
    }

    public function getDeliveryFee(Request $request)
    {
        $zone     = DeliveryZone::find($request->zone_id);
        $subtotal = (float) $request->subtotal;
        $fee      = $zone ? $zone->calculateFee($subtotal) : 0;

        return response()->json(['fee' => $fee, 'formatted' => number_format($fee, 2) . ' ₪']);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'phone'            => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
            'city'             => 'required|string|max:100',
            'address_line'     => 'required|string|max:500',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'payment_method'   => 'nullable|in:cod,lahza',
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972 ويتبعه 8 إلى 10 أرقام.',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        $zone     = DeliveryZone::findOrFail($request->delivery_zone_id);
        $coupon   = session('coupon');
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $discount = $coupon ? $coupon['discount'] : 0;

        // ── Loyalty redemption ──
        $pointsToRedeem  = 0;
        $loyaltyDiscount = 0.0;
        if (auth()->check() && (int) $request->input('loyalty_points', 0) > 0) {
            $requested = (int) $request->input('loyalty_points');
            $maxAllowed = LoyaltyService::maxRedeemablePoints(auth()->user(), $subtotal - $discount);
            $pointsToRedeem  = max(0, min($requested, $maxAllowed));
            $loyaltyDiscount = LoyaltyService::pointsToIls($pointsToRedeem);
        }

        $delivery = $zone->calculateFee($subtotal - $discount - $loyaltyDiscount);
        $total    = max(0, $subtotal - $discount - $loyaltyDiscount + $delivery);

        $paymentMethod = $request->input('payment_method', 'cod');

        DB::transaction(function () use ($request, $cart, $zone, $coupon, $subtotal, $discount, $delivery, $total, $pointsToRedeem, $loyaltyDiscount, $paymentMethod) {
            $order = Order::create([
                'customer_name'    => $request->first_name . ' ' . $request->last_name,
                'customer_phone'   => $request->phone,
                'customer_email'   => $request->email,
                'city'             => $request->city,
                'area'             => $request->area,
                'address_line'     => $request->address_line,
                'building'         => $request->building,
                'delivery_notes'   => $request->notes,
                'delivery_zone_id' => $zone->id,
                'subtotal'         => $subtotal,
                'delivery_fee'     => $delivery,
                'discount_amount'  => $discount,
                'loyalty_points_redeemed' => $pointsToRedeem,
                'loyalty_discount'        => $loyaltyDiscount,
                'total'            => $total,
                'coupon_code'      => $coupon['code'] ?? null,
                'coupon_id'        => $coupon['id'] ?? null,
                'user_id'          => auth()->id(),
                'status'           => 'pending',
                'payment_method'   => $paymentMethod,
                'payment_status'   => 'pending',
            ]);

            // ── Deduct points used as redemption ──
            if ($pointsToRedeem > 0 && auth()->check()) {
                LoyaltyService::redeemForOrder(auth()->user(), $order, $pointsToRedeem);
            }

            foreach ($cart as $item) {
                $order->items()->create([
                    'product_id'   => $item['product_id'],
                    'variant_id'   => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'product_image'=> $item['image'],
                    'variant_info' => $item['variant'],
                    'price'        => $item['price'],
                    'quantity'     => $item['qty'],
                    'total'        => $item['price'] * $item['qty'],
                ]);

                // Reduce stock
                Product::where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['qty']);
            }

            // Mark coupon used
            if ($coupon) {
                Coupon::where('id', $coupon['id'])->increment('used_count');
            }

            // For COD: clear cart immediately. For Lahza: clear only after successful redirect.
            if ($paymentMethod !== 'lahza') {
                session()->forget(['cart', 'coupon', 'delivery_fee']);
            }
            session(['last_order' => $order->id]);

            // ─── Notify all admin/staff with manage_orders permission ───
            $recipients = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get()
                ->filter(fn($u) => $u->hasPermission('manage_orders'));

            if ($recipients->isNotEmpty()) {
                FilamentNotification::make()
                    ->title('🛒 طلب جديد #' . $order->order_number)
                    ->body($order->customer_name . ' • ' . number_format($order->total, 2) . ' ₪ • ' . $order->city)
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
        });

        $order = Order::find(session('last_order'));

        // ── Lahza online payment ──
        if ($paymentMethod === 'lahza') {
            try {
                $lahza       = app(LahzaService::class);
                $callbackUrl = route('checkout.lahza.callback', ['orderNumber' => $order->order_number]);
                $result      = $lahza->initialize($order, $callbackUrl);

                // Save reference then clear cart (payment page is opening)
                $order->update(['payment_ref' => $result['reference']]);
                session()->forget(['cart', 'coupon', 'delivery_fee']);

                return redirect($result['authorization_url']);
            } catch (\Throwable $e) {
                // Initialization failed — cancel order, keep cart intact so user can retry
                $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
                session()->forget('last_order');
                return redirect()->route('checkout.index')
                                 ->with('error', 'تعذّر الاتصال ببوابة الدفع. يرجى المحاولة مجدداً أو اختيار الدفع عند الاستلام.');
            }
        }

        return redirect()->route('checkout.success', $order->order_number);
    }

    /**
     * Lahza redirects here after payment (success or failure).
     */
    public function lahzaCallback(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Already processed (webhook may have fired first)
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $orderNumber);
        }

        $reference = $request->query('reference') ?? $order->payment_ref;

        if (! $reference) {
            return redirect()->route('checkout.failed', $orderNumber);
        }

        try {
            $lahza = app(LahzaService::class);
            $data  = $lahza->verify($reference);

            if (($data['status'] ?? '') === 'success') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_ref'    => $reference,
                    'status'         => 'confirmed',
                    'confirmed_at'   => now(),
                ]);

                // Notify admins
                $recipients = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get()
                    ->filter(fn ($u) => $u->hasPermission('manage_orders'));

                if ($recipients->isNotEmpty()) {
                    FilamentNotification::make()
                        ->title('💳 دفع مكتمل — ' . $order->order_number)
                        ->body($order->customer_name . ' • ' . number_format($order->total, 2) . ' ₪ — تم الدفع عبر لحظة')
                        ->icon('heroicon-o-credit-card')
                        ->iconColor('success')
                        ->actions([
                            NotificationAction::make('view')
                                ->label('عرض الطلب')
                                ->url(url('/admin/orders/' . $order->id))
                                ->markAsRead(),
                        ])
                        ->sendToDatabase($recipients);
                }

                return redirect()->route('checkout.success', $orderNumber);
            } else {
                $order->update(['payment_status' => 'failed']);
                return redirect()->route('checkout.failed', $orderNumber);
            }
        } catch (\Throwable $e) {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('checkout.failed', $orderNumber)
                             ->with('error', $e->getMessage());
        }
    }

    /**
     * Payment failed page.
     */
    public function paymentFailed(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();
        return view('checkout.payment-failed', compact('order'));
    }

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();
        return view('checkout.success', compact('order'));
    }

    public function tracking(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->firstOrFail();
        return view('checkout.tracking', compact('order'));
    }
}
