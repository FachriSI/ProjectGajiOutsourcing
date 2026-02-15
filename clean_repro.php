<?php
use Illuminate\Support\Facades\DB;
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Based on previous script output: Paket 1, Karyawan 1
$paketId = 1;
$karyawanId = 1;

DB::table('paket_karyawan')
    ->where('paket_id', $paketId)
    ->where('karyawan_id', $karyawanId)
    ->where('beg_date', date('Y-m-d'))
    ->delete();

echo "Cleaned up packet_karyawan for Paket $paketId, Karyawan $karyawanId\n";
