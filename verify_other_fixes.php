<?php

use App\Models\Karyawan;
use App\Models\Paket;
use App\Services\ContractCalculatorService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Setup Data
$paket = Paket::first();
if (!$paket) die("No Paket found\n");
$paketId = $paket->paket_id;

// Pick an employee ALREADY in this packet
$paketKaryawan = DB::table('paket_karyawan')->where('paket_id', $paketId)->where('beg_date', '<=', now())->first();
if (!$paketKaryawan) die("No employee found in Paket $paketId\n");
$karyawanId = $paketKaryawan->karyawan_id;

$karyawan = Karyawan::find($karyawanId);
// Helper logging function
function logOut($msg) {
    \Log::info("VERIFY_SCRIPT: " . $msg);
    echo $msg . "\n";
}

logOut("\nTesting with Paket: " . $paket->paket . " (ID: $paketId)");
logOut("Testing with Karyawan: " . $karyawan->nama_tk . " (ID: $karyawanId)");

// ==========================================
// TEST 1: PROMOTION (Jabatan Change)
// ==========================================
logOut("\n--- TEST 1: PROMOTION ---");
$newJabatan = DB::table('md_jabatan')->where('kode_jabatan', '!=', '0')->first(); // Get any rank
if ($newJabatan) {
    logOut("Simulating Promotion to: " . $newJabatan->jabatan);
    $begDate = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
    $calcPeriode = Carbon::now()->addMonth()->format('Y-m');

    // Insert
    DB::table('riwayat_jabatan')->insert([
        'karyawan_id' => $karyawanId,
        'kode_jabatan' => $newJabatan->kode_jabatan,
        'beg_date' => $begDate
    ]);

    // Verify Insert
    $history = DB::table('riwayat_jabatan')->where('karyawan_id', $karyawanId)->orderByDesc('beg_date')->get();
    logOut("History in DB:");
    foreach($history as $h) {
        logOut(" - " . $h->beg_date . " | " . $h->kode_jabatan);
    }

    // Trigger Calc
    try {
        $service = app(ContractCalculatorService::class);
        $result = $service->calculateForPaket($paketId, $calcPeriode);
        $breakdown = $result->breakdown_json;
        
        // Find employee
        $k_data = null;
        foreach($breakdown['karyawan'] as $k) {
            if ($k['karyawan_id'] == $karyawanId) {
                $k_data = $k;
                break;
            }
        }

        if ($k_data && $k_data['jabatan'] == $newJabatan->jabatan) {
            logOut("SUCCESS: Jabatan updated to " . $k_data['jabatan']);
        } else {
            logOut("FAILURE: Jabatan mismatch. Expected " . $newJabatan->jabatan . ", Got " . ($k_data['jabatan'] ?? 'null'));
            // Check what Jabatan ID corresponds to the Got name
            $gotJabatan = DB::table('md_jabatan')->where('jabatan', $k_data['jabatan'] ?? '')->first();
            if ($gotJabatan) logOut("Got Jabatan ID: " . $gotJabatan->kode_jabatan);
        }

    } catch (\Exception $e) {
        logOut("ERROR: " . $e->getMessage());
    }

    // Cleanup
    DB::table('riwayat_jabatan')->where('karyawan_id', $karyawanId)->where('beg_date', $begDate)->delete();
}

// ==========================================
// TEST 2: AREA CHANGE
// ==========================================
logOut("\n--- TEST 2: AREA CHANGE ---");
$originalArea = $karyawan->area_id;
$newArea = DB::table('md_area')->where('area_id', '!=', $originalArea)->first();

if ($newArea) {
    logOut("Simulating Move to Area: " . $newArea->area);
    
    // Update Karyawan
    $karyawan->area_id = $newArea->area_id;
    $karyawan->save();

    // Trigger Calc for CURRENT month
    $calcPeriode = date('Y-m');
    
    try {
        $service = app(ContractCalculatorService::class);
        $result = $service->calculateForPaket($paketId, $calcPeriode);
        
        logOut("Calculation ran successfully for Area change.");

    } catch (\Exception $e) {
        logOut("ERROR: " . $e->getMessage());
    }

    // Restaur Original
    $karyawan->area_id = $originalArea;
    $karyawan->save();
}

logOut("Verification Complete.");
