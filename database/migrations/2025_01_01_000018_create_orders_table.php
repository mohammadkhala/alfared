<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // ORD-2025-0001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // بيانات العميل (محفوظة عند الطلب)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();

            // بيانات الشحن
            $table->string('city');
            $table->string('area')->nullable();
            $table->text('address_line');
            $table->string('building')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->foreignId('delivery_zone_id')->nullable()->constrained()->nullOnDelete();

            // المبالغ
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('total', 10, 2);

            // الكوبون
            $table->string('coupon_code')->nullable();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            // الحالة
            $table->enum('status', [
                'pending',      // بانتظار التأكيد
                'confirmed',    // تم التأكيد
                'processing',   // قيد التجهيز
                'shipped',      // تم الشحن
                'delivered',    // تم التوصيل
                'cancelled',    // ملغي
                'returned',     // مُرجَّع
            ])->default('pending');

            $table->enum('payment_method', ['cod'])->default('cod'); // دفع عند الاستلام
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');

            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
