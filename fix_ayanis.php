<?php

use Illuminate\Support\Facades\DB;
use App\Services\ContractCalculatorService;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "--- FIXING AYANIS STATUS ---\n";

// 1. Find Ayanis (ID 1213 based on screenshot, but let's match by name too to be safe)
$ayanis = DB::table('md_karyawan')->where('nama_tk', 'like', '%Ayanis%')->first();

if ($ayanis) {
    echo "Found Ayanis: ID {$ayanis->karyawan_id}, Status: {$ayanis->status_aktif}\n";

    if ($ayanis->status_aktif !== 'Sudah Diganti') {
        DB::table('md_karyawan')
            ->where('karyawan_id', $ayanis->karyawan_id)
            ->update([
                'status_aktif' => 'Sudah Diganti',
                'updated_at' => now()
            ]);
        echo "Updated Ayanis status to 'Sudah Diganti'.\n";
    } else {
        echo "Ayanis is already marked as 'Sudah Diganti'.\n";
    }

    // 2. Trigger Calculation for her package
    // Find the package she was in (or is appearing in)
    $paketKaryawan = DB::table('paket_karyawan')
        ->where('karyawan_id', $ayanis->karyawan_id)
        ->orderByDesc('beg_date')
        ->first();

    if ($paketKaryawan) {
        $paketId = $paketKaryawan->paket_id;
        echo "Ayanis belongs to Paket ID: $paketId\n";

        // Calculate for Current Month
        $periode = date('Y-m');
        echo "Triggering calculation for Paket $paketId, Periode $periode...\n";
        
        try {
            $calculator = app(ContractCalculatorService::class);
            $calculator->calculateForPaket($paketId, $periode);
            echo "Calculation Success!\n";
        } catch (\Exception $e) {
            echo "Calculation Failed: " . $e->getMessage() . "\n";
        }

    } else {
        echo "Could not find active package for Ayanis.\n";
    }

} else {
    echo "Ayanis not found in database.\n";
}

echo "--- DONE ---\n";
