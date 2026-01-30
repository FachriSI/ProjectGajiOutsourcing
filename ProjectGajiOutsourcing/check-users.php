<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Checking Users Table ===\n\n";

try {
    // Cek apakah tabel users ada
    $userCount = User::count();
    echo "✅ Table 'users' exists!\n";
    echo "📊 Total users: $userCount\n\n";

    // Cek user loly
    $loly = User::where('email', 'loly@gmail.com')->first();

    if ($loly) {
        echo "✅ User 'loly@gmail.com' found!\n";
        echo "   - ID: {$loly->id}\n";
        echo "   - Name: {$loly->name}\n";
        echo "   - Email: {$loly->email}\n";
        echo "   - Password hash: " . substr($loly->password, 0, 30) . "...\n";
    } else {
        echo "❌ User 'loly@gmail.com' NOT found!\n";
        echo "\n📋 Available users:\n";
        $users = User::select('id', 'name', 'email')->limit(5)->get();
        foreach ($users as $user) {
            echo "   - {$user->email} ({$user->name})\n";
        }
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
