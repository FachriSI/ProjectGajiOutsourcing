<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Kuotajam;

class KuotajamController extends Controller
{
    public function index()
    {
        $data = DB::table('kuota_jam')
            ->join('md_karyawan', 'kuota_jam.karyawan_id', '=', 'md_karyawan.karyawan_id')
            ->where('md_karyawan.is_deleted', 0)
            ->select('kuota_jam.*', 'md_karyawan.nama_tk as nama')
            ->get();

        $hasDeleted = false; // Table does not support soft deletes
        return view('kuotajam', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Kuotajam::where('is_deleted', 1)->get();
        return view('kuotajam-sampah', ['data' => $data]);
    }

    public function getTambah()
    {
        $karyawan = DB::table('md_karyawan')->where('is_deleted', 0)->get();
        return view('tambah-kuotajam', ['karyawan' => $karyawan]);
    }

    public function setTambah(Request $request)
    {
        $request->validate(['karyawan_id' => 'required', 'kuota' => 'required']);

        $last = Kuotajam::latest('kuota_id')->first();
        $newId = $last ? $last->kuota_id + 1 : 1;

        Kuotajam::create([
            'kuota_id' => $newId,
            'karyawan_id' => $request->karyawan_id,
            'kuota' => $request->kuota,
            'beg_date' => $request->beg_date ?? now(),
        ]);

        return redirect('/kuotajam')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataK = DB::table('kuota_jam')->where('kuota_id', '=', $id)->first();
        $karyawan = DB::table('md_karyawan')->get();
        return view('update-kuotajam', ['dataK' => $dataK, 'karyawan' => $karyawan]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate(['kuota' => 'required']);

        Kuotajam::where('kuota_id', $id)->update([
            'karyawan_id' => $request->karyawan_id,
            'kuota' => $request->kuota,
            'beg_date' => $request->beg_date,
        ]);

        return redirect('/kuotajam')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Kuotajam::where('kuota_id', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
