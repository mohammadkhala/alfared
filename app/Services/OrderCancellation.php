<?php

namespace App\Services;

use App\Models\Order;

/**
 * The rules for cancelling an order, in one place so the website, the app and
 * the admin panel can't disagree about when it's allowed.
 */
class OrderCancellation
{
    public const BY_CUSTOMER = 'customer';
    public const BY_ADMIN    = 'admin';
    public const BY_COURIER  = 'courier';

    /** Statuses a customer may still back out of. */
    private const CUSTOMER_CANCELLABLE = ['pending', 'confirmed', 'processing'];

    /**
     * Whether the customer can still cancel this themselves.
     *
     * The line is dispatch, not status alone: once a shipment exists at the
     * courier there's a parcel physically moving, and cancelling on our side
     * would leave the two systems disagreeing. After that the customer has to
     * go through the admin, who can also cancel it with the courier.
     */
    public static function customerMayCancel(Order $order): bool
    {
        return in_array($order->status, self::CUSTOMER_CANCELLABLE, true)
            && blank($order->roadfn_tracking_number);
    }

    /** Why the customer can't cancel, for showing in the UI. */
    public static function blockedReason(Order $order): ?string
    {
        if (self::customerMayCancel($order)) {
            return null;
        }

        return match (true) {
            $order->status === 'cancelled' => 'الطلب ملغي بالفعل.',
            $order->status === 'returned'  => 'الطلب مُرجَّع.',
            $order->status === 'delivered' => 'تم تسليم الطلب — يمكنك طلب إرجاعه بدلاً من إلغائه.',
            filled($order->roadfn_tracking_number),
            in_array($order->status, ['sent_to_delivery', 'shipped'], true)
                => 'الطلب مع شركة التوصيل — تواصل معنا لإلغائه.',
            default => 'لا يمكن إلغاء هذا الطلب.',
        };
    }

    /**
     * Cancels the order and records who asked for it.
     *
     * Stock restoration, loyalty reversal and the customer notification all run
     * off the status change in Order::booted, so they happen here for free —
     * and identically no matter who triggered the cancellation.
     */
    public static function cancel(Order $order, string $by, ?string $reason = null): bool
    {
        if (in_array($order->status, ['cancelled', 'returned'], true)) {
            return false;
        }

        $order->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancelled_by'        => $by,
            'cancellation_reason' => $reason ? mb_substr($reason, 0, 500) : null,
        ]);

        return true;
    }

    /** Arabic label for the admin panel. */
    public static function byLabel(?string $by): ?string
    {
        return match ($by) {
            self::BY_CUSTOMER => 'ألغاه الزبون',
            self::BY_ADMIN    => 'ألغاه الأدمن',
            self::BY_COURIER  => 'ألغته شركة التوصيل',
            default           => null,
        };
    }
}
