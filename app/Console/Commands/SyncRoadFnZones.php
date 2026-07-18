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

                // No single city matched — this may be a whole-region entry
                // such as "مناطق الداخل", which covers all of its cities.
                if (! $zone) {
                    if ($region = $this->matchRegion($cityName)) {
                        $cities = $region->children()->where('is_active', true)
                            ->whereNull('roadfn_city_id')->get();

                        if ($cities->isNotEmpty()) {
                            $defaultArea = $roadFn->pickDefaultAreaId($areas);
                            foreach ($cities as $c) {
                                $c->roadfn_city_id = $cityId;
                                $c->roadfn_area_id = $c->roadfn_area_id ?: $defaultArea;
                                $c->save();
                                $this->syncAreas($c, $areas);
                                $matched++;
                            }
                            $this->line("✓ {$region->name_ar}: رُبطت {$cities->count()} مدينة ← RoadFN {$cityId}");
                            continue;
                        }
                    }

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
     * Spelling differs between the two lists ("اريحا" vs "أريحا والأغوار",
     * "رام الله" vs "رام الله والبيرة"), so names are normalised before
     * comparing. Already-mapped cities are skipped — RoadFN lists Jerusalem
     * twice, and without this the second entry silently overwrote the first.
     */
    private function matchCityByName(string $name): ?DeliveryZone
    {
        $target = $this->normalize($name);

        $candidates = DeliveryZone::sub()
            ->where('is_active', true)
            ->whereNull('roadfn_city_id')
            ->get(['id', 'name_ar', 'parent_id', 'roadfn_city_id', 'roadfn_area_id']);

        // Exact match on the normalised form.
        foreach ($candidates as $c) {
            if ($this->normalize($c->name_ar) === $target) {
                return $c;
            }
        }

        // Then containment, shortest name first so a generic word doesn't
        // grab a longer unrelated city.
        $partial = $candidates
            ->filter(function ($c) use ($target) {
                $n = $this->normalize($c->name_ar);
                return $n !== '' && (str_contains($n, $target) || str_contains($target, $n));
            })
            ->sortBy(fn ($c) => mb_strlen($c->name_ar));

        return $partial->first();
    }

    /**
     * Maps a whole region in one go.
     *
     * RoadFN ships to all of الداخل under a single destination, so that entry
     * has to cover every city we list under that region rather than matching
     * one of them.
     */
    private function matchRegion(string $name): ?DeliveryZone
    {
        $target = $this->normalize($name);

        return DeliveryZone::main()->get(['id', 'name_ar'])
            ->first(function ($m) use ($target) {
                $n = $this->normalize($m->name_ar);
                return $n !== '' && (str_contains($target, $n) || str_contains($n, $target));
            });
    }

    /** Folds the spelling variants that differ between the two data sources. */
    private function normalize(string $s): string
    {
        $s = trim($s);

        // Qualifiers go first — folding ة→ه below would stop "محافظة" matching.
        $s = preg_replace('/^(محافظة|مناطق|مدينة)\s+/u', '', $s) ?? $s;
        // Leading "ال" only; stripping it per-word turns "رام الله" into
        // "رام له" and invites false matches.
        $s = preg_replace('/^ال/u', '', $s) ?? $s;

        $s = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $s);
        $s = str_replace(['ة'], 'ه', $s);
        $s = str_replace(['ى'], 'ي', $s);
        $s = preg_replace('/\s+/u', ' ', $s) ?? $s;

        return trim($s);
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
