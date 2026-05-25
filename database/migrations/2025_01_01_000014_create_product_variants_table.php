<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // color, size, volume
            $table->string('value'); // أحمر، L، 50ml
            $table->string('value_en')->nullable();
            $table->string('color_code')->nullable(); // #FF0000
            $table->decimal('price_modifier', 8, 2)->default(0); // إضافة للسعر
            $table->integer('stock_quantity')->default(0);
            $table->string('sku')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
