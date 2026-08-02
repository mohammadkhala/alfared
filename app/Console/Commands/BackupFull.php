<?php

namespace App\Console\Commands;

use App\Support\DatabaseDumper;
use App\Support\GoogleDriveUploader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * A complete backup — the database plus the uploaded files — in one zip.
 *
 * Uses ZipArchive and the pure-PHP DatabaseDumper, so nothing shells out
 * (proc_open/shell_exec are disabled on this host). Meant to run weekly; the
 * daily backup:database covers the data that changes often, this adds the
 * images that rarely do but are just as unrecoverable.
 */
class BackupFull extends Command
{
    protected $signature = 'backup:full {--keep=4 : كم نسخة أسبوعية نحتفظ بها}';

    protected $description = 'نسخة شاملة أسبوعية: قاعدة البيانات + الصور المرفوعة (PHP خالص)';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $dir  = 'backups';
        $disk->makeDirectory($dir);

        $name = 'full-' . now()->format('Y-m-d-His') . '.zip';
        $path = $disk->path("{$dir}/{$name}");

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('تعذّر إنشاء ملف الأرشيف.');
            return self::FAILURE;
        }

        try {
            // 1) Database as a single SQL entry inside the zip.
            $sql = '';
            (new DatabaseDumper())->dump(function (string $s) use (&$sql) {
                $sql .= $s;
            });
            $zip->addFromString('database.sql', $sql);
            unset($sql);

            // 2) Uploaded files. storage/app/public holds the product images
            //    (main_image = "products/…"). Skip our own backups folder so a
            //    backup can't try to swallow itself.
            $uploads = 0;
            $publicRoot = storage_path('app/public');
            if (is_dir($publicRoot)) {
                $uploads = $this->addTree($zip, $publicRoot, 'uploads');
            }
        } catch (\Throwable $e) {
            $zip->close();
            @unlink($path);
            $this->error('فشل النسخ: ' . $e->getMessage());
            return self::FAILURE;
        }

        $zip->close();

        $sizeMb = round(filesize($path) / 1048576, 2);
        $this->info("✓ نسخة شاملة: {$dir}/{$name} ({$sizeMb} م.ب — قاعدة + {$uploads} ملف)");

        if ($this->offload($path, $name)) {
            $disk->delete("{$dir}/{$name}");
            return self::SUCCESS;
        }

        $this->prune($disk, $dir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    /** Adds a directory tree to the zip, returning the file count. */
    private function addTree(ZipArchive $zip, string $root, string $prefix): int
    {
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $item) {
            $relative = $prefix . '/' . str_replace('\\', '/', substr($item->getPathname(), strlen($root) + 1));

            if ($item->isDir()) {
                $zip->addEmptyDir($relative);
            } else {
                $zip->addFile($item->getPathname(), $relative);
                $count++;
            }
        }

        return $count;
    }

    /** Uploads to Google Drive when configured; true means it's safely off-site. */
    private function offload(string $path, string $name): bool
    {
        $uploader = GoogleDriveUploader::fromConfig();
        if (! $uploader) {
            return false;
        }
        try {
            $uploader->upload($path, $name);
            $this->info("☁️  رُفعت إلى Google Drive.");
            return true;
        } catch (\Throwable $e) {
            $this->warn('تعذّر الرفع إلى Google Drive (بقيت النسخة محلياً): ' . $e->getMessage());
            return false;
        }
    }

    private function prune($disk, string $dir, int $keep): void
    {
        // Keep the newest $keep full archives; delete the rest.
        $files = collect($disk->files($dir))
            ->filter(fn ($f) => str_starts_with(basename($f), 'full-') && str_ends_with($f, '.zip'))
            ->sortByDesc(fn ($f) => $disk->lastModified($f))
            ->values();

        $removed = 0;
        foreach ($files->slice(max(1, $keep)) as $file) {
            $disk->delete($file);
            $removed++;
        }

        if ($removed) {
            $this->line("حُذفت {$removed} نسخة شاملة قديمة.");
        }
    }
}
