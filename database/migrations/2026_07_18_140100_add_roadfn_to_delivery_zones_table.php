<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RoadFN identifies cities/areas with its own IDs, unmapped to ours —
 * these are filled in manually per sub zone (city) from the admin panel,
 * using `php artisan roadfn:list-locations` to look up the real values.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->string('roadfn_city_id')->nullable()->after('sort_order');
            $table->string('roadfn_area_id')->nullable()->after('roadfn_city_id');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->dropColumn(['roadfn_city_id', 'roadfn_area_id']);
        });
    }
};
