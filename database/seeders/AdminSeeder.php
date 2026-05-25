<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@alfared.ps'],
            [
                'name'     => 'أبناء الفريد',
                'email'    => 'admin@alfared.ps',
                'password' => Hash::make('Admin@2025'),
                'role'     => 'admin',
            ]
        );
    }
}
