<?php

echo "=== Importing Database ===\n\n";

$dbHost = '127.0.0.1';
$dbName = 'db_gaji_outsourcing';
$dbUser = 'root';
$dbPass = '';
$sqlFile = __DIR__ . '/dataoutsourcingnew (2).sql';

if (!file_exists($sqlFile)) {
    die("❌ SQL file not found: $sqlFile\n");
}

echo "📂 SQL File: $sqlFile\n";
echo "📊 File size: " . round(filesize($sqlFile) / 1024 / 1024, 2) . " MB\n\n";

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "✅ Connected to database: $dbName\n";
    echo "⏳ Importing SQL file...\n\n";

    // Read and execute SQL file
    $sql = file_get_contents($sqlFile);

    // Split by semicolons but be careful with stored procedures
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function ($stmt) {
            return !empty($stmt) && substr($stmt, 0, 2) !== '--';
        }
    );

    $count = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $count++;
                if ($count % 50 == 0) {
                    echo "  Executed $count statements...\n";
                }
            } catch (PDOException $e) {
                // Ignore some errors like "table already exists"
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "⚠️  Warning on statement $count: " . substr($e->getMessage(), 0, 100) . "\n";
                }
            }
        }
    }

    echo "\n✅ Import completed! Executed $count statements.\n\n";

    // Verify import
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Tables in database: " . count($tables) . "\n";

    // Check for users table
    if (in_array('users', $tables)) {
        $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "✅ Users table found with $userCount users\n";

        // Check for loly
        $stmt = $pdo->prepare("SELECT email, name FROM users WHERE email = 'loly@gmail.com'");
        $stmt->execute();
        $loly = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($loly) {
            echo "✅ User loly@gmail.com found: {$loly['name']}\n";
        } else {
            echo "⚠️  User loly@gmail.com NOT found\n";
        }
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
