<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== Creating Login User ===\n\n";

try {
    // Check if user already exists
    $existingUser = DB::table('users')
        ->where('email', 'loly@gmail.com')
        ->first();

    if ($existingUser) {
        echo "⚠️  User loly@gmail.com already exists!\n";
        echo "Updating password...\n";

        DB::table('users')
            ->where('email', 'loly@gmail.com')
            ->update([
                'password' => Hash::make('loly123'),
                'updated_at' => now()
            ]);

        echo "✅ Password updated successfully!\n";
    } else {
        echo "Creating new user...\n";

        DB::table('users')->insert([
            'name' => 'Loly',
            'email' => 'loly@gmail.com',
            'password' => Hash::make('loly123'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ User created successfully!\n";
    }

    echo "\n📋 User Details:\n";
    echo "   Email: loly@gmail.com\n";
    echo "   Password: loly123\n";
    echo "   Name: Loly\n\n";

    echo "✅ You can now login with these credentials!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
