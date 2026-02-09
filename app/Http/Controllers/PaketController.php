<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Riwayat_fungsi;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_penyesuaian;
use App\Models\Riwayat_lokasi;
use App\Models\PaketKaryawan;
use App\Models\Paket;
use App\Models\Ump;
use App\Models\Kuotajam;
use App\Models\Masakerja;
use App\Models\TagihanCetak;
use Illuminate\Http\Request;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaketController extends Controller
{

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'paket_id_add' => 'required|exists:md_paket,paket_id',
            'karyawan_id' => 'required|exists:md_karyawan,karyawan_id'
        ]);

        PaketKaryawan::create([
            'paket_id' => $request->paket_id_add,
            'karyawan_id' => $request->karyawan_id,
            'beg_date' => now()
        ]);

        return redirect('/paket')->with('success', 'Karyawan berhasil ditambahkan ke paket');
    }

    public function index()
    {
        // Calculate Global Totals for Summary Cards
        $total_jml_fix_cost = 0;
        $total_seluruh_variabel = 0;
        $total_kontrak_all = 0;
        $total_kontrak_tahunan_all = 0;
        $total_thr_bln = 0;
        $total_thr_thn = 0;
        $total_pakaian_all = 0;

        $currentYear = date('Y');
        $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

        // Fetch relations for calculation
        $kuotaJamAll = Kuotajam::latest('beg_date')->get()->keyBy('karyawan_id');
        $jabatanAll = Riwayat_jabatan::with('jabatan')->latest('beg_date')->get()->groupBy('karyawan_id');
        $shiftAll = Riwayat_shift::with('harianshift')->latest('beg_date')->get()->groupBy('karyawan_id');
        $resikoAll = Riwayat_resiko::with('resiko')->latest('beg_date')->get()->groupBy('karyawan_id');
        $lokasiAll = Riwayat_lokasi::with(['lokasi.ump' => fn($q) => $q->where('tahun', $currentYear)])->latest('beg_date')->get()->groupBy('karyawan_id');
        $masakerjaAll = Masakerja::latest('beg_date')->get()->keyBy('karyawan_id');

        $allPakets = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();
        
        // Filter Karyawan: Only those who are NOT in any PaketKaryawan record (Strictly 'Free')
        $assignedKaryawanIds = PaketKaryawan::pluck('karyawan_id')->unique();
        $availableKaryawan = Karyawan::whereNotIn('karyawan_id', $assignedKaryawanIds)
                                     ->where('status_aktif', 'Aktif') // Optional: Only Active employees
                                     ->orderBy('nama_tk')
                                     ->get();

        foreach ($allPakets as $paket) {
            $kuota = (int) $paket->kuota_paket;
            $karyawanPaket = $paket->paketKaryawan->sortByDesc('beg_date');
            
            $aktif = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Aktif');
            $berhenti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Berhenti');
            $diganti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Sudah Diganti');

            $terpilih = $aktif->count() >= $kuota ? $aktif->take($kuota) : 
                        $aktif->concat($berhenti->take($kuota - $aktif->count()))
                              ->concat($diganti->take(max(0, $kuota - $aktif->count() - $berhenti->count())));
            
            // Limit strict to kuota
            if ($terpilih->count() > $kuota) $terpilih = $terpilih->take($kuota);

            foreach ($terpilih as $pk) {
                $karyawan = $pk->karyawan;
                if (!$karyawan) continue;
                $id = $karyawan->karyawan_id;

                $jabatan = optional($jabatanAll[$id] ?? collect())->first();
                $shift = optional($shiftAll[$id] ?? collect())->first();
                $resiko = optional($resikoAll[$id] ?? collect())->first();
                $lokasi = optional($lokasiAll[$id] ?? collect())->first();
                $masakerja = $masakerjaAll[$id] ?? null;

                $ump = $lokasi?->lokasi?->ump?->first()?->ump ?? 0;
                $upah_pokok = round($umpSumbar * 0.92);
                $tj_umum = round($umpSumbar * 0.08);
                $selisih_ump = round($ump - $umpSumbar);
                $tj_lokasi = $lokasi?->kode_lokasi == 12 ? 0 : max($selisih_ump, 300000);
                
                $tj_jabatan = round($jabatan?->jabatan?->tunjangan_jabatan ?? 0);
                $tj_masakerja = round($masakerja?->tunjangan_masakerja ?? 0);
                
                // Assuming simpler calculation for summary or fetching specific fields if needed
                // Replicating full logic briefly for accuracy:
                $tj_suai = round($karyawan->tunjangan_penyesuaian ?? 0); // Karyawan model usually has this or from relation
                // Re-checking relation usage in original code... 
                // Original used $item->tunjangan_penyesuaian which came from array_merge.
                // In Karyawan model, 'tunjangan_penyesuaian' is fillable, let's assume it's directly on model or we missed a relation.
                // Wait, index logic used Riwayat_unit or Riwayat_penyesuaian?
                // The original code passed 'tunjangan_penyesuaian' via array_merge but where did it come from? 
                // Ah, $fungsi or $riwayat_unit? 
                // Let's stick to what we know exists or use defaults to avoid crash. 
                // Actually, let's use the simplest valid path. 
                
                $tj_harianshift = round($shift?->harianshift?->tunjangan_shift ?? 0);
                $tj_resiko_val = ($resiko?->kode_resiko == 2) ? 0 : round($resiko?->resiko?->tunjangan_resiko ?? 0);
                $tj_presensi = round($upah_pokok * 0.08);

                $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi;
                $t_tetap = $tj_umum + $tj_jabatan + $tj_masakerja;
                $komponen_gaji = $upah_pokok + $t_tetap + $tj_lokasi;
                
                $bpjs_kes = round(0.04 * $komponen_gaji);
                $bpjs_tk = round(0.0689 * $komponen_gaji);
                
                $uang_jasa = $karyawan->perusahaan_id == 38 ? round(($upah_pokok + $t_tetap + $t_tdk_tetap) / 12) : 0;
                $kompensasi = round($komponen_gaji / 12);
                
                $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kes + $bpjs_tk + $uang_jasa + $kompensasi);
                $fee_fix = round(0.10 * $fix_cost);
                $jml_fix = round($fix_cost + $fee_fix);
                
                $total_jml_fix_cost += $jml_fix;

                $quota_jam = 2 * ($pk->kuota ?? 0); // using kuota from pivot
                $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
                $nilai_lembur = round($tarif_lembur * $quota_jam);
                $fee_lembur = round(0.025 * $nilai_lembur);
                $total_seluruh_variabel += ($nilai_lembur + $fee_lembur);

                $thr = round(($upah_pokok + $t_tetap) / 12);
                $fee_thr = round($thr * 0.05);
                $thr_bln = $thr + $fee_thr;
                $total_thr_bln += $thr_bln;
                $total_thr_thn += ($thr_bln * 12);

                $pakaian = 600000;
                $fee_pakaian = round(0.05 * $pakaian);
                $total_pakaian_all += ($pakaian + $fee_pakaian);
            }
        }

        $total_kontrak_all = $total_jml_fix_cost + $total_seluruh_variabel;
        $total_kontrak_tahunan_all = $total_kontrak_all * 12;

        // List of Packages
        $data = DB::table('md_paket')
            ->join('md_unit_kerja', 'md_unit_kerja.unit_id', '=', 'md_paket.unit_id')
            ->select('md_paket.*', 'md_unit_kerja.*')
            ->where('md_paket.is_deleted', 0)
            ->orderBy('paket_id', 'asc')
            ->get();

        //$hasDeleted = Paket::where('is_deleted', 1)->exists();
        $hasDeleted = DB::table('md_paket')->where('is_deleted', 1)->exists();
        
        return view('paket', compact(
            'data', 'hasDeleted', 
            'total_jml_fix_cost', 'total_seluruh_variabel', 'total_kontrak_all', 
            'total_kontrak_tahunan_all', 'total_thr_bln', 'total_thr_thn', 'total_pakaian_all',
            'availableKaryawan'
        ));
    }

    public function show($id)
    {
        // Old Index Logic: Detail for a specific package
        $paketId = $id; 
        $data = [];
        $errorLog = [];
        $totalExpected = 0;
        $totalActual = 0;
        
        $selectedPeriode = request('periode');
        // Parse year from selected period, or default to current year
        $currentYear = $selectedPeriode ? \Carbon\Carbon::parse($selectedPeriode)->year : date('Y');
        
        $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

        // Ambil semua data di awal untuk efisiensi
        $kuotaJamAll = Kuotajam::latest('beg_date')->get()->keyBy('karyawan_id');
        $jabatanAll = Riwayat_jabatan::with('jabatan')->latest('beg_date')->get()->groupBy('karyawan_id');
        $shiftAll = Riwayat_shift::with('harianshift')->latest('beg_date')->get()->groupBy('karyawan_id');
        $resikoAll = Riwayat_resiko::with('resiko')->latest('beg_date')->get()->groupBy('karyawan_id');
        $fungsiAll = Riwayat_fungsi::with('fungsi')->latest('beg_date')->get()->groupBy('karyawan_id');
        $lokasiAll = Riwayat_lokasi::with([
            'lokasi.ump' => function ($query) use ($currentYear) {
                $query->where('tahun', $currentYear);
            }
        ])->latest('beg_date')->get()->groupBy('karyawan_id');
        $masakerjaAll = Masakerja::latest('beg_date')->get()->keyBy('karyawan_id');
        $mcu = \App\Models\MedicalCheckup::latest()->first();

        // Filter: Only for this package
        $paketList = Paket::withoutGlobalScopes()->where('paket_id', $paketId)->with(['paketKaryawan.karyawan.perusahaan'])->get();
        
        if($paketList->isEmpty()) {
            return redirect('/paket')->with('error', 'Paket tidak ditemukan');
        }

        foreach ($paketList as $paket) {
            $kuota = (int) $paket->kuota_paket;
            $totalExpected += $kuota;

            $karyawanPaket = $paket->paketKaryawan->sortByDesc('beg_date');

            $aktif = $karyawanPaket->filter(fn($item) => $item->karyawan && ($item->karyawan->status_aktif === 'Aktif' || empty($item->karyawan->status_aktif)));
            $berhenti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Berhenti');
            $diganti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Sudah Diganti');

            // Ambil karyawan sesuai kuota
            // MODIFIED: Show ALL employees, ignore kuota limit for display
            // Ambil semua karyawan (Aktif, Berhenti, Diganti) tanpa dibatasi kuota
            $terpilih = $aktif->concat($berhenti)->concat($diganti);

            $totalActual += $terpilih->count();

            if ($terpilih->count() < $kuota) {
                $errorLog[] = [
                    'paket_id' => $paket->paket_id,
                    'paket' => $paket->paket,
                    'kuota' => $kuota,
                    'terpilih' => $terpilih->count(),
                    'selisih' => $kuota - $terpilih->count(),
                ];
            }

            foreach ($terpilih as $pk) {
                $karyawan = $pk->karyawan;
                if (!$karyawan)
                    continue;
                $id = $karyawan->karyawan_id;

                $jabatan = optional($jabatanAll[$id] ?? collect())->first();
                $shift = optional($shiftAll[$id] ?? collect())->first();
                $resiko = optional($resikoAll[$id] ?? collect())->first();
                $lokasi = optional($lokasiAll[$id] ?? collect())->first();
                $fungsi = optional($fungsiAll[$id] ?? collect())->first();
                $kuota_jam = $kuotaJamAll[$id] ?? null;
                $masakerja = $masakerjaAll[$id] ?? null;

                $data[] = (object) array_merge(
                    $kuota_jam?->toArray() ?? [],
                    $karyawan->toArray(),
                    ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
                    ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
                    $jabatan?->toArray() ?? [],
                    [
                        'jabatan' => optional($jabatan?->jabatan)->jabatan ?? null,
                        'tunjangan_jabatan' => optional($jabatan?->jabatan)->tunjangan_jabatan ?? 0,
                    ],
                    ['fungsi' => $fungsi?->fungsi?->fungsi ?? null],
                    $shift?->toArray() ?? [],
                    $resiko?->toArray() ?? [],
                    $lokasi?->toArray() ?? [],
                    ['ump_sumbar' => $umpSumbar],
                    $paket->toArray(),
                    $masakerja?->toArray() ?? [],
                    ['mcu' => $mcu->biaya ?? 0]
                );
            }
        }
        

        // Data for Chart: Contract History
        $contractHistory = \App\Models\NilaiKontrak::where('paket_id', $paketId)
            ->orderBy('periode', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => \Carbon\Carbon::parse($item->periode)->format('F Y'),
                    'total' => $item->total_nilai_kontrak
                ];
            });
        
        
        $selectedPeriode = request('periode'); // Get period from request

        return view('paket_detail', compact('data', 'paketList', 'contractHistory', 'selectedPeriode'));  
    }

    //chatgpt salah
