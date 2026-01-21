<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Harianshift;

class HarianshiftController extends Controller
{
    public function index()
    {
        $data = DB::table('harianshift')->get();
        return view('harianshift', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-harianshift');
    }

    public function setTambah(Request $request)
    {
        $request->validate(['nama' => 'required']);

        $last = Harianshift::latest('kode_harianshift')->first();
        $newId = $last ? $last->kode_harianshift + 1 : 1;

        Harianshift::create([
            'kode_harianshift' => $newId,
            'harianshift' => $request->nama,
            'tunjangan_shift' => $request->tunjangan ?? 0,
        ]);

        return redirect('/harianshift')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataH = DB::table('harianshift')->where('kode_harianshift', '=', $id)->first();
        return view('update-harianshift', ['dataH' => $dataH]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['nama' => 'required']);

        Harianshift::where('kode_harianshift', $id)->update([
            'harianshift' => $request->nama,
            'tunjangan_shift' => $request->tunjangan ?? 0,
        ]);

        return redirect('/harianshift')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Harianshift::where('kode_harianshift', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
