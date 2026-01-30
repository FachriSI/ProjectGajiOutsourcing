<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KaryawanBaruImport implements ToCollection, WithStartRow
{
    private $total = 0;
    private $gagal = 0;
    private $log = [];

    public function startRow(): int
    {
        return 2; // Asumsi baris 1 adalah header
    }

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
        return $this->log;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $this->total++;

            // Format Excel: No, Nama, Alamat, CP, CPJAB, CPTelp, CPEmail, idMesin, Deleted, TKP, NPP
            // Indexing: 0=No, 1=Nama, 2=Alamat, 3=CP, 4=CPJAB, 5=CPTelp, 6=CPEmail, 7=idMesin, 8=Deleted, 9=TKP, 10=NPP

            $nama = trim($row[1] ?? '');

            if (empty($nama)) {
                $this->gagal++;
                $this->log[] = "Baris " . ($index + 2) . ": Nama kosong.";
                continue;
            }

            try {
                // Cari karyawan berdasarkan Nama (atau bisa juga identifier lain jika ada, tapi di sini nama jadi acuan)
                // Atau Create baru jika belum ada.
                // Jika ingin update only, pakai updateOrCreate.

                // Karena user bilang "PERBARUI" dan "Upload Template", asumsi ini insert/update data master.
                // Kita akan gunakan Nama sebagai kunci pencarian sederhana untuk saat ini, atau create baru.

                // Namun, Karyawan butuh field wajib lain seperti osis_id, ktp, perusahaan_id, dll di sistem ini.
                // Jika data excel ini Partial Data, kita mungkin hanya update data yang ada.
                // Jika Create Baru, akan banyak field null.

                // Mari coba update berdasarkan Nama, atau Buat baru dengan data minimal.

                Karyawan::updateOrCreate(
                    ['nama_tk' => $nama], // Kunci pencarian
                    [
                        'alamat' => $row[2],
                        'cp' => $row[3],
                        'cp_jab' => $row[4],
                        'cp_telp' => $row[5],
                        'cp_email' => $row[6],
                        'id_mesin' => $row[7],
                        'deleted_data' => $row[8],
                        'tkp' => $row[9],
                        'npp' => $row[10],
                        // Field wajib lainnya mungkin perlu default value jika create baru
                        // 'perusahaan_id' => 1, // Default? Atau cari berdasarkan NPP?
                    ]
                );

            } catch (\Exception $e) {
                $this->gagal++;
                $this->log[] = "Baris " . ($index + 2) . " ($nama): " . $e->getMessage();
            }
        }
    }
}