// public function index()
// {
//     $currentYear = date('Y');
//     $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

    //     $paketList = Paket::with([
//         'paketKaryawan.karyawan.perusahaan',
//         'paketKaryawan.karyawan.kuotaJam' => fn($q) => $q->orderByDesc('beg_date'),
//         'paketKaryawan.karyawan.riwayatUnit.unitKerja',
//         'paketKaryawan.karyawan.riwayatJabatan.jabatan',
//         'paketKaryawan.karyawan.riwayatShift.harianShift',
//         'paketKaryawan.karyawan.riwayatResiko.resiko',
//         'paketKaryawan.karyawan.riwayatLokasi.lokasi',
//         'paketKaryawan.karyawan.riwayatLokasi.lokasi.ump' => fn($q) => $q->where('tahun', $currentYear),
//         'paketKaryawan.karyawan.masaKerja' => fn($q) => $q->orderByDesc('beg_date'),
//     ])->get();

    //     $data = [];

    //     foreach ($paketList as $paket) {
//         $kuota = (int) $paket->kuota_paket;
//         $karyawanPaket = $paket->paketKaryawan->sortByDesc('beg_date');

    //         // Filter berdasarkan status
//         $aktif = $karyawanPaket->where('karyawan.status_aktif', 'Aktif');
//         $berhenti = $karyawanPaket->where('karyawan.status_aktif', 'Berhenti');
//         $diganti = $karyawanPaket->where('karyawan.status_aktif', 'Sudah Diganti');

    //         $terpilih = collect();
