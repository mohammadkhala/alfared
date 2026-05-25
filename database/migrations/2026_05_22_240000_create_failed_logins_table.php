<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_logins', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->nullable()->index();
            $table->string('ip', 45)->nullable()->index();
            $table->string('user_agent', 500)->nullable();
            $table->string('source', 20)->default('storefront'); // storefront | admin
            $table->string('reason', 50)->default('invalid_credentials');
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['ip', 'attempted_at']);
            $table->index(['email', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_logins');
    }
};
