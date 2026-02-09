<?php

namespace App\Imports;

use App\Models\Lokasi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LokasiImport implements ToCollection, WithHeadingRow
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
                    if (empty($row['nama_lokasi'])) {
                        $this->gagal++;
                        $this->log[] = "Baris {$rowNumber}: Nama lokasi kosong";
                        continue;
                    }

                    $namaLokasi = trim($row['nama_lokasi']);
                    // Handle different possible column names
                    $jenis = trim($row['jenis_provinsikaupatenkota'] ?? $row['jenis'] ?? $row['jenis_provinsikabupaten_kota'] ?? 'Kota');

                    // Cari lokasi berdasarkan nama
                    $existingLokasi = Lokasi::where('lokasi', $namaLokasi)
                        ->where('is_deleted', 0)
                        ->first();

                    if ($existingLokasi) {
                        // Update lokasi yang sudah ada
                        $existingLokasi->update([
                            'jenis' => $jenis,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Lokasi '{$namaLokasi}' berhasil diupdate.";
                    } else {
                        // Tambah lokasi baru
                        Lokasi::create([
                            'lokasi' => $namaLokasi,
                            'jenis' => $jenis,
                            'is_deleted' => 0,
                        ]);
                        $this->log[] = "Baris {$rowNumber}: Lokasi '{$namaLokasi}' berhasil ditambahkan.";
                    }

                } catch (\Exception $e) {
                    $this->gagal++;
                    $this->log[] = "Baris {$rowNumber}: Gagal - " . $e->getMessage();
                    Log::error("Baris {$rowNumber}: Gagal import lokasi - " . $e->getMessage());
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import lokasi gagal total: " . $e->getMessage());
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
