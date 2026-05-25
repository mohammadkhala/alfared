<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_he')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('free_shipping_above', 8, 2)->nullable(); // شحن مجاني فوق هذا المبلغ
            $table->integer('estimated_days')->default(1);            // أيام التوصيل
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
