<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\RoadFnService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Pulls the current status of every open RoadFN shipment and reflects it
 * back onto the order. RoadFN has no documented webhook, so this polls.
 *
 * Status mapping lives entirely in RoadFnService::applyShipmentRecord /
 * config('services.roadfn.status_map'); this command only decides *which*
 * orders to refresh and hands each record over.
 */
class SyncRoadFnShipments extends Command
{
    protected $signature = 'roadfn:sync-shipments';

    protected $description = 'مزامنة حالة شحنات RoadFN المفتوحة مع حالة الطلب عندنا';

    public function handle(RoadFnService $roadFn): int
    {
        $orders = Order::whereNotNull('roadfn_tracking_number')
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('لا توجد شحنات مفتوحة للمزامنة.');
            return self::SUCCESS;
        }

        $this->info("مزامنة {$orders->count()} شحنة مفتوحة...");

        // createShipment resolves the internal id by searching the recent-shipment
        // list right after creating, which comes back empty if RoadFN hasn't
        // indexed it yet. Those orders keep a tracking number but no id, and
        // since this sync keys on the id they would never update again — the
        // customer would sit on a stale status forever. Recover them by tracking
        // number and backfill the id so later runs take the fast path.
        [$byId, $byTracking] = $orders->partition(fn ($o) => filled($o->roadfn_shipment_id));

        try {
            $records = collect($roadFn->shipmentsByIds(
                $byId->pluck('roadfn_shipment_id')->values()->all()
            ))->keyBy('ID');

            $trackingRecords = $byTracking->isEmpty()
                ? collect()
                : collect($roadFn->listShipments())->keyBy('ShipmentTrackingNo');
        } catch (\Throwable $e) {
            Log::error('RoadFN sync: failed to fetch shipments', ['error' => $e->getMessage()]);
            $this->error('فشل جلب الشحنات من RoadFN: ' . $e->getMessage());
            return self::FAILURE;
        }

        $synced = 0;
        $healed = 0;

        foreach ($orders as $order) {
            $record = filled($order->roadfn_shipment_id)
                ? $records->get($order->roadfn_shipment_id)
                : $trackingRecords->get($order->roadfn_tracking_number);

            if (! $record) {
                Log::warning('RoadFN sync: shipment not returned by RoadFN', [
                    'order'    => $order->order_number,
                    'shipment' => $order->roadfn_shipment_id,
                    'tracking' => $order->roadfn_tracking_number,
                ]);
                $this->warn("  ⚠️ {$order->order_number}: لم تُرجعه RoadFN");
                continue;
            }

            if (blank($order->roadfn_shipment_id) && isset($record['ID'])) {
                $order->update(['roadfn_shipment_id' => $record['ID']]);
                $this->line("  ↻ {$order->order_number}: استُرجع المعرّف {$record['ID']}");
                $healed++;
            }

            $roadFn->applyShipmentRecord($order, $record);
            $synced++;
        }

        $this->newLine();
        $this->info("تمت مزامنة {$synced} شحنة." . ($healed ? " (استُرجع معرّف {$healed} منها)" : ''));

        return self::SUCCESS;
    }
}
