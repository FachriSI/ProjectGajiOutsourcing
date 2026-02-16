<?php

namespace App\Http\Controllers;
use App\Imports\KaryawanImport;
use App\Imports\MutasiImport;
// use App\Imports\KaryawanBaruImport; // Not used anymore
use App\Imports\PerusahaanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new KaryawanImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;

            if ($berhasil > 0) {
                return redirect()->back()->with('success', "$berhasil data berhasil diimport. $gagal baris gagal diproses.");
            } else {
                return redirect()->back()->with('error', "Semua baris gagal diproses. Tidak ada data yang diimport.");
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importKaryawanBaru(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\KaryawanBaruImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;
            $logs = $import->getLog();

            if ($berhasil > 0) {
                return view('import_result', [
                    'successMessage' => "$berhasil data karyawan baru berhasil diimport. $gagal baris gagal diproses.",
                    'logs' => $logs
                ]);
            } else {
                return view('import_result', [
                    'errorMessage' => "Semua baris gagal diproses. Tidak ada data yang diimport.",
                    'logs' => $logs
                ]);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    // public function importMutasi(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls,csv'
    //     ]);

    //     $import = new MutasiImport;

    //     try {
    //         Excel::import($import, $request->file('file'));

    //         $total = $import->getTotal();
    //         $gagal = $import->getGagal();
    //         $berhasil = $total - $gagal;

    //         if ($berhasil > 0) {
    //             return redirect()->back()->with('success', "$berhasil data berhasil diimport. $gagal baris gagal diproses.");
    //         } else {
    //             return redirect()->back()->with('error', "Semua baris gagal diproses. Tidak ada data yang diimport.");
    //         }

    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
    //     }
    // }

    public function importMutasi(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new MutasiImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;
            $logs = $import->getLog(); // ambil log dari importer

            if ($berhasil > 0) {
                return view('import_result', [
                    'successMessage' => "$berhasil data berhasil diimport. $gagal baris gagal diproses.",
                    'logs' => $logs
                ]);
            } else {
                return view('import_result', [
                    'errorMessage' => "Semua baris gagal diproses. Tidak ada data yang diimport.",
                    'logs' => $logs
                ]);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }

    }

    public function importTemplateBaru(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new PerusahaanImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;
            $logs = $import->getLog();

            if ($berhasil > 0) {
                // Format log message simple for now
                $msg = "$berhasil data berhasil diupdate/import.";
                if ($gagal > 0)
                    $msg .= " $gagal gagal.";
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->with('error', "Gagal memproses file. " . implode(', ', $logs));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function importPakaian(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\PakaianImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;
            $logs = $import->getLog();

            if ($berhasil > 0) {
                return view('import_result', [
                    'successMessage' => "$berhasil data berhasil diimport. $gagal baris gagal diproses.",
                    'logs' => $logs
                ]);
            } else {
                return view('import_result', [
                    'errorMessage' => "Semua baris gagal diproses. Tidak ada data yang diimport.",
                    'logs' => $logs
                ]);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importPaket(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\PaketImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;
            $logs = $import->getLog();

            if ($berhasil > 0) {
                $msg = "$berhasil data paket berhasil diimport.";
                if ($gagal > 0)
                    $msg .= " $gagal gagal.";
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->with('error', "Gagal memproses file. " . implode(', ', $logs));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importLokasi(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\LokasiImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;

            if ($berhasil > 0) {
                $msg = "$berhasil data lokasi berhasil diimport.";
                if ($gagal > 0)
                    $msg .= " $gagal gagal.";
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->with('error', "Gagal memproses file. Pastikan format sesuai template.");
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importDepartemen(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\DepartemenImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;

            if ($berhasil > 0) {
                $msg = "$berhasil data departemen berhasil diimport.";
                if ($gagal > 0)
                    $msg .= " $gagal gagal.";
                return redirect()->back()->with('success', $msg);
            } else {
                $logs = $import->getLog();
                return redirect()->back()->with('error', "Gagal memproses file. " . implode(', ', $logs));
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importFungsi(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\FungsiImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;

            if ($berhasil > 0) {
                $msg = "$berhasil data fungsi berhasil diimport.";
                if ($gagal > 0)
                    $msg .= " $gagal gagal.";
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->with('error', "Gagal memproses file. Pastikan format sesuai template.");
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function importUnitKerja(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\UnitKerjaImport;

        try {
            Excel::import($import, $request->file('file'));

            $total = $import->getTotal();
            $gagal = $import->getGagal();
            $berhasil = $total - $gagal;

            if ($berhasil > 0) {
                $msg = "$berhasil data unit kerja berhasil diimport.";
                if ($gagal > 0)
                    $msg .= " $gagal gagal.";
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->with('error', "Gagal memproses file. Pastikan format sesuai template.");
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
