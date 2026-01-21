<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Departemen;


class DepartemenController extends Controller
{
    public function index()
    {
        $data = DB::table('departemen')
            ->get();
        return view('departemen', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-departemen');
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        $lastDepartemen = Departemen::latest('departemen_id')->first();
        $newId = $lastDepartemen ? $lastDepartemen->departemen_id + 1 : 1;

        Departemen::create([
            'departemen_id' => $newId,
            'departemen' => $request->nama,
            'is_si' => $request->is_si ?? 0,
        ]);

        return redirect('/departemen')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataD = DB::table('departemen')
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
        Departemen::where('departemen_id', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
