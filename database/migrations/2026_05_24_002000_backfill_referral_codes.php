<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        User::whereNull('referral_code')->each(function (User $u) {
            do {
                $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            } while (User::where('referral_code', $code)->exists());
            $u->update(['referral_code' => $code]);
        });
    }

    public function down(): void {}
};
