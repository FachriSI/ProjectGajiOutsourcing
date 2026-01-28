<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Penyesuaian;

class PenyesuaianController extends Controller
{
    public function index()
    {
        $data = DB::table('md_penyesuaian')
            ->where('is_deleted', 0)
             ->get();

        $hasDeleted = Penyesuaian::where('is_deleted', 1)->exists();
        return view('penyesuaian', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Penyesuaian::where('is_deleted', 1)->get();
        return view('penyesuaian-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-penyesuaian');
    }

    public function setTambah(Request $request)
    {
        $request->validate(['keterangan' => 'required']);

        $last = Penyesuaian::latest('kode_suai')->first();
        $newId = $last ? $last->kode_suai + 1 : 1;

        Penyesuaian::create([
            'kode_suai' => $newId,
            'keterangan' => $request->keterangan,
            'tunjangan_penyesuaian' => $request->tunjangan ?? 0,
        ]);

        return redirect('/penyesuaian')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('md_penyesuaian')->where('kode_suai', '=', $id)->first();
        return view('update-penyesuaian', ['dataP' => $dataP]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['keterangan' => 'required']);

        Penyesuaian::where('kode_suai', $id)->update([
            'keterangan' => $request->keterangan,
            'tunjangan_penyesuaian' => $request->tunjangan ?? 0,
        ]);

        return redirect('/penyesuaian')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Penyesuaian::where('kode_suai', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Penyesuaian::where('kode_suai', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }
}
