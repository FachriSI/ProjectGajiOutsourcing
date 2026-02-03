<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== 👥 Daftar User di Database ===\n\n";

try {
    $users = User::all();

    if ($users->count() > 0) {
        echo "✅ Total users: " . $users->count() . "\n\n";

        foreach ($users as $index => $user) {
            echo "User #" . ($index + 1) . ":\n";
            echo "  📧 Email: {$user->email}\n";
            echo "  👤 Name: {$user->name}\n";
            echo "  🔑 Password: (encrypted)\n";
            echo "  📅 Created: {$user->created_at}\n";
            echo "  ---\n";
        }

        echo "\n✅ Users berhasil ditambahkan ke database!\n";
        echo "📝 Gunakan kredensial berikut untuk login:\n\n";
        echo "   Email: admin@admin.com\n";
        echo "   Password: password\n\n";
        echo "   Email: dev@dev.com\n";
        echo "   Password: password\n";
    } else {
        echo "❌ Tidak ada user di database.\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
