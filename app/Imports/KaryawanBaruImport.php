<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\PaketKaryawan;
use App\Models\AuditLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KaryawanBaruImport implements ToCollection, WithStartRow
{
    private $total = 0;
    private $gagal = 0;
    private $logs = [];

    public function getTotal()
    {
        return $this->total;
    }

    public function getGagal()
    {
        return $this->gagal;
    }

    public function getLog()
    {
        return $this->logs;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        // Track affected paket IDs for auto-calculation
        $affectedPaketIds = [];

        try {
            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (!isset($row[0]) || trim($row[0]) == '') {
                    continue;
                }

                $this->total++;
                $rowIndex = $index + 2; // Adjust for 0-based index + header row

                // Mapping Columns
                // 0: OSIS ID
                // 1: No KTP
                // 2: Nama Lengkap
                // 3: ID Paket
                // 4: ID Perusahaan
                // 5: Tanggal Lahir (YYYY-MM-DD)
                // 6: Jenis Kelamin (L/P)
                // 7: Agama
                // 8: Status Pernikahan (S/M/D/J)
                // 9: Alamat
                // 10: Asal
                // 11: Kode Jabatan
                // 12: Harian/Shift (1=Harian, 2=Shift)
                // 13: Area (Nama Area)
                // 14: Kode Lokasi Kerja
                // 15: Tipe Pekerjaan

                $osis_id = trim($row[0]);
                $ktp = trim($row[1]);
                $nama = trim($row[2]);
                $paket_id = trim($row[3]);
                $perusahaan_id = trim($row[4]);

                // Handle Date
                $tanggal_lahir = null;
                try {
                    if (is_numeric($row[5])) {
                        $tanggal_lahir = Date::excelToDateTimeObject($row[5])->format('Y-m-d');
                    } else {
                        $tanggal_lahir = Carbon::parse($row[5])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $this->gagal++;
                    $this->logs[] = "Baris $rowIndex: Format Tanggal Lahir salah.";
                    continue;
                }

                $jenis_kelamin = strtoupper(trim($row[6]));
                $agama = trim($row[7]);
                $status = strtoupper(trim($row[8]));
                $alamat = trim($row[9]);
                $asal = isset($row[10]) ? trim($row[10]) : null;
                $kode_jabatan = isset($row[11]) ? trim($row[11]) : null;
                $kode_harianshift = isset($row[12]) ? trim($row[12]) : '1'; // Default Harian
                $area_nama = isset($row[13]) ? trim($row[13]) : null;
                $kode_lokasi = isset($row[14]) ? trim($row[14]) : null;
                $tipe_pekerjaan = isset($row[15]) ? trim($row[15]) : null;

                // Validation
                if (strlen($osis_id) != 4 || !is_numeric($osis_id)) {
                    $this->gagal++;
                    $this->logs[] = "Baris $rowIndex: OSIS ID harus 4 digit angka.";
                    continue;
                }

                if (Karyawan::where('osis_id', $osis_id)->exists()) {
                    $this->gagal++;
                    $this->logs[] = "Baris $rowIndex: OSIS ID $osis_id sudah terdaftar.";
                    continue;
                }

                if (strlen($ktp) != 16 || !is_numeric($ktp)) {
                    $this->gagal++;
                    $this->logs[] = "Baris $rowIndex: KTP harus 16 digit angka.";
                    continue;
                }

                if (Karyawan::where('ktp', $ktp)->exists()) {
                    $this->gagal++;
                    $this->logs[] = "Baris $rowIndex: KTP $ktp sudah terdaftar.";
                    continue;
                }

                // Check Paket
                $paket = DB::table('md_paket')->where('paket_id', $paket_id)->first();
                if (!$paket) {
                    $this->gagal++;
                    $this->logs[] = "Baris $rowIndex: ID Paket $paket_id tidak ditemukan.";
                    continue;
                }

                // Check Perusahaan
                $perusahaan = DB::table('md_perusahaan')->where('perusahaan_id', $perusahaan_id)->first();
                if (!$perusahaan) {
                    $this->gagal++;
                    $this->logs[] = "Baris $rowIndex: ID Perusahaan $perusahaan_id tidak ditemukan.";
                    continue;
                }

                // Check Jabatan (optional)
                if (!empty($kode_jabatan)) {
                    $jabatan = DB::table('md_jabatan')->where('kode_jabatan', $kode_jabatan)->first();
                    if (!$jabatan) {
                        $this->gagal++;
                        $this->logs[] = "Baris $rowIndex: Kode Jabatan '$kode_jabatan' tidak ditemukan.";
                        continue;
                    }
                }

                // Validate Harian/Shift against database
                if (!empty($kode_harianshift)) {
                    $harianshift = DB::table('md_harianshift')->where('kode_harianshift', $kode_harianshift)->first();
                    if (!$harianshift) {
                        $this->gagal++;
                        $this->logs[] = "Baris $rowIndex: Kode Harian/Shift '$kode_harianshift' tidak ditemukan. Gunakan 1 (Harian) atau 2 (Shift).";
                        continue;
                    }
                }

                // Check Area (optional - lookup by name)
                $area_id = null;
                if (!empty($area_nama)) {
                    $area = DB::table('md_area')->where('area', 'LIKE', '%' . $area_nama . '%')->where('is_deleted', 0)->first();
                    if (!$area) {
                        $this->gagal++;
                        $this->logs[] = "Baris $rowIndex: Area '$area_nama' tidak ditemukan.";
                        continue;
                    }
                    $area_id = $area->area_id;
                }

                // Check Lokasi (optional)
                if (!empty($kode_lokasi)) {
                    $lokasi = DB::table('md_lokasi')->where('kode_lokasi', $kode_lokasi)->where('is_deleted', 0)->first();
                    if (!$lokasi) {
                        $this->gagal++;
                        $this->logs[] = "Baris $rowIndex: Kode Lokasi '$kode_lokasi' tidak ditemukan.";
                        continue;
                    }
                }

                // Calculate Pension
                $tgl_lahir_carbon = Carbon::parse($tanggal_lahir);
                $tanggal_umur56 = $tgl_lahir_carbon->copy()->addYears(56);
                $tanggal_pensiun = $tanggal_umur56->addMonth()->startOfMonth();
                $tahun_pensiun = $tanggal_umur56->format('Y-m-d');

                // Create Karyawan
                $karyawan = Karyawan::create([
                    'osis_id' => $osis_id,
                    'ktp' => $ktp,
                    'nama_tk' => $nama,
                    'perusahaan_id' => $perusahaan_id,
                    'tanggal_lahir' => $tanggal_lahir,
                    'jenis_kelamin' => $jenis_kelamin,
                    'agama' => $agama,
                    'status' => $status,
                    'alamat' => $alamat,
                    'asal' => $asal,
                    'area_id' => $area_id,
                    'tipe_pekerjaan' => $tipe_pekerjaan,
                    'tahun_pensiun' => $tahun_pensiun,
                    'tanggal_pensiun' => $tanggal_pensiun,
                    'status_aktif' => 'Aktif',
                    'tanggal_bekerja' => now()->format('Y-m-d')
                ]);

                // Assign to Paket
                DB::table('paket_karyawan')->insert([
                    'paket_id' => $paket_id,
                    'karyawan_id' => $karyawan->karyawan_id,
                    'beg_date' => now()->format('Y-m-d'),
                ]);

                // Track paket for auto-calculation
                $affectedPaketIds[] = $paket_id;

                // Auto-assign Harian/Shift
                DB::table('riwayat_shift')->insert([
                    'karyawan_id' => $karyawan->karyawan_id,
                    'kode_harianshift' => is_numeric($kode_harianshift) ? $kode_harianshift : 1,
                    'beg_date' => now()->format('Y-m-d'),
                ]);

                // Auto-assign Jabatan
                if (!empty($kode_jabatan)) {
                    DB::table('riwayat_jabatan')->insert([
                        'karyawan_id' => $karyawan->karyawan_id,
                        'kode_jabatan' => $kode_jabatan,
                        'beg_date' => now()->format('Y-m-d'),
                    ]);
                }

                // Auto-assign Lokasi Kerja
                if (!empty($kode_lokasi)) {
                    DB::table('riwayat_lokasi')->insert([
                        'karyawan_id' => $karyawan->karyawan_id,
                        'kode_lokasi' => $kode_lokasi,
                        'beg_date' => now()->format('Y-m-d'),
                    ]);
                }

                // Auto-assign default Pakaian record
                $currentNilaiJatah = DB::table('md_pakaian')
                    ->where('is_deleted', 0)
                    ->orderByDesc('beg_date')
                    ->orderByDesc('created_at')
                    ->value('nilai_jatah') ?? 0;

                DB::table('md_pakaian')->insert([
                    'karyawan_id' => $karyawan->karyawan_id,
                    'nilai_jatah' => $currentNilaiJatah,
                    'ukuran_baju' => '-',
                    'ukuran_celana' => '-',
                    'beg_date' => now()->format('Y-m-d'),
                ]);

                $this->logs[] = "Baris $rowIndex: SUKSES - Karyawan '$nama' (OSIS: $osis_id) berhasil ditambahkan.";

                // Audit Log
                AuditLog::create([
                    'karyawan_id' => $karyawan->karyawan_id,
                    'aksi' => 'Dibuat (Import)',
                    'diubah_oleh' => auth()->user()->username ?? 'System',
                    'detail' => 'Karyawan baru ditambahkan via import: ' . $nama,
                    'data_baru' => $karyawan->toArray(),
                ]);
            }

            DB::commit();

            // Auto-Calculate Contract for affected pakets (after commit)
            $uniquePaketIds = array_unique($affectedPaketIds);
            if (!empty($uniquePaketIds)) {
                try {
                    $calculatorService = app(\App\Services\ContractCalculatorService::class);
                    foreach ($uniquePaketIds as $pid) {
                        $calculatorService->calculateForPaket($pid, date('Y-m'));
                    }
                } catch (\Exception $e) {
                    \Log::error('Auto-calculation after import failed: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->gagal++;
            $this->logs[] = "Error System: " . $e->getMessage();
            throw $e;
        }
    }
}
