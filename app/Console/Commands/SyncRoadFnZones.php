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

        $matched = 0;
        $unmatched = [];

        DB::transaction(function () use ($destinations, $roadFn, $areasByCity, &$matched, &$unmatched) {
            foreach ($destinations as $d) {
                $cityId   = (string) $d['ToCityId'];
                $cityName = trim($d['ToCity']);
                $areas    = $areasByCity[$cityId] ?? [];

                // Map onto an existing city (sub zone). Pricing stays on the main
                // zone — RoadFN's own fee is its cost to us, not the customer's.
                $zone = DeliveryZone::where('roadfn_city_id', $cityId)->orderBy('id')->first()
                    ?? $this->matchCityByName($cityName);

                if (! $zone) {
                    $unmatched[] = $cityName;
                    continue;
                }

                $zone->roadfn_city_id = $cityId;
                // Keep an admin override; the customer's picked area wins anyway.
                if (! $zone->roadfn_area_id) {
                    $zone->roadfn_area_id = $roadFn->pickDefaultAreaId($areas);
                }
                $zone->save();

                $this->syncAreas($zone, $areas);
                $matched++;

                $this->line("✓ {$zone->name_ar} ← RoadFN {$cityId} — " . count($areas) . ' حي');
            }
        });

        $this->newLine();
        $this->info("رُبطت {$matched} مدينة بـ RoadFN.");

        if ($unmatched) {
            $this->warn('وجهات لدى RoadFN بلا مدينة مطابقة عندنا: ' . implode('، ', $unmatched));
            $this->line('اربطها يدوياً من لوحة الأدمن إن لزم.');
        }

        $missing = DeliveryZone::sub()->where('is_active', true)
            ->whereNull('roadfn_city_id')->pluck('name_ar');
        if ($missing->isNotEmpty()) {
            $this->warn('مدن عندنا بلا ربط RoadFN (لن يعمل زر الإرسال لها): ' . $missing->implode('، '));
        }

        return self::SUCCESS;
    }

    /**
     * Finds our city for a RoadFN destination name.
     *
     * The two lists are written differently ("رام الله" vs "رام الله والبيرة",
     * "الخليل المدينة" vs "الخليل"), so fall back to a contains-match after the
     * exact one. Only unmapped cities are considered, so a name that matches
     * two of ours can't steal an already-mapped row.
     */
    private function matchCityByName(string $name): ?DeliveryZone
    {
        $exact = DeliveryZone::sub()->where('name_ar', $name)->first();
        if ($exact) {
            return $exact;
        }

        return DeliveryZone::sub()
            ->whereNull('roadfn_city_id')
            ->where(function ($q) use ($name) {
                $q->where('name_ar', 'like', "%{$name}%")
                  ->orWhereRaw('? like concat("%", name_ar, "%")', [$name]);
            })
            // Prefer the closest name so "رام الله" doesn't grab a longer unrelated one.
            ->orderByRaw('CHAR_LENGTH(name_ar) ASC')
            ->first();
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
