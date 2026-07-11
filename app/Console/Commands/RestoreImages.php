<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Restores original product/banner images from the backup folder and reverts
 * the DB paths (that images:optimize rewrote to .webp) back to the originals.
 *
 * Expects a backup of storage/app/public at storage/app/public_backup.
 */
class RestoreImages extends Command
{
    protected $signature = 'images:restore {--dry-run : List what would change without writing}';

    protected $description = 'استعادة الصور الأصلية من النسخة الاحتياطية وإرجاع مساراتها في قاعدة البيانات';

    private const EXTS = ['jpeg', 'jpg', 'png', 'webp'];

    public function handle(): int
    {
        $backupRoot = storage_path('app/public_backup');
        if (! is_dir($backupRoot)) {
            $this->error("مجلد النسخة الاحتياطية غير موجود: {$backupRoot}");
            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $restored = 0;
        $missing = 0;

        $handle = function ($model, string $column) use ($backupRoot, $dry, &$restored, &$missing) {
            $path = $model->{$column};
            if (! $path || str_starts_with($path, 'http')) {
                return;
            }

            // e.g. "products/ABC.webp" → dir "products", base "ABC"
            $dir  = trim(dirname($path), './');
            $base = pathinfo($path, PATHINFO_FILENAME);

            // Find the original in the backup by basename (any known extension).
            $original = null;
            foreach (self::EXTS as $ext) {
                $candidate = "{$dir}/{$base}.{$ext}";
                if (is_file("{$backupRoot}/{$candidate}")) {
                    $original = $candidate;
                    break;
                }
            }

            if ($original === null) {
                $this->warn("لا يوجد أصل في النسخة الاحتياطية لـ: {$path}");
                $missing++;
                return;
            }

            if ($dry) {
                $this->line("سيُستعاد: {$path} → {$original}");
                $restored++;
                return;
            }

            // Copy original back into the live public disk.
            $disk = Storage::disk('public');
            $disk->put($original, file_get_contents("{$backupRoot}/{$original}"));

            // Delete the degraded webp if the optimizer created a new filename.
            if ($original !== $path && $disk->exists($path)) {
                $disk->delete($path);
            }

            // Point the DB row back at the original.
            if ($model->{$column} !== $original) {
                $model->forceFill([$column => $original])->saveQuietly();
            }

            $this->line("✓ {$path} → {$original}");
            $restored++;
        };

        Product::whereNotNull('main_image')->chunkById(100, fn ($rows) =>
            $rows->each(fn ($m) => $handle($m, 'main_image')));

        ProductImage::whereNotNull('image')->chunkById(100, fn ($rows) =>
            $rows->each(fn ($m) => $handle($m, 'image')));

        Banner::whereNotNull('image')->chunkById(100, fn ($rows) =>
            $rows->each(fn ($m) => $handle($m, 'image')));

        $this->newLine();
        $this->info("اكتملت الاستعادة. مُستعادة: {$restored} · مفقودة: {$missing}");

        return self::SUCCESS;
    }
}
