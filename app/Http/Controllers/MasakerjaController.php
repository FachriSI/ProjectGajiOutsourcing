<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Masakerja;

class MasakerjaController extends Controller
{
    public function index()
    {
        $data = DB::table('masa_kerja')
            ->join('karyawan', 'masa_kerja.karyawan_id', '=', 'karyawan.karyawan_id')
            ->select('masa_kerja.*', 'karyawan.nama_tk as nama')
            ->get();
        return view('masakerja', ['data' => $data]);
    }

    public function getTambah()
    {
        $karyawan = DB::table('karyawan')->get();
        return view('tambah-masakerja', ['karyawan' => $karyawan]);
    }

    public function setTambah(Request $request)
    {
        $request->validate(['karyawan_id' => 'required', 'tunjangan_masakerja' => 'required']);

        $last = Masakerja::latest('id')->first();
        $newId = $last ? $last->id + 1 : 1;

        Masakerja::create([
            'id' => $newId,
            'karyawan_id' => $request->karyawan_id,
            'tunjangan_masakerja' => $request->tunjangan_masakerja,
            'beg_date' => $request->beg_date ?? now(),
        ]);

        return redirect('/masakerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataM = DB::table('masa_kerja')->where('id', '=', $id)->first();
        $karyawan = DB::table('karyawan')->get();
        return view('update-masakerja', ['dataM' => $dataM, 'karyawan' => $karyawan]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['tunjangan_masakerja' => 'required']);

        Masakerja::where('id', $id)->update([
            'karyawan_id' => $request->karyawan_id,
            'tunjangan_masakerja' => $request->tunjangan_masakerja,
            'beg_date' => $request->beg_date,
        ]);

        return redirect('/masakerja')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Masakerja::where('id', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
