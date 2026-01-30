<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Masakerja;
use App\Models\Ump;
use Illuminate\Http\Request;

class UmpController extends Controller
{
    public function index()
    {
        $data = DB::table('md_ump')
            ->where('md_ump.is_deleted', 0)
            ->join('md_lokasi','md_lokasi.kode_lokasi','=','md_ump.kode_lokasi')
            ->select('md_ump.*', 'md_lokasi.*')
             ->get();

        $hasDeleted = Ump::where('is_deleted', 1)->exists();
        return view('ump', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = Ump::where('md_ump.is_deleted', 1)
            ->join('md_lokasi','md_lokasi.kode_lokasi','=','md_ump.kode_lokasi')
            ->select('md_ump.*', 'md_lokasi.*')
            ->get();
        return view('ump-sampah', ['data' => $data]);
    }

    public function getTambah()
    {  
        $data = DB::table('md_lokasi')
            ->get();      
        return view('tambah-ump-tahunan', compact('data'));
    }

    public function setTambah(Request $request)
    {

        $umpData = $request->input('ump'); 
        foreach ($umpData as $lokasi => $nilaiUmp) {
             $nilai = str_replace('.', '', $nilaiUmp);
            // Simpan ke database atau lakukan proses lain
            // Misal:
            Ump::create([
                'kode_lokasi' => $lokasi,
                'ump' => $nilai,
                'tahun' => $request->input('tahun'),
            ]);
        }

        return redirect('/ump')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getTambah2()
    {  
        $data = DB::table('md_lokasi')
            ->get();      
        return view('tambah-ump', compact('data'));
    }

    public function setTambah2(Request $request)
    {
        $request->validate([
            'kode_lokasi' => 'required',
            'ump' => 'required',
            'tahun' => 'required'
        ]);
        
        $nilai = str_replace('.', '', $request->ump);
        Ump::create([
            'kode_lokasi' => $request->kode_lokasi,
            'ump' => $nilai,
            'tahun' => $request->tahun,
        ]);
    

        return redirect('/ump')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $data = DB::table('md_ump')
            ->join('md_lokasi', 'md_lokasi.kode_lokasi','=','md_ump.kode_lokasi')
            ->where('id','=', $id)
            ->first();

        return view('update-ump',['data' => $data]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'ump' => 'required',
            'tahun' => 'required'
        ]); 
        $ump = str_replace('.', '', $request->ump);
        Ump::where('id', $id)
        ->update([
            'ump' => $ump,
            'tahun' => $request->tahun
        ]);

        return redirect('/ump')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        Ump::where('id', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Ump::where('id', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }

}

