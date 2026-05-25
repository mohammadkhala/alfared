<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_ar' => 'مكياج',           'name_he' => 'איפור',        'name_en' => 'Makeup',      'slug' => 'makeup',   'icon' => '💄', 'color' => '#FF6B9D', 'sort_order' => 1],
            ['name_ar' => 'العناية بالبشرة', 'name_he' => 'טיפוח עור',   'name_en' => 'Skincare',   'slug' => 'skincare', 'icon' => '🧴', 'color' => '#A8E6CF', 'sort_order' => 2],
            ['name_ar' => 'العناية بالشعر', 'name_he' => 'טיפוח שיער',  'name_en' => 'Hair Care',  'slug' => 'hair',     'icon' => '💆', 'color' => '#FFD93D', 'sort_order' => 3],
            ['name_ar' => 'العطور',          'name_he' => 'בשמים',        'name_en' => 'Perfumes',   'slug' => 'perfume',  'icon' => '🌸', 'color' => '#C9B8FF', 'sort_order' => 4],
            ['name_ar' => 'العناية بالأظافر','name_he' => 'טיפוח ציפורניים','name_en' => 'Nail Care', 'slug' => 'nails',    'icon' => '💅', 'color' => '#FFB347', 'sort_order' => 5],
            ['name_ar' => 'الأجهزة والأدوات','name_he' => 'מכשירים וכלים','name_en' => 'Devices',   'slug' => 'devices',  'icon' => '🪭', 'color' => '#87CEEB', 'sort_order' => 6],
            ['name_ar' => 'العناية بالجسم', 'name_he' => 'טיפוח גוף',   'name_en' => 'Body Care',  'slug' => 'body',     'icon' => '🛁', 'color' => '#98FB98', 'sort_order' => 7],
            ['name_ar' => 'منتجات الأطفال', 'name_he' => 'מוצרי תינוקות','name_en' => 'Baby',      'slug' => 'baby',     'icon' => '👶', 'color' => '#FFE4E1', 'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }
    }
}
