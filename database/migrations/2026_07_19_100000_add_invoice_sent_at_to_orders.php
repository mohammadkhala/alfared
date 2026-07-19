<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'invoice_sent_at')) {
                // One invoice per order. A card order that is paid and then
                // delivered hits both triggers, and an admin toggling
                // delivered → shipped → delivered would send it again.
                $table->timestamp('invoice_sent_at')->nullable()->after('stock_restored_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'invoice_sent_at')) {
                $table->dropColumn('invoice_sent_at');
            }
        });
    }
};
