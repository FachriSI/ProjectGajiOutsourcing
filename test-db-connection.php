<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );

    echo "✅ Database connection successful!\n";
    echo "Database: " . $_ENV['DB_DATABASE'] . "\n";

    // Get table count
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '" . $_ENV['DB_DATABASE'] . "'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Tables imported: " . $result['table_count'] . "\n";

    // List some tables
    $stmt = $pdo->query("SHOW TABLES LIMIT 10");
    echo "\nSample tables:\n";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }

} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
