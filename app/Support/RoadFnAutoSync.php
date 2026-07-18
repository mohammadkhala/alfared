<?php

namespace App\Support;

use App\Models\Order;
use App\Services\RoadFnService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Refreshes open RoadFN shipments while someone is looking at the orders list.
 *
 * The scheduled roadfn:sync-shipments is the proper mechanism, but it only ever
 * runs if cron is calling schedule:run — which shared hosting doesn't always
 * have configured. Without it a shipment cancelled at RoadFN sits stale until
 * an admin presses refresh by hand.
 *
 * This is a safety net, not a replacement: it only fires when an admin loads
 * the page, so cron is still what keeps the customer's view current overnight.
 */
class RoadFnAutoSync
{
    private const STAMP    = 'roadfn:auto_sync_at';
    private const LOCK     = 'roadfn:auto_sync_lock';
    private const INTERVAL = 600;   // 10 minutes between attempts

    public static function runIfDue(): void
    {
        if (! self::isDue()) {
            return;
        }

        // A single admin opening two tabs shouldn't fire two syncs, and a slow
        // RoadFN response shouldn't pile requests up behind each other.
        $lock = Cache::lock(self::LOCK, 120);

        if (! $lock->get()) {
            return;
        }

        try {
            // Stamp first: if RoadFN is down, back off for the full interval
            // instead of retrying on every single page load.
            Cache::put(self::STAMP, now()->timestamp, self::INTERVAL * 3);

            self::sync();
        } catch (\Throwable $e) {
            // Never let a courier outage take the admin panel down with it.
            Log::warning('RoadFN auto-sync failed', ['error' => $e->getMessage()]);
        } finally {
            $lock->release();
        }
    }

    private static function isDue(): bool
    {
        $last = Cache::get(self::STAMP);

        return $last === null || (now()->timestamp - (int) $last) >= self::INTERVAL;
    }

    private static function sync(): void
    {
        $orders = Order::whereNotNull('roadfn_tracking_number')
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        $roadFn  = app(RoadFnService::class);
        $records = collect($roadFn->shipmentsByIds(
            $orders->pluck('roadfn_shipment_id')->filter()->values()->all()
        ))->keyBy('ID');

        // Only pay for the full list when something actually needs it.
        $needsTracking = $orders->filter(fn ($o) => blank($o->roadfn_shipment_id));
        $byTracking    = $needsTracking->isEmpty()
            ? collect()
            : collect($roadFn->listShipments())->keyBy('ShipmentTrackingNo');

        foreach ($orders as $order) {
            $record = filled($order->roadfn_shipment_id)
                ? $records->get($order->roadfn_shipment_id)
                : $byTracking->get($order->roadfn_tracking_number);

            if (! $record) {
                continue;
            }

            if (blank($order->roadfn_shipment_id) && isset($record['ID'])) {
                $order->update(['roadfn_shipment_id' => $record['ID']]);
            }

            $roadFn->applyShipmentRecord($order, $record);
        }
    }
}
