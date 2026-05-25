<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->enum('type', ['percentage', 'fixed']); // نسبة % أو مبلغ ثابت
            $table->decimal('value', 8, 2);
            $table->decimal('min_order_amount', 8, 2)->default(0); // حد أدنى للطلب
            $table->decimal('max_discount', 8, 2)->nullable();      // أقصى خصم
            $table->integer('usage_limit')->nullable();              // عدد مرات الاستخدام الكلي
            $table->integer('usage_per_user')->default(1);          // مرات الاستخدام للمستخدم
            $table->integer('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_categories')->nullable();       // أقسام محددة
            $table->json('applicable_products')->nullable();         // منتجات محددة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
