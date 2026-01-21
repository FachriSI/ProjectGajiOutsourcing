<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Fungsi;


class FungsiController extends Controller
{
    public function index()
    {
        $data = DB::table('fungsi')->get();
        return view('fungsi', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-fungsi');
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        $lastFungsi = Fungsi::latest('kode_fungsi')->first();
        $newId = $lastFungsi ? $lastFungsi->kode_fungsi + 1 : 1;

        Fungsi::create([
            'kode_fungsi' => $newId,
            'fungsi' => $request->nama,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect('/fungsi')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataF = DB::table('fungsi')
            ->where('kode_fungsi', '=', $id)
            ->first();

        return view('update-fungsi', ['dataF' => $dataF]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        Fungsi::where('kode_fungsi', $id)
            ->update([
                'fungsi' => $request->nama,
                'keterangan' => $request->keterangan ?? '',
            ]);

        return redirect('/fungsi')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Fungsi::where('kode_fungsi', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
