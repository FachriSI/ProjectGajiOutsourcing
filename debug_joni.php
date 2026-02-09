<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;
use App\Models\PaketKaryawan;

// Focus on the new Joni (ID 2321)
$joni = Karyawan::find(2321);
if ($joni) {
    echo "ID 2321 Details:\n";
    echo "Name: '{$joni->nama_tk}'\n";
    echo "Status: '{$joni->status_aktif}'\n";
    
    $inPaket = PaketKaryawan::where('karyawan_id', 2321)->exists();
    echo "In PaketKaryawan Table? " . ($inPaket ? "YES" : "NO") . "\n";
}

// Re-run the controller logic exact mismatch check
$assigned = PaketKaryawan::pluck('karyawan_id')->unique()->toArray();
echo "Total Assigned IDs: " . count($assigned) . "\n";

$isExcluded = in_array(2321, $assigned);
echo "Is ID 2321 in assigned list? " . ($isExcluded ? "YES" : "NO") . "\n";

$availableCount = Karyawan::whereNotIn('karyawan_id', $assigned)
                          ->where('status_aktif', 'Aktif')
                          ->count();
echo "Total Available 'Aktif' Employees: $availableCount\n";

if ($joni) {
    // Check if status matches
    $matchesStatus = $joni->status_aktif === 'Aktif';
    echo "Does Joni status === 'Aktif'? " . ($matchesStatus ? "YES" : "NO") . "\n";
}

