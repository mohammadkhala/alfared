<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 64)->index();      // session-based unique id
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->string('device_type', 20)->index();    // app | mobile | tablet | desktop | bot
            $table->string('browser', 60)->nullable();
            $table->string('os', 60)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('path', 255)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('language', 10)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('visited_at')->index();
            $table->timestamps();

            $table->index(['visited_at', 'device_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