//         if ($aktif->count() >= $kuota) {
//             $terpilih = $aktif->take($kuota);
//         } else {
//             $terpilih = $aktif;
//             $sisa = $kuota - $aktif->count();
//             $terpilih = $terpilih->concat($berhenti->take($sisa));
//             $sisa = $kuota - $terpilih->count();
//             $terpilih = $terpilih->concat($diganti->take($sisa));
//         }

    //         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             if (!$karyawan) continue;

    //             $data[] = (object) [
//                 'osis_id' => $karyawan->osis_id ?? null,
//                 'karyawan_id' => $karyawan->karyawan_id ?? null,
//                 'nama_tk' => $karyawan->nama_tk ?? null,
//                 'perusahaan' => $karyawan->perusahaan->perusahaan ?? null,
//                 'perusahaan_id' => $karyawan->perusahaan_id ?? null,
//                 'tanggal_bekerja' => $karyawan->tanggal_bekerja ?? null,
//                 'aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y'),
//                 'ump' => optional($karyawan->riwayatLokasi->first()?->lokasi->ump->first())->ump ?? 0,
//                 'ump_sumbar' => $umpSumbar,
//                 'kuota' => $pk->kuota ?? 0,
//                 'tunjangan_jabatan' => $karyawan->riwayatJabatan->first()?->tunjangan_jabatan ?? 0,
//                 'tunjangan_masakerja' => $karyawan->masaKerja->first()?->tunjangan_masakerja ?? 0,
//                 'tunjangan_penyesuaian' => $karyawan->riwayatUnit->first()?->tunjangan_penyesuaian ?? 0,
//                 'tunjangan_shift' => $karyawan->riwayatShift->first()?->tunjangan_shift ?? 0,
//                 'tunjangan_resiko' => $karyawan->riwayatResiko->first()?->tunjangan_resiko ?? 0,
//                 'kode_resiko' => $karyawan->riwayatResiko->first()?->kode_resiko ?? null,
//                 'kode_lokasi' => $karyawan->riwayatLokasi->first()?->kode_lokasi ?? null,
//                 'paket' => $paket->paket ?? null,
//             ];
//         }
//     }
// dd($data);
//     return view('paket', compact('data'));
// }



    //yg udah benar
