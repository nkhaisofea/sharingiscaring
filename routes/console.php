<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('db:backup', function () {
    $connection = DB::connection();
    $driver = $connection->getDriverName();
    $pdo = $connection->getPdo();

    $quoteValue = function ($value) use ($pdo) {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $pdo->quote((string) $value);
    };

    $quoteIdentifier = function (string $identifier) use ($driver) {
        if ($driver === 'mysql') {
            return '`' . str_replace('`', '``', $identifier) . '`';
        }

        return '"' . str_replace('"', '""', $identifier) . '"';
    };

    $lines = [
        '-- SharingIsCaring database backup',
        '-- Generated at ' . now()->toDateTimeString(),
        '',
    ];

    if ($driver === 'sqlite') {
        $tables = collect(DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"));

        foreach ($tables as $table) {
            $lines[] = 'DROP TABLE IF EXISTS ' . $quoteIdentifier($table->name) . ';';
            $lines[] = $table->sql . ';';

            foreach (DB::table($table->name)->get() as $row) {
                $values = collect((array) $row)->map($quoteValue)->implode(', ');
                $columns = collect(array_keys((array) $row))->map($quoteIdentifier)->implode(', ');
                $lines[] = 'INSERT INTO ' . $quoteIdentifier($table->name) . " ({$columns}) VALUES ({$values});";
            }

            $lines[] = '';
        }
    } elseif ($driver === 'mysql') {
        $tables = collect(DB::select('SHOW TABLES'))->map(function ($row) {
            return array_values((array) $row)[0];
        });

        $lines[] = 'SET FOREIGN_KEY_CHECKS=0;';

        foreach ($tables as $table) {
            $quotedTable = $quoteIdentifier($table);
            $create = (array) DB::selectOne('SHOW CREATE TABLE ' . $quotedTable);
            $createSql = $create['Create Table'] ?? array_values($create)[1];

            $lines[] = 'DROP TABLE IF EXISTS ' . $quotedTable . ';';
            $lines[] = $createSql . ';';

            foreach (DB::table($table)->get() as $row) {
                $values = collect((array) $row)->map($quoteValue)->implode(', ');
                $columns = collect(array_keys((array) $row))->map($quoteIdentifier)->implode(', ');
                $lines[] = 'INSERT INTO ' . $quotedTable . " ({$columns}) VALUES ({$values});";
            }

            $lines[] = '';
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';
    } else {
        $this->error("The db:backup command currently supports sqlite and mysql connections. Current driver: {$driver}");
        return 1;
    }

    $target = base_path('backup.sql');

    if (File::exists($target)) {
        $target = base_path('backup-' . now()->format('Ymd-His') . '.sql');
    }

    File::put($target, implode(PHP_EOL, $lines) . PHP_EOL);

    $this->info('Database backup created: ' . $target);

    return 0;
})->purpose('Create a non-destructive SQL database backup file');
