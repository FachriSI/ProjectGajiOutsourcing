<?php

// Simple script to get users from database
$host = '127.0.0.1';
$db = 'dataoutsourcingnew';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Users in Database ===\n\n";

    $stmt = $pdo->query("SELECT id, name, email FROM users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "Found " . count($users) . " user(s):\n\n";
        foreach ($users as $user) {
            echo "ID: {$user['id']}\n";
            echo "Name: {$user['name']}\n";
            echo "Email: {$user['email']}\n";
            echo "---\n";
        }
    } else {
        echo "No users found in database.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
