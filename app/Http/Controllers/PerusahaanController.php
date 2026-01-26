<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Perusahaan;


class PerusahaanController extends Controller
{
    public function index()
    {
        $data = DB::table('perusahaan')
            ->get();
        // dd($data);
        return view('perusahaan', ['data' => $data]);

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
        $dataP = DB::table('perusahaan')
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
        $hapus = Perusahaan::findorfail($id);
        $hapus->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
