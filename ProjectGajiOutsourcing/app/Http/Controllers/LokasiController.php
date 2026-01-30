<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Lokasi;

class LokasiController extends Controller
{
    public function index()
    {
        $data = DB::table('md_lokasi')
            ->where('is_deleted', 0)
             ->get();

        $hasDeleted = Lokasi::where('is_deleted', 1)->exists();
        return view('lokasi', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Lokasi::where('is_deleted', 1)->get();
        return view('lokasi-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-lokasi');
    }

    public function setTambah(Request $request)
    {
        $request->validate(['nama' => 'required']);

        $last = Lokasi::latest('kode_lokasi')->first();
        $newId = $last ? $last->kode_lokasi + 1 : 1;

        Lokasi::create([
            'kode_lokasi' => $newId,
            'lokasi' => $request->nama,
            'jenis' => $request->jenis ?? '',
        ]);

        return redirect('/lokasi')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataL = DB::table('md_lokasi')->where('kode_lokasi', '=', $id)->first();
        return view('update-lokasi', ['dataL' => $dataL]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['nama' => 'required']);

        Lokasi::where('kode_lokasi', $id)->update([
            'lokasi' => $request->nama,
            'jenis' => $request->jenis ?? '',
        ]);

        return redirect('/lokasi')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Lokasi::where('kode_lokasi', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Lokasi::where('kode_lokasi', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }
}
