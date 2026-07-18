<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RoadFN's neighborhoods/areas per destination city (≈1300 rows), so a
 * customer can pick their exact area for accurate delivery. Populated by
 * `roadfn:sync-zones`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadfn_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_zone_id')->constrained()->cascadeOnDelete();
            $table->string('roadfn_area_id');
            $table->string('name_ar');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['delivery_zone_id', 'roadfn_area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadfn_areas');
    }
};
