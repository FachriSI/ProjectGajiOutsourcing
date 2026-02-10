<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pakaian;

class PakaianController extends Controller
{
    public function index()
    {
        // Ambil satu contoh data pakaian terakhir dari karyawan aktif untuk menampilkan nilai jatah saat ini
        // Asumsi: Semua karyawan aktif memiliki nilai jatah yang sama setelah update global
        // Ambil satu contoh data pakaian terakhir (global setting terakhir)
        // Kita ambil record yang paling baru dibuat/berlaku
        // Ambil satu contoh data pakaian terakhir (global setting terakhir)
        // Kita ambil record yang paling baru DI-INPUT (created_at), bukan berdasarkan tanggal berlaku (beg_date)
        // Ini agar user melihat angka yang baru saja dia input, meskipun tanggal berlakunya mundur/maju.
        $sampleData = \App\Models\Pakaian::where('is_deleted', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        $currentNilai = $sampleData ? $sampleData->nilai_jatah : 0;

        // Use a simple history if needed, or just the current value.
        // For now, passing the current value.

        return view('pakaian', ['currentNilai' => $currentNilai]);
    }

    public function updateGlobal(Request $request)
    {
        $request->validate([
            'nilai_jatah' => 'required|numeric|min:0',
        ]);

        $nilaiJatah = $request->nilai_jatah;
        // Gunakan tanggal hari ini secara otomatis
        $begDate = now()->format('Y-m-d');

        // Ambil semua karyawan aktif
        $activeKaryawan = \App\Models\Karyawan::where('status_aktif', 'Aktif')->get();

        $count = 0;

        DB::transaction(function () use ($activeKaryawan, $nilaiJatah, $begDate, &$count) {
            foreach ($activeKaryawan as $karyawan) {
                // Ambil data pakaian terakhir untuk mendapatkan ukuran baju/celana
                $lastPakaian = Pakaian::where('karyawan_id', $karyawan->karyawan_id)
                    ->where('is_deleted', 0)
                    ->orderBy('beg_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $ukuranBaju = $lastPakaian ? $lastPakaian->ukuran_baju : '0';
                $ukuranCelana = $lastPakaian ? $lastPakaian->ukuran_celana : '0';

                // Buat record baru atau update jika tanggal sama
                Pakaian::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->karyawan_id,
                        'beg_date' => $begDate
                    ],
                    [
                        'nilai_jatah' => $nilaiJatah,
                        'ukuran_baju' => $ukuranBaju,
                        'ukuran_celana' => $ukuranCelana,
                        'is_deleted' => 0
                    ]
                );

                $count++;
            }
        });

        return redirect('/pakaian')->with('success', "Berhasil memperbarui Nilai Jatah untuk {$count} karyawan aktif.");
    }

}
