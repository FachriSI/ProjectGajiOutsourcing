<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paket;

$paket = Paket::find(162);
if ($paket) {
    echo "Paket 162 found. Deleted Status: " . ($paket->is_deleted ? 'DELETED' : 'ACTIVE') . "\n";
} else {
    echo "Paket 162 NOT FOUND.\n";
}
