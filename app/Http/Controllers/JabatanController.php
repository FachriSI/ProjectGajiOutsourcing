<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    public function index()
    {
        $data = DB::table('jabatan')->get();
        return view('jabatan', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-jabatan');
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        $last = Jabatan::latest('kode_jabatan')->first();
        $newId = $last ? $last->kode_jabatan + 1 : 1;

        Jabatan::create([
            'kode_jabatan' => $newId,
            'jabatan' => $request->nama,
            'tunjangan_jabatan' => $request->tunjangan ?? 0,
        ]);

        return redirect('/jabatan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataJ = DB::table('jabatan')->where('kode_jabatan', '=', $id)->first();
        return view('update-jabatan', ['dataJ' => $dataJ]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['nama' => 'required']);

        Jabatan::where('kode_jabatan', $id)->update([
            'jabatan' => $request->nama,
            'tunjangan_jabatan' => $request->tunjangan ?? 0,
        ]);

        return redirect('/jabatan')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Jabatan::where('kode_jabatan', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
