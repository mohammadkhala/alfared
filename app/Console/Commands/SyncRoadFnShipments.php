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

        // One precise call for all shipments we track by internal id.
        try {
            $ids     = $orders->pluck('roadfn_shipment_id')->filter()->values()->all();
            $records = collect($roadFn->shipmentsByIds($ids))->keyBy('ID');
        } catch (\Throwable $e) {
            Log::error('RoadFN sync: failed to fetch shipments', ['error' => $e->getMessage()]);
            $this->error('فشل جلب الشحنات من RoadFN: ' . $e->getMessage());
            return self::FAILURE;
        }

        foreach ($orders as $order) {
            $record = $records->get($order->roadfn_shipment_id);
            if (! $record) {
                Log::warning('RoadFN sync: shipment not returned by RoadFN', [
                    'order'    => $order->order_number,
                    'shipment' => $order->roadfn_shipment_id,
                    'tracking' => $order->roadfn_tracking_number,
                ]);
                continue;
            }
            $roadFn->applyShipmentRecord($order, $record);
        }

        $this->info('تمت المزامنة.');
        return self::SUCCESS;
    }
}
