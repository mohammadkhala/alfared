<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('roadfn_tracking_number')->nullable()->after('payment_ref')->index();
            $table->string('roadfn_status')->nullable()->after('roadfn_tracking_number');
            $table->timestamp('roadfn_sent_at')->nullable()->after('roadfn_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['roadfn_tracking_number', 'roadfn_status', 'roadfn_sent_at']);
        });
    }
};
