<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Turns delivery zones into two levels:
 *   main zone  (parent_id = null) — الضفة / القدس / الداخل, carries the fee
 *   sub zone   (parent_id = main) — governorate or city the customer picks
 *
 * Existing rows keep parent_id = null, so they stay valid as main zones and
 * no order loses its delivery_zone_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_zones', function (Blueprint $table) {
            if (! Schema::hasColumn('delivery_zones', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')
                    ->constrained('delivery_zones')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('delivery_zones', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_he');
            }
            if (! Schema::hasColumn('delivery_zones', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_zones', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_zones', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            foreach (['name_en', 'sort_order'] as $col) {
                if (Schema::hasColumn('delivery_zones', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
