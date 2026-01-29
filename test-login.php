<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Login Authentication ===\n\n";

// Get all users
$users = User::all();
echo "Total users in database: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "User ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Password Hash: " . $user->password . "\n";
    echo "---\n\n";
}

// Test password for loly@gmail.com
echo "=== Testing Password for loly@gmail.com ===\n";
$lolyUser = User::where('email', 'loly@gmail.com')->first();

if ($lolyUser) {
    $testPassword = 'loly123';
    echo "Testing password: $testPassword\n";

    if (Hash::check($testPassword, $lolyUser->password)) {
        echo "✓ Password CORRECT! Authentication should work.\n";
    } else {
        echo "✗ Password WRONG! This is the problem.\n";
        echo "Generating new hash for 'loly123':\n";
        $newHash = Hash::make($testPassword);
        echo "$newHash\n\n";
        echo "Run this SQL to fix:\n";
        echo "UPDATE users SET password = '$newHash' WHERE email = 'loly@gmail.com';\n";
    }
} else {
    echo "✗ User loly@gmail.com not found!\n";
}
