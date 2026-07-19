<?php

namespace App\Console\Commands;

use App\Models\DeliveryZone;
use App\Models\RoadFnArea;
use App\Services\RoadFnService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Builds delivery zones straight from RoadFN's price list, so the cities a
 * customer can pick and the fee they pay always match the courier.
 *
 * RoadFN is flat: the merchant ships from one branch to a list of destination
 * cities, one fee each, and every city carries its own neighbourhoods. Each
 * destination becomes one active, top-level DeliveryZone priced at RoadFN's
 * fee, and its neighbourhoods are imported for the checkout area picker.
 *
 * Anything RoadFN does not serve is deactivated, never deleted, so existing
 * orders keep resolving their delivery zone.
 */
class SyncRoadFnZones extends Command
{
    protected $signature = 'roadfn:sync-zones';

    protected $description = 'بناء مناطق التوصيل وأسعارها من قائمة أسعار رودفنتي';

    public function handle(RoadFnService $roadFn): int
    {
        $fees = collect($roadFn->getShippingFees());

        if ($fees->isEmpty()) {
            $this->error('رودفنتي لم يُرجع أي أسعار توصيل — لم يتغيّر شيء.');
            return self::FAILURE;
        }

        // One fee row per destination city (dedupe just in case).
        $destinations = $fees->unique('ToCityId')->values();
        $this->info("مزامنة {$destinations->count()} وجهة من رودفنتي...");

        // Fetch every city's areas up front (network) so the DB transaction stays short.
        $areasByCity = [];
        foreach ($destinations as $d) {
            $areasByCity[(string) $d['ToCityId']] = $roadFn->getAreas((string) $d['ToCityId']);
        }

        $syncedIds = [];

        DB::transaction(function () use ($destinations, $roadFn, $areasByCity, &$syncedIds) {
            // RoadFN is the source of truth: everything goes dark, then only the
            // destinations it actually serves come back on.
            DeliveryZone::query()->update(['is_active' => false]);

            // An earlier design mapped RoadFN onto sub zones of a region, where
            // the parent held the price. Those rows must not be reused as
            // cities — they price at 0 — so their mapping is cleared and they
            // stay behind as inactive history.
            DeliveryZone::whereNotNull('parent_id')
                ->update(['roadfn_city_id' => null, 'roadfn_area_id' => null]);

            foreach ($destinations as $i => $d) {
                $cityId = (string) $d['ToCityId'];
                $areas  = $areasByCity[$cityId] ?? [];

                // Only ever reuse a top-level zone, so a legacy sub zone can't
                // be promoted into a city behind our back.
                $zone = DeliveryZone::whereNull('parent_id')
                    ->where('roadfn_city_id', $cityId)
                    ->orderBy('id')->first() ?? new DeliveryZone();

                $isNew = ! $zone->exists;

                $zone->name_ar        = trim($d['ToCity']);
                $zone->base_fee       = $d['Fees'];
                $zone->roadfn_city_id = $cityId;
                $zone->parent_id      = null;
                $zone->is_active      = true;

                // Keep the admin's overrides; only seed them on a fresh row.
                if (! $zone->roadfn_area_id) {
                    $zone->roadfn_area_id = $roadFn->pickDefaultAreaId($areas);
                }
                if ($isNew) {
                    // Delivery price is RoadFN's. Free shipping is a store promo,
                    // off until the admin sets a threshold on the city.
                    $zone->free_above = null;
                    $zone->sort_order = $i;
                }

                $zone->save();
                $syncedIds[] = $zone->id;

                $this->syncAreas($zone, $areas);

                $this->line("✓ {$zone->name_ar} — {$zone->base_fee} ₪ (رودفنتي {$cityId}) — " . count($areas) . ' حي');
            }

            // Neighbourhoods left over from zones we no longer ship to.
            RoadFnArea::whereNotIn('delivery_zone_id', $syncedIds)->delete();
        });

        $this->newLine();
        $this->info('مدن فعّالة: ' . DeliveryZone::where('is_active', true)->count()
            . ' — أحياء: ' . RoadFnArea::count());

        $retired = DeliveryZone::where('is_active', false)->count();
        if ($retired) {
            $this->warn("مناطق معطّلة (لا يخدمها رودفنتي أو قديمة): {$retired} — لم تُحذف، فالطلبات القديمة تبقى سليمة.");
        }

        return self::SUCCESS;
    }

    /** Upserts a city's neighbourhoods and prunes any RoadFN no longer returns. */
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
