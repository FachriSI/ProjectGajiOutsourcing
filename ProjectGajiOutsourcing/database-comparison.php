<?php

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';

$output = "DATABASE COMPARISON REPORT\n";
$output .= str_repeat("=", 70) . "\n\n";

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $databases = ['dataoutsourching', 'dataoutcourching', 'dataoutsourcingnew'];
    $results = [];

    foreach ($databases as $db) {
        $info = ['name' => $db, 'exists' => false, 'tables' => 0, 'users' => 0, 'has_loly' => false];

        try {
            $pdo->exec("USE `$db`");
            $info['exists'] = true;

            // Count tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $info['tables'] = count($tables);

            // Count users
            if (in_array('users', $tables)) {
                $info['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

                // Check for loly
                $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = 'loly@gmail.com'");
                $stmt->execute();
                $loly = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($loly) {
                    $info['has_loly'] = true;
                    $info['loly_name'] = $loly['name'];
                    $info['loly_id'] = $loly['id'];
                }
            }

        } catch (PDOException $e) {
            $info['error'] = $e->getMessage();
        }

        $results[] = $info;

        $output .= "DATABASE: {$db}\n";
        if ($info['exists']) {
            $output .= "  Status: EXISTS\n";
            $output .= "  Tables: {$info['tables']}\n";
            $output .= "  Users: {$info['users']}\n";
            $output .= "  Loly: " . ($info['has_loly'] ? "FOUND - {$info['loly_name']} (ID: {$info['loly_id']})" : "NOT FOUND") . "\n";
        } else {
            $output .= "  Status: DOES NOT EXIST\n";
        }
        $output .= "\n";
    }

    $output .= str_repeat("=", 70) . "\n";
    $output .= "RECOMMENDATION:\n\n";

    // Find the best database
    $best = null;
    foreach ($results as $result) {
        if ($result['has_loly']) {
            if ($best === null || $result['users'] > $best['users']) {
                $best = $result;
            }
        }
    }

    if ($best) {
        $output .= ">>> USE THIS DATABASE: {$best['name']}\n";
        $output .= "    Reason: Has loly@gmail.com and {$best['users']} users\n\n";
        $output .= "ACTION REQUIRED:\n";
        $output .= "1. Update .env file: DB_DATABASE={$best['name']}\n";
        $output .= "2. Clear Laravel cache: php artisan config:clear\n";
        $output .= "3. Delete unused databases in phpMyAdmin\n\n";

        $output .= "DATABASES TO DELETE:\n";
        foreach ($results as $result) {
            if ($result['name'] !== $best['name'] && $result['exists']) {
                $output .= "  - {$result['name']}\n";
            }
        }
    } else {
        $output .= "ERROR: No database found with loly@gmail.com!\n";
    }

    $output .= str_repeat("=", 70) . "\n";

} catch (PDOException $e) {
    $output .= "ERROR: " . $e->getMessage() . "\n";
}

// Save to file
file_put_contents(__DIR__ . '/database-report.txt', $output);

// Also print to screen
echo $output;
