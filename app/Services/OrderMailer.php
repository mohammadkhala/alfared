<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Decides when a customer hears from us about their order, and in which
 * language. Both web and app checkout go through here so the two can't drift.
 */
class OrderMailer
{
    /** Sent the moment the order is placed — a receipt of intent, not of sale. */
    public static function sendConfirmation(Order $order): bool
    {
        $to = self::recipient($order);
        if (! $to) {
            return false;
        }

        return self::dispatch($order, $to, new OrderConfirmationMail($order->loadMissing('items')), 'confirmation');
    }

    /**
     * The invoice, sent once the money is actually ours: on delivery for cash
     * orders, on payment for card ones.
     *
     * Stamps invoice_sent_at so a card order — which is paid and later
     * delivered — doesn't get invoiced twice, and neither does an order whose
     * status an admin nudges back and forth.
     */
    public static function sendInvoice(Order $order): bool
    {
        if ($order->invoice_sent_at) {
            return false;
        }

        $to = self::recipient($order);
        if (! $to) {
            return false;
        }

        $order->loadMissing(['items', 'deliveryZone.parent']);

        $sent = self::dispatch($order, $to, new OrderInvoiceMail($order, self::lang($order)), 'invoice');

        if ($sent) {
            // saveQuietly: this must not re-enter the model's updated hooks and
            // set off another round of notifications.
            $order->forceFill(['invoice_sent_at' => now()])->saveQuietly();
        }

        return $sent;
    }

    /**
     * Guest checkout leaves the email optional, so fall back to the account's
     * address before giving up.
     */
    private static function recipient(Order $order): ?string
    {
        $email = $order->customer_email ?: $order->user?->email;

        return filter_var((string) $email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    /**
     * Resolved here rather than inside the queued job: this runs during the
     * request, where the locale is still the customer's. By the time the worker
     * picks the job up the locale is back to the store default.
     */
    private static function lang(Order $order): string
    {
        $lang = app()->getLocale();

        return in_array($lang, ['ar', 'he', 'en'], true) ? $lang : 'ar';
    }

    private static function dispatch(Order $order, string $to, $mailable, string $kind): bool
    {
        try {
            // Queued, and drained by the scheduled queue:work. Sending inline
            // would put an SMTP round-trip on the checkout request.
            Mail::to($to)->queue($mailable);

            return true;
        } catch (\Throwable $e) {
            // A mail failure must never lose the order or block a status change.
            Log::error("Order {$kind} email failed", [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
