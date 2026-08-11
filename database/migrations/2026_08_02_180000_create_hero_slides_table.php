<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            // Visible text, per language (ar is the fallback everywhere).
            foreach (['badge', 'title', 'highlight', 'subtitle', 'btn1_text', 'btn2_text'] as $f) {
                $table->string("{$f}_ar")->nullable();
                $table->string("{$f}_he")->nullable();
                $table->string("{$f}_en")->nullable();
            }
            $table->string('btn1_url')->nullable();
            $table->string('btn2_url')->nullable();
            $table->string('image')->nullable();     // background photo
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
