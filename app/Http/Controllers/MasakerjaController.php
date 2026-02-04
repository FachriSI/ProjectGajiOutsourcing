<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Masakerja;

class MasakerjaController extends Controller
{
    public function index()
    {
        $data = DB::table('masa_kerja')
            ->join('md_karyawan', 'masa_kerja.karyawan_id', '=', 'md_karyawan.karyawan_id')
            ->where('masa_kerja.is_deleted', 0)
            ->where('md_karyawan.is_deleted', 0)
            ->select('masa_kerja.*', 'md_karyawan.nama_tk as nama')
            ->get();

        $hasDeleted = DB::table('masa_kerja')->where('is_deleted', 1)->exists();
        return view('masakerja', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = DB::table('masa_kerja')
            ->where('masa_kerja.is_deleted', 1)
            ->join('md_karyawan', 'masa_kerja.karyawan_id', '=', 'md_karyawan.karyawan_id')
            ->select('masa_kerja.*', 'md_karyawan.nama_tk as nama')
            ->get();
        return view('masakerja-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        $karyawan = DB::table('md_karyawan')->where('is_deleted', 0)->get();
        return view('tambah-masakerja', ['karyawan' => $karyawan]);
    }

    public function setTambah(Request $request)
    {
        $request->validate(['karyawan_id' => 'required', 'tunjangan_masakerja' => 'required']);

        $last = Masakerja::latest('id')->first();
        $newId = $last ? $last->id + 1 : 1;

        Masakerja::create([
            'id' => $newId,
            'karyawan_id' => $request->karyawan_id,
            'tunjangan_masakerja' => $request->tunjangan_masakerja,
            'beg_date' => $request->beg_date ?? now(),
        ]);

        return redirect('/masakerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataM = DB::table('masa_kerja')->where('id', '=', $id)->first();
        $karyawan = DB::table('md_karyawan')->get();
        return view('update-masakerja', ['dataM' => $dataM, 'karyawan' => $karyawan]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['tunjangan_masakerja' => 'required']);

        Masakerja::where('id', $id)->update([
            'karyawan_id' => $request->karyawan_id,
            'tunjangan_masakerja' => $request->tunjangan_masakerja,
            'beg_date' => $request->beg_date,
        ]);

        return redirect('/masakerja')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Masakerja::where('id', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Masakerja::where('id', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return redirect('/masakerja')->with('success', 'Data berhasil dipulihkan!');
    }
}
