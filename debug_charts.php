<?php
echo '--- COST DATA ---' . PHP_EOL;
try {
    $cost = \App\Models\NilaiKontrak::with('paket.unitKerja')->get()->groupBy(fn($i) => $i->paket->unitKerja->unit_kerja ?? 'None')->map(fn($g) => $g->sum('total_nilai_kontrak'))->sortDesc()->take(5);
    dump($cost->toArray());
} catch (\Exception $e) { echo $e->getMessage() . PHP_EOL; }

echo '--- TURNOVER DATA ---' . PHP_EOL;
try {
    $turnover = \App\Models\Karyawan::whereNotNull('tanggal_berhenti')->count();
    dump('Total Karyawan Berhenti: ' . $turnover);
    
    $reasons = \App\Models\Karyawan::whereNotNull('catatan_berhenti')->groupBy('catatan_berhenti')->selectRaw('catatan_berhenti, count(*) as total')->get();
    dump($reasons->toArray());
} catch (\Exception $e) { echo $e->getMessage() . PHP_EOL; }

echo '--- SHIFT DATA ---' . PHP_EOL;
try {
    $shift = \App\Models\Riwayat_shift::count();
    dump('Total Riwayat Shift: ' . $shift);
    
    // Check if we can link to HarianShift
    $shiftDetails = \App\Models\Riwayat_shift::with('harianshift')->take(5)->get();
    foreach($shiftDetails as $s) {
        echo "Shift: " . ($s->harianshift->harianshift ?? 'NULL') . PHP_EOL;
    }
} catch (\Exception $e) { echo $e->getMessage() . PHP_EOL; }

echo '--- RISK DATA ---' . PHP_EOL;
try {
    $risk = \App\Models\Riwayat_resiko::count();
    dump('Total Riwayat Resiko: ' . $risk);
     $riskDetails = \App\Models\Riwayat_resiko::with('resiko')->take(5)->get();
    foreach($riskDetails as $r) {
        echo "Risk: " . ($r->resiko->resiko ?? 'NULL') . PHP_EOL;
    }
} catch (\Exception $e) { echo $e->getMessage() . PHP_EOL; }
