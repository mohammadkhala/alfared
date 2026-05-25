<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'L\'Oréal',        'slug' => 'loreal'],
            ['name' => 'Maybelline',     'slug' => 'maybelline'],
            ['name' => 'NYX',            'slug' => 'nyx'],
            ['name' => 'MAC',            'slug' => 'mac'],
            ['name' => 'Nivea',          'slug' => 'nivea'],
            ['name' => 'Garnier',        'slug' => 'garnier'],
            ['name' => 'Pantene',        'slug' => 'pantene'],
            ['name' => 'Head & Shoulders','slug' => 'head-shoulders'],
            ['name' => 'Dove',           'slug' => 'dove'],
            ['name' => 'Revlon',         'slug' => 'revlon'],
            ['name' => 'OPI',            'slug' => 'opi'],
            ['name' => 'Schwarzkopf',   'slug' => 'schwarzkopf'],
            ['name' => 'Essence',        'slug' => 'essence'],
            ['name' => 'Flormar',        'slug' => 'flormar'],
            ['name' => 'Rimmel',         'slug' => 'rimmel'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['slug' => $brand['slug']], array_merge($brand, ['is_active' => true]));
        }
    }
}
