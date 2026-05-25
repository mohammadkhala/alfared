<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('points');                          // موجب = مكسب، سالب = مستهلك
            $table->string('type');                            // earned / redeemed / expired / bonus
            $table->string('description_ar');
            $table->timestamps();
        });

        // إضافة عمود إجمالي النقاط لجدول المستخدمين
        Schema::table('users', function (Blueprint $table) {
            $table->integer('loyalty_points_balance')->default(0)->after('email');
            $table->string('phone')->nullable()->after('loyalty_points_balance');
            $table->enum('gender', ['female', 'male', 'other'])->nullable()->after('phone');
            $table->date('birthdate')->nullable()->after('gender');
            $table->string('avatar')->nullable()->after('birthdate');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
