<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('points');                  // positive = added, negative = redeemed/deducted
            $table->string('action', 40);               // earned | redeemed | admin_add | admin_deduct | expired
            $table->decimal('value_ils', 10, 2)->nullable(); // monetary value at the time (for redemptions)
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Make sure loyalty_points column exists on users
        if (! Schema::hasColumn('users', 'loyalty_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('loyalty_points')->default(0)->after('role');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
