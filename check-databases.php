<?php

echo "=== Database Migration Strategy ===\n\n";

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';

try {
    // Connect ke server MySQL
    $pdo = new PDO(
        "mysql:host=$dbHost;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "📋 Available databases:\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE 'data%'");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($databases as $db) {
        echo "  - $db\n";
    }

    // Cek database lama
    if (in_array('dataoutsourcingnew', $databases)) {
        echo "\n✅ Found source database: dataoutsourcingnew\n";

        // Cek tabel users di database lama
        $pdo->exec("USE dataoutsourcingnew");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "   Tables: " . count($tables) . "\n";

        if (in_array('users', $tables)) {
            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            echo "   Users: $userCount\n";

            // Cek user loly
            $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = 'loly@gmail.com'");
            $stmt->execute();
            $loly = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($loly) {
                echo "   ✅ Found loly@gmail.com: {$loly['name']}\n\n";

                // Sarankan copy database
                echo "💡 SUGGESTION: Copy data from 'dataoutsourcingnew' to 'dataoutcourching'\n";
                echo "\nOption 1: Via phpMyAdmin\n";
                echo "  1. Export 'dataoutsourcingnew'\n";
                echo "  2. Import to 'dataoutcourching'\n\n";

                echo "Option 2: Via MySQL command\n";
                echo "  mysqldump -u root dataoutsourcingnew | mysql -u root dataoutcourching\n\n";

                echo "Option 3: Just use the old database!\n";
                echo "  Change DB_DATABASE in .env back to: dataoutsourcingnew\n";
            }
        }
    }

    // Cek database baru
    if (in_array('dataoutcourching', $databases)) {
        echo "\n📊 Target database: dataoutcourching\n";
        $pdo->exec("USE dataoutcourching");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "   Tables: " . count($tables) . "\n";

        if (in_array('users', $tables)) {
            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            echo "   Users: $userCount\n";
        } else {
            echo "   ⚠️  No users table!\n";
        }
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
