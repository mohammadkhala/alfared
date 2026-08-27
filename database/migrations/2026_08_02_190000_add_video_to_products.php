<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'video')) {
                // An uploaded file path, or a YouTube/Vimeo URL — a hosted link
                // costs no disk space, which matters on shared hosting.
                $table->string('video')->nullable()->after('main_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'video')) {
                $table->dropColumn('video');
            }
        });
    }
};
