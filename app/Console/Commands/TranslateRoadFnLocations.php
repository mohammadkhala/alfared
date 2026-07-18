<?php

namespace App\Console\Commands;

use App\Models\DeliveryZone;
use App\Models\RoadFnArea;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

/**
 * RoadFN only gives us Arabic city/area names, but the store is also served in
 * Hebrew and English. This fills name_he/name_en for both delivery zones
 * (cities) and their neighbourhoods.
 *
 * Safe to re-run: by default only rows missing a translation are touched.
 */
class TranslateRoadFnLocations extends Command
{
    protected $signature = 'roadfn:translate-locations
                            {--force : إعادة ترجمة كل الأسماء حتى المترجمة مسبقاً}
                            {--chunk=40 : عدد الأسماء في كل طلب ترجمة}';

    protected $description = 'ترجمة أسماء المدن والمناطق الفرعية إلى العبرية والإنجليزية';

    public function handle(TranslationService $translator): int
    {
        $force = (bool) $this->option('force');
        $chunk = max(1, (int) $this->option('chunk'));

        foreach (['he', 'en'] as $lang) {
            $column = "name_{$lang}";

            $this->info("── الترجمة إلى «{$lang}» ──");

            $zones = DeliveryZone::query()
                ->whereNotNull('name_ar')
                ->when(! $force, fn ($q) => $q->where(fn ($w) => $w->whereNull($column)->orWhere($column, '')))
                ->get();

            $areas = RoadFnArea::query()
                ->whereNotNull('name_ar')
                ->when(! $force, fn ($q) => $q->where(fn ($w) => $w->whereNull($column)->orWhere($column, '')))
                ->get();

            $this->translateRows($translator, $zones, $column, $lang, $chunk, 'المدن');
            $this->translateRows($translator, $areas, $column, $lang, $chunk, 'المناطق الفرعية');
        }

        $this->newLine();
        $this->info('اكتملت الترجمة.');
        return self::SUCCESS;
    }

    private function translateRows(
        TranslationService $translator,
        Collection $rows,
        string $column,
        string $lang,
        int $chunk,
        string $label,
    ): void {
        if ($rows->isEmpty()) {
            $this->line("  {$label}: لا يوجد ما يُترجم ✓");
            return;
        }

        $bar = $this->output->createProgressBar($rows->count());
        $bar->setFormat("  {$label}: %current%/%max% [%bar%] %message%");
        $bar->setMessage('');
        $bar->start();

        $done = 0;
        foreach ($rows->chunk($chunk) as $group) {
            $names        = $group->pluck('name_ar')->all();
            $translations = $translator->translateBatch($names, 'ar', $lang);

            foreach ($group->values() as $i => $row) {
                $value = $translations[$i] ?? '';
                if ($value !== '') {
                    $row->update([$column => $value]);
                    $done++;
                }
            }

            $bar->advance($group->count());
            $bar->setMessage((string) $done);
            usleep(300_000); // be gentle on the free endpoint
        }

        $bar->finish();
        $this->newLine();
    }
}
