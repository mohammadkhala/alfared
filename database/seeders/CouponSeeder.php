<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code'             => 'ALFARED10',
                'name_ar'          => 'خصم 10% لأبناء الفريد',
                'type'             => 'percentage',
                'value'            => 10,
                'min_order_amount' => 100,
                'usage_limit'      => 500,
                'used_count'       => 0,
                'is_active'        => true,
                'expires_at'       => now()->addYear(),
            ],
            [
                'code'             => 'WELCOME20',
                'name_ar'          => 'خصم 20% للعملاء الجدد',
                'type'             => 'percentage',
                'value'            => 20,
                'min_order_amount' => 200,
                'usage_limit'      => 200,
                'used_count'       => 0,
                'is_active'        => true,
                'expires_at'       => now()->addMonths(6),
            ],
            [
                'code'             => 'SAVE15',
                'name_ar'          => 'خصم 15 شيكل',
                'type'             => 'fixed',
                'value'            => 15,
                'min_order_amount' => 80,
                'usage_limit'      => 300,
                'used_count'       => 0,
                'is_active'        => true,
                'expires_at'       => now()->addYear(),
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(['code' => $coupon['code']], $coupon);
        }
    }
}
