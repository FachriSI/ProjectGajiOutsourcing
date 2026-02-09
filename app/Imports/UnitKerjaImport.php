<?php

namespace App\Imports;

use App\Models\UnitKerja;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UnitKerjaImport implements ToCollection, WithHeadingRow
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
                    if (empty($row['nama_unit_kerja'])) {
                        $this->gagal++;
                        $this->log[] = "Baris {$rowNumber}: Nama unit kerja kosong";
                        continue;
                    }

                    $unitId = trim($row['id_unit'] ?? '');
                    $namaUnitKerja = trim($row['nama_unit_kerja']);

                    // Cari unit kerja berdasarkan nama atau ID
                    $existing = null;
                    if (!empty($unitId)) {
                        $existing = UnitKerja::where('unit_id', $unitId)
                            ->where('is_deleted', 0)
                            ->first();
                    }

                    if (!$existing) {
                        $existing = UnitKerja::where('unit_kerja', $namaUnitKerja)
                            ->where('is_deleted', 0)
                            ->first();
                    }

                    if ($existing) {
                        // Update yang sudah ada
                        $existing->update([
                            'unit_kerja' => $namaUnitKerja,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Unit Kerja '{$namaUnitKerja}' berhasil diupdate.";
                    } else {
                        // Tambah baru
                        $newData = [
                            'unit_kerja' => $namaUnitKerja,
                            'is_deleted' => 0,
                        ];

                        if (!empty($unitId)) {
                            $newData['unit_id'] = $unitId;
                        }

                        UnitKerja::create($newData);
                        $this->log[] = "Baris {$rowNumber}: Unit Kerja '{$namaUnitKerja}' berhasil ditambahkan.";
                    }

                } catch (\Exception $e) {
                    $this->gagal++;
                    $this->log[] = "Baris {$rowNumber}: Gagal - " . $e->getMessage();
                    Log::error("Baris {$rowNumber}: Gagal import unit kerja - " . $e->getMessage());
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import unit kerja gagal total: " . $e->getMessage());
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
