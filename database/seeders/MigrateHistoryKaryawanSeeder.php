<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateHistoryKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil semua karyawan yang memiliki catatan pengganti
        $karyawans = DB::table('md_karyawan')
            ->whereNotNull('catatan_pengganti')
            ->where('catatan_pengganti', '!=', '')
            ->get();

        $count = 0;

        foreach ($karyawans as $karyawan) {
            // Regex untuk mengambil ID karyawan lama
            if (preg_match('/Pengganti\s+ID\s*:?\s*(\d+)/i', $karyawan->catatan_pengganti, $matches)) {
                $oldId = $matches[1];
                $newId = $karyawan->karyawan_id;

                // Cek apakah data sudah ada di history_karyawan (untuk menghindari duplikasi saat re-run)
                $exists = DB::table('history_karyawan')
                    ->where('karyawan_id', $newId)
                    ->where('karyawan_sebelumnya_id', $oldId)
                    ->exists();

                if (!$exists) {
                    // Ambil data karyawan lama untuk detail tanggal dan catatan
                    $oldKaryawan = DB::table('md_karyawan')
                        ->where('karyawan_id', $oldId)
                        ->first();

                    if ($oldKaryawan) {
                        DB::table('history_karyawan')->insert([
                            // Paket ID mungkin tidak tersedia secara langsung, bisa ambil dari paket_karyawan terakhir karyawan lama atau baru
                            // Untuk simplifikasi data lama, kita biarkan null atau ambil dari logic lain jika perlu.
                            // Disini kita biarkan null dulu atau attempt fetch.
                            'paket_id' => $this->getPaketId($newId), 
                            'karyawan_id' => $newId,
                            'karyawan_sebelumnya_id' => $oldId,
                            'tanggal_diberhentikan' => $oldKaryawan->tanggal_berhenti,
                            'diberhentikan_oleh' => $oldKaryawan->diberhentikan_oleh,
                            'catatan' => $oldKaryawan->catatan_berhenti,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $count++;
                    }
                }
            }
        }

        $this->command->info("Berhasil memigrasikan $count data riwayat ke tabel history_karyawan.");
    }

    private function getPaketId($karyawanId)
    {
        $paket = DB::table('paket_karyawan')
            ->where('karyawan_id', $karyawanId)
            ->orderByDesc('beg_date')
            ->first();
        
        return $paket ? $paket->paket_id : null;
    }
}