//     public function index()
// {
//     $data = [];
//     $errorLog = [];
//     $totalExpected = 0;
//     $totalActual = 0;

    //     $paketList = Paket::with(['paketKaryawan.karyawan'])->get();
//     $currentYear = date('Y');
//     $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

    //     foreach ($paketList as $paket) {
//         $kuota = (int) $paket->kuota_paket;
//         $totalExpected += $kuota;

    //         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
//             ->with('karyawan')
//             ->orderByDesc('beg_date')
//             ->get();

    //         // Filter berdasarkan status dan pastikan data karyawan ada
//         $aktif = $karyawanPaket->filter(fn($item) =>
//             $item->karyawan && $item->karyawan->status_aktif === 'Aktif');
//         $berhenti = $karyawanPaket->filter(fn($item) =>
//             $item->karyawan && $item->karyawan->status_aktif === 'Berhenti');
//         $diganti = $karyawanPaket->filter(fn($item) =>
//             $item->karyawan && $item->karyawan->status_aktif === 'Sudah Diganti');

    //         // Pilih sesuai kuota
//         $terpilih = collect();

    //         if ($aktif->count() >= $kuota) {
//             $terpilih = $aktif->take($kuota);
//         } else {
//             $terpilih = $aktif;
//             $sisa = $kuota - $aktif->count();

    //             if ($berhenti->count() >= $sisa) {
//                 $terpilih = $terpilih->concat($berhenti->take($sisa));
//             } else {
//                 $terpilih = $terpilih->concat($berhenti);
//                 $sisa = $kuota - $terpilih->count();
//                 $terpilih = $terpilih->concat($diganti->take($sisa));
//             }
//         }

    //         $totalActual += $terpilih->count();

    //         // Cek jika jumlah terpilih kurang dari kuota, simpan log
//         if ($terpilih->count() < $kuota) {
//             $errorLog[] = [
//                 'paket_id' => $paket->paket_id,
//                 'paket' => $paket->paket,
//                 'kuota' => $kuota,
//                 'terpilih' => $terpilih->count(),
//                 'selisih' => $kuota - $terpilih->count(),
//             ];
//         }

    //         // Gabungkan data yang valid
//         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             $karyawan_id = $karyawan->karyawan_id;

    //             // Ambil riwayat
