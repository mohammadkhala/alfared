<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            DeliveryZoneSeeder::class,
            CouponSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
