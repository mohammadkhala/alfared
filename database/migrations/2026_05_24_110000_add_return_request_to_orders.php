<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('return_requested_at')->nullable()->after('status');
            $table->string('return_reason', 500)->nullable()->after('return_requested_at');
            $table->string('return_status', 30)->nullable()->after('return_reason'); // pending|approved|rejected
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['return_requested_at', 'return_reason', 'return_status']);
        });
    }
};
