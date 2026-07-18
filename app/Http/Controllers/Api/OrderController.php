<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Wishlist;
use App\Services\LahzaService;
use App\Services\LoyaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15)
            ->through(fn ($o) => $this->formatOrder($o, false));
        return response()->json($orders);
    }

    public function show(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->with('items')
            ->firstOrFail();
        return response()->json(['order' => $this->formatOrder($order, true)]);
    }

    public function track(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();

        // Strip PII for unauthenticated requests; authenticated users see full order
        $user = $request->user('sanctum');
        if ($user && $user->id !== $order->user_id) {
            abort(403);
        }

        $data = $this->formatOrder($order, true);

        // Remove PII from guest responses
        if (! $user) {
            unset($data['customer_name'], $data['customer_phone'], $data['address']);
        }

        return response()->json(['order' => $data]);
    }

    /**
     * POST /api/v1/orders/{orderNumber}/verify-payment
     * Called by the Flutter WebView when user closes Lahza page.
     * Actively queries Lahza to confirm payment and updates our order.
     */
    public function verifyPayment(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Already confirmed — just return current status
        if ($order->payment_status === 'paid') {
            return response()->json(['payment_status' => 'paid']);
        }

        $ref = $order->payment_ref;
        if (! $ref) {
            return response()->json(['payment_status' => $order->payment_status]);
        }

        try {
            $lahza = app(LahzaService::class);
            $data  = $lahza->verify($ref);

            if (($data['status'] ?? '') === 'success') {
                $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'confirmed',
                    'confirmed_at'   => now(),
                ]);
                return response()->json(['payment_status' => 'paid']);
            }

            $order->update(['payment_status' => 'failed']);
            return response()->json(['payment_status' => 'failed']);

        } catch (\Throwable $e) {
            // Lahza API error — return current status without changing it
            return response()->json(['payment_status' => $order->payment_status]);
        }
    }

    /** ── Return request ───────────────────────────────────── */
    public function returnRequest(Request $request, string $orderNumber): JsonResponse
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (! in_array($order->status, ['delivered'])) {
            return response()->json(['error' => 'لا يمكن طلب الإرجاع إلا للطلبات المُسلَّمة'], 422);
        }

        if ($order->return_requested_at) {
            return response()->json(['error' => 'تم إرسال طلب الإرجاع مسبقاً'], 422);
        }

        $order->update([
            'return_requested_at' => now(),
            'return_reason'       => $data['reason'],
            'return_status'       => 'pending',
        ]);

        return response()->json(['message' => 'تم إرسال طلب الإرجاع بنجاح. سنتواصل معك قريباً.']);
    }

    /** ── Wishlist ────────────────────────────────────────── */
    public function wishlist(Request $request): JsonResponse
    {
        $items = Wishlist::where('user_id', $request->user()->id)
            ->with('product:id,name_ar,name_en,name_he,slug,main_image,price,compare_price,stock_quantity')
            ->latest()
            ->get()
            ->filter(fn ($w) => $w->product)
            ->map(fn ($w) => [
                'id'             => $w->product->id,
                'name'           => $w->product->name_ar,
                'slug'           => $w->product->slug,
                'image'          => $w->product->main_image ? (str_starts_with($w->product->main_image, 'http') ? $w->product->main_image : url('storage/' . $w->product->main_image)) : null,
                'price'          => (float) $w->product->price,
                'compare_price'  => $w->product->compare_price ? (float) $w->product->compare_price : null,
                'in_stock'       => $w->product->stock_quantity > 0,
            ])
            ->values();
        return response()->json(['items' => $items]);
    }

    public function wishlistToggle(Request $request): JsonResponse
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();
        if ($existing) {
            $existing->delete();
            return response()->json(['added' => false]);
        }
        Wishlist::create([
            'user_id'    => $request->user()->id,
            'product_id' => $request->product_id,
        ]);
        return response()->json(['added' => true]);
    }

    /** ── Loyalty ────────────────────────────────────────── */
    public function loyalty(Request $request): JsonResponse
    {
        $user = $request->user();
        $cfg = LoyaltyService::config();

        $history = LoyaltyTransaction::where('user_id', $user->id)
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($tx) => [
                'id'        => $tx->id,
                'points'    => (int) $tx->points,
                'action'    => $tx->action,
                'action_label' => LoyaltyTransaction::$actionLabels[$tx->action] ?? $tx->action,
                'note'      => $tx->note,
                'date'      => $tx->created_at?->format('Y-m-d'),
                'date_human'=> $tx->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'balance'         => (int) $user->loyalty_points,
            'value_ils'       => LoyaltyService::pointsToIls((int) $user->loyalty_points),
            'config'          => $cfg,
            'history'         => $history,
        ]);
    }

    protected function formatOrder(Order $order, bool $detailed): array
    {
        $base = [
            'order_number'    => $order->order_number,
            'status'          => $order->status,
            'status_label'    => Order::$statusLabels[$order->status] ?? $order->status,
            'subtotal'        => (float) $order->subtotal,
            'delivery_fee'    => (float) $order->delivery_fee,
            'discount_amount' => (float) $order->discount_amount,
            'loyalty_discount'=> (float) ($order->loyalty_discount ?? 0),
            'total'           => (float) $order->total,
            'created_at'      => $order->created_at,
            'created_at_human'=> $order->created_at?->diffForHumans(),
            'items_count'     => $order->items()->count(),
        ];

        if (! $detailed) {
            return $base;
        }

        return array_merge($base, [
            'customer_name'        => $order->customer_name,
            'customer_phone'       => $order->customer_phone,
            'city'                 => $order->city,
            'area'                 => $order->area,
            'address_line'         => $order->address_line,
            'building'             => $order->building,
            'delivery_notes'       => $order->delivery_notes,
            'payment_method'       => $order->payment_method,
            'payment_status'       => $order->payment_status,
            'coupon_code'          => $order->coupon_code,
            'return_requested_at'  => $order->return_requested_at?->toIso8601String(),
            'return_reason'        => $order->return_reason,
            'return_status'        => $order->return_status,
            'return_reject_reason' => $order->return_reject_reason,
            'roadfn_tracking_number' => $order->roadfn_tracking_number,
            'roadfn_status'          => $order->roadfn_status,
            'items'          => $order->items->map(fn ($i) => [
                'id'           => (int) $i->id,
                'product_id'   => (int) $i->product_id,
                'product_name' => $i->product_name,
                'product_image'=> $i->product_image ? (str_starts_with($i->product_image, 'http') ? $i->product_image : url('storage/' . $i->product_image)) : null,
                'price'        => (float) $i->price,
                'quantity'     => (int) $i->quantity,
                'total'        => (float) $i->total,
                'variant_info' => $i->variant_info,
            ])->all(),
        ]);
    }
}
