<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code', 16)->nullable()->unique()->after('phone');
            }
            if (! Schema::hasColumn('users', 'referred_by')) {
                $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'last_checkin_at')) {
                $table->date('last_checkin_at')->nullable()->after('referred_by');
            }
            if (! Schema::hasColumn('users', 'checkin_streak')) {
                $table->integer('checkin_streak')->default(0)->after('last_checkin_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by');
            $table->dropColumn(['referral_code', 'last_checkin_at', 'checkin_streak']);
        });
    }
};
