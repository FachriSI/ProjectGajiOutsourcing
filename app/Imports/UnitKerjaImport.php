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
                    
                    // Sanitize ID (remove non-numeric because DB is INT)
                    $cleanId = preg_replace('/[^0-9]/', '', $unitId);

                    // Cari unit kerja berdasarkan nama atau ID
                    $existing = null;
                    if (!empty($cleanId)) {
                        $existing = UnitKerja::where('unit_id', $cleanId)->first();
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
                            'is_deleted' => 0,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Unit Kerja '{$namaUnitKerja}' berhasil diupdate.";
                    } else {
                        // Tambah baru
                        
                        // Generate Manual ID
                        $lastUnit = UnitKerja::latest('unit_id')->first();
                        $newId = $lastUnit ? $lastUnit->unit_id + 1 : 1;
                        
                        // Check transaction-safe max ID (simulated)
                        static $maxId = null;
                        if ($maxId === null) {
                             $last = UnitKerja::latest('unit_id')->first();
                             $maxId = $last ? $last->unit_id : 0;
                        }
                        $maxId++;

                        // Default Departemen ID (Required by DB)
                        // Use 1 as default or find first available
                        $deptId = 1; 

                        $newData = [
                            'unit_id' => $maxId,
                            'unit_kerja' => $namaUnitKerja,
                            'departemen_id' => $deptId,
                            'is_deleted' => 0,
                        ];

                        if (!empty($cleanId)) {
                             $newData['unit_id'] = $cleanId;
                             if ($cleanId >= $maxId) {
                                 $maxId = $cleanId;
                             }
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
