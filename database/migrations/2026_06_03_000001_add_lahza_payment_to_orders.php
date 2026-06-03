<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand payment_method enum to include 'lahza'
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cod','lahza','card','transfer') NOT NULL DEFAULT 'cod'");

        // 2. Expand payment_status enum to include 'failed'
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending'");

        // 3. Add payment reference column (Lahza transaction reference)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_ref')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_ref');
        });
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cod') NOT NULL DEFAULT 'cod'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid') NOT NULL DEFAULT 'pending'");
    }
};
