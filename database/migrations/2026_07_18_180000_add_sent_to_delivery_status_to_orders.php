<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds a "sent_to_delivery" (مرسل للتوصيل) status between processing and
 * shipped: the admin owns everything up to this point, then RoadFN drives
 * the rest (shipped → delivered / cancelled / returned).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','processing','sent_to_delivery','shipped','delivered','cancelled','returned') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("UPDATE orders SET status = 'processing' WHERE status = 'sent_to_delivery'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','returned') NOT NULL DEFAULT 'pending'");
    }
};
