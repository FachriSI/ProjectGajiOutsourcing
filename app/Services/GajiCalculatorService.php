<?php

namespace App\Services;

/**
 * Centralized per-employee salary/contract calculation.
 *
 * Single source of truth for all gaji formulas.
 * Used by: paket_detail.blade.php, PaketDetailExport, ContractCalculatorService.
 */
class GajiCalculatorService
{
    /**
     * Calculate all salary components for one employee.
     *
     * @param array $params Employee parameters:
     *   - ump_sumbar (float)         : UMP Sumbar value
     *   - ump_lokasi (float)         : UMP for employee's location
     *   - kode_lokasi (int)          : Location code (12 = Sumbar)
     *   - tunjangan_jabatan (float)  : Position allowance
     *   - tunjangan_masakerja (float): Seniority allowance
     *   - tunjangan_penyesuaian (float): Adjustment allowance
     *   - tunjangan_shift (float)    : Shift allowance
     *   - kode_resiko (int)          : Risk code (2 = no risk allowance)
     *   - tunjangan_resiko (float)   : Risk allowance
     *   - perusahaan_id (int)        : Company ID (38 = gets uang jasa)
     *   - kuota_jam (float)          : Overtime quota hours
     *   - nilai_jatah (float)        : Clothing allowance
     *   - mcu (float)               : Medical checkup cost
     *
     * @return array All calculated components
     */
    public static function calculate(array $params): array
    {
        $ump_sumbar = $params['ump_sumbar'] ?? 0;
        $ump_lokasi = $params['ump_lokasi'] ?? 0;
        $kode_lokasi = $params['kode_lokasi'] ?? 12;
        $tj_jabatan = round($params['tunjangan_jabatan'] ?? 0);
        $tj_masakerja = round($params['tunjangan_masakerja'] ?? 0);
        $tj_suai = round($params['tunjangan_penyesuaian'] ?? 0);
        $tj_harianshift = round($params['tunjangan_shift'] ?? 0);
        $kode_resiko = $params['kode_resiko'] ?? 2;
        $tj_resiko = ($kode_resiko == 2) ? 0 : round($params['tunjangan_resiko'] ?? 0);
        $perusahaan_id = $params['perusahaan_id'] ?? 0;
        $kuota_jam = $params['kuota_jam'] ?? 0;
        $nilai_jatah = $params['nilai_jatah'] ?? 0;
        $mcu = $params['mcu'] ?? 0;

        // Upah Pokok = UMP Sumbar
        $upah_pokok = $ump_sumbar;

        // Tunjangan Lokasi
        $selisih_ump = round($ump_lokasi - $ump_sumbar);
        $tj_lokasi = $kode_lokasi == 12 ? 0 : max($selisih_ump, 300000);

        // Tunjangan Presensi (8% of Upah Pokok)
        $tj_presensi = round($upah_pokok * 0.08);

        // Tunjangan Tetap & Tidak Tetap
        $t_tetap = $tj_jabatan + $tj_masakerja;
        $t_tdk_tetap = $tj_suai + $tj_harianshift + $tj_presensi + $tj_resiko;

        // BPJS base = Upah Pokok + Tj. Tetap + Tj. Lokasi
        $komponen_gaji = $upah_pokok + $t_tetap + $tj_lokasi;
        $bpjs_kesehatan = round(0.04 * $komponen_gaji);
        $bpjs_ketenagakerjaan = round(0.0689 * $komponen_gaji);

        // Uang Jasa (only for perusahaan_id == 38)
        $uang_jasa = $perusahaan_id == 38
            ? round(($upah_pokok + $t_tetap + $t_tdk_tetap) / 12)
            : 0;

        // Kompensasi
        $kompensasi = round($komponen_gaji / 12);

        // Fix Cost
        $fix_cost = round($upah_pokok + $t_tetap + $t_tdk_tetap + $bpjs_kesehatan + $bpjs_ketenagakerjaan + $uang_jasa + $kompensasi);
        $fee_fix_cost = round(0.10 * $fix_cost);
        $jumlah_fix_cost = round($fix_cost + $fee_fix_cost);

        // Lembur (Overtime)
        $quota_jam_perkalian = 2 * $kuota_jam;
        $tarif_lembur = round((($upah_pokok + $t_tetap + $t_tdk_tetap) * 0.75) / 173);
        $nilai_lembur = round($tarif_lembur * $quota_jam_perkalian);
        $fee_lembur = round(0.025 * $nilai_lembur);
        $total_variabel = $nilai_lembur + $fee_lembur;

        // Total Kontrak
        $total_kontrak = $jumlah_fix_cost + $total_variabel;
        $total_kontrak_tahunan = $total_kontrak * 12;

        // THR
        $thr = round(($upah_pokok + $t_tetap) / 12);
        $fee_thr = round($thr * 0.05);
        $thr_bln = $thr + $fee_thr;
        $thr_thn = $thr_bln * 12;

        // Pakaian
        $pakaian = $nilai_jatah;
        $fee_pakaian = round(0.05 * $pakaian);
        $total_pakaian = $pakaian + $fee_pakaian;

        return [
            'upah_pokok' => $upah_pokok,
            'tj_lokasi' => $tj_lokasi,
            'tj_jabatan' => $tj_jabatan,
            'tj_masakerja' => $tj_masakerja,
            'tj_suai' => $tj_suai,
            'tj_harianshift' => $tj_harianshift,
            'tj_resiko' => $tj_resiko,
            'tj_presensi' => $tj_presensi,
            't_tetap' => $t_tetap,
            't_tdk_tetap' => $t_tdk_tetap,
            'komponen_gaji' => $komponen_gaji,
            'bpjs_kesehatan' => $bpjs_kesehatan,
            'bpjs_ketenagakerjaan' => $bpjs_ketenagakerjaan,
            'uang_jasa' => $uang_jasa,
            'kompensasi' => $kompensasi,
            'fix_cost' => $fix_cost,
            'fee_fix_cost' => $fee_fix_cost,
            'jumlah_fix_cost' => $jumlah_fix_cost,
            'tarif_lembur' => $tarif_lembur,
            'nilai_lembur' => $nilai_lembur,
            'fee_lembur' => $fee_lembur,
            'total_variabel' => $total_variabel,
            'total_kontrak' => $total_kontrak,
            'total_kontrak_tahunan' => $total_kontrak_tahunan,
            'thr' => $thr,
            'fee_thr' => $fee_thr,
            'thr_bln' => $thr_bln,
            'thr_thn' => $thr_thn,
            'pakaian' => $pakaian,
            'fee_pakaian' => $fee_pakaian,
            'total_pakaian' => $total_pakaian,
            'mcu' => $mcu,
        ];
    }
}
