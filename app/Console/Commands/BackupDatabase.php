<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Support\DatabaseDumper;
use App\Support\GoogleDriveUploader;
use Illuminate\Support\Facades\Storage;

/**
 * Dumps the database to a gzipped .sql file, keeping the last few days.
 *
 * Written in pure PHP on purpose: this host disables proc_open and
 * shell_exec, so we can't call mysqldump. Everything here goes through PDO —
 * SHOW CREATE TABLE for the schema, streamed row reads for the data — and the
 * output is gzipped with PHP's own gz* functions, no external process.
 */
class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--keep=7 : كم يوماً نحتفظ بالنسخ}';

    protected $description = 'نسخة احتياطية مضغوطة لقاعدة البيانات (PHP خالص، بلا mysqldump)';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $dir  = 'backups';
        $disk->makeDirectory($dir);

        $name = 'db-' . now()->format('Y-m-d-His') . '.sql.gz';
        $path = $disk->path("{$dir}/{$name}");

        $gz = gzopen($path, 'wb9');
        if (! $gz) {
            $this->error('تعذّر فتح ملف النسخة للكتابة.');
            return self::FAILURE;
        }

        try {
            (new DatabaseDumper())->dump(fn (string $s) => gzwrite($gz, $s));
        } catch (\Throwable $e) {
            gzclose($gz);
            @unlink($path);   // never leave a half-written backup behind
            $this->error('فشل النسخ: ' . $e->getMessage());
            return self::FAILURE;
        }

        gzclose($gz);

        $sizeMb = round(filesize($path) / 1048576, 2);
        $this->info("✓ نسخة احتياطية: {$dir}/{$name} ({$sizeMb} م.ب)");

        // Push off-site if Google Drive is configured; only then drop the local
        // copy, so a failed upload never loses the backup.
        if ($this->offload($path, $name)) {
            $disk->delete("{$dir}/{$name}");
            return self::SUCCESS;
        }

        $this->prune($disk, $dir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    /** Uploads to Google Drive when configured; true means it's safely off-site. */
    private function offload(string $path, string $name): bool
    {
        $uploader = GoogleDriveUploader::fromConfig();
        if (! $uploader) {
            return false;   // not configured — keep local, prune as usual
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

    private function prune($disk, string $dir, int $keepDays): void
    {
        $cutoff = now()->subDays(max(1, $keepDays))->timestamp;
        $removed = 0;

        foreach ($disk->files($dir) as $file) {
            if (! str_ends_with($file, '.sql.gz')) {
                continue;
            }
            if ($disk->lastModified($file) < $cutoff) {
                $disk->delete($file);
                $removed++;
            }
        }

        if ($removed) {
            $this->line("حُذفت {$removed} نسخة أقدم من {$keepDays} أيام.");
        }
    }
}
