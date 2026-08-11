<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

/**
 * Seeds three starter hero slides. Idempotent: only inserts when the table is
 * empty, so re-running never duplicates or overwrites an admin's edits.
 */
class HeroSlidesSeeder extends Seeder
{
    public function run(): void
    {
        if (HeroSlide::query()->exists()) {
            $this->command?->info('hero_slides already populated — skipped.');
            return;
        }

        $slides = [
            // Slide 1 mirrors the original static hero.
            [
                'badge_ar' => 'مجموعة 2026 وصلت', 'badge_he' => 'קולקציית 2026 הגיעה', 'badge_en' => '2026 Collection is here',
                'title_ar' => 'جمالك يبدأ من', 'title_he' => 'היופי שלך מתחיל מ', 'title_en' => 'Your beauty starts with',
                'highlight_ar' => 'أبناء الفريد', 'highlight_he' => 'אבנא אלפריד', 'highlight_en' => 'Alfared',
                'subtitle_ar' => 'الصين بين يديك في أضخم معرض للمنتجات في البلاد — أكثر من 5000 منتج، وتوصيل سريع لجميع مناطق فلسطين والداخل.',
                'subtitle_he' => 'סין בהישג יד בתערוכת המוצרים הגדולה בארץ — יותר מ-5000 מוצרים ומשלוח מהיר לכל האזורים.',
                'subtitle_en' => 'China at your fingertips — the largest product showcase, 5000+ products with fast delivery everywhere.',
                'btn1_text_ar' => 'تسوّق الآن', 'btn1_text_he' => 'לקנייה', 'btn1_text_en' => 'Shop now', 'btn1_url' => '/products',
                'btn2_text_ar' => 'تواصل معنا', 'btn2_text_he' => 'צרו קשר', 'btn2_text_en' => 'Contact us', 'btn2_url' => 'https://wa.me/970598191312',
                'image' => null, 'sort_order' => 1,
            ],
            // Slide 2 — offers.
            [
                'badge_ar' => 'عروض لا تُفوّت', 'badge_he' => 'מבצעים שאסור לפספס', 'badge_en' => "Deals you can't miss",
                'title_ar' => 'خصومات تصل إلى', 'title_he' => 'הנחות עד', 'title_en' => 'Discounts up to',
                'highlight_ar' => '40% هذا الأسبوع', 'highlight_he' => '40% השבוע', 'highlight_en' => '40% this week',
                'subtitle_ar' => 'أفضل الأسعار على مستحضرات التجميل والعطور والعناية — اطلب اليوم واستفد قبل نفاد الكمية.',
                'subtitle_he' => 'המחירים הטובים ביותר על קוסמטיקה, בשמים וטיפוח — הזמינו היום לפני שייגמר.',
                'subtitle_en' => 'The best prices on cosmetics, perfumes and care — order today before stock runs out.',
                'btn1_text_ar' => 'اكتشف العروض', 'btn1_text_he' => 'למבצעים', 'btn1_text_en' => 'See offers', 'btn1_url' => '/products?on_sale=1',
                'btn2_text_ar' => 'تواصل معنا', 'btn2_text_he' => 'צרו קשר', 'btn2_text_en' => 'Contact us', 'btn2_url' => 'https://wa.me/970598191312',
                'image' => null, 'sort_order' => 2,
            ],
            // Slide 3 — for retailers.
            [
                'badge_ar' => 'لأصحاب المحلات والتجار', 'badge_he' => 'לחנויות וסוחרים', 'badge_en' => 'For shops & resellers',
                'title_ar' => 'شريكك للجملة', 'title_he' => 'השותף שלך לסיטונאות', 'title_en' => 'Your wholesale partner',
                'highlight_ar' => 'من المصدر مباشرة', 'highlight_he' => 'ישירות מהמקור', 'highlight_en' => 'straight from the source',
                'subtitle_ar' => 'نستورد لك أفضل المنتجات من الصين بأسعار الجملة — مخزون ضخم دائماً، وتوصيل موثوق لكل فلسطين.',
                'subtitle_he' => 'אנו מייבאים עבורכם את המוצרים הטובים ביותר מסין במחירי סיטונאות — מלאי גדול ומשלוח אמין.',
                'subtitle_en' => 'We import the best products from China at wholesale prices — always in stock, delivered across Palestine.',
                'btn1_text_ar' => 'تصفّح المنتجات', 'btn1_text_he' => 'עיון במוצרים', 'btn1_text_en' => 'Browse products', 'btn1_url' => '/products',
                'btn2_text_ar' => 'اطلب عرض سعر', 'btn2_text_he' => 'בקשת הצעת מחיר', 'btn2_text_en' => 'Request a quote', 'btn2_url' => 'https://wa.me/970598191312',
                'image' => null, 'sort_order' => 3,
            ],
        ];

        foreach ($slides as $s) {
            HeroSlide::create($s + ['is_active' => true]);
        }

        $this->command?->info('✓ زُرعت 3 شرائح للسلايدر.');
    }
}
