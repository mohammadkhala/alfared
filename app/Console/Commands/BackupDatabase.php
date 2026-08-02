<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
            $this->dump($gz);
        } catch (\Throwable $e) {
            gzclose($gz);
            @unlink($path);   // never leave a half-written backup behind
            $this->error('فشل النسخ: ' . $e->getMessage());
            return self::FAILURE;
        }

        gzclose($gz);

        $sizeMb = round(filesize($path) / 1048576, 2);
        $this->info("✓ نسخة احتياطية: {$dir}/{$name} ({$sizeMb} م.ب)");

        $this->prune($disk, $dir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    private function dump($gz): void
    {
        $pdo = DB::connection()->getPdo();

        gzwrite($gz, "-- Alfared database backup — " . now()->toDateTimeString() . "\n");
        gzwrite($gz, "SET NAMES utf8mb4;\n");
        gzwrite($gz, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($this->tables() as $table) {
            // Schema, verbatim from the server so column types/indexes are exact.
            $create = DB::selectOne("SHOW CREATE TABLE `{$table}`");
            $createSql = $create->{'Create Table'} ?? ($create->{'Create View'} ?? null);
            if ($createSql === null) {
                continue;   // skip anything that isn't a plain table
            }

            gzwrite($gz, "DROP TABLE IF EXISTS `{$table}`;\n");
            gzwrite($gz, $createSql . ";\n\n");

            // Data — streamed one row at a time so a big table can't exhaust
            // memory. Batched into multi-row INSERTs for a smaller, faster file.
            $columns = null;
            $batch = [];
            $batchSize = 200;

            foreach (DB::connection()->cursor("SELECT * FROM `{$table}`") as $row) {
                $row = (array) $row;
                $columns ??= '`' . implode('`, `', array_keys($row)) . '`';

                $values = array_map(function ($v) use ($pdo) {
                    if ($v === null) {
                        return 'NULL';
                    }
                    return $pdo->quote((string) $v);
                }, array_values($row));

                $batch[] = '(' . implode(', ', $values) . ')';

                if (count($batch) >= $batchSize) {
                    $this->writeBatch($gz, $table, $columns, $batch);
                    $batch = [];
                }
            }

            if ($batch) {
                $this->writeBatch($gz, $table, $columns, $batch);
            }

            gzwrite($gz, "\n");
        }

        gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n");
        gzwrite($gz, "-- Dump completed on " . now()->toDateTimeString() . "\n");
    }

    private function writeBatch($gz, string $table, string $columns, array $batch): void
    {
        gzwrite($gz, "INSERT INTO `{$table}` ({$columns}) VALUES\n" . implode(",\n", $batch) . ";\n");
    }

    /** @return string[] */
    private function tables(): array
    {
        // getAllTables() returns rows keyed by a driver-specific column, so pull
        // the first value of each row rather than guessing the key.
        return array_map(
            fn ($row) => array_values((array) $row)[0],
            DB::select('SHOW TABLES'),
        );
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
