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

    echo "=== SCANNING ALL DATABASES FOR LOLY ===\n\n";

    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $foundIn = [];

    foreach ($databases as $db) {
        // Skip system databases
        if (in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys', 'phpmyadmin'])) {
            continue;
        }

        try {
            $pdo->exec("USE `$db`");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

            // Check for users table
            if (in_array('users', $tables)) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'loly@gmail.com'");
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = 'loly@gmail.com'");
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    echo "✅ FOUND IN: $db\n";
                    echo "   Name: {$user['name']}\n";
                    echo "   Email: {$user['email']}\n";
                    echo "   ID: {$user['id']}\n\n";

                    $foundIn[] = $db;
                }
            }
        } catch (PDOException $e) {
            // Skip errors
        }
    }

    if (empty($foundIn)) {
        echo "❌ loly@gmail.com NOT FOUND in any database!\n";
    } else {
        echo "\n=== RECOMMENDATION ===\n";
        echo "✅ Use database: " . $foundIn[0] . "\n";
        echo "📝 Update .env: DB_DATABASE=" . $foundIn[0] . "\n\n";

        if (count($foundIn) > 1) {
            echo "⚠️  Also found in: " . implode(', ', array_slice($foundIn, 1)) . "\n";
            echo "   You can delete these duplicates.\n";
        }
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
