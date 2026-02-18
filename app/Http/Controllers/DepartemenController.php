<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Departemen;


class DepartemenController extends Controller
{
    public function index()
    {
        $data = DB::table('md_departemen')
            ->where('is_deleted', 0)
            ->get();

        $hasDeleted = Departemen::where('is_deleted', 1)->exists();

        return view('departemen', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Departemen::where('is_deleted', 1)->get();
        return view('departemen-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-departemen');
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'departemen' => 'required',
        ]);

        $lastDepartemen = Departemen::latest('departemen_id')->first();
        $newId = $lastDepartemen ? $lastDepartemen->departemen_id + 1 : 1;

        Departemen::create([
            'departemen_id' => $newId,
            'departemen' => $request->departemen,
            'is_si' => 0,
        ]);

        return redirect('/departemen')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataD = DB::table('md_departemen')
            ->where('departemen_id', '=', $id)
            ->first();

        return view('update-departemen', ['dataD' => $dataD]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        Departemen::where('departemen_id', $id)
            ->update([
                'departemen' => $request->nama,
                'is_si' => $request->is_si ?? 0,
            ]);

        return redirect('/departemen')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        // Soft Delete
        Departemen::where('departemen_id', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Departemen::where('departemen_id', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        DB::table('md_departemen')->where('departemen_id', $id)->delete();
        return redirect('/departemen/sampah')->with('success', 'Data berhasil dihapus permanen!');
    }
}
