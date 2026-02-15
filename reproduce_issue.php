<?php

use App\Models\Karyawan;
use App\Models\Paket;
use App\Services\ContractCalculatorService;
use App\Models\NilaiKontrak;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Pick a specific Package (e.g. Paket 1 or first available)
$paket = Paket::first();
if (!$paket) die("No Paket found\n");
$paketId = $paket->paket_id;
echo "Testing with Paket: " . $paket->paket . " (ID: $paketId)\n";

// 2. Pick a random Employee who is NOT in this package
$assignedIds = DB::table('paket_karyawan')->where('paket_id', $paketId)->pluck('karyawan_id')->toArray();
$karyawan = Karyawan::whereNotIn('karyawan_id', $assignedIds)->where('status_aktif', 'Aktif')->first();

if (!$karyawan) die("No available Karyawan found\n");
$karyawanId = $karyawan->karyawan_id;
echo "Testing with Karyawan: " . $karyawan->nama_tk . " (ID: $karyawanId)\n";

// 3. Simulate Mutation (Insert into paket_karyawan) for NEXT MONTH
$begDate = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
DB::table('paket_karyawan')->insert([
    'karyawan_id' => $karyawanId,
    'paket_id' => $paketId,
    'beg_date' => $begDate
]);
echo "Simulated Mutation (Inserted into DB) for Date: $begDate\n";

// 4. Trigger Calculation (Mimic OLD KaryawanController behavior: Current Month)
$currentPeriode = date('Y-m');
echo "Triggering Calculation for Current Periode (Old Logic): $currentPeriode\n";

try {
    $calculatorService = app(ContractCalculatorService::class);
    $nilaikontrak = $calculatorService->calculateForPaket($paketId, $currentPeriode);
    
    // Check if Karyawan is in Breakdown of CURRENT period
    $breakdown = $nilaikontrak->breakdown_json;
    $karyawanList = $breakdown['karyawan'];
    
    $found = false;
    foreach ($karyawanList as $k) {
        if ($k['karyawan_id'] == $karyawanId) {
            $found = true;
            break;
        }
    }
    
    if ($found) {
        echo "UNEXPECTED: Karyawan found in Current Period breakdown despite future mutation!\n";
    } else {
        echo "EXPECTED: Karyawan NOT found in Current Period breakdown (Old Logic Correctly missed it).\n";
    }

    // 5. Trigger Calculation for NEW Logic (Mutation Period)
    $mutationPeriode = Carbon::parse($begDate)->format('Y-m');
    echo "Triggering Calculation for Mutation Periode (New Logic): $mutationPeriode\n";
    
    $nilaikontrakNext = $calculatorService->calculateForPaket($paketId, $mutationPeriode);
    $breakdownNext = $nilaikontrakNext->breakdown_json;
    $karyawanListNext = $breakdownNext['karyawan'];

    $foundNext = false;
    foreach ($karyawanListNext as $k) {
        if ($k['karyawan_id'] == $karyawanId) {
            $foundNext = true;
            break;
        }
    }

    if ($foundNext) {
        echo "SUCCESS: Karyawan found in Mutation Period breakdown (New Logic Works)!\n";
    } else {
        echo "FAILURE: Karyawan NOT found in Mutation Period breakdown.\n";
    }

} catch (\Exception $e) {
    echo "ERROR during calculation: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

// Cleanup
DB::table('paket_karyawan')
    ->where('karyawan_id', $karyawanId)
    ->where('paket_id', $paketId)
    ->where('beg_date', $begDate)
    ->delete();
echo "Cleanup done.\n";

