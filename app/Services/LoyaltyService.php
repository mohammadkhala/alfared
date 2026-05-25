<?php

namespace App\Services;

use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Central place for all loyalty-points logic. Reads its configuration from the
 * `settings` table so admins can tweak rates live without code changes.
 *
 * Settings (group=loyalty):
 *  - loyalty_enabled            (bool, default true)
 *  - earn_amount_per_point      (decimal, ₪ spent to earn 1 point — default 10)
 *  - points_per_redeem_ils      (int, points needed for 1 ₪ discount — default 20)
 *  - min_redeem_points          (int, minimum points to redeem in one order — default 100)
 *  - max_redeem_percent         (int, cap on discount % of subtotal — default 50)
 *  - earn_on_status             (string, when points are credited — default "delivered")
 */
class LoyaltyService
{
    public static function enabled(): bool
    {
        return (bool) Setting::get('loyalty_enabled', true);
    }

    public static function config(): array
    {
        return [
            'enabled'              => self::enabled(),
            'earn_amount_per_point'=> (float) Setting::get('earn_amount_per_point', 10),
            'points_per_redeem_ils'=> max(1, (int) Setting::get('points_per_redeem_ils', 20)),
            'min_redeem_points'    => max(0, (int) Setting::get('min_redeem_points', 100)),
            'max_redeem_percent'   => max(0, min(100, (int) Setting::get('max_redeem_percent', 50))),
            'earn_on_status'       => (string) Setting::get('earn_on_status', 'delivered'),

            // ── advanced ──
            'first_order_bonus'    => max(0, (int) Setting::get('first_order_bonus', 0)),       // extra points on first delivered order
            'signup_bonus'         => max(0, (int) Setting::get('signup_bonus', 0)),            // bonus on registration
            'include_delivery'     => (bool) Setting::get('include_delivery_in_earn', false),   // count delivery_fee in earning base?
            'include_discount'     => (bool) Setting::get('include_discount_in_earn', false),   // count discount-reduced subtotal?
            'expiry_days'          => max(0, (int) Setting::get('points_expiry_days', 0)),      // 0 = never expires
            'multiplier'           => max(0.1, (float) Setting::get('earn_multiplier', 1.0)),   // global multiplier for promotions
        ];
    }

    /**
     * Calculate points earned from an order. Optionally takes the full order
     * so it can apply include_delivery/include_discount/multiplier rules.
     */
    public static function calculateEarn(float $subtotal, ?Order $order = null): int
    {
        $cfg = self::config();
        if (! $cfg['enabled'] || $cfg['earn_amount_per_point'] <= 0) return 0;

        $base = $subtotal;
        if ($order) {
            // apply discount-aware base?
            if ($cfg['include_discount']) {
                $base = max(0, $subtotal - (float) $order->discount_amount - (float) $order->loyalty_discount);
            }
            // add delivery to earning base?
            if ($cfg['include_delivery']) {
                $base += (float) $order->delivery_fee;
            }
        }

        $points = floor($base / $cfg['earn_amount_per_point']);
        $points = $points * $cfg['multiplier'];

        return (int) max(0, floor($points));
    }

    /** Convert a number of points to its ₪ value. */
    public static function pointsToIls(int $points): float
    {
        $cfg = self::config();
        return round($points / max(1, $cfg['points_per_redeem_ils']), 2);
    }

    /**
     * Given a user and the order subtotal, return the max points they can
     * actually redeem on this order (respecting balance + min + max% caps).
     */
    public static function maxRedeemablePoints(User $user, float $subtotal): int
    {
        $cfg = self::config();
        if (! $cfg['enabled']) return 0;

        $balance = max(0, (int) $user->loyalty_points);
        if ($balance < $cfg['min_redeem_points']) return 0;

        // cap by subtotal × max_redeem_percent
        $maxIls    = $subtotal * ($cfg['max_redeem_percent'] / 100);
        $maxPoints = (int) floor($maxIls * $cfg['points_per_redeem_ils']);

        return max(0, min($balance, $maxPoints));
    }

    /** Credit points for a delivered order. Idempotent (won't double-credit). */
    public static function awardForOrder(Order $order): ?LoyaltyTransaction
    {
        $cfg = self::config();
        if (! $cfg['enabled']) return null;
        if (! $order->user_id) return null;

        // Avoid double-crediting
        $exists = LoyaltyTransaction::where('order_id', $order->id)
            ->where('action', 'earned')->exists();
        if ($exists) return null;

        $points = self::calculateEarn((float) $order->subtotal, $order);

        // ── First-order bonus ──
        $isFirstOrder = $cfg['first_order_bonus'] > 0
            && Order::where('user_id', $order->user_id)
                ->where('id', '!=', $order->id)
                ->where('status', $cfg['earn_on_status'])
                ->doesntExist();
        if ($isFirstOrder) {
            $points += $cfg['first_order_bonus'];
        }

        if ($points <= 0) return null;

        return DB::transaction(function () use ($order, $points, $isFirstOrder) {
            $user = User::find($order->user_id);
            if (! $user) return null;

            $user->increment('loyalty_points', $points);

            $note = 'Order #' . $order->order_number;
            if ($isFirstOrder) $note .= ' (+first-order bonus)';

            return LoyaltyTransaction::create([
                'user_id'  => $user->id,
                'order_id' => $order->id,
                'points'   => $points,
                'action'   => 'earned',
                'note'     => $note,
            ]);
        });
    }

    /** Deduct points used as redemption on an order. */
    public static function redeemForOrder(User $user, Order $order, int $points): ?LoyaltyTransaction
    {
        if ($points <= 0) return null;
        if ($user->loyalty_points < $points) return null;

        return DB::transaction(function () use ($user, $order, $points) {
            $user->decrement('loyalty_points', $points);

            return LoyaltyTransaction::create([
                'user_id'   => $user->id,
                'order_id'  => $order->id,
                'points'    => -$points,
                'value_ils' => self::pointsToIls($points),
                'action'    => 'redeemed',
                'note'      => 'Order #' . $order->order_number,
            ]);
        });
    }

    /** Admin manual adjustment. $delta can be negative. */
    public static function adminAdjust(User $user, int $delta, ?string $note = null): LoyaltyTransaction
    {
        return DB::transaction(function () use ($user, $delta, $note) {
            // Don't let balance go below zero on a deduction
            if ($delta < 0) {
                $delta = -min(abs($delta), (int) $user->loyalty_points);
            }

            $user->increment('loyalty_points', $delta);

            return LoyaltyTransaction::create([
                'user_id'  => $user->id,
                'admin_id' => auth()->id(),
                'points'   => $delta,
                'action'   => $delta >= 0 ? 'admin_add' : 'admin_deduct',
                'note'     => $note,
            ]);
        });
    }
}
