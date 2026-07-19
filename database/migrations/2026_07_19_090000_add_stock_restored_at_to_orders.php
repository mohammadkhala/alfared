<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'stock_restored_at')) {
                // Marks that a cancelled/returned order has already had its
                // items put back, so a second status change — or an admin
                // toggling cancelled → returned — can't inflate stock.
                $table->timestamp('stock_restored_at')->nullable()->after('roadfn_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'stock_restored_at')) {
                $table->dropColumn('stock_restored_at');
            }
        });
    }
};
