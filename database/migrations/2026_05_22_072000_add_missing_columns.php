<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categories: add name_en, color
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_he');
            }
            if (!Schema::hasColumn('categories', 'color')) {
                $table->string('color', 20)->nullable()->after('icon');
            }
        });

        // Delivery zones: add name_en, base_fee, free_above
        Schema::table('delivery_zones', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_zones', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_he');
            }
            if (!Schema::hasColumn('delivery_zones', 'base_fee')) {
                $table->decimal('base_fee', 8, 2)->default(0)->after('name_en');
            }
            if (!Schema::hasColumn('delivery_zones', 'free_above')) {
                $table->decimal('free_above', 8, 2)->nullable()->after('base_fee');
            }
        });

        // Products: add name_en, description_en, is_published, reviews_count
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_he');
            }
            if (!Schema::hasColumn('products', 'description_en')) {
                $table->text('description_en')->nullable()->after('description_he');
            }
            if (!Schema::hasColumn('products', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('is_new');
            }
            if (!Schema::hasColumn('products', 'reviews_count')) {
                $table->unsignedInteger('reviews_count')->default(0)->after('rating_count');
            }
        });

        // Orders: add user_id if missing (should already exist)
        // loyalty_points table: ensure it has the right columns
        if (Schema::hasTable('loyalty_points')) {
            Schema::table('loyalty_points', function (Blueprint $table) {
                if (!Schema::hasColumn('loyalty_points', 'balance')) {
                    $table->integer('balance')->default(0)->after('points');
                }
                if (!Schema::hasColumn('loyalty_points', 'reason')) {
                    $table->string('reason')->nullable()->after('balance');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'color']);
        });
        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'base_fee', 'free_above']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'description_en', 'is_published', 'reviews_count']);
        });
    }
};
