<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /* ── Unsplash image helpers ─────────────────────────────── */
    private function img(string $id, int $w = 400): string
    {
        return "https://images.unsplash.com/photo-{$id}?w={$w}&q=80&fit=crop";
    }

    public function run(): void
    {
        /* ── Categories ── */
        $makeup   = Category::where('slug', 'makeup')->first();
        $skincare = Category::where('slug', 'skincare')->first();
        $hair     = Category::where('slug', 'hair')->first();
        $perfume  = Category::where('slug', 'perfume')->first();
        $nails    = Category::where('slug', 'nails')->first();
        $devices  = Category::where('slug', 'devices')->first();
        $body     = Category::where('slug', 'body')->first();

        /* ── Brands ── */
        $loreal     = Brand::where('slug', 'loreal')->first();
        $maybelline = Brand::where('slug', 'maybelline')->first();
        $nyx        = Brand::where('slug', 'nyx')->first();
        $nivea      = Brand::where('slug', 'nivea')->first();
        $garnier    = Brand::where('slug', 'garnier')->first();
        $pantene    = Brand::where('slug', 'pantene')->first();
        $essence    = Brand::where('slug', 'essence')->first();
        $flormar    = Brand::where('slug', 'flormar')->first();

        /* ─────────────────────────────────────────────────────
         *  Confirmed Unsplash photo IDs for beauty products
         * ───────────────────────────────────────────────────── */

        // MAKEUP images
        $imgMakeup1  = $this->img('1596462502278-27bfdc403348'); // palette / brushes
        $imgMakeup2  = $this->img('1522335789203-aabd1fc54bc9'); // eye shadow / powder
        $imgMakeup3  = $this->img('1512207736890-6ffed8a84e8d'); // lipstick
        $imgMakeup4  = $this->img('1526045612212-70caf35c14df'); // eye liner
        $imgMakeup5  = $this->img('1583241800698-e8ab01830a6e'); // mascara
        $imgMakeup6  = $this->img('1631214500003-5aab2b3a2e63'); // foundation bottle
        $imgMakeup7  = $this->img('1616394158622-5b55efb6d711'); // concealer / highlighter

        // SKINCARE images
        $imgSkin1    = $this->img('1556228578-8c89e6adf883'); // skincare jars
        $imgSkin2    = $this->img('1601628828688-632f38a5a7d0'); // serum dropper
        $imgSkin3    = $this->img('1570194065650-d99fb4bedf0a'); // cleanser pump
        $imgSkin4    = $this->img('1617897903246-719242758050'); // moisturizer
        $imgSkin5    = $this->img('1612817288484-6f916006741a'); // cream jar
        $imgSkin6    = $this->img('1614859049119-c4e5ae1b87e5'); // toner / spray

        // HAIR images
        $imgHair1    = $this->img('1527799820374-dcf8d9d4a388'); // hair products
        $imgHair2    = $this->img('1590540316758-4fa9a96e2c16'); // shampoo bottles
        $imgHair3    = $this->img('1583847268964-b28dc8f51f92'); // hair oil
        $imgHair4    = $this->img('1626696058897-0cdde4d00af7'); // conditioner

        // PERFUME images
        $imgPerf1    = $this->img('1541643600914-78b084683702'); // perfume bottle clear
        $imgPerf2    = $this->img('1588776814546-daab30f310ce'); // luxury perfume
        $imgPerf3    = $this->img('1547887538-e3a2f32cb1cc');    // perfume dark bg
        $imgPerf4    = $this->img('1615634870024-1e11aae3b5de'); // perfume set

        // NAILS images
        $imgNails1   = $this->img('1604654894610-df63bc536371'); // nail polish bottles
        $imgNails2   = $this->img('1604654894610-df63bc536371?auto=format&h=400'); // reuse with diff

        // DEVICES images
        $imgDev1     = $this->img('1522338242992-e1a54906a8da'); // hair styling tool
        $imgDev2     = $this->img('1560765447-0e5ef0c84427'); // beauty devices

        // BODY images
        $imgBody1    = $this->img('1571781926291-c477ebfd024b'); // body lotion
        $imgBody2    = $this->img('1556228453-efd6c1ff04f6'); // body scrub/cream

        /* ─────────────────────────────────────────────────────
         *  Product list
         * ───────────────────────────────────────────────────── */
        $products = [

            // ══════════════════ MAKEUP ══════════════════
            [
                'name_ar' => 'أحمر شفاه ميبلين ماتي لاستينغ',
                'name_en' => 'Maybelline Matte Lasting Lipstick',
                'category_id' => $makeup?->id, 'brand_id' => $maybelline?->id,
                'price' => 35, 'compare_price' => 45, 'stock_quantity' => 50,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 120,
                'main_image' => $imgMakeup3,
            ],
            [
                'name_ar' => 'مسكارا لوريال فولوم الضخامة',
                'name_en' => "L'Oréal Volume Stretch Mascara",
                'category_id' => $makeup?->id, 'brand_id' => $loreal?->id,
                'price' => 42, 'compare_price' => null, 'stock_quantity' => 35,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.7, 'sales_count' => 89,
                'main_image' => $imgMakeup5,
            ],
            [
                'name_ar' => 'بودرة NYX HD Finishing Powder',
                'name_en' => 'NYX HD Finishing Powder',
                'category_id' => $makeup?->id, 'brand_id' => $nyx?->id,
                'price' => 55, 'compare_price' => 70, 'stock_quantity' => 28,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.3, 'sales_count' => 67,
                'main_image' => $imgMakeup2,
            ],
            [
                'name_ar' => 'كحل مايبلين ماستر دراما',
                'name_en' => 'Maybelline Master Drama Eye Liner',
                'category_id' => $makeup?->id, 'brand_id' => $maybelline?->id,
                'price' => 28, 'compare_price' => null, 'stock_quantity' => 60,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.6, 'sales_count' => 145,
                'main_image' => $imgMakeup4,
            ],
            [
                'name_ar' => 'فاونديشن إيسينس ستاي ماتي',
                'name_en' => 'Essence Stay Matt Foundation',
                'category_id' => $makeup?->id, 'brand_id' => $essence?->id,
                'price' => 38, 'compare_price' => 48, 'stock_quantity' => 45,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.2, 'sales_count' => 78,
                'main_image' => $imgMakeup6,
            ],
            [
                'name_ar' => 'بلاشر فلورمار كوزي روز',
                'name_en' => 'Flormar Cozy Rose Blush',
                'category_id' => $makeup?->id, 'brand_id' => $flormar?->id,
                'price' => 32, 'compare_price' => null, 'stock_quantity' => 40,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.0, 'sales_count' => 55,
                'main_image' => $imgMakeup2,
            ],
            [
                'name_ar' => 'باليت ظلال عيون NYX 12 لون',
                'name_en' => 'NYX 12-Color Eye Shadow Palette',
                'category_id' => $makeup?->id, 'brand_id' => $nyx?->id,
                'price' => 85, 'compare_price' => 110, 'stock_quantity' => 20,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.8, 'sales_count' => 95,
                'main_image' => $imgMakeup1,
            ],
            [
                'name_ar' => 'كونسيلر لوريال ترو ماتش',
                'name_en' => "L'Oréal True Match Concealer",
                'category_id' => $makeup?->id, 'brand_id' => $loreal?->id,
                'price' => 48, 'compare_price' => 60, 'stock_quantity' => 33,
                'is_featured' => false, 'is_new' => true, 'rating_avg' => 4.5, 'sales_count' => 88,
                'main_image' => $imgMakeup7,
            ],
            [
                'name_ar' => 'هايلايتر إيسينس براق',
                'name_en' => 'Essence Luminous Highlighter',
                'category_id' => $makeup?->id, 'brand_id' => $essence?->id,
                'price' => 25, 'compare_price' => null, 'stock_quantity' => 55,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.3, 'sales_count' => 72,
                'main_image' => $imgMakeup7,
            ],
            [
                'name_ar' => 'أحمر شفاه غلوسي مايبلين',
                'name_en' => 'Maybelline Glossy Lipstick',
                'category_id' => $makeup?->id, 'brand_id' => $maybelline?->id,
                'price' => 30, 'compare_price' => 38, 'stock_quantity' => 70,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.4, 'sales_count' => 110,
                'main_image' => $imgMakeup3,
            ],
            [
                'name_ar' => 'برايمر الوجه NYX Studio Perfect',
                'name_en' => 'NYX Studio Perfect Primer',
                'category_id' => $makeup?->id, 'brand_id' => $nyx?->id,
                'price' => 62, 'compare_price' => 78, 'stock_quantity' => 25,
                'is_featured' => false, 'is_new' => true, 'rating_avg' => 4.6, 'sales_count' => 63,
                'main_image' => $imgMakeup6,
            ],
            [
                'name_ar' => 'برونزر فلورمار سمر ستايل',
                'name_en' => 'Flormar Summer Style Bronzer',
                'category_id' => $makeup?->id, 'brand_id' => $flormar?->id,
                'price' => 45, 'compare_price' => 55, 'stock_quantity' => 30,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.1, 'sales_count' => 58,
                'main_image' => $imgMakeup2,
            ],
            [
                'name_ar' => 'قلم حواجب لوريال ميكرو براو',
                'name_en' => "L'Oréal Micro Brow Pencil",
                'category_id' => $makeup?->id, 'brand_id' => $loreal?->id,
                'price' => 38, 'compare_price' => null, 'stock_quantity' => 48,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.4, 'sales_count' => 82,
                'main_image' => $imgMakeup4,
            ],
            [
                'name_ar' => 'ريميل رموش إيسينس دابل',
                'name_en' => 'Essence Double Lash Mascara',
                'category_id' => $makeup?->id, 'brand_id' => $essence?->id,
                'price' => 22, 'compare_price' => null, 'stock_quantity' => 65,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.2, 'sales_count' => 97,
                'main_image' => $imgMakeup5,
            ],
            [
                'name_ar' => 'سيتينغ سبراي NYX مثبت مكياج',
                'name_en' => 'NYX Matte Finish Makeup Setting Spray',
                'category_id' => $makeup?->id, 'brand_id' => $nyx?->id,
                'price' => 68, 'compare_price' => 85, 'stock_quantity' => 18,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.7, 'sales_count' => 74,
                'main_image' => $imgMakeup1,
            ],

            // ══════════════════ SKINCARE ══════════════════
            [
                'name_ar' => 'كريم نيفيا الترطيب اليومي 150مل',
                'name_en' => 'Nivea Daily Moisturizer Cream 150ml',
                'category_id' => $skincare?->id, 'brand_id' => $nivea?->id,
                'price' => 29, 'compare_price' => null, 'stock_quantity' => 80,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.8, 'sales_count' => 200,
                'main_image' => $imgSkin1,
            ],
            [
                'name_ar' => 'سيروم فيتامين C من غارنييه',
                'name_en' => 'Garnier Vitamin C Brightening Serum',
                'category_id' => $skincare?->id, 'brand_id' => $garnier?->id,
                'price' => 65, 'compare_price' => 80, 'stock_quantity' => 25,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.6, 'sales_count' => 93,
                'main_image' => $imgSkin2,
            ],
            [
                'name_ar' => 'غسول وجه نيفيا للبشرة العادية',
                'name_en' => 'Nivea Face Wash Normal Skin',
                'category_id' => $skincare?->id, 'brand_id' => $nivea?->id,
                'price' => 22, 'compare_price' => null, 'stock_quantity' => 100,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.4, 'sales_count' => 155,
                'main_image' => $imgSkin3,
            ],
            [
                'name_ar' => 'مرطب لوريال هيالورونيك اكسبرت',
                'name_en' => "L'Oréal Hyaluronic Expert Moisturizer",
                'category_id' => $skincare?->id, 'brand_id' => $loreal?->id,
                'price' => 72, 'compare_price' => 90, 'stock_quantity' => 18,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.7, 'sales_count' => 77,
                'main_image' => $imgSkin4,
            ],
            [
                'name_ar' => 'واقي شمس غارنييه SPF50 خفيف',
                'name_en' => 'Garnier SPF50 Light Sunscreen',
                'category_id' => $skincare?->id, 'brand_id' => $garnier?->id,
                'price' => 58, 'compare_price' => 72, 'stock_quantity' => 35,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.5, 'sales_count' => 112,
                'main_image' => $imgSkin5,
            ],
            [
                'name_ar' => 'قناع وجه ورقي نيفيا مرطب',
                'name_en' => 'Nivea Sheet Face Mask Hydrating',
                'category_id' => $skincare?->id, 'brand_id' => $nivea?->id,
                'price' => 15, 'compare_price' => null, 'stock_quantity' => 200,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.3, 'sales_count' => 180,
                'main_image' => $imgSkin5,
            ],
            [
                'name_ar' => 'تونر غارنييه آيس أكوا',
                'name_en' => 'Garnier Ice Aqua Toner',
                'category_id' => $skincare?->id, 'brand_id' => $garnier?->id,
                'price' => 45, 'compare_price' => 55, 'stock_quantity' => 42,
                'is_featured' => false, 'is_new' => true, 'rating_avg' => 4.4, 'sales_count' => 88,
                'main_image' => $imgSkin6,
            ],
            [
                'name_ar' => 'كريم العيون لوريال ريفيتاليفت',
                'name_en' => "L'Oréal Revitalift Eye Cream",
                'category_id' => $skincare?->id, 'brand_id' => $loreal?->id,
                'price' => 95, 'compare_price' => 120, 'stock_quantity' => 15,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.6, 'sales_count' => 55,
                'main_image' => $imgSkin4,
            ],
            [
                'name_ar' => 'ماء ميسيلار غارنييه 400مل',
                'name_en' => 'Garnier Micellar Water 400ml',
                'category_id' => $skincare?->id, 'brand_id' => $garnier?->id,
                'price' => 32, 'compare_price' => null, 'stock_quantity' => 75,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.7, 'sales_count' => 165,
                'main_image' => $imgSkin6,
            ],
            [
                'name_ar' => 'سيروم ريتينول نيفيا للتجديد',
                'name_en' => 'Nivea Retinol Renewing Night Serum',
                'category_id' => $skincare?->id, 'brand_id' => $nivea?->id,
                'price' => 88, 'compare_price' => 110, 'stock_quantity' => 12,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.8, 'sales_count' => 42,
                'main_image' => $imgSkin2,
            ],
            [
                'name_ar' => 'مقشر وجه غارنييه حبيبات',
                'name_en' => 'Garnier Grain Face Scrub',
                'category_id' => $skincare?->id, 'brand_id' => $garnier?->id,
                'price' => 35, 'compare_price' => 44, 'stock_quantity' => 50,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.2, 'sales_count' => 98,
                'main_image' => $imgSkin3,
            ],
            [
                'name_ar' => 'كريم ليلي لوريال مضاد للتجاعيد',
                'name_en' => "L'Oréal Anti-Wrinkle Night Cream",
                'category_id' => $skincare?->id, 'brand_id' => $loreal?->id,
                'price' => 78, 'compare_price' => 95, 'stock_quantity' => 20,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 68,
                'main_image' => $imgSkin1,
            ],

            // ══════════════════ HAIR CARE ══════════════════
            [
                'name_ar' => 'شامبو بانتين للشعر التالف',
                'name_en' => 'Pantene Repair & Protect Shampoo',
                'category_id' => $hair?->id, 'brand_id' => $pantene?->id,
                'price' => 25, 'compare_price' => null, 'stock_quantity' => 90,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 180,
                'main_image' => $imgHair2,
            ],
            [
                'name_ar' => 'بلسم غارنييه للشعر الجاف',
                'name_en' => 'Garnier Dry Hair Conditioner',
                'category_id' => $hair?->id, 'brand_id' => $garnier?->id,
                'price' => 27, 'compare_price' => 35, 'stock_quantity' => 55,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.3, 'sales_count' => 102,
                'main_image' => $imgHair4,
            ],
            [
                'name_ar' => 'زيت الشعر لوريال إكسترا أور',
                'name_en' => "L'Oréal Extra Ord-Oil Hair Oil",
                'category_id' => $hair?->id, 'brand_id' => $loreal?->id,
                'price' => 48, 'compare_price' => 60, 'stock_quantity' => 30,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.6, 'sales_count' => 85,
                'main_image' => $imgHair3,
            ],
            [
                'name_ar' => 'قناع شعر بانتين اكسترا كير',
                'name_en' => 'Pantene Extra Care Hair Mask',
                'category_id' => $hair?->id, 'brand_id' => $pantene?->id,
                'price' => 38, 'compare_price' => 48, 'stock_quantity' => 40,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.7, 'sales_count' => 73,
                'main_image' => $imgHair1,
            ],
            [
                'name_ar' => 'سيروم الشعر لوريال ستيم بودر',
                'name_en' => "L'Oréal Steam Hair Serum",
                'category_id' => $hair?->id, 'brand_id' => $loreal?->id,
                'price' => 55, 'compare_price' => 68, 'stock_quantity' => 25,
                'is_featured' => false, 'is_new' => true, 'rating_avg' => 4.5, 'sales_count' => 62,
                'main_image' => $imgHair3,
            ],
            [
                'name_ar' => 'بخاخ حماية حرارة غارنييه',
                'name_en' => 'Garnier Fructis Heat Protection Spray',
                'category_id' => $hair?->id, 'brand_id' => $garnier?->id,
                'price' => 32, 'compare_price' => null, 'stock_quantity' => 45,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.4, 'sales_count' => 88,
                'main_image' => $imgHair2,
            ],
            [
                'name_ar' => 'كريم فرد الشعر بانتين',
                'name_en' => 'Pantene Hair Smoothing Cream',
                'category_id' => $hair?->id, 'brand_id' => $pantene?->id,
                'price' => 42, 'compare_price' => 52, 'stock_quantity' => 35,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.3, 'sales_count' => 76,
                'main_image' => $imgHair1,
            ],
            [
                'name_ar' => 'شامبو غارنييه ضد القشرة',
                'name_en' => 'Garnier Anti-Dandruff Shampoo',
                'category_id' => $hair?->id, 'brand_id' => $garnier?->id,
                'price' => 28, 'compare_price' => null, 'stock_quantity' => 70,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 130,
                'main_image' => $imgHair4,
            ],
            [
                'name_ar' => 'زيت أرغان لوريال للشعر المجعد',
                'name_en' => "L'Oréal Argan Oil Curl Serum",
                'category_id' => $hair?->id, 'brand_id' => $loreal?->id,
                'price' => 65, 'compare_price' => 80, 'stock_quantity' => 22,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.8, 'sales_count' => 55,
                'main_image' => $imgHair3,
            ],
            [
                'name_ar' => 'كريم الشعر بانتين كيراتين',
                'name_en' => 'Pantene Keratin Smooth Hair Cream',
                'category_id' => $hair?->id, 'brand_id' => $pantene?->id,
                'price' => 35, 'compare_price' => 44, 'stock_quantity' => 48,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.4, 'sales_count' => 92,
                'main_image' => $imgHair1,
            ],

            // ══════════════════ PERFUME ══════════════════
            [
                'name_ar' => 'عطر فلورمار للنساء روز بلوم',
                'name_en' => 'Flormar Rose Bloom Women Perfume',
                'category_id' => $perfume?->id, 'brand_id' => $flormar?->id,
                'price' => 120, 'compare_price' => 150, 'stock_quantity' => 20,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.4, 'sales_count' => 45,
                'main_image' => $imgPerf1,
            ],
            [
                'name_ar' => 'بخاخ جسم فاخر عطر الفانيليا',
                'name_en' => 'Luxury Vanilla Body Spray',
                'category_id' => $perfume?->id, 'brand_id' => null,
                'price' => 35, 'compare_price' => null, 'stock_quantity' => 65,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.1, 'sales_count' => 70,
                'main_image' => $imgPerf2,
            ],
            [
                'name_ar' => 'عطر رجالي أكوا كلاسيك',
                'name_en' => 'Aqua Classic Men Perfume',
                'category_id' => $perfume?->id, 'brand_id' => null,
                'price' => 145, 'compare_price' => 180, 'stock_quantity' => 15,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.6, 'sales_count' => 38,
                'main_image' => $imgPerf3,
            ],
            [
                'name_ar' => 'عطر زهري فرنسي لوريال',
                'name_en' => "L'Oréal French Floral Perfume",
                'category_id' => $perfume?->id, 'brand_id' => $loreal?->id,
                'price' => 165, 'compare_price' => 200, 'stock_quantity' => 10,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.7, 'sales_count' => 28,
                'main_image' => $imgPerf1,
            ],
            [
                'name_ar' => 'عطر عود فاخر شرقي',
                'name_en' => 'Luxury Oriental Oud Perfume',
                'category_id' => $perfume?->id, 'brand_id' => null,
                'price' => 195, 'compare_price' => 240, 'stock_quantity' => 8,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.8, 'sales_count' => 22,
                'main_image' => $imgPerf3,
            ],
            [
                'name_ar' => 'بخاخ جسم روز فلورمار',
                'name_en' => 'Flormar Rose Body Mist',
                'category_id' => $perfume?->id, 'brand_id' => $flormar?->id,
                'price' => 42, 'compare_price' => 55, 'stock_quantity' => 50,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.2, 'sales_count' => 80,
                'main_image' => $imgPerf2,
            ],
            [
                'name_ar' => 'عطر نسائي فاطمة خفيف',
                'name_en' => 'Light Feminine Perfume Fatima',
                'category_id' => $perfume?->id, 'brand_id' => null,
                'price' => 88, 'compare_price' => 110, 'stock_quantity' => 18,
                'is_featured' => false, 'is_new' => true, 'rating_avg' => 4.3, 'sales_count' => 35,
                'main_image' => $imgPerf1,
            ],
            [
                'name_ar' => 'طقم عطر هدية للمرأة',
                'name_en' => "Women's Perfume Gift Set",
                'category_id' => $perfume?->id, 'brand_id' => null,
                'price' => 225, 'compare_price' => 280, 'stock_quantity' => 5,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.9, 'sales_count' => 18,
                'main_image' => $imgPerf4,
            ],
            [
                'name_ar' => 'رول أون مزيل عرق نيفيا',
                'name_en' => 'Nivea Fresh Deodorant Roll-On',
                'category_id' => $perfume?->id, 'brand_id' => $nivea?->id,
                'price' => 18, 'compare_price' => null, 'stock_quantity' => 120,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 220,
                'main_image' => $imgPerf2,
            ],
            [
                'name_ar' => 'عطر مسك أبيض كلاسيكي',
                'name_en' => 'Classic White Musk Perfume',
                'category_id' => $perfume?->id, 'brand_id' => null,
                'price' => 75, 'compare_price' => 95, 'stock_quantity' => 25,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.4, 'sales_count' => 65,
                'main_image' => $imgPerf3,
            ],

            // ══════════════════ NAILS ══════════════════
            [
                'name_ar' => 'طلاء أظافر فلورمار أحمر كلاسيك',
                'name_en' => 'Flormar Classic Red Nail Polish',
                'category_id' => $nails?->id, 'brand_id' => $flormar?->id,
                'price' => 18, 'compare_price' => null, 'stock_quantity' => 120,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.2, 'sales_count' => 190,
                'main_image' => $imgNails1,
            ],
            [
                'name_ar' => 'طلاء جل للأظافر طويل الأمد',
                'name_en' => 'Long-Lasting Gel Nail Polish',
                'category_id' => $nails?->id, 'brand_id' => null,
                'price' => 25, 'compare_price' => 32, 'stock_quantity' => 75,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 130,
                'main_image' => $imgNails1,
            ],
            [
                'name_ar' => 'طلاء أظافر إيسينس مطفي أسود',
                'name_en' => 'Essence Matte Black Nail Polish',
                'category_id' => $nails?->id, 'brand_id' => $essence?->id,
                'price' => 15, 'compare_price' => null, 'stock_quantity' => 90,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.3, 'sales_count' => 145,
                'main_image' => $imgNails1,
            ],
            [
                'name_ar' => 'طلاء جلتر ذهبي براق فلورمار',
                'name_en' => 'Flormar Shimmery Gold Glitter Polish',
                'category_id' => $nails?->id, 'brand_id' => $flormar?->id,
                'price' => 22, 'compare_price' => 28, 'stock_quantity' => 60,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.6, 'sales_count' => 88,
                'main_image' => $imgNails1,
            ],
            [
                'name_ar' => 'قاعدة أظافر لتقوية وحماية',
                'name_en' => 'Nail Strengthening Base Coat',
                'category_id' => $nails?->id, 'brand_id' => null,
                'price' => 20, 'compare_price' => null, 'stock_quantity' => 85,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.1, 'sales_count' => 110,
                'main_image' => $imgNails1,
            ],
            [
                'name_ar' => 'مزيل طلاء الأظافر برائحة الفواكه',
                'name_en' => 'Fruity Nail Polish Remover',
                'category_id' => $nails?->id, 'brand_id' => null,
                'price' => 12, 'compare_price' => null, 'stock_quantity' => 150,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.0, 'sales_count' => 200,
                'main_image' => $imgNails1,
            ],
            [
                'name_ar' => 'طقم فرش وأدوات الأظافر الاحترافي',
                'name_en' => 'Professional Nail Art Brush Set',
                'category_id' => $nails?->id, 'brand_id' => null,
                'price' => 45, 'compare_price' => 58, 'stock_quantity' => 40,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.4, 'sales_count' => 65,
                'main_image' => $imgNails1,
            ],
            [
                'name_ar' => 'طلاء فرنسي أبيض كلاسيك',
                'name_en' => 'Classic French White Nail Polish',
                'category_id' => $nails?->id, 'brand_id' => $flormar?->id,
                'price' => 18, 'compare_price' => null, 'stock_quantity' => 100,
                'is_featured' => false, 'is_new' => false, 'rating_avg' => 4.3, 'sales_count' => 155,
                'main_image' => $imgNails1,
            ],

            // ══════════════════ DEVICES ══════════════════
            [
                'name_ar' => 'جهاز تجعيد الشعر الاحترافي',
                'name_en' => 'Professional Hair Curler',
                'category_id' => $devices?->id, 'brand_id' => null,
                'price' => 185, 'compare_price' => 230, 'stock_quantity' => 15,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 48,
                'main_image' => $imgDev1,
            ],
            [
                'name_ar' => 'جهاز فرد الشعر بالكيراتين',
                'name_en' => 'Keratin Hair Straightener',
                'category_id' => $devices?->id, 'brand_id' => null,
                'price' => 215, 'compare_price' => 270, 'stock_quantity' => 12,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.7, 'sales_count' => 38,
                'main_image' => $imgDev1,
            ],
            [
                'name_ar' => 'مجفف شعر سريع 2200 واط',
                'name_en' => 'Fast Hair Dryer 2200W',
                'category_id' => $devices?->id, 'brand_id' => null,
                'price' => 155, 'compare_price' => 195, 'stock_quantity' => 20,
                'is_featured' => false, 'is_new' => true, 'rating_avg' => 4.4, 'sales_count' => 55,
                'main_image' => $imgDev1,
            ],
            [
                'name_ar' => 'جهاز تدليك الوجه بالتردد الكهربائي',
                'name_en' => 'Electric Face Massage Device',
                'category_id' => $devices?->id, 'brand_id' => null,
                'price' => 125, 'compare_price' => 160, 'stock_quantity' => 18,
                'is_featured' => true, 'is_new' => true, 'rating_avg' => 4.6, 'sales_count' => 30,
                'main_image' => $imgDev2,
            ],
            [
                'name_ar' => 'مجموعة فرش مكياج احترافية 12 قطعة',
                'name_en' => 'Professional 12-Piece Makeup Brush Set',
                'category_id' => $devices?->id, 'brand_id' => null,
                'price' => 95, 'compare_price' => 120, 'stock_quantity' => 30,
                'is_featured' => true, 'is_new' => false, 'rating_avg' => 4.5, 'sales_count' => 72,
                'main_image' => $imgMakeup1,
            ],
            [
                'name_ar' => 'ماكينة حلاقة بيكيني للمرأة',
                'name_en' => "Women's Bikini Trimmer",
                'category_id' => $devices?->id, 'brand_id' => null,
                'price' => 145, 'compare_price' => 175, 'stock_quantity' => 10,
                'is_featured' => false, 'is_new' => true, 'rating_avg' => 4.3, 'sales_count' => 25,
                'main_image' => $imgDev2,
            ],
        ];

        /* ─────────────────────────────────────────────────────
         *  Insert / update products
         * ───────────────────────────────────────────────────── */
        foreach ($products as $data) {
            $slug = Str::slug($data['name_en'] ?? $data['name_ar']) . '-' . rand(100, 999);

            Product::updateOrCreate(
                ['name_ar' => $data['name_ar']],
                array_merge($data, [
                    'name_he'        => $data['name_en'],  // placeholder
                    'slug'           => Product::where('name_ar', $data['name_ar'])->value('slug') ?? $slug,
                    'is_active'      => true,
                    'is_published'   => true,
                    'sku'            => Product::where('name_ar', $data['name_ar'])->value('sku') ?? 'SKU-' . strtoupper(Str::random(6)),
                    'description_ar' => 'منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',
                    'description_en' => 'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',
                ])
            );
        }
    }
}
