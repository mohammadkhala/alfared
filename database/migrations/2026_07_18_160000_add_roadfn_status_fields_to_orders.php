<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RoadFN drives the order status after a shipment is created. We keep its
 * internal shipment ID (for precise ShipmentListWithIds polling) and its
 * numeric StatusId (the stable key we map to our own status).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('roadfn_shipment_id')->nullable()->after('roadfn_tracking_number');
            $table->integer('roadfn_status_id')->nullable()->after('roadfn_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['roadfn_shipment_id', 'roadfn_status_id']);
        });
    }
};
