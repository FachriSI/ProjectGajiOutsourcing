<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\PaketKaryawan;
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
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->gagal++;
            $this->logs[] = "Error System: " . $e->getMessage();
            throw $e;
        }
    }
}
