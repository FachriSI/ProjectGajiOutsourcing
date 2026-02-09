<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pakaian;

class PakaianController extends Controller
{
    public function index()
    {
        // 1. Sync: Ensure all active employees have a Pakaian record
        $activeKaryawan = \App\Models\Karyawan::where('status_aktif', 'Aktif')->get();

        foreach ($activeKaryawan as $k) {
            $exists = Pakaian::where('karyawan_id', $k->karyawan_id)
                ->where('is_deleted', 0)
                ->exists();

            if (!$exists) {
                Pakaian::create([
                    'karyawan_id' => $k->karyawan_id,
                    'nilai_jatah' => 690000, // Default requested by user
                    'ukuran_baju' => '0',
                    'ukuran_celana' => '0',
                    'beg_date' => now()->format('Y-m-d'),
                ]);
            }
        }

        // 2. Fetch Data for View
        // Join with subquery to get ONLY the latest Pakaian record by ID (most recent entry)
        $latestPakaian = DB::table('md_pakaian')
            ->select(DB::raw('MAX(pakaian_id) as max_id'))
            ->where('is_deleted', 0)
            ->groupBy('karyawan_id');

        $data = DB::table('md_karyawan')
            ->join('md_pakaian', function ($join) {
                $join->on('md_karyawan.karyawan_id', '=', 'md_pakaian.karyawan_id');
            })
            ->joinSub($latestPakaian, 'latest_pakaian', function ($join) {
                $join->on('md_pakaian.pakaian_id', '=', 'latest_pakaian.max_id');
            })
            ->where('md_karyawan.status_aktif', 'Aktif')
            ->where('md_pakaian.is_deleted', 0)
            ->select(
                'md_karyawan.nama_tk',
                'md_karyawan.karyawan_id',
                'md_pakaian.pakaian_id',
                'md_pakaian.nilai_jatah',
                'md_pakaian.ukuran_baju',
                'md_pakaian.ukuran_celana'
            )
            ->get();

        $hasDeleted = Pakaian::where('is_deleted', 1)->exists();
        return view('pakaian', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Pakaian::where('is_deleted', 1)->get();
        return view('pakaian-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        $karyawan = DB::table('md_karyawan')->get();
        $masterUkuran = \App\Models\MasterUkuran::all();
        return view('tambah-pakaian', ['karyawan' => $karyawan, 'masterUkuran' => $masterUkuran]);
    }

    public function setTambah(Request $request)
    {
        $request->validate(['karyawan_id' => 'required']);

        Pakaian::create([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => $request->nilai_jatah ?? 0,
            'ukuran_baju' => $request->ukuran_baju ?? 0,
            'ukuran_celana' => $request->ukuran_celana ?? 0,
            'beg_date' => $request->beg_date ?? now(),
        ]);

        return redirect('/pakaian')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('md_pakaian')->where('pakaian_id', '=', $id)->first();
        $karyawan = DB::table('md_karyawan')->get();
        $masterUkuran = \App\Models\MasterUkuran::all();
        return view('update-pakaian', ['dataP' => $dataP, 'karyawan' => $karyawan, 'masterUkuran' => $masterUkuran]);
    }

    public function setUpdate(Request $request, $id)
    {
        Pakaian::where('pakaian_id', $id)->update([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => $request->nilai_jatah ?? 0,
            'ukuran_baju' => $request->ukuran_baju ?? 0,
            'ukuran_celana' => $request->ukuran_celana ?? 0,
            'beg_date' => $request->beg_date,
        ]);

        return redirect('/pakaian')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Pakaian::where('pakaian_id', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Pakaian::where('pakaian_id', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }
}
