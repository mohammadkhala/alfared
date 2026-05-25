<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $makeup   = Category::where('slug','makeup')->first();
        $skincare = Category::where('slug','skincare')->first();
        $hair     = Category::where('slug','hair')->first();
        $perfume  = Category::where('slug','perfume')->first();
        $nails    = Category::where('slug','nails')->first();

        $loreal    = Brand::where('slug','loreal')->first();
        $maybelline= Brand::where('slug','maybelline')->first();
        $nyx       = Brand::where('slug','nyx')->first();
        $nivea     = Brand::where('slug','nivea')->first();
        $garnier   = Brand::where('slug','garnier')->first();
        $pantene   = Brand::where('slug','pantene')->first();
        $essence   = Brand::where('slug','essence')->first();
        $flormar   = Brand::where('slug','flormar')->first();

        $products = [
            // MAKEUP
            ['name_ar' => 'أحمر شفاه ميبلين ماتي', 'name_en' => 'Maybelline Matte Lipstick', 'category_id' => $makeup?->id, 'brand_id' => $maybelline?->id, 'price' => 35, 'compare_price' => 45, 'stock_quantity' => 50, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.5, 'sales_count' => 120],
            ['name_ar' => 'مسكارا L\'Oréal Volume', 'name_en' => 'L\'Oréal Volume Mascara', 'category_id' => $makeup?->id, 'brand_id' => $loreal?->id, 'price' => 42, 'compare_price' => null, 'stock_quantity' => 35, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.7, 'sales_count' => 89],
            ['name_ar' => 'بودرة NYX HD Finishing', 'name_en' => 'NYX HD Finishing Powder', 'category_id' => $makeup?->id, 'brand_id' => $nyx?->id, 'price' => 55, 'compare_price' => 70, 'stock_quantity' => 28, 'is_featured' => false, 'is_published' => true, 'rating_avg' => 4.3, 'sales_count' => 67],
            ['name_ar' => 'كحل مايبلين العيون', 'name_en' => 'Maybelline Eye Liner', 'category_id' => $makeup?->id, 'brand_id' => $maybelline?->id, 'price' => 28, 'compare_price' => null, 'stock_quantity' => 60, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.6, 'sales_count' => 145],
            ['name_ar' => 'فاونديشن إيسينس', 'name_en' => 'Essence Foundation', 'category_id' => $makeup?->id, 'brand_id' => $essence?->id, 'price' => 38, 'compare_price' => 48, 'stock_quantity' => 45, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.2, 'sales_count' => 78],
            ['name_ar' => 'بلاشر فلورمار', 'name_en' => 'Flormar Blush', 'category_id' => $makeup?->id, 'brand_id' => $flormar?->id, 'price' => 32, 'compare_price' => null, 'stock_quantity' => 40, 'is_featured' => false, 'is_published' => true, 'rating_avg' => 4.0, 'sales_count' => 55],

            // SKINCARE
            ['name_ar' => 'كريم نيفيا الترطيب اليومي', 'name_en' => 'Nivea Daily Moisturizer', 'category_id' => $skincare?->id, 'brand_id' => $nivea?->id, 'price' => 29, 'compare_price' => null, 'stock_quantity' => 80, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.8, 'sales_count' => 200],
            ['name_ar' => 'سيروم فيتامين C من غارنييه', 'name_en' => 'Garnier Vitamin C Serum', 'category_id' => $skincare?->id, 'brand_id' => $garnier?->id, 'price' => 65, 'compare_price' => 80, 'stock_quantity' => 25, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.6, 'sales_count' => 93],
            ['name_ar' => 'غسول وجه نيفيا', 'name_en' => 'Nivea Face Wash', 'category_id' => $skincare?->id, 'brand_id' => $nivea?->id, 'price' => 22, 'compare_price' => null, 'stock_quantity' => 100, 'is_featured' => false, 'is_published' => true, 'rating_avg' => 4.4, 'sales_count' => 155],
            ['name_ar' => 'مرطب لوريال هيالورونيك', 'name_en' => 'L\'Oréal Hyaluronic Moisturizer', 'category_id' => $skincare?->id, 'brand_id' => $loreal?->id, 'price' => 72, 'compare_price' => 90, 'stock_quantity' => 18, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.7, 'sales_count' => 77],

            // HAIR
            ['name_ar' => 'شامبو بانتين للشعر التالف', 'name_en' => 'Pantene Damaged Hair Shampoo', 'category_id' => $hair?->id, 'brand_id' => $pantene?->id, 'price' => 25, 'compare_price' => null, 'stock_quantity' => 90, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.5, 'sales_count' => 180],
            ['name_ar' => 'بلسم غارنييه للشعر الجاف', 'name_en' => 'Garnier Conditioner Dry Hair', 'category_id' => $hair?->id, 'brand_id' => $garnier?->id, 'price' => 27, 'compare_price' => 35, 'stock_quantity' => 55, 'is_featured' => false, 'is_published' => true, 'rating_avg' => 4.3, 'sales_count' => 102],
            ['name_ar' => 'زيت الشعر لوريال', 'name_en' => 'L\'Oréal Hair Oil', 'category_id' => $hair?->id, 'brand_id' => $loreal?->id, 'price' => 48, 'compare_price' => 60, 'stock_quantity' => 30, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.6, 'sales_count' => 85],

            // PERFUME
            ['name_ar' => 'عطر فلورمار للنساء', 'name_en' => 'Flormar Women Perfume', 'category_id' => $perfume?->id, 'brand_id' => $flormar?->id, 'price' => 120, 'compare_price' => 150, 'stock_quantity' => 20, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.4, 'sales_count' => 45],
            ['name_ar' => 'بخاخ جسم فاخر', 'name_en' => 'Luxury Body Spray', 'category_id' => $perfume?->id, 'brand_id' => null, 'price' => 35, 'compare_price' => null, 'stock_quantity' => 65, 'is_featured' => false, 'is_published' => true, 'rating_avg' => 4.1, 'sales_count' => 70],

            // NAILS
            ['name_ar' => 'طلاء أظافر فلورمار', 'name_en' => 'Flormar Nail Polish', 'category_id' => $nails?->id, 'brand_id' => $flormar?->id, 'price' => 18, 'compare_price' => null, 'stock_quantity' => 120, 'is_featured' => false, 'is_published' => true, 'rating_avg' => 4.2, 'sales_count' => 190],
            ['name_ar' => 'طلاء جل للأظافر', 'name_en' => 'Gel Nail Polish', 'category_id' => $nails?->id, 'brand_id' => null, 'price' => 25, 'compare_price' => 32, 'stock_quantity' => 75, 'is_featured' => true, 'is_published' => true, 'rating_avg' => 4.5, 'sales_count' => 130],
        ];

        foreach ($products as $data) {
            $slug = Str::slug($data['name_en'] ?? $data['name_ar']) . '-' . rand(100, 999);
            $name_he = $data['name_en']; // placeholder

            Product::updateOrCreate(
                ['name_ar' => $data['name_ar']],
                array_merge($data, [
                    'name_he'        => $name_he,
                    'slug'           => $slug,
                    'is_active'      => true,
                    'sku'            => 'SKU-' . strtoupper(Str::random(6)),
                    'description_ar' => 'منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',
                    'description_en' => 'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',
                ])
            );
        }
    }
}
