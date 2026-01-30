<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pakaian;

class PakaianController extends Controller
{
    public function index()
    {
        $data = DB::table('md_pakaian')
            ->where('is_deleted', 0)
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
        return view('tambah-pakaian', ['karyawan' => $karyawan]);
    }

    public function setTambah(Request $request)
    {
        $request->validate(['karyawan_id' => 'required']);

        Pakaian::create([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => $request->nilai_jatah ?? 0,
            'ukuran_baju' => $request->ukuran_baju ?? '',
            'ukuran_celana' => $request->ukuran_celana ?? '',
            'beg_date' => $request->beg_date ?? now(),
        ]);

        return redirect('/pakaian')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('md_pakaian')->where('pakaian_id', '=', $id)->first();
        $karyawan = DB::table('md_karyawan')->get();
        return view('update-pakaian', ['dataP' => $dataP, 'karyawan' => $karyawan]);
    }

    public function setUpdate(Request $request, $id)
    {
        Pakaian::where('pakaian_id', $id)->update([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => $request->nilai_jatah ?? 0,
            'ukuran_baju' => $request->ukuran_baju ?? '',
            'ukuran_celana' => $request->ukuran_celana ?? '',
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
