<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalCheckup;
use Illuminate\Support\Facades\Auth;

class MedicalCheckupController extends Controller
{
    public function index()
    {
        $data = MedicalCheckup::where('is_deleted', false)->get();
        $hasDeleted = MedicalCheckup::where('is_deleted', true)->exists();

        return view('medical-checkup', compact('data', 'hasDeleted'));
    }

    public function getTambah()
    {
        return view('tambah-medical-checkup'); // Or use modal
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'biaya' => 'required|numeric'
        ]);

        MedicalCheckup::create([
            'biaya' => $request->biaya
        ]);

        return redirect()->route('medical-checkup')->with('success', 'Data Medical Checkup berhasil ditambahkan');
    }

    public function getUpdate($id)
    {
        $data = MedicalCheckup::findOrFail($id);
        return response()->json($data);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'biaya' => 'required|numeric'
        ]);

        $data = MedicalCheckup::findOrFail($id);
        $data->update([
            'biaya' => $request->biaya
        ]);

        return redirect()->route('medical-checkup')->with('success', 'Data Medical Checkup berhasil diperbarui');
    }

    public function destroy($id)
    {
        $data = MedicalCheckup::findOrFail($id);
        $data->update([
            'is_deleted' => true,
            'deleted_by' => auth()->user()->id ?? 'System', // Assuming auth is used or fallback
            'deleted_at' => now()
        ]);

        return redirect()->route('medical-checkup')->with('success', 'Data Medical Checkup berhasil dihapus');
    }
    
    public function trash()
    {
        $data = MedicalCheckup::where('is_deleted', true)->get();
        return view('medical-checkup-trash', compact('data')); // Needs trash view
    }

    public function restore($id)
    {
        $data = MedicalCheckup::findOrFail($id);
        $data->update([
            'is_deleted' => false,
            'deleted_by' => null,
            'deleted_at' => null
        ]);

        return redirect()->route('medical-checkup')->with('success', 'Data Medical Checkup berhasil dipulihkan');
    }
}
