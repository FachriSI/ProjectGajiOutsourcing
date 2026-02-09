<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lebaran;
use Illuminate\Support\Facades\DB;

class LebaranController extends Controller
{
    public function index()
    {
        $data = Lebaran::where('is_deleted', false)->orderBy('tahun', 'desc')->get();
        $hasDeleted = Lebaran::where('is_deleted', true)->exists();

        return view('lebaran', compact('data', 'hasDeleted'));
    }

    // Manual creation disabled as data is seeded/system-generated
    public function getTambah()
    {
        return redirect('/lebaran')->with('error', 'Data Lebaran ditambahkan secara otomatis oleh sistem.');
    }

    public function setTambah(Request $request)
    {
        return redirect('/lebaran')->with('error', 'Data Lebaran ditambahkan secara otomatis oleh sistem.');
    }

    public function getUpdate($id)
    {
        $data = Lebaran::findOrFail($id);
        return view('update-lebaran', compact('data'));
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|numeric',
            'tanggal' => 'required|date'
        ]);

        $lebaran = Lebaran::findOrFail($id);
        
        // Check uniqueness if year changed
        if ($lebaran->tahun != $request->tahun) {
            $exists = Lebaran::where('tahun', $request->tahun)
                ->where('is_deleted', 0)
                ->where('id', '!=', $id)
                ->exists();
            if ($exists) {
                return back()->with('error', 'Data untuk tahun tersebut sudah ada.');
            }
        }

        $lebaran->update([
            'tahun' => $request->tahun,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan
        ]);

        return redirect('/lebaran')->with('success', 'Data Lebaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $data = Lebaran::findOrFail($id);
        $data->update([
            'is_deleted' => true,
            'deleted_by' => session('user_name') ?? 'System',
            'deleted_at' => now()
        ]);

        return redirect('/lebaran')->with('success', 'Data Lebaran berhasil dihapus');
    }

    public function trash()
    {
        $data = Lebaran::where('is_deleted', true)->orderBy('deleted_at', 'desc')->get();
        return view('lebaran-sampah', compact('data'));
    }

    public function restore($id)
    {
        $data = Lebaran::findOrFail($id);
        $data->update([
            'is_deleted' => false,
            'deleted_by' => null,
            'deleted_at' => null
        ]);

        return redirect('/lebaran')->with('success', 'Data Lebaran berhasil dipulihkan');
    }
}