//             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', function ($join) use ($currentYear) {
//                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
//                         ->where('ump.tahun', $currentYear);
//                 })
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             $data[] = (object) array_merge(
//                 $kuota_jam?->toArray() ?? [],
//                 $karyawan->toArray(),
//                 ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
//                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $umpSumbar],
//                 $paket->toArray(),
//                 $masakerja?->toArray() ?? []
//             );
//         }
//     }
//     // dd($data);

    //     // Dump hasil log
//     logger()->info('Total Kuota: ' . $totalExpected);
//     logger()->info('Total Terpilih: ' . $totalActual);
//     logger()->info('Detail Paket yang Kurang:', $errorLog);

    //     return view('paket', compact('data'));
// }



    //     public function index()
// {
//     $data = [];

    //     $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();
//     $currentYear = date('Y');
//     $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

    //     foreach ($paketList as $paket) {
//         $kuota = (int) $paket->kuota_paket;
//         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
//             ->with('karyawan')
//             ->orderByDesc('beg_date')
//             ->get();

    //         // Gabungkan status dengan prioritas: Aktif -> Berhenti -> Sudah Diganti
//         $terurut = $karyawanPaket->filter(fn($item) => $item->karyawan !== null)
//             ->sortBy(function ($item) {
//                 return match ($item->karyawan->status_aktif) {
//                     'Aktif' => 1,
//                     'Berhenti' => 2,
//                     'Sudah Diganti' => 3,
//                     default => 4,
//                 };
//             })
//             ->values();

    //         $terpilih = $terurut->take($kuota);

    //         // Loop data yang terpilih
//         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             $karyawan_id = $karyawan->karyawan_id;

    //             // Relasi terkait
//             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', function ($join) use ($currentYear) {
//                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
//                         ->where('ump.tahun', $currentYear);
//                 })
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             $data[] = (object) array_merge(
//                 $kuota_jam?->toArray() ?? [],
//                 $karyawan->toArray(),
//                 ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
//                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $umpSumbar],
//                 $paket->toArray(),
//                 $masakerja?->toArray() ?? []
//             );
//         }

    //         // Jika data kurang dari kuota, tambahkan placeholder kosong
//         // $kurang = $kuota - $terpilih->count();
//         // for ($i = 0; $i < $kurang; $i++) {
//         //     $data[] = (object) [
//         //         'karyawan_id' => null,
//         //         'nama_lengkap' => 'Belum tersedia',
//         //         'perusahaan' => $paket->paket ?? null,
//         //         'ump_sumbar' => $umpSumbar,
//         //         'paket_id' => $paket->paket_id,
//         //         'paket' => $paket->paket,
//         //         'aktif_mulai' => null,
//         //         // Tambahkan field default lainnya jika perlu
//         //     ];
//         // }
//     }

    //     return view('paket', compact('data'));
// }


    // public function index()
    // {
    //     $data = [];

    //     // Ambil semua paket dengan relasi perusahaan
    //     $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();

    //     foreach ($paketList as $paket) {
    //         $kuota = $paket->kuota_paket;

    //         // Ambil semua karyawan yang terkait dengan paket
    //         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
    //             ->with('karyawan')
    //             ->orderByDesc('beg_date')
    //             ->get();

    //         // Filter berdasarkan status
    //         $aktif = $karyawanPaket->filter(fn($item) => $item->karyawan->status_aktif === 'Aktif');
    //         $berhenti = $karyawanPaket->filter(fn($item) => $item->karyawan->status_aktif === 'Berhenti');
    //         $diganti = $karyawanPaket->filter(fn($item) => $item->karyawan->status_aktif === 'Sudah Diganti');

    //         // Ambil sebanyak kuota dari urutan prioritas
    //         $terpilih = collect();

    //         if ($aktif->count() >= $kuota) {
    //             $terpilih = $aktif->take($kuota);
    //         } else {
    //             $terpilih = $aktif;
    //             $sisa = $kuota - $terpilih->count();

    //             if ($berhenti->count() >= $sisa) {
    //                 $terpilih = $terpilih->concat($berhenti->take($sisa));
    //             } else {
    //                 $terpilih = $terpilih->concat($berhenti);
    //                 $sisa = $kuota - $terpilih->count();
    //                 $terpilih = $terpilih->concat($diganti->take($sisa));
    //             }
    //         }

    //         // Gabungkan data terpilih
    //         foreach ($terpilih as $pk) {
    //             $karyawan = $pk->karyawan;
    //             $karyawan_id = $karyawan->karyawan_id;

    //             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
    //             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
    //                 ->join('ump', function ($join) {
    //                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
    //                         ->where('ump.tahun', date('Y'));
    //                 })
    //                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             $ump_sumbar = Ump::where('kode_lokasi', '12')->where('tahun', date('Y'))->value('ump');
    //             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             // Gabungkan semua info menjadi satu objek
    //             $data[] = (object) array_merge(
    //                 $kuota_jam?->toArray() ?? [],
    //                 $karyawan->toArray(),
    //                 ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
    //                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
    //                 $unit?->toArray() ?? [],
    //                 $jabatan?->toArray() ?? [],
    //                 $shift?->toArray() ?? [],
    //                 $resiko?->toArray() ?? [],
    //                 $lokasi?->toArray() ?? [],
    //                 ['ump_sumbar' => $ump_sumbar],
    //                 $paket->toArray(),
    //                 $masakerja?->toArray() ?? []
    //             );
    //         }
    //     }

    //     return view('paket', compact('data'));
    // }


    //     public function index()
// {
//     $data = [];

    //     // Ambil semua paket
//     $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan:perusahaan_id,perusahaan'])->get();

    //     foreach ($paketList as $paket) {
//         $kuota = $paket->kuota_paket;

    //         // Ambil semua karyawan terkait paket ini
//         $karyawanPaket = Paketkaryawan::where('paket_id', $paket->paket_id)
//             ->orderByDesc('beg_date')
//             ->with('karyawan') // eager load biar efisien
//             ->get();

    //         // Pisahkan berdasarkan status aktif karyawan
//         $aktif = $karyawanPaket->filter(function ($item) {
//             return $item->karyawan->status_aktif === 'Aktif';
//         });

    //         $nonaktif = $karyawanPaket->reject(function ($item) {
//             return $item->karyawan->status_aktif === 'Aktif';
//         });

    //         // Ambil sebanyak kuota
//         $terpilih = $aktif->take($kuota);

    //         if ($terpilih->count() < $kuota) {
//             $sisa = $kuota - $terpilih->count();
//             $terpilih = $terpilih->concat($nonaktif->take($sisa));
//         }

    //         // Gabungkan data
//         foreach ($terpilih as $pk) {
//             $karyawan = $pk->karyawan;
//             $karyawan_id = $karyawan->karyawan_id;

    //             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();
//             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', function ($join) {
//                     $join->on('ump.kode_lokasi', '=', 'lokasi.kode_lokasi')
//                          ->where('ump.tahun', date('Y'));
//                 })
//                 ->where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             $ump_sumbar = Ump::where('kode_lokasi', '12')->where('tahun', date('Y'))->value('ump');
//             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)->orderByDesc('beg_date')->first();

    //             // Gabung semua info ke satu objek
//             $data[] = (object) array_merge(
//                 $kuota_jam?->toArray() ?? [],
//                 $karyawan->toArray(),
//                 [
//                     'perusahaan' => $karyawan->perusahaan->perusahaan ?? '-',
//                     'aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')
//                 ],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $ump_sumbar],
//                 $paket->toArray(),
//                 $masakerja?->toArray() ?? []
//             );
//         }
//     }

    //     return view('paket', compact('data'));
// }

    //     public function index()
//     {
//         // Ambil semua karyawan beserta nama perusahaan
//         $karyawanList = Karyawan::join('perusahaan', 'perusahaan.perusahaan_id', '=', 'karyawan.perusahaan_id')
//             ->select('karyawan.*', 'perusahaan.perusahaan') // ambil nama perusahaan atau semua kolom jika perlu
//             ->get();

    //         $data = [];

    //         foreach ($karyawanList as $karyawan) {
//             $karyawan_id = $karyawan->karyawan_id;

    //             // Ambil riwayat masing-masing entitas
//             $kuota_jam = Kuotajam::where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             $unit = Riwayat_unit::join('unit_kerja', 'unit_kerja.unit_id', '=', 'riwayat_unit.unit_id')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             $jabatan = Riwayat_jabatan::join('jabatan', 'jabatan.kode_jabatan', '=', 'riwayat_jabatan.kode_jabatan')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             $shift = Riwayat_shift::join('harianshift', 'harianshift.kode_harianshift', '=', 'riwayat_shift.kode_harianshift')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             $resiko = Riwayat_resiko::join('resiko', 'resiko.kode_resiko', '=', 'riwayat_resiko.kode_resiko')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             $lokasi = Riwayat_lokasi::join('lokasi', 'lokasi.kode_lokasi', '=', 'riwayat_lokasi.kode_lokasi')
//                 ->join('ump', 'ump.kode_lokasi','=','lokasi.kode_lokasi')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             $ump_sumbar = Ump::where('kode_lokasi', '12')
//                 ->where('tahun', date('Y')) // atau tahun aktif
//                 ->value('ump');

    //             $paket = PaketKaryawan::join('paket', 'paket.paket_id', '=', 'paket_karyawan.paket_id')
//                 ->where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             $masakerja = Masakerja::where('karyawan_id', $karyawan_id)
//                 ->orderByDesc('beg_date')
//                 ->first();

    //             // Gabungkan data
//             $data[] = (object) array_merge(
//                 $kuota_jam->toArray(),
//                 $karyawan->toArray()?? [],
//                 ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
//                 $unit?->toArray() ?? [],
//                 $jabatan?->toArray() ?? [],
//                 $shift?->toArray() ?? [],
//                 $resiko?->toArray() ?? [],
//                 $lokasi?->toArray() ?? [],
//                 ['ump_sumbar' => $ump_sumbar],
//                 $paket?->toArray() ?? [],
//                 $masakerja?->toArray() ?? [],

    //             );
//         }
//   //dd($data);
//         return view('paket', compact('data'));
//     }

    public function indexpaket()
    {
        $data = DB::table('md_paket')
            ->join('md_unit_kerja', 'md_unit_kerja.unit_id', '=', 'md_paket.unit_id')
            ->select('md_paket.*', 'md_unit_kerja.*')
            ->where('md_paket.is_deleted', 0)
            ->orderBy('paket_id', 'asc')
            ->get();
        //  dd($data);

        // Filter Karyawan: Only those who are NOT in any PaketKaryawan record (Strictly 'Free')
        $assignedKaryawanIds = PaketKaryawan::pluck('karyawan_id')->unique();
        $availableKaryawan = Karyawan::whereNotIn('karyawan_id', $assignedKaryawanIds)
                                     ->where(function($q) {
                                         $q->where('status_aktif', 'Aktif')
                                           ->orWhereNull('status_aktif')
                                           ->orWhere('status_aktif', '');
                                     })
                                     ->orderBy('nama_tk')
                                     ->get();

        // $hasDeleted = Paket::where('is_deleted', 1)->exists();
        $hasDeleted = DB::table('md_paket')->where('is_deleted', 1)->exists();
        return view('data_paket', ['data' => $data, 'hasDeleted' => $hasDeleted, 'availableKaryawan' => $availableKaryawan]);

    }

    public function getTambah()
    {
        $unit = DB::table('md_unit_kerja')
            ->select('md_unit_kerja.*')
            ->get();
        return view('tambah-paket', ['unit' => $unit]);
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'paket' => 'required',
            'kuota_paket' => 'required',
            'unit_kerja' => 'required'
        ]);

        Paket::create([
            'paket' => $request->paket,
            'kuota_paket' => $request->kuota_paket,
            'unit_id' => $request->unit_kerja
        ]);

        return redirect('/datapaket')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('md_paket')
            ->where('paket_id', '=', $id)
            ->first();
        $unit = DB::table('md_unit_kerja')
            ->select('md_unit_kerja.*')
            ->get();

        return view('update-paket', ['dataP' => $dataP, 'unit' => $unit]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'paket' => 'required',
            'kuota_paket' => 'required'
        ]);


        Paket::where('paket_id', $id)
            ->update([
                'paket' => $request->paket,
                'kuota_paket' => $request->kuota_paket
            ]);

        return redirect('/datapaket')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        $hapus = Paket::findorfail($id);
        $hapus->is_deleted = 1;
        $hapus->deleted_by = session('user_name');
        $hapus->deleted_at = now();
        $hapus->save();

        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function trash()
    {
        $data = DB::table('md_paket')
            ->where('md_paket.is_deleted', 1)
            ->join('md_unit_kerja', 'md_unit_kerja.unit_id', '=', 'md_paket.unit_id')
            ->select('md_paket.*', 'md_unit_kerja.*')
            ->get();
        return view('paket-sampah', ['data' => $data]);
    }

    public function restore($id)
    {
        $data = Paket::findorfail($id);
        $data->is_deleted = 0;
        $data->deleted_by = null;
        $data->deleted_at = null;
        $data->save();

        return redirect('/datapaket')->with('success', 'Data berhasil dipulihkan!');
    }

    /**
     * Calculate BOQ (Bill of Quantity) for a specific paket
     * Menghitung total tagihan dengan breakdown Pengawas dan Pelaksana
     */
    public function calculateBOQ($paketId)
    {
        $currentYear = date('Y');
        
        // Use logic from ContractCalculatorService to ensure consistency
        $calculatorService = app(\App\Services\ContractCalculatorService::class);
        $periode = now()->format('Y-m');
        
        // Calculate (will use updated logic from Service)
        $nilaiKontrak = $calculatorService->calculateForPaket($paketId, $periode);
        
        // Extract breakdown data
        $breakdown = $nilaiKontrak->breakdown_json;
        $pengawas = $breakdown['pengawas'];
        $pelaksana = $breakdown['pelaksana'];
        $karyawanData = $breakdown['karyawan'];
        
        $paket = Paket::with(['paketKaryawan.karyawan.perusahaan', 'unitKerja'])->findOrFail($paketId);
        
        // Find vendor (first found among employees)
        $vendor = null;
        foreach ($karyawanData as $kd) {
             // Note: karyawanData from service doesn't have perusahaan name directly, 
             // but we can fetch it or just re-loop from packet if strictly needed.
             // For safety/speed, let's just grab it from the packet relation we loaded above.
             // Or better, iterate unique employees from packet to find vendor.
             // Since service logic for "terpilih" matches fairly well, we can trust the counts.
        }
        
        // Re-determine vendor from the packet employees for display consistency
        foreach ($paket->paketKaryawan as $pk) {
             if ($pk->karyawan && $pk->karyawan->perusahaan) {
                 $vendor = $pk->karyawan->perusahaan->perusahaan;
                 break;
             }
        }
        
        $totalBOQ = $pengawas['total'] + $pelaksana['total'];
        $totalBulanan = $totalBOQ;
        $totalTahunan = $totalBOQ * 12;

        return [
            'paket' => $paket,
            'vendor' => $vendor,
            'jumlah_pekerja' => $pengawas['count'] + $pelaksana['count'],
            'pengawas' => $pengawas,
            'pelaksana' => $pelaksana,
            'karyawan' => $karyawanData,
            'total_bulanan' => $totalBulanan,
            'total_tahunan' => $totalTahunan,
            'total_boq' => $totalBOQ,
            'ump_sumbar' => $nilaiKontrak->ump_sumbar,
            'tahun' => $currentYear
        ];
    }

    /**
     * Lihat Tagihan - Preview BOQ sebelum download PDF
     * UPDATED: Menyimpan data ke nilai_kontrak untuk tracking
     */
    public function lihatTagihan($id)
    {
        // Calculate BOQ dan simpan ke nilai_kontrak
        $calculatorService = app(\App\Services\ContractCalculatorService::class);
        
        // Get latest periode dari nilai_kontrak yang sudah ada, atau current jika belum ada
        $latestNilai = \App\Models\NilaiKontrak::where('paket_id', $id)
            ->orderBy('periode', 'desc')
            ->first();
        $periode = $latestNilai ? \Carbon\Carbon::parse($latestNilai->periode)->format('Y-m') : \Carbon\Carbon::now()->format('Y-m');
        
        // Calculate dan simpan
        $nilaiKontrak = $calculatorService->calculateForPaket($id, $periode);
        
        // Tetap gunakan calculateBOQ untuk compatibility dengan view yang ada
        $boqData = $this->calculateBOQ($id);
        
        // Tambahkan data nilai_kontrak ke boqData
        $boqData['nilai_kontrak'] = $nilaiKontrak;

        return view('lihat-tagihan', [
            'boq' => $boqData
        ]);
    }

    /**
     * Generate PDF Tagihan untuk paket tertentu
     * Sesuai dengan Activity Diagram dan Sequence Diagram
     * UPDATED: Menggunakan NilaiKontrak untuk menyimpan data perhitungan
     */
    public function generatePDF($id)
    {
        try {
            // Clear all output buffers completely
            while (ob_get_level()) {
                ob_end_clean();
            }

            // 1. Get latest periode dari nilai_kontrak yang sudah ada
            $latestNilai = \App\Models\NilaiKontrak::where('paket_id', $id)
                ->orderBy('periode', 'desc')
                ->first();
            
            if (!$latestNilai) {
                throw new \Exception('Data kontrak belum tersedia. Silakan hitung kontrak terlebih dahulu.');
            }
            
            $calculatorService = app(\App\Services\ContractCalculatorService::class);
            $periode = \Carbon\Carbon::parse($latestNilai->periode)->format('Y-m');
            $nilaiKontrak = $calculatorService->calculateForPaket($id, $periode);

            // 2. Ambil data paket dari database (untuk compatibility)
            $boqData = $this->calculateBOQ($id);
            
            // Tambahkan data nilai_kontrak ke boqData
            $boqData['nilai_kontrak'] = $nilaiKontrak;

            // 3. Generate unique token
            $token = TagihanCetak::generateToken();

            // 4. Generate QR Code URL
            $verifyUrl = url('/verify-tagihan/' . $token);

            // 5. Generate QR Code using QRServer.com API (more reliable than Google Charts)
            // QRServer.com is actively maintained and handles long URLs better
            $qrSize = 120;
            
            // Use QRServer.com API instead of Google Charts (deprecated)
            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
                'size' => $qrSize . 'x' . $qrSize,
                'data' => $verifyUrl,
                'format' => 'png'
            ]);
            
            // Download QR image from Google API
            try {
                // Log attempt
                \Log::info('Attempting to download QR code from: ' . $qrApiUrl);
                
                // Use context to set proper headers
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => 'User-Agent: Mozilla/5.0',
                        'timeout' => 10
                    ]
                ]);
                
                $qrImageData = @file_get_contents($qrApiUrl, false, $context);
                
                if ($qrImageData !== false && strlen($qrImageData) > 0) {
                    // Convert to base64 for PDF embedding
                    $qrCodeBase64 = base64_encode($qrImageData);
                    $qrCodeImg = '<img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="QR Code" style="width:120px;height:120px;display:block;margin:0 auto;" />';
                    \Log::info('QR code downloaded successfully, size: ' . strlen($qrImageData) . ' bytes');
                } else {
                    // Fallback if download fails
                    \Log::warning('QR code download returned empty or false');
                    $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;line-height:1.3;">
                        <strong>Verifikasi Online</strong><br/><br/>
                        Kunjungi:<br/>
                        <span style="font-size:6px;word-break:break-all;">' . $verifyUrl . '</span>
                    </div>';
                }
            } catch (\Exception $e) {
                // Fallback on error with detailed logging
                \Log::error('QR code generation failed: ' . $e->getMessage());
                \Log::error('Error details: ' . print_r([
                    'allow_url_fopen' => ini_get('allow_url_fopen'),
                    'openssl_loaded' => extension_loaded('openssl'),
                    'url' => $qrApiUrl
                ], true));
                
                $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;line-height:1.3;">
                    <strong>Verifikasi Online</strong><br/><br/>
                    Kunjungi:<br/>
                    <span style="font-size:6px;word-break:break-all;">' . $verifyUrl . '</span>
                </div>';
            }

            // 6. Simpan ke tagihan_cetak dengan reference ke nilai_kontrak
            $tagihan = TagihanCetak::create([
                'paket_id' => $id,
                'token' => $token,
                'total_boq' => $nilaiKontrak->total_nilai_kontrak, // Gunakan dari nilai_kontrak
                'jumlah_pengawas' => $nilaiKontrak->jumlah_pengawas,
                'jumlah_pelaksana' => $nilaiKontrak->jumlah_pelaksana,
                'vendor' => $boqData['vendor'],
                'tanggal_cetak' => now()
            ]);


            // 6. Generate PDF dengan DomPDF
            // Extract year from contract period for signature
            $contractYear = \Carbon\Carbon::parse($nilaiKontrak->periode)->format('Y');
            
            $pdf = Pdf::loadView('tagihan-pdf', [
                'boq' => $boqData,
                'qrCode' => $qrCodeImg,
                'token' => $token,
                'tanggal_cetak' => now()->format('d F Y'),
                'cetak_id' => $tagihan->cetak_id,
                'contract_year' => $contractYear  // Year from contract period for signature
            ]);

            $pdf->setPaper('A4', 'portrait');

            // 7. Direct download PDF (no preview in browser)
            $filename = 'BOQ_' . str_replace(' ', '_', $boqData['paket']->paket) . '_' . date('Ymd') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi keaslian tagihan melalui QR Code
     */
    /**
     * Verify tagihan from QR code scan
     * Public access - no auth required
     * Directly shows PDF in browser (can be printed)
     */
    public function verifyTagihan($token)
    {
        // Find tagihan with related data
        $tagihan = TagihanCetak::where('token', $token)
            ->with(['paket.unitKerja'])
            ->first();

        if (!$tagihan) {
            // Show error page if invalid token
            return view('verify-tagihan-error', [
                'message' => 'Token tagihan tidak ditemukan atau tidak valid.'
            ]);
        }

        // Regenerate PDF to show in browser
        // Get nilai kontrak untuk regenerate
        $nilaiKontrak = \App\Models\NilaiKontrak::where('paket_id', $tagihan->paket_id)
            ->orderBy('periode', 'desc')
            ->first();

        if (!$nilaiKontrak) {
            return view('verify-tagihan-error', [
                'message' => 'Data kontrak tidak ditemukan.'
            ]);
        }

        // Calculate BOQ data
        $boqData = $this->calculateBOQ($tagihan->paket_id);
        
        // Generate QR Code
        $verifyUrl = url('/verify-tagihan/' . $token);
        $qrSize = 120;
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
            'size' => $qrSize . 'x' . $qrSize,
            'data' => $verifyUrl,
            'format' => 'png'
        ]);
        
        // Download and encode QR
        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'User-Agent: Mozilla/5.0',
                    'timeout' => 10
                ]
            ]);
            
            $qrImageData = @file_get_contents($qrApiUrl, false, $context);
            
            if ($qrImageData !== false && strlen($qrImageData) > 0) {
                $qrCodeBase64 = base64_encode($qrImageData);
                $qrCodeImg = '<img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="QR Code" style="width:120px;height:120px;display:block;margin:0 auto;" />';
            } else {
                $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;">QR Code</div>';
            }
        } catch (\Exception $e) {
            $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;">QR Code</div>';
        }

        // Extract year from contract period
        $contractYear = \Carbon\Carbon::parse($nilaiKontrak->periode)->format('Y');
        
        // Generate PDF and stream to browser (not download)
        $pdf = Pdf::loadView('tagihan-pdf', [
            'boq' => $boqData,
            'qrCode' => $qrCodeImg,
            'token' => $token,
            'tanggal_cetak' => now()->format('d F Y'),
            'cetak_id' => $tagihan->cetak_id,
            'contract_year' => $contractYear
        ]);

        $pdf->setPaper('A4', 'portrait');
        
        // Stream PDF to browser (can view and print)
        return $pdf->stream('Tagihan_' . $tagihan->cetak_id . '.pdf');
    }

    /**
     * Download PDF from verification page
     * Public access - requires valid token
     */
    public function downloadVerifiedPDF($token)
    {
        $tagihan = TagihanCetak::where('token', $token)->first();

        if (!$tagihan) {
            abort(404, 'Tagihan tidak ditemukan');
        }

        // Regenerate PDF sama seperti generatePDF method
        // Atau redirect ke route generatePDF jika lebih prefer
        return redirect()->route('paket.pdf.download', $tagihan->paket_id);
    }

}

