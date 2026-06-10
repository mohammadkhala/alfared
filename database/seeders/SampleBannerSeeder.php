<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SampleBannerSeeder extends Seeder
{
    public function run(): void
    {
        // ── Build banner image with GD ──────────────────────────────────────
        $w = 1200;
        $h = 500;
        $img = imagecreatetruecolor($w, $h);

        // Colors
        $navy      = imagecolorallocate($img, 27,  59,  140);
        $navyDark  = imagecolorallocate($img, 18,  40,  100);
        $orange    = imagecolorallocate($img, 233, 113,  26);
        $white     = imagecolorallocate($img, 255, 255, 255);
        $whiteGray = imagecolorallocate($img, 220, 228, 245);
        $gold      = imagecolorallocate($img, 201, 169, 110);

        // Background gradient (manual rows)
        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int)(27  + ($ratio * (18 - 27)));
            $g = (int)(59  + ($ratio * (40 - 59)));
            $b = (int)(140 + ($ratio * (100 - 140)));
            $c = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $c);
        }

        // Decorative circle right side
        imagesetthickness($img, 2);
        imagearc($img, 950, 250, 460, 460, 0, 360, imagecolorallocatealpha($img, 255,255,255, 110));
        imagearc($img, 950, 250, 320, 320, 0, 360, imagecolorallocatealpha($img, 249,115,22,  90));
        imagearc($img, 950, 250, 180, 180, 0, 360, imagecolorallocatealpha($img, 255,255,255,  70));

        // Orange accent left bar
        imagefilledrectangle($img, 60, 120, 70, 380, $orange);

        // Badge
        imagefilledrectangle($img, 88, 118, 300, 148, $orange);
        imagestring($img, 4, 100, 126, 'SUMMER SALE 2025', $white);

        // Main headline (large font via imagettftext if available, else fallback)
        $fontPath = base_path('public/fonts/Tajawal-Bold.ttf');
        if (function_exists('imagettftext') && file_exists($fontPath)) {
            imagettftext($img, 52, 0, 85, 240, $white,     $fontPath, 'تشكيلة صيف 2025');
            imagettftext($img, 28, 0, 85, 300, $whiteGray, $fontPath, 'خصومات تصل إلى 40% على أفضل الماركات');
            imagettftext($img, 22, 0, 85, 365, $gold,      $fontPath, 'توصيل سريع لفلسطين والداخل');
        } else {
            // Fallback: built-in font
            imagestring($img, 5, 85, 180, 'Tashkilat Sayf 2025',     $white);
            imagestring($img, 4, 85, 220, 'Khusumat hatta 40%',       $whiteGray);
            imagestring($img, 3, 85, 260, 'Tawsil sari3 - Filastin',  $gold);
        }

        // CTA button
        imagefilledrectangle($img, 85, 390, 305, 430, $orange);
        imagestring($img, 4, 130, 405, 'Tasawwaqi Al-An', $white);

        // Save to storage
        $path = 'banners/sample-summer-2025.jpg';
        $tmpFile = tempnam(sys_get_temp_dir(), 'banner_') . '.jpg';
        imagejpeg($img, $tmpFile, 90);
        imagedestroy($img);

        Storage::disk('public')->put($path, file_get_contents($tmpFile));
        unlink($tmpFile);

        // ── Insert banner record ─────────────────────────────────────────────
        Banner::updateOrCreate(
            ['title_ar' => 'تشكيلة صيف 2025 — خصومات تصل إلى 40%'],
            [
                'subtitle_ar'   => 'أفضل ماركات التجميل بأسعار لا تُفوَّت — توصيل سريع لفلسطين والداخل',
                'badge_text'    => '🔥 عرض محدود',
                'button_text_ar'=> 'تسوّقي الآن',
                'link'          => '/store',
                'image'         => $path,
                'position'      => 'hero',
                'sort_order'    => 1,
                'is_active'     => true,
                'starts_at'     => now(),
                'ends_at'       => now()->addDays(30),
            ]
        );

        $this->command->info('✅ تمت إضافة البانر: تشكيلة صيف 2025');
    }
}
