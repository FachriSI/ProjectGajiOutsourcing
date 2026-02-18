<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Area;
use App\Models\Karyawan;

// Check md_area content
$areas = DB::table('md_area')->get();
echo "Total Areas: " . $areas->count() . "\n";
foreach ($areas as $area) {
    echo "ID: " . $area->area_id . ", Area: " . $area->area . ", Deleted: " . ($area->is_deleted ?? 'N/A') . "\n";
}

// Check assignment
$karyawan = Karyawan::with('area')->first();
if ($karyawan) {
    echo "\nSample Karyawan: " . $karyawan->nama_tk . "\n";
    echo "Area ID: " . $karyawan->area_id . "\n";
    
    // Check direct DB query
    $areaDb = DB::table('md_area')->where('area_id', $karyawan->area_id)->first();
    if ($areaDb) {
        echo "Matched Area: " . $areaDb->area . "\n";
    } else {
        echo "No matching area found for ID " . $karyawan->area_id . "\n";
    }
}
