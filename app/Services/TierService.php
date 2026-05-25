<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

/**
 * Customer spending tiers (5 levels).
 *
 * Thresholds are read from settings (group=tiers) so the admin can change them.
 * Defaults (₪): bronze=200, silver=500, gold=1000, vip=2000.
 */
class TierService
{
    public const DEFAULT_BRONZE = 200;
    public const DEFAULT_SILVER = 500;
    public const DEFAULT_GOLD   = 1000;
    public const DEFAULT_VIP    = 2000;

    /** Returns the five tier definitions with live thresholds from settings. */
    public static function tiers(): array
    {
        $bronze = (int) Setting::get('tier_min_bronze', self::DEFAULT_BRONZE);
        $silver = (int) Setting::get('tier_min_silver', self::DEFAULT_SILVER);
        $gold   = (int) Setting::get('tier_min_gold',   self::DEFAULT_GOLD);
        $vip    = (int) Setting::get('tier_min_vip',    self::DEFAULT_VIP);

        // Enforce ascending order regardless of what admin entered
        $bronze = max(1,          $bronze);
        $silver = max($bronze + 1, $silver);
        $gold   = max($silver + 1, $gold);
        $vip    = max($gold   + 1, $vip);

        return [
            ['key' => 'new',    'label' => 'جديد',   'emoji' => '🎯', 'color' => 'gray',    'min' => 0],
            ['key' => 'bronze', 'label' => 'برونزي', 'emoji' => '🥉', 'color' => 'warning', 'min' => $bronze],
            ['key' => 'silver', 'label' => 'فضي',    'emoji' => '🥈', 'color' => 'info',    'min' => $silver],
            ['key' => 'gold',   'label' => 'ذهبي',   'emoji' => '🥇', 'color' => 'warning', 'min' => $gold],
            ['key' => 'vip',    'label' => 'VIP',    'emoji' => '💎', 'color' => 'success', 'min' => $vip],
        ];
    }

    /** Compute the user's total spent (non-cancelled/returned orders). */
    public static function totalSpent(User $user): float
    {
        return (float) $user->orders()
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('total');
    }

    /** Return full tier data array for a given spend amount. */
    public static function tierForAmount(float $spent): array
    {
        $current = self::tiers()[0];
        foreach (self::tiers() as $tier) {
            if ($spent >= $tier['min']) {
                $current = $tier;
            }
        }
        return $current;
    }

    /** Return the next tier (null if already VIP). */
    public static function nextTierForAmount(float $spent): ?array
    {
        foreach (self::tiers() as $tier) {
            if ($tier['min'] > $spent) {
                return $tier;
            }
        }
        return null;
    }

    /** Full tier summary for a user — used in API responses. */
    public static function summary(User $user): array
    {
        $spent   = self::totalSpent($user);
        $current = self::tierForAmount($spent);
        $next    = self::nextTierForAmount($spent);

        $amountToNext = $next ? max(0, $next['min'] - $spent) : 0;
        $rangeStart   = $current['min'];
        $rangeEnd     = $next ? $next['min'] : $current['min'];
        $progress     = $rangeEnd > $rangeStart
            ? min(1.0, ($spent - $rangeStart) / ($rangeEnd - $rangeStart))
            : 1.0;

        return [
            'total_spent'      => round($spent, 2),
            'tier_key'         => $current['key'],
            'tier_label'       => $current['label'],
            'tier_emoji'       => $current['emoji'],
            'next_tier_label'  => $next['label'] ?? null,
            'next_tier_emoji'  => $next['emoji'] ?? null,
            'next_tier_min'    => $next['min']    ?? null,
            'amount_to_next'   => round($amountToNext, 2),
            'tier_progress'    => round($progress, 4),
        ];
    }
}
