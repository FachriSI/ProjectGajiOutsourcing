<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalCheckup;
use Illuminate\Support\Facades\Auth;

class MedicalCheckupController extends Controller
{
    public function index()
    {
        // Ambil data MCU terakhir (global setting terakhir)
        $sampleData = MedicalCheckup::where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->first();

        $currentBiaya = $sampleData ? $sampleData->biaya : 0;

        return view('medical-checkup', ['currentBiaya' => $currentBiaya]);
    }

    public function updateGlobal(Request $request)
    {
        $request->validate([
            'biaya' => 'required|numeric|min:0',
        ]);

        $biaya = $request->biaya;

        // Ambil semua karyawan aktif
        $activeKaryawan = \App\Models\Karyawan::where('status_aktif', 'Aktif')->get();

        $count = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($activeKaryawan, $biaya, &$count) {
            foreach ($activeKaryawan as $karyawan) {
                MedicalCheckup::updateOrCreate(
                    [
                        'karyawan_id' => $karyawan->karyawan_id,
                    ],
                    [
                        'biaya' => $biaya,
                        'is_deleted' => false
                    ]
                );

                $count++;
            }
        });

        return redirect('/medical-checkup')->with('success', "Berhasil memperbarui Biaya MCU untuk {$count} karyawan aktif.");
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
            'deleted_by' => auth()->user()->name ?? 'System',
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
