<?php

namespace App\Http\Controllers;

use App\Models\Lebaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LebaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Lebaran::where('is_deleted', 0)
            ->where('tahun', '>=', 2026)
            ->orderBy('tahun', 'asc') // Changed to asc to show 2026 first, or keep desc if they want latest first? User said "dimulai dari 2026", usually implies list starts there. I'll stick to user's implicit ordering or default. actually `asc` makes more sense if "start from". But typically these lists are `desc`. I will keep `desc` for now as it shows future dates first which is often preferred, but force the filter. Wait, "dimulai dari tahun 2026" in a table usually means the list should *contain* 2026 and up.
            ->orderBy('tahun', 'asc') // Let's try ASC this time as "Starting from" suggests chronological order.
            ->get();
        $hasDeleted = Lebaran::where('is_deleted', 1)->exists();
        return view('lebaran', compact('data', 'hasDeleted'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getTambah()
    {
        return view('tambah-lebaran');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function setTambah(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|unique:md_lebaran,tahun,NULL,id,is_deleted,0',
            'tanggal' => 'required|date',
            'tahun_hijriyah' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ], [
            'tahun.unique' => 'Data untuk tahun tersebut sudah ada.'
        ]);

        Lebaran::create([
            'tahun' => $request->tahun,
            'tanggal' => $request->tanggal,
            'tahun_hijriyah' => $request->tahun_hijriyah,
            'keterangan' => $request->keterangan
        ]);

        return redirect('/lebaran')->with('success', 'Data Lebaran berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getUpdate($id)
    {
        $lebaran = Lebaran::findOrFail($id);
        return view('update-lebaran', compact('lebaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'tanggal' => 'required|date',
            'tahun_hijriyah' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        $lebaran = Lebaran::findOrFail($id);
        
        // Check uniqueness if year changed
        if ($lebaran->tahun != $request->tahun) {
            $exists = Lebaran::where('tahun', $request->tahun)->where('is_deleted', 0)->exists();
            if ($exists) {
                return back()->withErrors(['tahun' => 'Data untuk tahun tersebut sudah ada.']);
            }
        }

        $lebaran->update([
            'tahun' => $request->tahun,
            'tanggal' => $request->tanggal,
            'tahun_hijriyah' => $request->tahun_hijriyah,
            'keterangan' => $request->keterangan
        ]);

        return redirect('/lebaran')->with('success', 'Data Lebaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lebaran = Lebaran::findOrFail($id);
        $lebaran->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user()->name ?? 'System',
            'deleted_at' => now()
        ]);

        return back()->with('success', 'Data Lebaran berhasil dihapus (soft delete)');
    }

    /**
     * Display trash
     */
    public function trash()
    {
        $data = Lebaran::where('is_deleted', 1)->orderBy('deleted_at', 'desc')->get();
        return view('lebaran-sampah', compact('data'));
    }

    /**
     * Restore deleted item
     */
    public function restore($id)
    {
        $lebaran = Lebaran::where('id', $id)->firstOrFail();
        
        // Check if year already exists in active data
        $exists = Lebaran::where('tahun', $lebaran->tahun)->where('is_deleted', 0)->exists();
        if ($exists) {
            return back()->with('error', 'Tidak dapat memulihkan. Data untuk tahun ' . $lebaran->tahun . ' sudah ada di daftar aktif.');
        }

        $lebaran->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);

        return redirect('/lebaran')->with('success', 'Data Lebaran berhasil dipulihkan');
    }
}
