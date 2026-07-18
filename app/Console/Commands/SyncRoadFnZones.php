<?php

namespace App\Console\Commands;

use App\Models\DeliveryZone;
use App\Models\RoadFnArea;
use App\Services\RoadFnService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Rebuilds delivery zones from RoadFN's own price list, so the destinations
 * a customer can pick and the fee they pay always match the courier.
 *
 * RoadFN is flat: the merchant ships from one branch to a list of destination
 * cities, one fee each. Each destination becomes one active DeliveryZone.
 * Zones RoadFN doesn't serve (e.g. غزة) are deactivated, never deleted, so
 * existing orders keep their zone reference.
 */
class SyncRoadFnZones extends Command
{
    protected $signature = 'roadfn:sync-zones';

    protected $description = 'بناء مناطق التوصيل وأسعارها من قائمة أسعار RoadFN';

    public function handle(RoadFnService $roadFn): int
    {
        $fees = collect($roadFn->getShippingFees());

        if ($fees->isEmpty()) {
            $this->error('RoadFN لم يُرجع أي أسعار توصيل.');
            return self::FAILURE;
        }

        // One fee row per destination city (dedupe just in case).
        $destinations = $fees->unique('ToCityId')->values();
        $this->info("مزامنة {$destinations->count()} وجهة من RoadFN...");

        // Fetch each city's areas up front (network) so the DB transaction stays short.
        $areasByCity = [];
        foreach ($destinations as $d) {
            $cityId = (string) $d['ToCityId'];
            $areasByCity[$cityId] = $roadFn->getAreas($cityId);
        }

        DB::transaction(function () use ($destinations, $roadFn, $areasByCity) {
            // RoadFN is the source of truth: reset, then reactivate what it serves.
            DeliveryZone::query()->update(['is_active' => false]);

            foreach ($destinations as $i => $d) {
                $cityId = (string) $d['ToCityId'];
                $areas  = $areasByCity[$cityId] ?? [];

                $zone = DeliveryZone::where('roadfn_city_id', $cityId)->orderBy('id')->first()
                    ?? new DeliveryZone(['roadfn_city_id' => $cityId]);

                $zone->name_ar        = trim($d['ToCity']);
                $zone->base_fee       = $d['Fees'];
                // Delivery price = RoadFN's. Free-shipping is a store promo, off by
                // default here — the admin can set a threshold per city afterwards.
                $zone->free_above     = null;
                $zone->roadfn_city_id = $cityId;
                $zone->parent_id      = null;
                $zone->is_active      = true;
                // Only resolve the fallback area for rows that don't already have one —
                // keeps an admin override, and the customer's picked area wins anyway.
                if (! $zone->roadfn_area_id) {
                    $zone->roadfn_area_id = $roadFn->pickDefaultAreaId($areas);
                }
                if (! $zone->exists) {
                    $zone->sort_order = $i;
                }
                $zone->save();

                $this->syncAreas($zone, $areas);

                $this->line("✓ {$zone->name_ar} — {$zone->base_fee} ₪ (RoadFN {$cityId}) — " . count($areas) . " منطقة فرعية");
            }
        });

        $deactivated = DeliveryZone::where('is_active', false)->pluck('name_ar');
        if ($deactivated->isNotEmpty()) {
            $this->warn('مناطق مُعطّلة (لا يخدمها RoadFN أو مكرّرة): ' . $deactivated->implode('، '));
        }

        $this->info('اكتملت المزامنة.');
        return self::SUCCESS;
    }

    /** Upserts a city's neighborhoods and prunes any RoadFN no longer returns. */
    private function syncAreas(DeliveryZone $zone, array $areas): void
    {
        $keep = [];
        foreach ($areas as $i => $a) {
            $areaId = (string) $a['Id'];
            $keep[] = $areaId;
            RoadFnArea::updateOrCreate(
                ['delivery_zone_id' => $zone->id, 'roadfn_area_id' => $areaId],
                ['name_ar' => trim($a['AreaName'] ?? ''), 'sort_order' => $i],
            );
        }

        RoadFnArea::where('delivery_zone_id', $zone->id)
            ->whereNotIn('roadfn_area_id', $keep ?: ['__none__'])
            ->delete();
    }
}
