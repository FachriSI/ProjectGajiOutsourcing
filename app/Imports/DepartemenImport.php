<?php

namespace App\Imports;

use App\Models\Departemen;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DepartemenImport implements ToCollection, WithHeadingRow
{
    protected $total = 0;
    protected $gagal = 0;
    protected $log = [];

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $this->total++;

                try {
                    // Skip baris kosong
                    if (empty($row['nama_departemen'])) {
                        $this->gagal++;
                        $this->log[] = "Baris {$rowNumber}: Nama departemen kosong";
                        continue;
                    }

                    $namaDepartemen = trim($row['nama_departemen']);
                    $isSi = isset($row['is_si_1ya_0tidak']) ? (int) $row['is_si_1ya_0tidak'] : 0;

                    // Cari departemen berdasarkan nama
                    $existing = Departemen::where('departemen', $namaDepartemen)
                        ->where('is_deleted', 0)
                        ->first();

                    if ($existing) {
                        // Update yang sudah ada
                        $existing->update([
                            'is_si' => $isSi,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Departemen '{$namaDepartemen}' berhasil diupdate.";
                    } else {
                        // Tambah baru
                        Departemen::create([
                            'departemen' => $namaDepartemen,
                            'is_si' => $isSi,
                            'is_deleted' => 0,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Departemen '{$namaDepartemen}' berhasil ditambahkan.";
                    }

                } catch (\Exception $e) {
                    $this->gagal++;
                    $this->log[] = "Baris {$rowNumber}: Gagal - " . $e->getMessage();
                    Log::error("Baris {$rowNumber}: Gagal import departemen - " . $e->getMessage());
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import departemen gagal total: " . $e->getMessage());
            throw $e;
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
