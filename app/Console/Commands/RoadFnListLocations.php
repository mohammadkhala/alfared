<?php

namespace App\Console\Commands;

use App\Services\RoadFnService;
use Illuminate\Console\Command;

/**
 * One-off lookup to find RoadFN's own city/area IDs, so an admin can fill
 * roadfn_city_id/roadfn_area_id on each DeliveryZone from the admin panel.
 */
class RoadFnListLocations extends Command
{
    protected $signature = 'roadfn:list-locations {city_id? : اعرض مناطق مدينة محددة فقط}';

    protected $description = 'عرض مدن ومناطق رودفنتي لربطها بمناطق التوصيل عندنا';

    public function handle(RoadFnService $roadFn): int
    {
        $cityId = $this->argument('city_id');

        if ($cityId) {
            $areas = $roadFn->getAreas($cityId);
            $this->info("مناطق المدينة #{$cityId}:");
            $this->line(json_encode($areas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return self::SUCCESS;
        }

        $cities = $roadFn->getCities();
        $this->info('مدن رودفنتي:');
        $this->line(json_encode($cities, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $this->newLine();
        $this->comment('لعرض مناطق مدينة معينة: php artisan roadfn:list-locations {city_id}');

        return self::SUCCESS;
    }
}
