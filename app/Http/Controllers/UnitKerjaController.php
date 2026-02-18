<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UnitKerja;
use App\Models\Bidang;
use App\Models\Area;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $data = DB::table('md_unit_kerja as unit_kerja')
            ->where('is_deleted', 0)
            ->select('unit_kerja.unit_id', 'unit_kerja.unit_kerja')
             ->get();

        $hasDeleted = UnitKerja::where('is_deleted', 1)->exists();
        return view('unit-kerja', ['data' => $data, 'hasDeleted' => $hasDeleted]);
    }

    public function trash()
    {
        $data = UnitKerja::where('is_deleted', 1)->get();
        return view('unit-kerja-sampah', ['data' => $data]);
    }

    public function getTambah()
    {        
        $fungsi = \App\Models\Fungsi::where('is_deleted', 0)->get();
        return view('tambah-unit', ['fungsi' => $fungsi]);
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'unit_kerja' => 'required',
            'fungsi_id' => 'required'
        ]); 

        UnitKerja::create([
            'unit_kerja' => $request->unit_kerja,
            'fungsi_id' => $request->fungsi_id,
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getTambahBidang()
    {        
        $dataU = DB::table('md_unit_kerja')
        ->get();
        return view('tambah-bidang',['dataU'=>$dataU ]);
    }

    public function setTambahBidang(Request $request)
    {
        $request->validate([
            'unit' => 'required',
            'bidang' => 'required',
        ]); 
    
        Bidang::create([
            'unit_id' =>$request->unit,
            'bidang' => $request->bidang,
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getTambahArea()
    {        
        $dataU = DB::table('md_unit_kerja')
        ->get();
        return view('tambah-area',['dataU'=>$dataU ]);
    }

    public function getBidang($unit_id)
    {
        $bidang = DB::table('md_bidang')->where('unit_id', $unit_id)->get();
        return response()->json($bidang);
    }


    public function setTambahArea(Request $request)
    {
        $request->validate([
            'bidang' => 'required',
            'area' => 'required'
        ]); 
    
        Area::create([
            'bidang_id' =>$request->bidang,
            'area' => $request->area,
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('md_unit_kerja')
            ->where('unit_id','=', $id)
            ->first();

        return view('update-unit',['dataP' => $dataP]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'unit' => 'required',
        ]); 

        
        UnitKerja::where('unit_id', $id)
        ->update([
            'unit_kerja' => $request->unit,
        ]);

        return redirect('/unit-kerja')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        UnitKerja::where('unit_id', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        UnitKerja::where('unit_id', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        DB::table('md_unit_kerja')->where('unit_id', $id)->delete();
        return redirect('/unit-kerja/sampah')->with('success', 'Data berhasil dihapus permanen!');
    }
}
