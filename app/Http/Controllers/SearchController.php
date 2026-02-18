<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Paket;
use App\Models\Perusahaan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\Lokasi;
use App\Models\UnitKerja;
use App\Models\Fungsi;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->back();
        }

        $limit = 5; // Reduced limit per category for cleaner UI

        // 1. Karyawan
        $karyawan = Karyawan::with('jabatan')
                    ->where('nama_tk', 'LIKE', "%{$query}%")
                    ->orWhere('nik', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

        // 2. Paket
        $paket = Paket::where('paket', 'LIKE', "%{$query}%")
                    ->orWhere('paket_id', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

        // 3. Perusahaan
        $perusahaan = Perusahaan::where('perusahaan', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

        // 4. Jabatan
        $jabatan = Jabatan::where('jabatan', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

        // 5. Departemen
        $departemen = Departemen::where('departemen', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

        // 6. Lokasi
        $lokasi = Lokasi::where('lokasi', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();
        
        // 7. Unit Kerja
        $unitKerja = UnitKerja::where('unit_kerja', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

        // 8. Fungsi
        $fungsi = Fungsi::where('fungsi', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

        return view('search.index', compact(
            'query', 
            'karyawan', 
            'paket', 
            'perusahaan', 
            'jabatan', 
            'departemen', 
            'lokasi', 
            'unitKerja', 
            'fungsi'
        ));
    }
}
