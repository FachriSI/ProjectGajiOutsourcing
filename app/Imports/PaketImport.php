<?php

namespace App\Imports;

use App\Models\Paket;
use App\Models\UnitKerja;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class PaketImport implements ToCollection
{
    private $total = 0;
    private $gagal = 0;
    protected $log = [];

    public function getTotal()
    {
        return $this->total;
    }

    public function getGagal()
    {
        return $this->gagal;
    }

    public function collection(Collection $rows)
    {
        unset($rows[0]); // Lewati header

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $this->total++;

                $nama_paket = $row[0] ?? null;
                $kuota = $row[1] ?? null;
                $unit_kerja_nama = $row[2] ?? null;

                // Validasi: Nama Paket wajib
                if (empty($nama_paket)) {
                    $this->log[] = "Baris " . ($index + 1) . ": SKIP - Nama Paket kosong";
                    $this->gagal++;
                    continue;
                }

                // Cari Unit Kerja berdasarkan nama
                $unit_kerja = null;
                if (!empty($unit_kerja_nama)) {
                    $unit_kerja = UnitKerja::where('unit_kerja', 'LIKE', '%' . $unit_kerja_nama . '%')->first();
                    if (!$unit_kerja) {
                        $this->log[] = "Baris " . ($index + 1) . ": GAGAL - Unit Kerja '$unit_kerja_nama' tidak ditemukan";
                        $this->gagal++;
                        continue;
                    }
                }

                // Cek apakah paket dengan nama yang sama sudah ada
                $existingPaket = Paket::where('paket', $nama_paket)->first();

                if ($existingPaket) {
                    // Update existing
                    $existingPaket->kuota_paket = $kuota ?? $existingPaket->kuota_paket;
                    if ($unit_kerja) {
                        $existingPaket->unit_id = $unit_kerja->unit_id;
                    }
                    $existingPaket->save();
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Paket '$nama_paket' diupdate";
                } else {
                    // Create new
                    Paket::create([
                        'paket' => $nama_paket,
                        'kuota_paket' => $kuota ?? 0,
                        'unit_id' => $unit_kerja ? $unit_kerja->unit_id : null,
                    ]);
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Paket '$nama_paket' ditambahkan";
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getLog()
    {
        return $this->log;
    }
}
