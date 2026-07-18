<?php

namespace App\Support;

use App\Models\Order;

/**
 * Decides who may see an order's detail pages.
 *
 * Order numbers run in sequence (ORD-2026-0036), so a bare order number in the
 * URL is guessable: without this gate anyone could walk the range and read
 * every customer's name, phone, address and basket.
 *
 * Access is granted to the signed-in owner, to staff who manage orders, and to
 * a guest session that has proven it knows the order — either by having just
 * placed it, or by passing the phone check on the tracking page.
 */
class OrderAccess
{
    private const KEY = 'viewable_orders';

    /** Remembers, for this session only, that the visitor may view this order. */
    public static function grant(Order $order): void
    {
        $ids   = session(self::KEY, []);
        $ids[] = $order->id;

        session([self::KEY => array_values(array_unique($ids))]);
    }

    public static function allows(Order $order): bool
    {
        if ($user = auth()->user()) {
            if ((int) $order->user_id === (int) $user->id) {
                return true;
            }
            if (method_exists($user, 'hasPermission') && $user->hasPermission('manage_orders')) {
                return true;
            }
        }

        return in_array($order->id, session(self::KEY, []), true);
    }

    public static function authorize(Order $order): void
    {
        abort_unless(self::allows($order), 403, 'لا تملك صلاحية عرض هذا الطلب.');
    }

    /**
     * Whether a typed phone belongs to this order.
     *
     * Compares the last 9 digits so 0594513978, +970594513978 and 970-59-451-3978
     * all match the same customer.
     */
    public static function phoneMatches(Order $order, string $phone): bool
    {
        $tail = fn (?string $p) => substr(preg_replace('/\D+/', '', (string) $p), -9);

        $given = $tail($phone);

        return strlen($given) === 9 && $given === $tail($order->customer_phone);
    }
}
