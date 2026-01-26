<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Karyawan;
use App\Models\MasterUkuran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PakaianImport implements ToCollection, WithHeadingRow
{
    private $total = 0;
    private $gagal = 0;
    private $log = [];
    private $validSizes = [];

    public function __construct()
    {
        // Preload valid sizes for validation
        $this->validSizes = MasterUkuran::pluck('nama_ukuran')->toArray();
    }

    public function collection(Collection $rows)
    {
        $this->total = count($rows);

        foreach ($rows as $index => $row) {
            $line = $index + 2; // Adjust for heading row

            try {
                // Validate required fields
                if (empty($row['osis_id'])) {
                    $this->gagal++;
                    $this->log[] = "Baris $line: OSIS ID kosong.";
                    continue;
                }

                // Find Karyawan
                $karyawan = Karyawan::where('osis_id', $row['osis_id'])->first();
                if (!$karyawan) {
                    $this->gagal++;
                    $this->log[] = "Baris $line: Karyawan dengan OSIS ID '{$row['osis_id']}' tidak ditemukan.";
                    continue;
                }

                // Validate Sizes
                $baju = strtoupper(trim($row['ukuran_baju']));
                // Allow celana to be number or string, but typically number
                $celana = trim($row['ukuran_celana']);

                if (!empty($baju) && !in_array($baju, $this->validSizes)) {
                    $this->gagal++;
                    $this->log[] = "Baris $line: Ukuran baju '$baju' tidak valid (Tidak ada di Master Data).";
                    continue;
                }

                // Insert into Pakaian table
                DB::table('pakaian')->insert([
                    'karyawan_id' => $karyawan->karyawan_id,
                    'nilai_jatah' => 600000, // Default value
                    'ukuran_baju' => $baju,
                    'ukuran_celana' => $celana,
                    'beg_date' => Carbon::now(), // Set to current date
                ]);

            } catch (\Exception $e) {
                $this->gagal++;
                $this->log[] = "Baris $line: Error - " . $e->getMessage();
            }
        }
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
}
