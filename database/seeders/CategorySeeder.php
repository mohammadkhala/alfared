<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        /* External Unsplash images — stored as full URL, displayed directly in home view */
        $categories = [
            [
                'name_ar' => 'مكياج', 'name_he' => 'איפור', 'name_en' => 'Makeup',
                'slug' => 'makeup', 'icon' => '💄', 'color' => '#FF6B9D', 'sort_order' => 1,
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&q=80&fit=crop',
            ],
            [
                'name_ar' => 'العناية بالبشرة', 'name_he' => 'טיפוח עור', 'name_en' => 'Skincare',
                'slug' => 'skincare', 'icon' => '🧴', 'color' => '#A8E6CF', 'sort_order' => 2,
                'image' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=600&q=80&fit=crop',
            ],
            [
                'name_ar' => 'العناية بالشعر', 'name_he' => 'טיפוח שיער', 'name_en' => 'Hair Care',
                'slug' => 'hair', 'icon' => '💆', 'color' => '#FFD93D', 'sort_order' => 3,
                'image' => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=600&q=80&fit=crop',
            ],
            [
                'name_ar' => 'العطور', 'name_he' => 'בשמים', 'name_en' => 'Perfumes',
                'slug' => 'perfume', 'icon' => '🌸', 'color' => '#C9B8FF', 'sort_order' => 4,
                'image' => 'https://images.unsplash.com/photo-1588776814546-daab30f310ce?w=600&q=80',
            ],
            [
                'name_ar' => 'العناية بالأظافر', 'name_he' => 'טיפוח ציפורניים', 'name_en' => 'Nail Care',
                'slug' => 'nails', 'icon' => '💅', 'color' => '#FFB347', 'sort_order' => 5,
                'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=600&q=80&fit=crop',
            ],
            [
                'name_ar' => 'الأجهزة والأدوات', 'name_he' => 'מכשירים וכלים', 'name_en' => 'Devices',
                'slug' => 'devices', 'icon' => '🪭', 'color' => '#87CEEB', 'sort_order' => 6,
                'image' => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=600&q=80&fit=crop',
            ],
            [
                'name_ar' => 'العناية بالجسم', 'name_he' => 'טיפוח גוף', 'name_en' => 'Body Care',
                'slug' => 'body', 'icon' => '🛁', 'color' => '#98FB98', 'sort_order' => 7,
                'image' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=600&q=80&fit=crop',
            ],
            [
                'name_ar' => 'منتجات الأطفال', 'name_he' => 'מוצרי תינוקות', 'name_en' => 'Baby',
                'slug' => 'baby', 'icon' => '👶', 'color' => '#FFE4E1', 'sort_order' => 8,
                'image' => 'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=600&q=80&fit=crop',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }
    }
}
