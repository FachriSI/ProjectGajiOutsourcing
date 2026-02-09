<?php

namespace App\Imports;

use App\Models\Fungsi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FungsiImport implements ToCollection, WithHeadingRow
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
                    if (empty($row['nama_fungsi'])) {
                        $this->gagal++;
                        $this->log[] = "Baris {$rowNumber}: Nama fungsi kosong";
                        continue;
                    }

                    $namaFungsi = trim($row['nama_fungsi']);
                    $keterangan = trim($row['keterangan'] ?? '');

                    // Cari fungsi berdasarkan nama
                    $existing = Fungsi::where('fungsi', $namaFungsi)
                        ->where('is_deleted', 0)
                        ->first();

                    if ($existing) {
                        // Update yang sudah ada
                        $existing->update([
                            'keterangan' => $keterangan,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Fungsi '{$namaFungsi}' berhasil diupdate.";
                    } else {
                        // Tambah baru
                        Fungsi::create([
                            'fungsi' => $namaFungsi,
                            'keterangan' => $keterangan,
                            'is_deleted' => 0,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Fungsi '{$namaFungsi}' berhasil ditambahkan.";
                    }

                } catch (\Exception $e) {
                    $this->gagal++;
                    $this->log[] = "Baris {$rowNumber}: Gagal - " . $e->getMessage();
                    Log::error("Baris {$rowNumber}: Gagal import fungsi - " . $e->getMessage());
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import fungsi gagal total: " . $e->getMessage());
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
