<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karyawan;
use App\Models\Paket;
use App\Models\NilaiKontrak;
use Illuminate\Support\Facades\DB;

$empId = 720;
$paketId = 115;
$periode = '2026-02';

$result = [];

// 1. Check Karyawan Status
$karyawan = Karyawan::find($empId);
if (!$karyawan) {
    $result['karyawan'] = 'NOT FOUND';
} else {
    $result['karyawan'] = [
        'id' => $karyawan->karyawan_id,
        'name' => $karyawan->nama_tk,
        'status_aktif' => $karyawan->status_aktif
    ];
}

// 2. Check Paket Karyawan Records
$history = DB::table('paket_karyawan')
    ->where('karyawan_id', $empId)
    ->orderBy('beg_date', 'desc')
    ->get();

$result['history'] = $history->map(function($h) {
    return [
        'paket_id' => $h->paket_id,
        'beg_date' => $h->beg_date
    ];
})->toArray();

// 3. Search and Check Pakets
$paketNamanya115 = Paket::where('paket', 'like', '%115%')->get();
$paketId215 = Paket::find(215);

$result['paket_search_115'] = $paketNamanya115->map(fn($p) => ['id' => $p->paket_id, 'name' => $p->paket, 'kuota' => $p->kuota_paket])->toArray();
if ($paketId215) {
    $result['paket_id_215'] = ['id' => $paketId215->paket_id, 'name' => $paketId215->paket, 'kuota' => $paketId215->kuota_paket];
}

// 3. Check Specific Paket Config
$paket = Paket::find($paketId);
if ($paket) {
    $result['paket'] = [
        'id' => $paket->paket_id,
        'name' => $paket->paket,
        'kuota' => $paket->kuota_paket
    ];
} else {
    $result['paket'] = 'NOT FOUND';
}

// 4. Check Calculation Breakdown
$nilai = NilaiKontrak::where('paket_id', $paketId)->where('periode', $periode)->first();

if ($nilai) {
    $result['nilai_kontrak'] = [
        'total_employees' => $nilai->jumlah_karyawan_total,
        'calculated_at' => $nilai->calculated_at,
        'breakdown_ids' => collect($nilai->breakdown_json['karyawan'] ?? [])->pluck('karyawan_id')->toArray()
    ];
    
    $result['is_in_calculation'] = in_array($empId, $result['nilai_kontrak']['breakdown_ids']);
} else {
    $result['nilai_kontrak'] = 'NOT FOUND';
}


file_put_contents('debug_result.json', json_encode($result, JSON_PRETTY_PRINT));
echo "Debug result saved to debug_result.json";
