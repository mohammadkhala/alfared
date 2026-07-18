<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

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
        'west_bank' => ['ar' => 'الضفة الغربية', 'en' => 'West Bank',  'fee' => 20, 'free' => 300, 'days' => 2],
        'jerusalem' => ['ar' => 'القدس',          'en' => 'Jerusalem',  'fee' => 30, 'free' => 400, 'days' => 2],
        'inside_48' => ['ar' => 'الداخل',         'en' => 'Inside',     'fee' => 40, 'free' => 500, 'days' => 3],
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
            $main = DeliveryZone::updateOrCreate(
                ['name_ar' => $cfg['ar'], 'parent_id' => null],
                [
                    'name_en'             => $cfg['en'],
                    'delivery_fee'        => $cfg['fee'],
                    'free_shipping_above' => $cfg['free'],
                    'estimated_days'      => $cfg['days'],
                    'is_active'           => true,
                ]
            );

            foreach ($this->subsFor($region, $locations, $governorates, $insideCities) as $i => $sub) {
                DeliveryZone::updateOrCreate(
                    ['name_ar' => $sub['ar'], 'parent_id' => $main->id],
                    [
                        'name_en'    => $sub['en'] ?? null,
                        // Fee lives on the main zone; sub zones inherit it.
                        'is_active'  => true,
                        'sort_order' => $i,
                    ]
                );
                $created++;
            }

            $this->command?->info("✓ {$cfg['ar']}");
        }

        $this->retireLegacyZones();

        $this->command?->info("اكتمل: 3 مناطق رئيسية و{$created} منطقة فرعية.");
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
