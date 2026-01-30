<?php

namespace App\Imports;

use App\Models\Perusahaan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PerusahaanImport implements ToCollection, WithStartRow
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
                $this->log[] = "Baris " . ($index + 2) . ": Nama Perusahaan kosong.";
                continue;
            }

            try {
                // Update or Create Perusahaan
                Perusahaan::updateOrCreate(
                    ['perusahaan' => $nama], // Kunci pencarian berdasarkan Nama Perusahaan
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
                    ]
                );

            } catch (\Exception $e) {
                $this->gagal++;
                $this->log[] = "Baris " . ($index + 2) . " ($nama): " . $e->getMessage();
            }
        }
    }
}
