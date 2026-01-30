<?php

// Delete migration files
$migrationFiles = [
    __DIR__ . '/database/migrations/2014_10_12_100000_create_password_resets_table.php',
    __DIR__ . '/database/migrations/2019_08_19_000000_create_failed_jobs_table.php'
];

foreach ($migrationFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Deleted: $file\n";
    } else {
        echo "File not found: $file\n";
    }
}

// Include Laravel's bootstrap
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Drop tables from database
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (Schema::hasTable('password_resets')) {
    Schema::drop('password_resets');
    echo "Table 'password_resets' dropped successfully.\n";
} else {
    echo "Table 'password_resets' does not exist.\n";
}

if (Schema::hasTable('failed_jobs')) {
    Schema::drop('failed_jobs');
    echo "Table 'failed_jobs' dropped successfully.\n";
} else {
    echo "Table 'failed_jobs' does not exist.\n";
}

echo "\nAll done!\n";
