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
            ->where('status_aktif', 'Aktif')
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

        // New: Strategic Global KPIs
        // Total Karyawan
        $totalKaryawan = \App\Models\Karyawan::count();

        // Attrition Rate (Annual)
        $activeEmployees = \App\Models\Karyawan::where('status_aktif', 'Aktif')->count();
        $exitedThisYear = \App\Models\Karyawan::whereNotNull('tanggal_berhenti')
            ->whereYear('tanggal_berhenti', $currentYear)
            ->count();
        $attritionRate = $activeEmployees > 0 ? round(($exitedThisYear / $activeEmployees) * 100, 1) : 0;

        // Average Tenure (years)
        // Total Paket
        $totalPaket = \App\Models\Paket::where('is_deleted', 0)->count();

        // Average Tenure (years) - DEPRECATED (Moved to replace with Total Paket)
        $avgTenure = \App\Models\Karyawan::where('status_aktif', 'Aktif')
            ->whereNotNull('tanggal_bekerja')
            ->selectRaw('AVG(TIMESTAMPDIFF(YEAR, tanggal_bekerja, CURDATE())) as avg_years')
            ->value('avg_years');
        $avgTenure = round($avgTenure ?? 0, 1);

        // UMP Compliance Rate (% of employees with salary >= UMP for their location)
        // Note: This requires gaji field in md_karyawan and proper UMP mapping
        // For now, using a placeholder calculation
        $umpCompliance = 98.5; // Placeholder - needs actual implementation

        // 2. Age Distribution (SQL Calculation)
        $usiaRaw = \App\Models\Karyawan::selectRaw("timestampdiff(YEAR, tanggal_lahir, CURDATE()) as age")
            ->whereNotNull('tanggal_lahir')
            ->where('status_aktif', 'Aktif')
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
            ->where('status_aktif', 'Aktif')
            ->get();

        $masaKerjaCount = [
            '< 1 Tahun' => $masaKerjaRaw->where('tenure', '<', 1)->count(),
            '1-3 Tahun' => $masaKerjaRaw->whereBetween('tenure', [1, 2])->count(),
            '3-5 Tahun' => $masaKerjaRaw->whereBetween('tenure', [3, 4])->count(),
            '5-10 Tahun' => $masaKerjaRaw->whereBetween('tenure', [5, 10])->count(),
            '> 10 Tahun' => $masaKerjaRaw->where('tenure', '>', 10)->count(),
        ];

        // 4. Origin (Asal Kecamatan) - REMOVED (No decision-making value)


        // 5. Shift Distribution (Latest Active Shift)
        // Correct Table: riwayat_shift, md_harianshift
        // Correct Join: riwayat_shift.kode_harianshift = md_harianshift.kode_harianshift
        // PK: riwayat_shift.id
        $shiftCount = \DB::table('riwayat_shift')
            ->join('md_harianshift', 'riwayat_shift.kode_harianshift', '=', 'md_harianshift.kode_harianshift')
            ->join('md_karyawan', 'riwayat_shift.karyawan_id', '=', 'md_karyawan.karyawan_id')
            ->select('md_harianshift.harianshift', \DB::raw('count(*) as total'))
            ->whereIn('riwayat_shift.id', function ($query) {
                // Get Max ID per Karyawan to find latest
                $query->select(\DB::raw('MAX(id)'))
                    ->from('riwayat_shift')
                    ->groupBy('karyawan_id');
            })
            ->where('md_karyawan.status_aktif', 'Aktif')
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
            ->whereIn('riwayat_resiko.id', function ($query) {
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
            ->whereIn('paket_karyawan.paket_karyawan_id', function ($q) {
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


        // 8. Paket Analysis - Two separate visualizations

        // 8a. Top 10 Paket by Kuota Terbesar (informational)
        // Fixed: Handle invalid beg_date values ('0000-00-00') by checking for valid dates OR invalid/null dates
        $topPaketKuota = Paket::select('paket_id', 'paket', 'kuota_paket')
            ->withCount([
                'paketKaryawan as terisi' => function ($query) {
                    $query->where(function ($q) {
                        // Include records with valid beg_date <= now OR invalid dates ('0000-00-00', NULL)
                        $q->whereDate('beg_date', '<=', now())
                            ->orWhere('beg_date', '0000-00-00')
                            ->orWhereNull('beg_date');
                    })
                        ->whereHas('karyawan', function ($q) {
                            $q->where('status_aktif', 'Aktif');
                        });
                }
            ])
            ->orderByDesc('terisi')
            ->take(10)
            ->get()
            ->map(function ($p) {
                return [
                    'nama_paket' => $p->paket,
                    'kuota' => (int) $p->kuota_paket,
                    'terisi' => $p->terisi,
                    'kosong' => (int) $p->kuota_paket - $p->terisi
                ];
            });

        // 8b. Top 10 Paket by % Kuota Kosong Terbesar (actionable)
        // Fixed: Handle invalid beg_date values ('0000-00-00') by checking for valid dates OR invalid/null dates
        $topPaketKosong = Paket::select('paket_id', 'paket', 'kuota_paket')
            ->withCount([
                'paketKaryawan as terisi' => function ($query) {
                    $query->where(function ($q) {
                        // Include records with valid beg_date <= now OR invalid dates ('0000-00-00', NULL)
                        $q->whereDate('beg_date', '<=', now())
                            ->orWhere('beg_date', '0000-00-00')
                            ->orWhereNull('beg_date');
                    })
                        ->whereHas('karyawan', function ($q) {
                            $q->where('status_aktif', 'Aktif');
                        });
                }
            ])
            ->where('kuota_paket', '>', 0) // Avoid division by zero
            ->get()
            ->map(function ($p) {
                $kuota = (int) $p->kuota_paket;
                $terisi = $p->terisi;
                $kosong = $kuota - $terisi;
                $persen_kosong = $kuota > 0 ? round(($kosong / $kuota) * 100, 1) : 0;

                return [
                    'nama_paket' => $p->paket,
                    'kuota' => $kuota,
                    'terisi' => $terisi,
                    'kosong' => $kosong,
                    'persen_kosong' => $persen_kosong
                ];
            })
            ->sortByDesc('persen_kosong')
            ->take(10)
            ->values(); // Reset array keys 


        // 9. Cost Analysis (Unit Kerja Cost)
        // Tables: md_paket -> nilai_kontrak (LEFT JOIN to show 0 if no contract)
        $unitKerjaCost = \App\Models\Paket::leftJoin('nilai_kontrak', 'md_paket.paket_id', '=', 'nilai_kontrak.paket_id')
            ->selectRaw('md_paket.paket, COALESCE(SUM(nilai_kontrak.total_nilai_kontrak), 0) as total_biaya')
            ->where('md_paket.is_deleted', 0)
            ->groupBy('md_paket.paket_id', 'md_paket.paket')
            ->orderByDesc('total_biaya')
            ->take(10)
            ->pluck('total_biaya', 'paket');


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


        // 11. New Trend Metrics (Employee Dynamics with ACTUAL population counts)
        $years = $trendData->pluck('tahun')->merge($attritionTrend->pluck('tahun'))->push($currentYear)->unique()->sort()->values();
        $employeeDynamics = [];

        foreach ($years as $year) {
            $in = $trendData->firstWhere('tahun', $year)->jumlah ?? 0;
            $out = $attritionTrend->firstWhere('tahun', $year)->jumlah ?? 0;

            // Calculate ACTUAL population at year-end by counting employees
            // who were hired on/before this year AND (still active OR left after this year)
            $populationAtYearEnd = \App\Models\Karyawan::where(function ($q) use ($year) {
                // Hired on or before this year (or unknown hire date)
                $q->whereYear('tanggal_bekerja', '<=', $year)
                    ->orWhereNull('tanggal_bekerja');
            })
                ->where(function ($q) use ($year) {
                    // STILL ACTIVE:
                    // 1. Status is directly 'Aktif' (covers current active employees)
                    // OR
                    // 2. We allow them if they have a termination date AFTER this year (meaning they were active during this year)
                    $q->where('status_aktif', 'Aktif')
                        ->orWhere(function ($q2) use ($year) {
                        $q2->whereNotNull('tanggal_berhenti')
                            ->whereYear('tanggal_berhenti', '>', $year);
                    });
                    // REMOVED: orWhereNull('tanggal_berhenti') 
                    // Reason: IF status is NOT 'Aktif' AND date is NULL, they are effectively inactive/unknown and should NOT be counted as active population.
                })
                ->count();

            $employeeDynamics[] = [
                'tahun' => $year,
                'masuk' => $in,
                'keluar' => $out,
                'populasi' => $populationAtYearEnd // Actual count, not cumulative math
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
            ->join('md_karyawan', 'riwayat_jabatan.karyawan_id', '=', 'md_karyawan.karyawan_id')
            ->select('md_jabatan.jabatan', \DB::raw('count(*) as total'))
            ->whereIn('riwayat_jabatan.id', function ($q) {
                $q->select(\DB::raw('MAX(id)'))->from('riwayat_jabatan')->groupBy('karyawan_id');
            })
            ->where('md_karyawan.status_aktif', 'Aktif')
            ->groupBy('md_jabatan.jabatan')
            ->orderByDesc('total')
            ->pluck('total', 'jabatan')
            ->toArray();

        // Department Distribution
        $departemenCount = \DB::table('paket_karyawan')
            ->join('md_paket', 'paket_karyawan.paket_id', '=', 'md_paket.paket_id')
            ->join('md_unit_kerja', 'md_paket.unit_id', '=', 'md_unit_kerja.unit_id')
            ->join('md_departemen', 'md_unit_kerja.departemen_id', '=', 'md_departemen.departemen_id')
            ->join('md_karyawan', 'paket_karyawan.karyawan_id', '=', 'md_karyawan.karyawan_id')
            ->whereIn('paket_karyawan.paket_karyawan_id', function ($q) {
                $q->select(\DB::raw('MAX(paket_karyawan_id)'))->from('paket_karyawan')->groupBy('karyawan_id');
            })
            ->where('md_karyawan.status_aktif', 'Aktif')
            ->select('md_departemen.departemen', \DB::raw('count(*) as total'))
            ->groupBy('md_departemen.departemen')
            ->orderByDesc('total')
            ->pluck('total', 'departemen')
            ->toArray();


        // 7b. Vendor Distribution (Perusahaan) - NEW
        $vendorCount = \DB::table('md_karyawan')
            ->join('md_perusahaan', 'md_karyawan.perusahaan_id', '=', 'md_perusahaan.perusahaan_id')
            ->select('md_perusahaan.perusahaan', \DB::raw('count(*) as total'))
            ->where('md_karyawan.status_aktif', 'Aktif')
            ->where('md_perusahaan.is_deleted', 0)
            ->groupBy('md_perusahaan.perusahaan')
            ->orderByDesc('total')
            ->pluck('total', 'perusahaan')
            ->toArray();

        return view('dashboard', compact(
            'genderCount',
            'statusAktifCount',
            'totalKaryawan',
            'avgTenure',
            'usiaCount',
            'masaKerjaCount',
            'shiftCount',
            'trendData',
            'umpTrend',
            'umpGrowth',
            'umpPerLokasi',
            'umpMatrix',
            'umpYears',
            'employeeDynamics',
            'jabatanCount',
            'departemenCount',
            'topPaketKuota',
            'topPaketKosong',
            'unitKerjaCost',
            'contractTrend',
            'totalPaket',
            'perusahaanCount',
            'vendorCount'
        ));
    }
}
