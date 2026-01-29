<?php

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "DATABASE SCAN RESULTS\n";
    echo str_repeat("=", 60) . "\n\n";

    $databases = ['dataoutsourching', 'dataoutcourching', 'dataoutsourcingnew'];

    foreach ($databases as $db) {
        echo "Database: $db\n";

        try {
            $pdo->exec("USE `$db`");

            // Check users table
            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            echo "  Total users: $userCount\n";

            // Check loly
            $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = 'loly@gmail.com'");
            $stmt->execute();
            $loly = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($loly) {
                echo "  >>> LOLY FOUND: {$loly['name']} (ID: {$loly['id']})\n";
            } else {
                echo "  Loly: NOT FOUND\n";
            }

            // Count all tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "  Total tables: " . count($tables) . "\n";

        } catch (PDOException $e) {
            echo "  ERROR: Database does not exist\n";
        }

        echo "\n";
    }

    echo str_repeat("=", 60) . "\n";
    echo "RECOMMENDATION:\n";
    echo "Use the database that has:\n";
    echo "  1. User loly@gmail.com exists\n";
    echo "  2. Has the most tables/data\n";
    echo str_repeat("=", 60) . "\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
