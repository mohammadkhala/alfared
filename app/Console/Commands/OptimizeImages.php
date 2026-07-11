<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\ImageOptimizer;
use Illuminate\Console\Command;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--dry-run : List what would change without writing}';

    protected $description = 'ضغط صور المنتجات والبانرات الموجودة وتحويلها إلى WebP';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $done = 0;
        $skipped = 0;

        $this->info($dry ? 'وضع المعاينة (لن تُعدّل أي صورة)' : 'بدء ضغط الصور...');

        // ── Product main images ──
        Product::whereNotNull('main_image')->chunkById(100, function ($products) use (&$done, &$skipped, $dry) {
            foreach ($products as $p) {
                $this->processRecord($p, 'main_image', $done, $skipped, $dry);
            }
        });

        // ── Additional product images ──
        ProductImage::whereNotNull('image')->chunkById(100, function ($images) use (&$done, &$skipped, $dry) {
            foreach ($images as $img) {
                $this->processRecord($img, 'image', $done, $skipped, $dry);
            }
        });

        // ── Banner images ──
        Banner::whereNotNull('image')->chunkById(100, function ($banners) use (&$done, &$skipped, $dry) {
            foreach ($banners as $b) {
                $this->processRecord($b, 'image', $done, $skipped, $dry);
            }
        });

        $this->newLine();
        $this->info("اكتمل. مُعالَجة: {$done} · متخطّاة: {$skipped}");

        return self::SUCCESS;
    }

    private function processRecord($model, string $column, int &$done, int &$skipped, bool $dry): void
    {
        $path = $model->{$column};

        // Skip absolute URLs (externally hosted) — only optimize local storage files.
        if (! $path || str_starts_with($path, 'http')) {
            $skipped++;
            return;
        }

        if ($dry) {
            $this->line("سيُضغط: {$path}");
            $done++;
            return;
        }

        $newPath = ImageOptimizer::optimizeStored($path);

        if ($newPath === null) {
            $this->warn("تعذّرت المعالجة: {$path}");
            $skipped++;
            return;
        }

        if ($newPath !== $path) {
            $model->forceFill([$column => $newPath])->saveQuietly();
        }

        $this->line("✓ {$path} → {$newPath}");
        $done++;
    }
}
