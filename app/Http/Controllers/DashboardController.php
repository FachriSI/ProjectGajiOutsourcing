<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Models\Riwayat_fungsi;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_lokasi;
use App\Models\Paket;
use App\Models\Ump;
use App\Models\Kuotajam;
use App\Models\Masakerja;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = date('Y');

        // 1. Demographics Aggregation (Gender & Status)
        $genderCount = \App\Models\Karyawan::selectRaw("
                CASE 
                    WHEN jenis_kelamin = 'L' THEN 'Laki-laki' 
                    WHEN jenis_kelamin = 'P' THEN 'Perempuan' 
                    ELSE 'Lainnya' 
                END as label, count(*) as total")
            ->groupBy('label')
            ->pluck('total', 'label')
            ->toArray();
        // Ensure default keys exist
        $genderCount = array_merge(['Laki-laki' => 0, 'Perempuan' => 0], $genderCount);

        $statusAktifCount = \App\Models\Karyawan::selectRaw("
                CASE 
                    WHEN status_aktif = 'Aktif' THEN 'Aktif' 
                    ELSE 'Tidak Aktif' 
                END as label, count(*) as total")
            ->groupBy('label')
            ->pluck('total', 'label')
            ->toArray();
        $statusAktifCount = array_merge(['Aktif' => 0, 'Tidak Aktif' => 0], $statusAktifCount);

        // 2. Age Distribution (SQL Calculation)
        $usiaRaw = \App\Models\Karyawan::selectRaw("timestampdiff(YEAR, tanggal_lahir, CURDATE()) as age")
            ->whereNotNull('tanggal_lahir')
            ->get();
            
        $usiaCount = [
            '< 20' => $usiaRaw->where('age', '<', 20)->count(),
            '20-29' => $usiaRaw->whereBetween('age', [20, 29])->count(),
            '30-39' => $usiaRaw->whereBetween('age', [30, 39])->count(),
            '40-49' => $usiaRaw->whereBetween('age', [40, 49])->count(),
            '50+' => $usiaRaw->where('age', '>=', 50)->count(),
        ];

        // 3. Tenure Distribution (Masa Kerja)
        $masaKerjaRaw = \App\Models\Karyawan::selectRaw("timestampdiff(YEAR, tanggal_bekerja, CURDATE()) as tenure")
            ->whereNotNull('tanggal_bekerja')
            ->get();
            
        $masaKerjaCount = [
            '< 1 Tahun' => $masaKerjaRaw->where('tenure', '<', 1)->count(),
            '1-3 Tahun' => $masaKerjaRaw->whereBetween('tenure', [1, 2])->count(),
            '3-5 Tahun' => $masaKerjaRaw->whereBetween('tenure', [3, 4])->count(),
            '5-10 Tahun' => $masaKerjaRaw->whereBetween('tenure', [5, 10])->count(),
            '> 10 Tahun' => $masaKerjaRaw->where('tenure', '>', 10)->count(),
        ];

        // 4. Origin (Asal Kecamatan) - Top 10
        $asalKecamatanCount = \App\Models\Karyawan::selectRaw("SUBSTRING_INDEX(asal, ',', -1) as kecamatan, count(*) as total")
            ->whereNotNull('asal')
            ->groupBy('kecamatan')
            ->orderByDesc('total')
            ->take(10)
            ->pluck('total', 'kecamatan')
            ->mapWithKeys(fn($item, $key) => [trim($key) => $item])
            ->toArray();


        // 5. Shift Distribution (Latest Active Shift)
        // Correct Table: riwayat_shift, md_harianshift
        // Correct Join: riwayat_shift.kode_harianshift = md_harianshift.kode_harianshift
        // PK: riwayat_shift.id
        $shiftCount = \DB::table('riwayat_shift')
            ->join('md_harianshift', 'riwayat_shift.kode_harianshift', '=', 'md_harianshift.kode_harianshift')
            ->select('md_harianshift.harianshift', \DB::raw('count(*) as total'))
            ->whereIn('riwayat_shift.id', function($query) {
                // Get Max ID per Karyawan to find latest
                $query->select(\DB::raw('MAX(id)'))
                      ->from('riwayat_shift')
                      ->groupBy('karyawan_id');
            })
            ->groupBy('md_harianshift.harianshift')
            ->pluck('total', 'harianshift')
            ->toArray();

        // 6. Risk Distribution (Latest Active Risk)
        // Correct Table: riwayat_resiko, md_resiko
        // Correct Join: riwayat_resiko.kode_resiko = md_resiko.kode_resiko
        // PK: riwayat_resiko.id
        $resikoCount = \DB::table('riwayat_resiko')
            ->join('md_resiko', 'riwayat_resiko.kode_resiko', '=', 'md_resiko.kode_resiko')
            ->select('md_resiko.resiko', \DB::raw('count(*) as total'))
            ->whereIn('riwayat_resiko.id', function($query) {
                $query->select(\DB::raw('MAX(id)'))
                      ->from('riwayat_resiko')
                      ->groupBy('karyawan_id');
            })
            ->groupBy('md_resiko.resiko')
            ->pluck('total', 'resiko')
            ->toArray();


        // 7. Company Distribution (SI vs SP)
        // Joined Tables: paket_karyawan -> md_paket -> md_unit_kerja -> md_departemen -> md_karyawan
        // Join Keys Checked:
        // paket_karyawan.paket_id = md_paket.paket_id
        // md_paket.unit_id = md_unit_kerja.unit_id
        // md_unit_kerja.departemen_id = md_departemen.departemen_id
        // paket_karyawan.karyawan_id = md_karyawan.karyawan_id
        $perusahaanCount = [
            'SI' => ['aktif' => 0, 'jumlah' => 0],
            'SP' => ['aktif' => 0, 'jumlah' => 0],
        ];
        
        // This is still complex, let's keep it simple or approximate if performance is key.
        // Or use a raw query if needed. For now, let's try a simplified Eloquent approach.
        // To avoid heavy join, we can fetch Department stats.
        // Alternatively, if this is not critical, we can skip or simplify. 
        // Let's implement a direct DB query for speed.
        $companyStats = \DB::table('paket_karyawan')
            ->join('md_paket', 'paket_karyawan.paket_id', '=', 'md_paket.paket_id')
            ->join('md_unit_kerja', 'md_paket.unit_id', '=', 'md_unit_kerja.unit_id')
            ->join('md_departemen', 'md_unit_kerja.departemen_id', '=', 'md_departemen.departemen_id')
            ->join('md_karyawan', 'paket_karyawan.karyawan_id', '=', 'md_karyawan.karyawan_id')
            ->whereIn('paket_karyawan.paket_karyawan_id', function($q) {
                 $q->select(\DB::raw('MAX(paket_karyawan_id)'))->from('paket_karyawan')->groupBy('karyawan_id');
            })
            ->selectRaw('md_departemen.is_si, md_karyawan.status_aktif, count(*) as total')
            ->groupBy('md_departemen.is_si', 'md_karyawan.status_aktif')
            ->get();

        foreach ($companyStats as $stat) {
            $key = $stat->is_si ? 'SI' : 'SP';
            $perusahaanCount[$key]['jumlah'] += $stat->total;
            if ($stat->status_aktif === 'Aktif') {
                 $perusahaanCount[$key]['aktif'] += $stat->total;
            }
        }
        $perusahaanCount['Total'] = [
            'aktif' => $perusahaanCount['SI']['aktif'] + $perusahaanCount['SP']['aktif'],
             'jumlah' => $perusahaanCount['SI']['jumlah'] + $perusahaanCount['SP']['jumlah']
        ];


        // 8. Paket Analysis (Top 10 Quota & Realization)
        // Use withCount for optimized counting of related active employees
        $topPaketList = Paket::select('paket_id', 'paket', 'kuota_paket')
            ->withCount(['paketKaryawan as terisi' => function ($query) {
                // Count only currently active or valid assignments
                // Removed end_date check as column does not exist
                $query->whereDate('beg_date', '<=', now())
                      ->whereHas('karyawan', function($q) {
                          $q->where('status_aktif', 'Aktif');
                      });
            }])
            ->orderByDesc('kuota_paket')
            ->take(10)
            ->get();

        $topPaket = $topPaketList->map(function($p) {
            return [
                'nama_paket' => $p->paket,
                'kuota' => (int) $p->kuota_paket,
                'terisi' => $p->terisi
            ];
        });
        
        // Ensure $paketStats matches logic if needed, or just reuse $topPaket
        $paketStats = $topPaket; 


        // 9. Cost Analysis (Unit Kerja Cost)
        // Tables: nilai_kontrak -> md_paket -> md_unit_kerja
        $unitKerjaCost = \App\Models\NilaiKontrak::join('md_paket', 'nilai_kontrak.paket_id', '=', 'md_paket.paket_id')
            ->join('md_unit_kerja', 'md_paket.unit_id', '=', 'md_unit_kerja.unit_id')
            ->selectRaw('md_unit_kerja.unit_kerja, SUM(nilai_kontrak.total_nilai_kontrak) as total_biaya')
            ->groupBy('md_unit_kerja.unit_kerja')
            ->orderByDesc('total_biaya')
            ->take(10)
            ->pluck('total_biaya', 'unit_kerja');


        // 10. Trends (Historical)
        $trendData = \App\Models\Karyawan::selectRaw('YEAR(tanggal_bekerja) as tahun, count(*) as jumlah')
            ->whereNotNull('tanggal_bekerja')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        $contractTrend = \App\Models\NilaiKontrak::selectRaw('tahun, SUM(total_nilai_kontrak) as total_nilai')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        $umpTrend = \App\Models\Ump::where('kode_lokasi', '12') // Sumbar
            ->where('is_deleted', 0)
            ->orderBy('tahun', 'asc')
            ->get(['tahun', 'ump']);

        $umpPerLokasi = \App\Models\Ump::join('md_lokasi', 'md_ump.kode_lokasi', '=', 'md_lokasi.kode_lokasi')
            ->where('md_ump.tahun', $currentYear)
            ->where('md_ump.is_deleted', 0)
            ->pluck('md_ump.ump', 'md_lokasi.lokasi');

        $attritionTrend = \App\Models\Karyawan::selectRaw('YEAR(tanggal_berhenti) as tahun, count(*) as jumlah')
            ->whereNotNull('tanggal_berhenti')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        $exitReasons = \App\Models\Karyawan::selectRaw('catatan_berhenti, count(*) as jumlah')
            ->whereNotNull('tanggal_berhenti')
            ->where('catatan_berhenti', '!=', '')
            ->groupBy('catatan_berhenti')
            ->orderByDesc('jumlah')
            ->take(10)
            ->pluck('jumlah', 'catatan_berhenti');


        // 11. New Trend Metrics (Dynamics & UMP Growth)
        $years = $trendData->pluck('tahun')->merge($attritionTrend->pluck('tahun'))->unique()->sort()->values();
        $employeeDynamics = [];
        $cumulativePopulation = 0;
        
        foreach ($years as $year) {
            $in = $trendData->firstWhere('tahun', $year)->jumlah ?? 0;
            $out = $attritionTrend->firstWhere('tahun', $year)->jumlah ?? 0;
            $cumulativePopulation += ($in - $out); 
            $employeeDynamics[] = [
                'tahun' => $year,
                'masuk' => $in,
                'keluar' => $out,
                'populasi' => max(0, $cumulativePopulation)
            ];
        }
        $employeeDynamics = collect($employeeDynamics);

        $umpGrowth = [];
        $previousUmp = 0;
        foreach ($umpTrend as $data) {
            $growth = ($previousUmp > 0) ? (($data->ump - $previousUmp) / $previousUmp) * 100 : 0;
            $umpGrowth[] = [
                'tahun' => $data->tahun,
                'ump' => $data->ump,
                'growth' => round($growth, 2)
            ];
            $previousUmp = $data->ump;
        }
        $umpGrowth = collect($umpGrowth);
        
        // 12. Detailed UMP Matrix (Pivot: Location x Year)
        $umpRaw = \App\Models\Ump::join('md_lokasi', 'md_ump.kode_lokasi', '=', 'md_lokasi.kode_lokasi')
            ->where('md_ump.is_deleted', 0)
            ->select('md_lokasi.lokasi', 'md_ump.tahun', 'md_ump.ump') // ump is string/int
            ->get();

        // Extract unique years and sort them
        $umpYears = $umpRaw->pluck('tahun')->unique()->sort()->values();
        
        // Build Matrix: [Location => [Year => Value]]
        $umpMatrix = [];
        foreach ($umpRaw as $row) {
            $umpMatrix[$row->lokasi][$row->tahun] = $row->ump;
        }
        // Key sort locations for neatness
        ksort($umpMatrix);

        // 13. Organizational Analysis
        // Top 10 Job Titles (Jabatan)
        $jabatanCount = \DB::table('riwayat_jabatan')
            ->join('md_jabatan', 'riwayat_jabatan.kode_jabatan', '=', 'md_jabatan.kode_jabatan')
            ->select('md_jabatan.jabatan', \DB::raw('count(*) as total'))
            ->whereIn('riwayat_jabatan.id', function($q) {
                $q->select(\DB::raw('MAX(id)'))->from('riwayat_jabatan')->groupBy('karyawan_id');
            })
            ->groupBy('md_jabatan.jabatan')
            ->orderByDesc('total')
            ->take(10)
            ->pluck('total', 'jabatan')
            ->toArray();

        // Department Distribution
        $departemenCount = \DB::table('paket_karyawan')
            ->join('md_paket', 'paket_karyawan.paket_id', '=', 'md_paket.paket_id')
            ->join('md_unit_kerja', 'md_paket.unit_id', '=', 'md_unit_kerja.unit_id')
            ->join('md_departemen', 'md_unit_kerja.departemen_id', '=', 'md_departemen.departemen_id')
            ->whereIn('paket_karyawan.paket_karyawan_id', function($q) {
                 $q->select(\DB::raw('MAX(paket_karyawan_id)'))->from('paket_karyawan')->groupBy('karyawan_id');
            })
            ->select('md_departemen.departemen', \DB::raw('count(*) as total'))
            ->groupBy('md_departemen.departemen')
            ->orderByDesc('total')
            ->pluck('total', 'departemen')
            ->toArray();


        return view('dashboard', compact(
            'genderCount', 'statusAktifCount', 'perusahaanCount', 'asalKecamatanCount',
            'usiaCount', 'masaKerjaCount', 'shiftCount', 'resikoCount',
            'topPaket', 'paketStats', 'unitKerjaCost',
            'trendData', 'contractTrend', 'attritionTrend', 'exitReasons',
            'umpTrend', 'umpPerLokasi', 'umpMatrix', 'umpYears',
            'employeeDynamics', 'umpGrowth', 'jabatanCount', 'departemenCount'
        ));
    }

}
