<?php

namespace App\Services;

use App\Models\NilaiKontrak;
use App\Models\KontrakHistory;
use App\Models\Paket;
use App\Models\Ump;
use App\Models\Kuotajam;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_fungsi;
use App\Models\Riwayat_lokasi;
use App\Models\Masakerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractCalculatorService
{
    /**
     * Calculate contract value for a specific paket
     * 
     * @param int $paketId
     * @param string|null $periode Format: YYYY-MM (e.g., 2026-01)
     * @param int|null $userId User ID yang trigger perhitungan
     * @return NilaiKontrak
     */
    public function calculateForPaket($paketId, $periode = null, $userId = null)
    {
        // Default periode ke bulan sekarang jika tidak disediakan
        if (!$periode) {
            $periode = Carbon::now()->format('Y-m');
        }

        // Parse periode
        [$tahun, $bulan] = explode('-', $periode);
        $currentYear = (int) $tahun;

        // Ambil UMP Sumbar untuk tahun tersebut
        $umpSumbar = Ump::where('kode_lokasi', '12')
            ->where('tahun', $currentYear)
            ->value('ump');

        if (!$umpSumbar) {
            throw new \Exception("UMP Sumbar untuk tahun {$currentYear} belum tersedia");
        }

        // Ambil data untuk efisiensi (sama seperti PaketController::calculateBOQ)
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

        // Ambil data paket
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
        $jumlahKaryawanAktif = 0;

        foreach ($terpilih as $pk) {
            $karyawan = $pk->karyawan;
            if (!$karyawan) continue;
            
            $id = $karyawan->karyawan_id;

            // Hitung jika karyawan aktif
            if ($karyawan->status_aktif === 'Aktif') {
                $jumlahKaryawanAktif++;
            }

            $jabatan = optional($jabatanAll[$id] ?? collect())->first();
            $shift = optional($shiftAll[$id] ?? collect())->first();
            $resiko = optional($resikoAll[$id] ?? collect())->first();
            $lokasi = optional($lokasiAll[$id] ?? collect())->first();
            $kuota_jam = $kuotaJamAll[$id] ?? null;
            $masakerja = $masakerjaAll[$id] ?? null;

            // Kalkulasi komponen gaji (sama seperti calculateBOQ)
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

            // Tentukan kategori (Pengawas atau Pelaksana)
            $jabatan_nama = optional($jabatan?->jabatan)->jabatan ?? '';
            $isPengawas = stripos($jabatan_nama, 'pengawas') !== false || 
                          stripos($jabatan_nama, 'supervisor') !== false ||
                          stripos($jabatan_nama, 'koordinator') !== false;

            if ($isPengawas) {
                $pengawas['count']++;
                $pengawas['upah_pokok'] += $upah_pokok;
                $pengawas['tj_tetap'] += $t_tetap;
                $pengawas['tj_tidak_tetap'] += $t_tdk_tetap;
                $pengawas['tj_lokasi'] += $tj_lokasi;
                $pengawas['bpjs_kesehatan'] += $bpjs_kesehatan;
                $pengawas['bpjs_ketenagakerjaan'] += $bpjs_ketenagakerjaan;
                $pengawas['kompensasi'] += $kompensasi;
                $pengawas['total'] += $fix_cost;
            } else {
                $pelaksana['count']++;
                $pelaksana['upah_pokok'] += $upah_pokok;
                $pelaksana['tj_tetap'] += $t_tetap;
                $pelaksana['tj_tidak_tetap'] += $t_tdk_tetap;
                $pelaksana['tj_lokasi'] += $tj_lokasi;
                $pelaksana['bpjs_kesehatan'] += $bpjs_kesehatan;
                $pelaksana['bpjs_ketenagakerjaan'] += $bpjs_ketenagakerjaan;
                $pelaksana['kompensasi'] += $kompensasi;
                $pelaksana['total'] += $fix_cost;
            }

            // Simpan detail karyawan untuk breakdown
            $karyawanData[] = [
                'karyawan_id' => $id,
                'nama' => $karyawan->nama,
                'status' => $karyawan->status_aktif,
                'jabatan' => $jabatan_nama,
                'kategori' => $isPengawas ? 'Pengawas' : 'Pelaksana',
                'upah_pokok' => $upah_pokok,
                'tj_tetap' => $t_tetap,
                'tj_tidak_tetap' => $t_tdk_tetap,
                'tj_lokasi' => $tj_lokasi,
                'bpjs_kesehatan' => $bpjs_kesehatan,
                'bpjs_ketenagakerjaan' => $bpjs_ketenagakerjaan,
                'kompensasi' => $kompensasi,
                'total' => $fix_cost
            ];
        }

        // Total nilai kontrak
        $totalNilaiKontrak = $pengawas['total'] + $pelaksana['total'];

        // Simpan atau update ke database
        $nilaiKontrak = NilaiKontrak::updateOrCreate(
            [
                'paket_id' => $paketId,
                'periode' => $periode
            ],
            [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'ump_sumbar' => $umpSumbar,
                'jumlah_karyawan_aktif' => $jumlahKaryawanAktif,
                'jumlah_karyawan_total' => $terpilih->count(),
                'kuota_paket' => $kuota,
                'total_nilai_kontrak' => $totalNilaiKontrak,
                'total_pengawas' => $pengawas['total'],
                'total_pelaksana' => $pelaksana['total'],
                'jumlah_pengawas' => $pengawas['count'],
                'jumlah_pelaksana' => $pelaksana['count'],
                'breakdown_json' => [
                    'pengawas' => $pengawas,
                    'pelaksana' => $pelaksana,
                    'karyawan' => $karyawanData
                ],
                'calculated_at' => now(),
                'calculated_by' => $userId
            ]
        );

        Log::info("Contract calculated for Paket ID: {$paketId}, Periode: {$periode}, Total: {$totalNilaiKontrak}");

        return $nilaiKontrak;
    }

    /**
     * Recalculate all active pakets
     * 
     * @param string|null $periode
     * @param int|null $userId
     * @return array Summary of calculations
     */
    public function recalculateAllPakets($periode = null, $userId = null)
    {
        $pakets = Paket::all();
        $summary = [
            'total_pakets' => $pakets->count(),
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($pakets as $paket) {
            try {
                $this->calculateForPaket($paket->paket_id, $periode, $userId);
                $summary['success']++;
            } catch (\Exception $e) {
                $summary['failed']++;
                $summary['errors'][] = [
                    'paket_id' => $paket->paket_id,
                    'paket' => $paket->paket,
                    'error' => $e->getMessage()
                ];
                Log::error("Failed to calculate for Paket ID: {$paket->paket_id}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Recalculated all pakets", $summary);

        return $summary;
    }

    /**
     * Recalculate pakets affected by UMP change
     * 
     * @param string $kodeLokasi
     * @param int $tahun
     * @param int|null $userId
     * @return array Summary
     */
    public function recalculateByUmpChange($kodeLokasi, $tahun, $userId = null)
    {
        // Untuk UMP Sumbar (kode_lokasi = 12), recalculate semua paket
        // Untuk UMP lokasi lain, recalculate paket yang punya karyawan di lokasi tersebut
        
        $periode = $tahun . '-' . date('m'); // Default ke bulan sekarang

        if ($kodeLokasi == '12') {
            // UMP Sumbar berubah, affect semua paket
            $summary = $this->recalculateAllPakets($periode, $userId);
            
            // Create history entry untuk semua paket
            $pakets = Paket::all();
            foreach ($pakets as $paket) {
                $oldNilai = NilaiKontrak::where('paket_id', $paket->paket_id)
                    ->where('periode', '<', $periode)
                    ->orderBy('periode', 'desc')
                    ->first();

                $newNilai = NilaiKontrak::where('paket_id', $paket->paket_id)
                    ->where('periode', $periode)
                    ->first();

                if ($oldNilai && $newNilai) {
                    KontrakHistory::createEntry(
                        $paket->paket_id,
                        'ump_change',
                        ['total_nilai_kontrak' => $oldNilai->total_nilai_kontrak],
                        ['total_nilai_kontrak' => $newNilai->total_nilai_kontrak],
                        "UMP Sumbar berubah untuk tahun {$tahun}",
                        $userId
                    );
                }
            }
        } else {
            // UMP lokasi tertentu berubah, cari paket yang terdampak
            // TODO: Implement logic untuk cari paket dengan karyawan di lokasi tersebut
            $summary = ['message' => 'UMP lokasi change not fully implemented yet'];
        }

        return $summary;
    }

    /**
     * Recalculate when kuota_paket changes
     * 
     * @param int $paketId
     * @param int $oldKuota
     * @param int $newKuota
     * @param int|null $userId
     * @return NilaiKontrak
     */
    public function recalculateByKuotaChange($paketId, $oldKuota, $newKuota, $userId = null)
    {
        $periode = Carbon::now()->format('Y-m');
        
        // Get old value
        $oldNilai = NilaiKontrak::getLatestForPaket($paketId);

        // Recalculate
        $newNilai = $this->calculateForPaket($paketId, $periode, $userId);

        // Create history
        KontrakHistory::createEntry(
            $paketId,
            'kuota_change',
            $oldNilai ? ['total_nilai_kontrak' => $oldNilai->total_nilai_kontrak, 'kuota' => $oldKuota] : null,
            ['total_nilai_kontrak' => $newNilai->total_nilai_kontrak, 'kuota' => $newKuota],
            "Kuota paket berubah dari {$oldKuota} menjadi {$newKuota}",
            $userId
        );

        return $newNilai;
    }

    /**
     * Recalculate when employee count changes
     * 
     * @param int $paketId
     * @param int|null $userId
     * @return NilaiKontrak
     */
    public function recalculateByEmployeeChange($paketId, $userId = null)
    {
        $periode = Carbon::now()->format('Y-m');
        
        // Get old value
        $oldNilai = NilaiKontrak::getLatestForPaket($paketId);

        // Recalculate
        $newNilai = $this->calculateForPaket($paketId, $periode, $userId);

        // Create history
        KontrakHistory::createEntry(
            $paketId,
            'employee_change',
            $oldNilai ? [
                'total_nilai_kontrak' => $oldNilai->total_nilai_kontrak,
                'jumlah_karyawan' => $oldNilai->jumlah_karyawan_total
            ] : null,
            [
                'total_nilai_kontrak' => $newNilai->total_nilai_kontrak,
                'jumlah_karyawan' => $newNilai->jumlah_karyawan_total
            ],
            "Jumlah karyawan berubah",
            $userId
        );

        return $newNilai;
    }
}
