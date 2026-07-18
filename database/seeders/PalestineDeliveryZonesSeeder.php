<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Builds the two-level delivery zone tree from /palestine-locations.
 *
 *   الضفة الغربية  → 10 محافظات
 *   القدس          → المدينة وبلداتها وقراها
 *   الداخل         → 24 مدينة/مركز
 *
 * Only main zones carry a fee. Re-running updates names in place and never
 * duplicates, so it is safe to run after the data files change.
 */
class PalestineDeliveryZonesSeeder extends Seeder
{
    /** Main zones and their default pricing (adjust from the admin panel). */
    private const MAIN = [
        'west_bank' => ['ar' => 'الضفة الغربية', 'en' => 'West Bank', 'fee' => 20, 'days' => 2],
        'jerusalem' => ['ar' => 'القدس',          'en' => 'Jerusalem', 'fee' => 25, 'days' => 2],
        'inside_48' => ['ar' => 'الداخل',         'en' => 'Inside',    'fee' => 70, 'days' => 3],
    ];

    public function run(): void
    {
        $dir = base_path('palestine-locations');

        if (! File::exists("{$dir}/locations.json")) {
            $this->command?->error("palestine-locations/locations.json غير موجود — تخطّي.");
            return;
        }

        $locations   = json_decode(File::get("{$dir}/locations.json"), true) ?: [];
        $governorates = File::exists("{$dir}/governorates.json")
            ? (json_decode(File::get("{$dir}/governorates.json"), true) ?: []) : [];
        $insideCities = File::exists("{$dir}/inside_cities.json")
            ? (json_decode(File::get("{$dir}/inside_cities.json"), true) ?: []) : [];

        $created = 0;

        foreach (self::MAIN as $region => $cfg) {
            $main = DeliveryZone::firstOrNew(['name_ar' => $cfg['ar'], 'parent_id' => null]);

            // Fee is authoritative — it is what the store owner asked for.
            $main->name_en   = $cfg['en'];
            $main->is_active = true;
            $this->setFee($main, $cfg['fee']);

            // Delivery days only seed a new row, so a value tuned in the admin
            // panel survives re-running this seeder.
            if (! $main->exists) {
                $main->estimated_days = $cfg['days'];
            }

            $main->save();

            foreach ($this->subsFor($region, $locations, $governorates, $insideCities) as $i => $sub) {
                $city = DeliveryZone::firstOrNew(['name_ar' => $sub['ar'], 'parent_id' => $main->id]);
                $city->name_en    = $sub['en'] ?? null;
                // Fee lives on the main zone; sub zones inherit it.
                $city->is_active  = true;
                $city->sort_order = $i;

                $this->inheritRoadFnMapping($city);

                $city->save();
                $created++;
            }

            $this->command?->info("✓ {$cfg['ar']}");
        }

        $this->retireLegacyZones();

        $this->command?->info("اكتمل: 3 مناطق رئيسية و{$created} منطقة فرعية.");
    }

    /**
     * Carries a legacy flat zone's RoadFN ids onto the matching new city.
     *
     * Those ids were looked up against the live RoadFN account, so losing them
     * when the old rows are retired would mean redoing that mapping by hand.
     * An id already set on the city always wins.
     */
    private function inheritRoadFnMapping(DeliveryZone $city): void
    {
        if (! Schema::hasColumn('delivery_zones', 'roadfn_city_id')) {
            return;
        }
        if (filled($city->roadfn_city_id)) {
            return;
        }

        // Exact name first, then the legacy naming variants — the old rows
        // used "الخليل المدينة" / "محافظة الخليل" where the governorate list
        // simply says "الخليل".
        $legacy = DeliveryZone::query()
            ->whereNull('parent_id')
            ->whereNotNull('roadfn_city_id')
            ->where(function ($q) use ($city) {
                $q->where('name_ar', $city->name_ar)
                  ->orWhere('name_ar', "محافظة {$city->name_ar}")
                  ->orWhere('name_ar', "{$city->name_ar} المدينة");
            })
            // Prefer the exact match when several variants exist.
            ->orderByRaw('CASE WHEN name_ar = ? THEN 0 ELSE 1 END', [$city->name_ar])
            ->first();

        if (! $legacy) {
            return;
        }

        $city->roadfn_city_id = $legacy->roadfn_city_id;
        $city->roadfn_area_id = $legacy->roadfn_area_id;

        $this->command?->line("  ↳ نُقل ربط RoadFN إلى: {$city->name_ar}");
    }

    /**
     * Old flat zones (الخليل، نابلس، …) would otherwise sit in the region
     * dropdown next to the new tree. They are deactivated rather than deleted
     * so existing orders keep pointing at a real row.
     */
    private function retireLegacyZones(): void
    {
        $keep = array_column(self::MAIN, 'ar');

        $legacy = DeliveryZone::main()
            ->whereNotIn('name_ar', $keep)
            ->whereDoesntHave('children')
            ->where('is_active', true)
            ->get();

        if ($legacy->isEmpty()) {
            return;
        }

        foreach ($legacy as $zone) {
            $zone->update(['is_active' => false]);
        }

        $names = $legacy->pluck('name_ar')->implode('، ');
        $this->command?->warn("عُطّلت مناطق قديمة (لم تُحذف، والطلبات السابقة سليمة): {$names}");
    }

    /**
     * The table carries both `base_fee` (admin panel) and `delivery_fee`
     * (original migration). Writing both keeps them from drifting apart.
     */
    private function setFee(DeliveryZone $zone, float $fee): void
    {
        foreach (['base_fee', 'delivery_fee'] as $col) {
            if (Schema::hasColumn('delivery_zones', $col)) {
                $zone->{$col} = $fee;
            }
        }
    }

    /** Picks the right sub level for each region. */
    private function subsFor(string $region, array $locations, array $govs, array $inside): array
    {
        if ($region === 'west_bank') {
            // Governorates keep the list short and match how people describe
            // where they live.
            return collect($govs)
                ->where('region', 'west_bank')
                ->map(fn ($g) => ['ar' => $g['name'], 'en' => $g['name_en'] ?? null])
                ->values()->all();
        }

        if ($region === 'inside_48') {
            return collect($inside)
                ->map(fn ($c) => ['ar' => $c['name'], 'en' => $c['name_en'] ?? null])
                ->values()->all();
        }

        // Jerusalem: city + towns + villages. Neighbourhoods are too granular
        // for a dropdown — the customer types those in the address field.
        return collect($locations)
            ->where('region', 'jerusalem')
            ->whereIn('type', ['city', 'town', 'village'])
            ->sortBy('name')
            ->map(fn ($l) => ['ar' => $l['name'], 'en' => $l['name_en'] ?? null])
            ->unique('ar')
            ->values()->all();
    }
}
