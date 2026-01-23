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
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaketController extends Controller
{

    public function index()
    {
        $data = [];
        $errorLog = [];
        $totalExpected = 0;
        $totalActual = 0;
        $currentYear = date('Y');
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

        $paketList = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();

        foreach ($paketList as $paket) {
            $kuota = (int) $paket->kuota_paket;
            $totalExpected += $kuota;

            $karyawanPaket = $paket->paketKaryawan->sortByDesc('beg_date');

            $aktif = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Aktif');
            $berhenti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Berhenti');
            $diganti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Sudah Diganti');

            // Ambil karyawan sesuai kuota
            $terpilih = collect();
            if ($aktif->count() >= $kuota) {
                $terpilih = $aktif->take($kuota);
            } else {
                $terpilih = $aktif;
                $sisa = $kuota - $aktif->count();
                $terpilih = $terpilih->concat($berhenti->take($sisa));
                $sisa = $kuota - $terpilih->count();
                $terpilih = $terpilih->concat($diganti->take($sisa));
            }

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
                    $masakerja?->toArray() ?? []
                );
            }
        }
        // dd($data[100]);
        logger()->info('Total Kuota: ' . $totalExpected);
        logger()->info('Total Terpilih: ' . $totalActual);
        logger()->info('Detail Paket yang Kurang:', $errorLog);
        return view('paket', compact('data'));
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
        $data = DB::table('paket')
            ->join('unit_kerja', 'unit_kerja.unit_id', '=', 'paket.unit_id')
            ->select('paket.*', 'unit_kerja.*')
            ->orderBy('paket_id', 'asc')
            ->get();
        //  dd($data);
        return view('data_paket', ['data' => $data]);

    }

    public function getTambah()
    {
        $unit = DB::table('unit_kerja')
            ->select('unit_kerja.*')
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
        $dataP = DB::table('paket')
            ->where('paket_id', '=', $id)
            ->first();
        $unit = DB::table('unit_kerja')
            ->select('unit_kerja.*')
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
        $hapus->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Calculate BOQ (Bill of Quantity) for a specific paket
     * Menghitung total tagihan dengan breakdown Pengawas dan Pelaksana
     */
    public function calculateBOQ($paketId)
    {
        $currentYear = date('Y');
        $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

        // Ambil data untuk efisiensi
        $kuotaJamAll = Kuotajam::latest('beg_date')->get()->keyBy('karyawan_id');
        $jabatanAll = Riwayat_jabatan::with('jabatan')->latest('beg_date')->get()->groupBy('karyawan_id');
        $shiftAll = Riwayat_shift::with('harianshift')->latest('beg_date')->get()->groupBy('karyawan_id');
        $resikoAll = Riwayat_resiko::with('resiko')->latest('beg_date')->get()->groupBy('karyawan_id');
        $lokasiAll = Riwayat_lokasi::with([
            'lokasi.ump' => function ($query) use ($currentYear) {
                $query->where('tahun', $currentYear);
            }
        ])->latest('beg_date')->get()->groupBy('karyawan_id');
        $masakerjaAll = Masakerja::latest('beg_date')->get()->keyBy('karyawan_id');

        $paket = Paket::with(['paketKaryawan.karyawan.perusahaan', 'unitKerja'])->findOrFail($paketId);

        $kuota = (int) $paket->kuota_paket;
        $karyawanPaket = $paket->paketKaryawan->sortByDesc('beg_date');

        // Filter berdasarkan status
        $aktif = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Aktif');
        $berhenti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Berhenti');
        $diganti = $karyawanPaket->filter(fn($item) => $item->karyawan && $item->karyawan->status_aktif === 'Sudah Diganti');

        // Ambil karyawan sesuai kuota
        $terpilih = collect();
        if ($aktif->count() >= $kuota) {
            $terpilih = $aktif->take($kuota);
        } else {
            $terpilih = $aktif;
            $sisa = $kuota - $aktif->count();
            $terpilih = $terpilih->concat($berhenti->take($sisa));
            $sisa = $kuota - $terpilih->count();
            $terpilih = $terpilih->concat($diganti->take($sisa));
        }

        // Initialize totals untuk Pengawas dan Pelaksana
        $pengawas = [
            'count' => 0,
            'upah_pokok' => 0,
            'tj_tetap' => 0,
            'tj_tidak_tetap' => 0,
            'tj_lokasi' => 0,
            'bpjs_kesehatan' => 0,
            'bpjs_ketenagakerjaan' => 0,
            'kompensasi' => 0,
            'nilai_kontrak' => 0,
            'lembur' => 0,
            'total' => 0
        ];
        $pelaksana = [
            'count' => 0,
            'upah_pokok' => 0,
            'tj_tetap' => 0,
            'tj_tidak_tetap' => 0,
            'tj_lokasi' => 0,
            'bpjs_kesehatan' => 0,
            'bpjs_ketenagakerjaan' => 0,
            'kompensasi' => 0,
            'nilai_kontrak' => 0,
            'lembur' => 0,
            'total' => 0
        ];

        $karyawanData = [];
        $vendor = null;

        foreach ($terpilih as $pk) {
            $karyawan = $pk->karyawan;
            if (!$karyawan)
                continue;
            $id = $karyawan->karyawan_id;

            if (!$vendor && $karyawan->perusahaan) {
                $vendor = $karyawan->perusahaan->perusahaan;
            }

            $jabatan = optional($jabatanAll[$id] ?? collect())->first();
            $shift = optional($shiftAll[$id] ?? collect())->first();
            $resiko = optional($resikoAll[$id] ?? collect())->first();
            $lokasi = optional($lokasiAll[$id] ?? collect())->first();
            $kuota_jam = $kuotaJamAll[$id] ?? null;
            $masakerja = $masakerjaAll[$id] ?? null;

            // Kalkulasi komponen gaji
            $upah_pokok = round($umpSumbar * 0.92);
            $tj_umum = round($umpSumbar * 0.08);
            $ump_lokasi = $lokasi->lokasi['ump']['ump'] ?? 0;
            $kode_lokasi = $lokasi->kode_lokasi ?? 12;
            $selisih_ump = round($ump_lokasi - $umpSumbar);
            $tj_lokasi = $kode_lokasi == 12 ? 0 : max($selisih_ump, 300000);
            $tj_jabatan = optional($jabatan?->jabatan)->tunjangan_jabatan ?? 0;
            $tj_masakerja = $masakerja->tunjangan_masakerja ?? 0;
            $tj_suai = $karyawan->tunjangan_penyesuaian ?? 0;
            $tj_harianshift = $shift->harianshift['tunjangan_shift'] ?? 0;
            $kode_resiko = $resiko->kode_resiko ?? 2;
            $tj_resiko = ($kode_resiko == 2) ? 0 : ($resiko->resiko['tunjangan_resiko'] ?? 0);
            $tj_presensi = round($upah_pokok * 0.08);

            $t_tetap = $tj_umum + $tj_jabatan + $tj_masakerja;
            $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi;

            $komponen_gaji = $upah_pokok + $t_tetap + $tj_lokasi;
            $bpjs_kesehatan = round(0.04 * $komponen_gaji);
            $bpjs_ketenagakerjaan = round(0.0689 * $komponen_gaji);

            $perusahaan_id = $karyawan->perusahaan_id ?? 0;
            $uang_jasa = $perusahaan_id == 38 ? round(($upah_pokok + $t_tetap + $t_tdk_tetap) / 12) : 0;
            $kompensasi = round($komponen_gaji / 12);

            $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kesehatan + $bpjs_ketenagakerjaan + $uang_jasa + $kompensasi);
            $fee_fix_cost = round(0.10 * $fix_cost);
            $jumlah_fix_cost = round($fix_cost + $fee_fix_cost);

            // Lembur
            $quota_jam = $kuota_jam->kuota ?? 0;
            $quota_jam_perkalian = 2 * $quota_jam;
            $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
            $nilai_lembur = round($tarif_lembur * $quota_jam_perkalian);
            $fee_lembur = round(0.025 * $nilai_lembur);
            $total_variabel = $nilai_lembur + $fee_lembur;

            $total_kontrak = $jumlah_fix_cost + $total_variabel;

            // Determine if Pengawas or Pelaksana based on jabatan name
            $namaJabatan = optional($jabatan?->jabatan)->jabatan ?? '';
            $isPengawas = stripos($namaJabatan, 'Pengawas') !== false;

            $target = $isPengawas ? 'pengawas' : 'pelaksana';

            if ($isPengawas) {
                $pengawas['count']++;
                $pengawas['upah_pokok'] += $upah_pokok;
                $pengawas['tj_tetap'] += $t_tetap;
                $pengawas['tj_tidak_tetap'] += $t_tdk_tetap;
                $pengawas['tj_lokasi'] += $tj_lokasi;
                $pengawas['bpjs_kesehatan'] += $bpjs_kesehatan;
                $pengawas['bpjs_ketenagakerjaan'] += $bpjs_ketenagakerjaan;
                $pengawas['kompensasi'] += $kompensasi;
                $pengawas['nilai_kontrak'] += $jumlah_fix_cost;
                $pengawas['lembur'] += $total_variabel;
                $pengawas['total'] += $total_kontrak;
            } else {
                $pelaksana['count']++;
                $pelaksana['upah_pokok'] += $upah_pokok;
                $pelaksana['tj_tetap'] += $t_tetap;
                $pelaksana['tj_tidak_tetap'] += $t_tdk_tetap;
                $pelaksana['tj_lokasi'] += $tj_lokasi;
                $pelaksana['bpjs_kesehatan'] += $bpjs_kesehatan;
                $pelaksana['bpjs_ketenagakerjaan'] += $bpjs_ketenagakerjaan;
                $pelaksana['kompensasi'] += $kompensasi;
                $pelaksana['nilai_kontrak'] += $jumlah_fix_cost;
                $pelaksana['lembur'] += $total_variabel;
                $pelaksana['total'] += $total_kontrak;
            }

            $karyawanData[] = [
                'nama' => $karyawan->nama_tk,
                'jabatan' => $namaJabatan,
                'tipe' => $isPengawas ? 'Pengawas' : 'Pelaksana',
                'upah_pokok' => $upah_pokok,
                'tj_tetap' => $t_tetap,
                'tj_tidak_tetap' => $t_tdk_tetap,
                'tj_lokasi' => $tj_lokasi,
                'bpjs_kesehatan' => $bpjs_kesehatan,
                'bpjs_ketenagakerjaan' => $bpjs_ketenagakerjaan,
                'kompensasi' => $kompensasi,
                'fix_cost' => $jumlah_fix_cost,
                'lembur' => $total_variabel,
                'total' => $total_kontrak
            ];
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
            'ump_sumbar' => $umpSumbar,
            'tahun' => $currentYear
        ];
    }

    /**
     * Lihat Tagihan - Preview BOQ sebelum download PDF
     */
    public function lihatTagihan($id)
    {
        $boqData = $this->calculateBOQ($id);

        return view('lihat-tagihan', [
            'boq' => $boqData
        ]);
    }

    /**
     * Generate PDF Tagihan untuk paket tertentu
     * Sesuai dengan Activity Diagram dan Sequence Diagram
     */
    public function generatePDF($id)
    {
        try {
            // Clear all output buffers completely
            while (ob_get_level()) {
                ob_end_clean();
            }

            // 1. Ambil data paket dari database
            $boqData = $this->calculateBOQ($id);

            // 2. Generate unique token
            $token = TagihanCetak::generateToken();

            // 3. Generate QR Code URL
            $verifyUrl = url('/verify-tagihan/' . $token);

            // 4. Generate QR Code as SVG for PDF (SVG doesn't require imagick extension)
            $qrCode = QrCode::format('svg')->size(100)->generate($verifyUrl);

            // 5. Simpan ke tagihan_cetak
            $tagihan = TagihanCetak::create([
                'paket_id' => $id,
                'token' => $token,
                'total_boq' => $boqData['total_boq'],
                'jumlah_pengawas' => $boqData['pengawas']['count'],
                'jumlah_pelaksana' => $boqData['pelaksana']['count'],
                'vendor' => $boqData['vendor'],
                'tanggal_cetak' => now()
            ]);

            // 6. Generate PDF dengan DomPDF
            $pdf = Pdf::loadView('tagihan-pdf', [
                'boq' => $boqData,
                'qrCode' => $qrCode,
                'token' => $token,
                'tanggal_cetak' => now()->format('d F Y'),
                'cetak_id' => $tagihan->cetak_id
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
    public function verifyTagihan($token)
    {
        $tagihan = TagihanCetak::where('token', $token)->with('paket')->first();

        if (!$tagihan) {
            return view('verify-tagihan', [
                'valid' => false,
                'message' => 'Token tagihan tidak ditemukan atau tidak valid.'
            ]);
        }

        return view('verify-tagihan', [
            'valid' => true,
            'tagihan' => $tagihan,
            'message' => 'Tagihan terverifikasi dan valid.'
        ]);
    }

}

