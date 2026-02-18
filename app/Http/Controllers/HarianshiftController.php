<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Harianshift;

class HarianshiftController extends Controller
{
    public function index()
    {
        $data = DB::table('md_harianshift')
            ->where('is_deleted', 0)
             ->get();

        $hasDeleted = Harianshift::where('is_deleted', 1)->exists();
        return view('harianshift', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Harianshift::where('is_deleted', 1)->get();
        return view('harianshift-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-harianshift');
    }

    public function setTambah(Request $request)
    {
        $request->validate(['harianshift' => 'required', 'tunjangan' => 'required']);

        $last = Harianshift::latest('kode_harianshift')->first();
        $newId = $last ? $last->kode_harianshift + 1 : 1;

        $tunjangan = str_replace('.', '', $request->tunjangan);

        Harianshift::create([
            'kode_harianshift' => $newId,
            'harianshift' => $request->harianshift,
            'tunjangan_shift' => $tunjangan,
        ]);

        return redirect('/harianshift')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataH = DB::table('md_harianshift')->where('kode_harianshift', '=', $id)->first();
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
        Harianshift::where('kode_harianshift', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Harianshift::where('kode_harianshift', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        DB::table('md_harianshift')->where('kode_harianshift', $id)->delete();
        return redirect('/harianshift/sampah')->with('success', 'Data berhasil dihapus permanen!');
    }
}
