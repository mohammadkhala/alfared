<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'fcm_token')) {
                $table->string('fcm_token', 255)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'device_type')) {
                $table->string('device_type', 20)->nullable()->after('fcm_token'); // ios | android
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fcm_token', 'device_type']);
        });
    }
};
