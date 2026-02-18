<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    public function index()
    {
        $data = DB::table('md_jabatan')
            ->where('is_deleted', 0)
             ->get();

        $hasDeleted = Jabatan::where('is_deleted', 1)->exists();
        return view('jabatan', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Jabatan::where('is_deleted', 1)->get();
        return view('jabatan-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-jabatan');
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'jabatan' => 'required',
            'tunjangan' => 'required'
        ]);

        $last = Jabatan::latest('kode_jabatan')->first();
        $newId = $last ? $last->kode_jabatan + 1 : 1;

        $tunjangan = str_replace('.', '', $request->tunjangan);

        Jabatan::create([
            'kode_jabatan' => $newId,
            'jabatan' => $request->jabatan,
            'tunjangan_jabatan' => $tunjangan,
        ]);

        return redirect('/jabatan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataJ = DB::table('md_jabatan')->where('kode_jabatan', '=', $id)->first();
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
        Jabatan::where('kode_jabatan', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Jabatan::where('kode_jabatan', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        DB::table('md_jabatan')->where('kode_jabatan', $id)->delete();
        return redirect('/jabatan/sampah')->with('success', 'Data berhasil dihapus permanen!');
    }
}
