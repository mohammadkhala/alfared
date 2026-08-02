<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Dumps the whole database as SQL, one write at a time through a callback.
 *
 * Pure PDO on purpose — this host disables proc_open/shell_exec, so mysqldump
 * is out. The caller decides where the SQL goes (a gzip stream, a plain file,
 * a zip entry) by passing a writer, so the dump logic lives in one place.
 */
class DatabaseDumper
{
    /** @param callable(string): void $write */
    public function dump(callable $write): void
    {
        $pdo = DB::connection()->getPdo();

        $write("-- Alfared database backup — " . now()->toDateTimeString() . "\n");
        $write("SET NAMES utf8mb4;\n");
        $write("SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($this->tables() as $table) {
            $create = DB::selectOne("SHOW CREATE TABLE `{$table}`");
            $createSql = $create->{'Create Table'} ?? ($create->{'Create View'} ?? null);
            if ($createSql === null) {
                continue;
            }

            $write("DROP TABLE IF EXISTS `{$table}`;\n");
            $write($createSql . ";\n\n");

            // Streamed row-by-row so a big table can't exhaust memory, batched
            // into multi-row INSERTs for a smaller file.
            $columns = null;
            $batch = [];

            foreach (DB::connection()->cursor("SELECT * FROM `{$table}`") as $row) {
                $row = (array) $row;
                $columns ??= '`' . implode('`, `', array_keys($row)) . '`';

                $values = array_map(
                    fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v),
                    array_values($row),
                );
                $batch[] = '(' . implode(', ', $values) . ')';

                if (count($batch) >= 200) {
                    $write("INSERT INTO `{$table}` ({$columns}) VALUES\n" . implode(",\n", $batch) . ";\n");
                    $batch = [];
                }
            }

            if ($batch) {
                $write("INSERT INTO `{$table}` ({$columns}) VALUES\n" . implode(",\n", $batch) . ";\n");
            }

            $write("\n");
        }

        $write("SET FOREIGN_KEY_CHECKS=1;\n");
        $write("-- Dump completed on " . now()->toDateTimeString() . "\n");
    }

    /** @return string[] */
    private function tables(): array
    {
        return array_map(
            fn ($row) => array_values((array) $row)[0],
            DB::select('SHOW TABLES'),
        );
    }
}
