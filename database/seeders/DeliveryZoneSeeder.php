<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name_ar'           => 'الخليل المدينة',
                'name_he'           => 'חברון עיר',
                'name_en'           => 'Hebron City',
                'base_fee'          => 10,
                'free_above'        => 200,
                'estimated_days'    => 1,
                'is_active'         => true,
            ],
            [
                'name_ar'           => 'محافظة الخليل',
                'name_he'           => 'מחוז חברון',
                'name_en'           => 'Hebron Governorate',
                'base_fee'          => 15,
                'free_above'        => 300,
                'estimated_days'    => 1,
                'is_active'         => true,
            ],
            [
                'name_ar'           => 'رام الله والبيرة',
                'name_he'           => 'רמאללה',
                'name_en'           => 'Ramallah',
                'base_fee'          => 25,
                'free_above'        => 400,
                'estimated_days'    => 2,
                'is_active'         => true,
            ],
            [
                'name_ar'           => 'نابلس',
                'name_he'           => 'שכם',
                'name_en'           => 'Nablus',
                'base_fee'          => 25,
                'free_above'        => 400,
                'estimated_days'    => 2,
                'is_active'         => true,
            ],
            [
                'name_ar'           => 'جنين',
                'name_he'           => 'ג\'נין',
                'name_en'           => 'Jenin',
                'base_fee'          => 30,
                'free_above'        => 500,
                'estimated_days'    => 2,
                'is_active'         => true,
            ],
            [
                'name_ar'           => 'القدس',
                'name_he'           => 'ירושלים',
                'name_en'           => 'Jerusalem',
                'base_fee'          => 20,
                'free_above'        => 350,
                'estimated_days'    => 1,
                'is_active'         => true,
            ],
            [
                'name_ar'           => 'بيت لحم',
                'name_he'           => 'בית לחם',
                'name_en'           => 'Bethlehem',
                'base_fee'          => 15,
                'free_above'        => 300,
                'estimated_days'    => 1,
                'is_active'         => true,
            ],
            [
                'name_ar'           => 'غزة',
                'name_he'           => 'עזה',
                'name_en'           => 'Gaza',
                'base_fee'          => 35,
                'free_above'        => 600,
                'estimated_days'    => 3,
                'is_active'         => true,
            ],
        ];

        foreach ($zones as $zone) {
            DeliveryZone::updateOrCreate(['name_ar' => $zone['name_ar']], $zone);
        }
    }
}
