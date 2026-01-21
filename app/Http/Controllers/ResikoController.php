<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Resiko;

class ResikoController extends Controller
{
    public function index()
    {
        $data = DB::table('resiko')->get();
        return view('resiko', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-resiko');
    }

    public function setTambah(Request $request)
    {
        $request->validate(['nama' => 'required']);

        $last = Resiko::latest('kode_resiko')->first();
        $newId = $last ? $last->kode_resiko + 1 : 1;

        Resiko::create([
            'kode_resiko' => $newId,
            'resiko' => $request->nama,
            'tunjangan_resiko' => $request->tunjangan ?? 0,
        ]);

        return redirect('/resiko')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataR = DB::table('resiko')->where('kode_resiko', '=', $id)->first();
        return view('update-resiko', ['dataR' => $dataR]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['nama' => 'required']);

        Resiko::where('kode_resiko', $id)->update([
            'resiko' => $request->nama,
            'tunjangan_resiko' => $request->tunjangan ?? 0,
        ]);

        return redirect('/resiko')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Resiko::where('kode_resiko', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
