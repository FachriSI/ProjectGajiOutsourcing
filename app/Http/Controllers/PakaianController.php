<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pakaian;

class PakaianController extends Controller
{
    public function index()
    {
        $data = DB::table('pakaian')
            ->join('karyawan', 'pakaian.karyawan_id', '=', 'karyawan.karyawan_id')
            ->select('pakaian.*', 'karyawan.nama_tk as nama')
            ->get();
        return view('pakaian', ['data' => $data]);
    }

    public function getTambah()
    {
        $karyawan = DB::table('karyawan')->get();
        return view('tambah-pakaian', ['karyawan' => $karyawan]);
    }

    public function setTambah(Request $request)
    {
        $request->validate(['karyawan_id' => 'required']);

        Pakaian::create([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => $request->nilai_jatah ?? 0,
            'ukuran_baju' => $request->ukuran_baju ?? '',
            'ukuran_celana' => $request->ukuran_celana ?? '',
            'beg_date' => $request->beg_date ?? now(),
        ]);

        return redirect('/pakaian')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('pakaian')->where('pakaian_id', '=', $id)->first();
        $karyawan = DB::table('karyawan')->get();
        return view('update-pakaian', ['dataP' => $dataP, 'karyawan' => $karyawan]);
    }

    public function setUpdate(Request $request, $id)
    {
        Pakaian::where('pakaian_id', $id)->update([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => $request->nilai_jatah ?? 0,
            'ukuran_baju' => $request->ukuran_baju ?? '',
            'ukuran_celana' => $request->ukuran_celana ?? '',
            'beg_date' => $request->beg_date,
        ]);

        return redirect('/pakaian')->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        Pakaian::where('pakaian_id', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
