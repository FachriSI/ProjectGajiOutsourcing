<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Connect tanpa database dulu
    $pdo = new PDO(
        "mysql:host=127.0.0.1;port=3306",
        "root",
        ""
    );

    // Buat database
    $dbName = 'dataoutcourching';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "✅ Database '$dbName' berhasil dibuat atau sudah ada!\n";

    // Cek list database
    echo "\n📋 List database yang ada:\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE 'data%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
