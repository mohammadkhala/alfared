<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_he')->nullable();
            $table->string('slug')->unique();
            $table->text('description_ar')->nullable();
            $table->text('description_he')->nullable();
            $table->text('short_description')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable(); // سعر قبل الخصم
            $table->decimal('cost_price', 10, 2)->nullable();    // سعر التكلفة
            $table->string('sku')->unique()->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_alert')->default(5);      // تنبيه مخزون منخفض
            $table->string('main_image')->nullable();
            $table->decimal('weight', 8, 2)->nullable();         // الوزن للشحن
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);      // منتج مميز
            $table->boolean('is_new')->default(false);           // منتج جديد
            $table->boolean('track_quantity')->default(true);    // تتبع المخزون
            $table->boolean('allow_backorder')->default(false);  // السماح بالطلب رغم نفاد المخزون
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
