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

    echo "=== ALL DATABASES ===\n";
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($databases as $db) {
        echo "\n📁 $db\n";

        // Skip system databases
        if (in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys', 'phpmyadmin'])) {
            continue;
        }

        try {
            $pdo->exec("USE `$db`");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "   Tables: " . count($tables) . "\n";

            // Check for users table
            if (in_array('users', $tables)) {
                $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                echo "   👤 Users table: $count records\n";

                // Check for loly
                $stmt = $pdo->prepare("SELECT email, name FROM users WHERE email LIKE '%loly%' LIMIT 5");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($users) {
                    foreach ($users as $user) {
                        echo "      ✅ {$user['email']} - {$user['name']}\n";
                    }
                }
            }
        } catch (PDOException $e) {
            echo "   ⚠️  Error: " . substr($e->getMessage(), 0, 50) . "\n";
        }
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
