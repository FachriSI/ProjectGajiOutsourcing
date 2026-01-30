<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Perusahaan;


class PerusahaanController extends Controller
{
    public function index()
    {
        $data = DB::table('md_perusahaan')
            ->where('is_deleted', 0)
            ->get();
        // dd($data);
        $hasDeleted = Perusahaan::where('is_deleted', 1)->exists();
        return view('perusahaan', ['data' => $data, 'hasDeleted' => $hasDeleted]);

    }

    public function trash()
    {
        $data = Perusahaan::where('is_deleted', 1)->get();
        return view('perusahaan-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        return view('tambah-perusahaan');
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        // Fix: Use correct primary key for latest
        $lastPerusahaan = Perusahaan::latest('perusahaan_id')->first();
        $newId = $lastPerusahaan ? $lastPerusahaan->perusahaan_id + 1 : 1;

        Perusahaan::create([
            'perusahaan_id' => $newId,
            'perusahaan' => $request->nama,
            'alamat' => $request->alamat,
            'cp' => $request->cp,
            'cp_jab' => $request->cp_jab,
            'cp_telp' => $request->cp_telp,
            'cp_email' => $request->cp_email,
            'id_mesin' => $request->id_mesin,
            'tkp' => $request->tkp,
            'npp' => $request->npp,
            // 'deleted_data' default null or handled elsewhere
        ]);

        return redirect('/perusahaan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('md_perusahaan')
            ->where('perusahaan_id', '=', $id)
            ->first();

        return view('update-perusahaan', ['dataP' => $dataP]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
        ]);


        Perusahaan::where('perusahaan_id', $id)
            ->update([
                'perusahaan' => $request->nama,
                'alamat' => $request->alamat,
                'cp' => $request->cp,
                'cp_jab' => $request->cp_jab,
                'cp_telp' => $request->cp_telp,
                'cp_email' => $request->cp_email,
                'id_mesin' => $request->id_mesin,
                'tkp' => $request->tkp,
                'npp' => $request->npp,
                'deleted_data' => $request->deleted_data,
            ]);

        return redirect('/perusahaan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        Perusahaan::where('perusahaan_id', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Perusahaan::where('perusahaan_id', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }
}
