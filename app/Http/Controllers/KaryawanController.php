<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\AuditLog;
use App\Models\Paketkaryawan;
use App\Models\Riwayat_shift;
use App\Exports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Perusahaan;
use App\Models\UnitKerja;
use PDF;
use App\Models\MasterUkuran;


class KaryawanController extends Controller
{
    public function index()
    {
        // $data = DB::table('karyawan')
        //     ->join('perusahaan', 'karyawan.perusahaan_id', '=', 'perusahaan.perusahaan_id')
        //     ->select('karyawan.*', 'perusahaan.perusahaan')
        //     ->get();

        $data = Karyawan::with([
            'perusahaan',
            'pakaianTerakhir',
        ])->where('is_deleted', 0)->get();

        $paketList = DB::table('md_paket')->get();

        // Ambil paket aktif saat ini untuk tiap karyawan
        $paketKaryawan = DB::table('paket_karyawan as pk1')
            ->join('md_paket as paket', 'pk1.paket_id', '=', 'paket.paket_id')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM paket_karyawan
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('pk1.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('pk1.beg_date', '=', 'latest.max_date');
            })
            ->select('pk1.karyawan_id', 'paket.paket as nama_paket')
            ->get()
            ->keyBy('karyawan_id');


        // $harianShift = DB::table('karyawan as k')
        //     ->select('k.*', 'rs.kode_harianshift', 'hs.harianshift')
        //     ->leftJoin(DB::raw('riwayat_shift as rs'), function($join) {
        //         $join->on('rs.id', '=', DB::raw('(SELECT rs2.id FROM riwayat_shift as rs2 WHERE rs2.karyawan_id = k.karyawan_id ORDER BY rs2.beg_date DESC LIMIT 1)'));
        //     })
        //     ->leftJoin('harianshift as hs', 'hs.kode_harianshift', '=', 'rs.kode_harianshift')
        //     ->get();

        $harianShift = DB::table('riwayat_shift as rs1')
            ->join('md_harianshift', 'rs1.kode_harianshift', '=', 'md_harianshift.kode_harianshift')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM riwayat_shift
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('rs1.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('rs1.beg_date', '=', 'latest.max_date');
            })
            ->select('rs1.karyawan_id', 'md_harianshift.harianshift as harianshift')
            ->get()
            ->keyBy('karyawan_id');

        $jabatan = DB::table('riwayat_jabatan as rj1')
            ->join('md_jabatan', 'rj1.kode_jabatan', '=', 'md_jabatan.kode_jabatan')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM riwayat_jabatan
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('rj1.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('rj1.beg_date', '=', 'latest.max_date');
            })
            ->select('rj1.karyawan_id', 'md_jabatan.jabatan as jabatan')
            ->get()
            ->keyBy('karyawan_id');

        $jabatanList = DB::table('md_jabatan')->get();

        $area = DB::table('md_karyawan')
            ->where('md_karyawan.is_deleted', 0)
            ->leftJoin('md_area', 'md_karyawan.area_id', '=', 'md_area.area_id')
            ->select('md_karyawan.karyawan_id', 'md_area.area')
            ->get()
            ->keyBy('karyawan_id');

        $masterUkuran = MasterUkuran::all();

        // $pakaian = Karyawan::with('pakaianTerakhir')->get();
        //  dd($pakaian[0]->pakaianTerakhir->nilai_jatah);

        $hasDeleted = Karyawan::where('is_deleted', 1)->exists();

        // Fetch area list for modal dropdown
        $areaList = DB::table('md_area')->where('is_deleted', 0)->get();

        return view('karyawan', [
            'data' => $data,
            'paketList' => $paketList,
            'paketKaryawan' => $paketKaryawan,
            'harianShift' => $harianShift,
            'jabatan' => $jabatan,
            'jabatanList' => $jabatanList,

            'area' => $area,
            'areaList' => $areaList,
            'masterUkuran' => $masterUkuran,
            'hasDeleted' => $hasDeleted
            // 'pakaian'      => $pakaian
        ]);
    }


    public function exportExcel()
    {
        $data = Karyawan::with(['perusahaan'])->where('is_deleted', 0)->get();

        $paketKaryawan = DB::table('paket_karyawan as pk1')
            ->join('md_paket as paket', 'pk1.paket_id', '=', 'paket.paket_id')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM paket_karyawan
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('pk1.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('pk1.beg_date', '=', 'latest.max_date');
            })
            ->select('pk1.karyawan_id', 'paket.paket as nama_paket')
            ->get()
            ->keyBy('karyawan_id');

        $harianShift = DB::table('riwayat_shift as rs')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM riwayat_shift
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('rs.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('rs.beg_date', '=', 'latest.max_date');
            })
            ->join('md_harianshift as hs', 'rs.kode_harianshift', '=', 'hs.kode_harianshift')
            ->select('rs.karyawan_id', 'hs.harianshift')
            ->get()
            ->keyBy('karyawan_id');

        $jabatan = DB::table('riwayat_jabatan as rj')
            ->join(DB::raw('(
                SELECT karyawan_id, MAX(beg_date) as max_date
                FROM riwayat_jabatan
                GROUP BY karyawan_id
            ) as latest'), function ($join) {
                $join->on('rj.karyawan_id', '=', 'latest.karyawan_id')
                    ->on('rj.beg_date', '=', 'latest.max_date');
            })
            ->join('md_jabatan as j', 'rj.kode_jabatan', '=', 'j.kode_jabatan')
            ->select('rj.karyawan_id', 'j.jabatan')
            ->get()
            ->keyBy('karyawan_id');

        $area = DB::table('md_karyawan')
            ->where('md_karyawan.is_deleted', 0)
            ->leftJoin('md_area', 'md_karyawan.area_id', '=', 'md_area.area_id')
            ->select('md_karyawan.karyawan_id', 'md_area.area')
            ->get()
            ->keyBy('karyawan_id');

        return Excel::download(
            new KaryawanExport($data, $paketKaryawan, $jabatan, $harianShift, $area),
            'Data_Karyawan_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function cetakPdf($id)
    {
        $dataM = DB::table('md_karyawan')
            ->where('karyawan_id', '=', $id)
            ->first();

        if (!$dataM) {
            return redirect()->back()->with('error', 'Data Karyawan tidak ditemukan.');
        }

        $dataP = DB::table('md_perusahaan')->get();
        
        $pdf = PDF::loadView('pdf.detail_karyawan_pdf', [
            'dataM' => $dataM,
            'dataP' => $dataP
        ]);

        return $pdf->download('Detail_Karyawan_' . str_replace(' ', '_', $dataM->nama_tk) . '.pdf');
    }

    public function trash()
    {
        $data = Karyawan::with(['perusahaan'])->where('is_deleted', 1)->get();
        return view('karyawan-sampah', ['data' => $data]);
    }

    public function detail($id)
    {
        $dataM = DB::table('md_karyawan')
            ->where('karyawan_id', '=', $id)
            ->first();
        $dataP = DB::table('md_perusahaan')
            ->get();
        $dataU = Db::table('md_unit_kerja')
            ->get();

        $auditLogs = AuditLog::where('karyawan_id', $id)
            ->orderBy('waktu', 'desc')
            ->limit(20)
            ->get();

        return view('detail-karyawan', ['dataM' => $dataM, 'dataP' => $dataP, 'dataU' => $dataU, 'auditLogs' => $auditLogs]);
    }

    public function getTambah()
    {
        $dataP = DB::table('md_perusahaan')->get();
        $dataU = Db::table('md_unit_kerja')->get();
        $paketList = DB::table('md_paket')->where('is_deleted', 0)->get();

        // Calculate remaining quota for each package
        foreach ($paketList as $paket) {
            $currentActive = DB::table('paket_karyawan as pk')
                ->join('md_karyawan as k', 'pk.karyawan_id', '=', 'k.karyawan_id')
                ->where('pk.paket_id', $paket->paket_id)
                ->where('k.status_aktif', 'Aktif')
                ->distinct('pk.karyawan_id')
                ->count();

            $paket->sisa_kuota = max(0, $paket->kuota_paket - $currentActive);
        }

        // Filter packages that are full (sisa_kuota <= 0)
        $paketList = $paketList->filter(function ($paket) {
            return $paket->sisa_kuota > 0;
        });

        // Fetch existing unique fields for client-side validation
        $existingOsis = Karyawan::pluck('osis_id')->toArray();
        $existingKtp = Karyawan::pluck('ktp')->toArray();

        // Fetch lokasi data for dropdown
        $lokasiList = DB::table('md_lokasi')->where('is_deleted', 0)->get();

        // Fetch jabatan list for dropdown
        $jabatanList = DB::table('md_jabatan')->get();

        // Fetch area list for dropdown
        $areaList = DB::table('md_area')->where('is_deleted', 0)->get();

        return view('tambah-karyawan', [
            'dataP' => $dataP,
            'dataU' => $dataU,
            'paketList' => $paketList,
            'existingOsis' => $existingOsis,
            'existingKtp' => $existingKtp,
            'lokasiList' => $lokasiList,
            'jabatanList' => $jabatanList,
            'areaList' => $areaList
        ]);
    }

    public function setTambah(Request $request)
    {
        $request->validate([
            'osis_id' => 'required|numeric|digits:4|unique:md_karyawan,osis_id',
            'ktp' => 'required|numeric|digits:16|unique:md_karyawan,ktp',
            'nama' => 'required',
            'perusahaan' => 'required',
            'tanggal_lahir' => 'required|date|before:-18 years|after:-56 years',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'status' => 'required',
            'alamat' => 'required',
            'asal' => 'nullable',
            'paket_id' => 'required|exists:md_paket,paket_id',
            'kode_lokasi' => 'required|exists:md_lokasi,kode_lokasi',
            'kode_jabatan' => 'required|exists:md_jabatan,kode_jabatan',
            'area_id' => 'required|exists:md_area,area_id',
            'tipe_pekerjaan' => 'nullable|string|max:100',
        ], [
            'osis_id.unique' => 'OSIS ID sudah terdaftar.',
            'osis_id.digits' => 'OSIS ID harus 4 digit angka.',
            'ktp.unique' => 'Nomor KTP sudah terdaftar.',
            'ktp.digits' => 'Nomor KTP harus 16 digit angka.',
            'tanggal_lahir.before' => 'Usia minimal harus 18 tahun.',
            'tanggal_lahir.after' => 'Usia maksimal harus 56 tahun.',
        ]);

        // QUOTA CHECK
        $paket = DB::table('md_paket')->where('paket_id', $request->paket_id)->first();
        if ($paket) {
            $kuota = $paket->kuota_paket;

            // Count active employees in this package
            // Logic matches PaketController: count 'Aktif' status in paket_karyawan (latest beg_date per employee)
            // Simplified check: Count all 'Aktif' employees currently assigned to this package

            // Complex query to get current active count specifically for this package
            // Or simple approach: query paket_karyawan for this paket, get unique employees, check their status
            // Keeping it consistent with PaketController logic is best, but for now let's do a direct verification

            $currentActive = DB::table('paket_karyawan as pk')
                ->join('md_karyawan as k', 'pk.karyawan_id', '=', 'k.karyawan_id')
                ->where('pk.paket_id', $request->paket_id)
                ->where('k.status_aktif', 'Aktif')
                ->distinct('pk.karyawan_id') // Ensure unique employees
                ->count();

            if ($currentActive >= $kuota) {
                return redirect()->back()->with('error', 'Gagal menambah karyawan. Kuota Paket Penuh! (' . $currentActive . '/' . $kuota . ')')->withInput();
            }
        }

        $tanggal_lahir = Carbon::parse($request->tanggal_lahir);
        $tanggal_umur56 = $tanggal_lahir->copy()->addYears(56);
        $tanggal_pensiun = $tanggal_umur56->addMonth()->startOfMonth();
        $tahun_pensiun = $tanggal_umur56->format('Y-m-d');

        $karyawan = Karyawan::create([
            'osis_id' => $request->osis_id,
            'ktp' => $request->ktp,
            'nama_tk' => $request->nama,
            'perusahaan_id' => $request->perusahaan,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'status' => $request->status,
            'alamat' => $request->alamat,
            'asal' => $request->asal ?: null,
            'tipe_pekerjaan' => $request->tipe_pekerjaan,
            'tahun_pensiun' => $tahun_pensiun,
            'tanggal_pensiun' => $tanggal_pensiun,
            'status_aktif' => 'Aktif',
            'tanggal_bekerja' => $request->tanggal_bekerja ?? now()->format('Y-m-d'),
            'area_id' => $request->area_id,
        ]);

        // Assign to Paket
        PaketKaryawan::create([
            'paket_id' => $request->paket_id,
            'karyawan_id' => $karyawan->karyawan_id,
            'beg_date' => now()->format('Y-m-d'), // Start date in package
        ]);

        // Auto-assign default Harian/Shift (kode_harianshift = 1 untuk "Harian")
        Riwayat_shift::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'kode_harianshift' => 1, // Default ke "Harian"
            'beg_date' => now()->format('Y-m-d'),
        ]);

        // Auto-assign jabatan
        DB::table('riwayat_jabatan')->insert([
            'karyawan_id' => $karyawan->karyawan_id,
            'kode_jabatan' => $request->kode_jabatan,
            'beg_date' => now()->format('Y-m-d'),
        ]);

        // Auto-assign lokasi kerja
        DB::table('riwayat_lokasi')->insert([
            'karyawan_id' => $karyawan->karyawan_id,
            'kode_lokasi' => $request->kode_lokasi,
            'beg_date' => now()->format('Y-m-d'),
        ]);

        // Auto-assign default pakaian record
        $currentNilaiJatah = \App\Models\Pakaian::where('is_deleted', 0)
            ->orderBy('beg_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->value('nilai_jatah') ?? 0;

        DB::table('md_pakaian')->insert([
            'karyawan_id' => $karyawan->karyawan_id,
            'nilai_jatah' => $currentNilaiJatah,
            'ukuran_baju' => '-',
            'ukuran_celana' => '-',
            'beg_date' => now()->format('Y-m-d'),
        ]);

        // Auto-Calculate to ensure data appears immediately in Paket Detail
        try {
            $calculatorService = app(\App\Services\ContractCalculatorService::class);
            $calculatorService->calculateForPaket($request->paket_id, date('Y-m'));
        } catch (\Exception $e) {
            \Log::error('Auto-calculation failed after creating employee: ' . $e->getMessage());
        }

        // Audit Log
        AuditLog::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'aksi' => 'Dibuat',
            'diubah_oleh' => auth()->user()->username ?? 'System',
            'detail' => 'Karyawan baru ditambahkan: ' . $karyawan->nama_tk,
            'data_baru' => $karyawan->toArray(),
        ]);

        return redirect('/karyawan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataM = DB::table('md_karyawan')
            ->where('karyawan_id', '=', $id)
            ->first();
        $dataP = DB::table('md_perusahaan')
            ->get();
        $dataU = Db::table('md_unit_kerja')
            ->get();

        return view('update-karyawan', ['dataM' => $dataM, 'dataP' => $dataP, 'dataU' => $dataU]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'osis_id' => 'required',
            'ktp' => 'required',
            'nama' => 'required',
            'perusahaan' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
        ]);

        // dd($request);

        Karyawan::where('karyawan_id', $id)
            ->update([
                'osis_id' => $request->osis_id,
                'ktp' => $request->ktp,
                'nama_tk' => $request->nama,
                'perusahaan_id' => $request->perusahaan,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'status' => $request->status,
                'asal' => $request->asal,
                'tipe_pekerjaan' => $request->tipe_pekerjaan,
            ]);

        // Audit Log
        $karyawan = Karyawan::where('karyawan_id', $id)->first();
        AuditLog::create([
            'karyawan_id' => $id,
            'aksi' => 'Diubah',
            'diubah_oleh' => auth()->user()->username ?? 'System',
            'detail' => 'Data karyawan diperbarui: ' . ($karyawan->nama_tk ?? $id),
            'data_baru' => $karyawan ? $karyawan->toArray() : null,
        ]);

        return redirect('/karyawan')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        // Audit Log
        $karyawan = Karyawan::where('karyawan_id', $id)->first();
        AuditLog::create([
            'karyawan_id' => $id,
            'aksi' => 'Dihapus',
            'diubah_oleh' => auth()->user()->username ?? 'System',
            'detail' => 'Karyawan dihapus (soft delete): ' . ($karyawan->nama_tk ?? $id),
            'data_lama' => $karyawan ? $karyawan->toArray() : null,
        ]);

        Karyawan::where('karyawan_id', $id)->update([
            'is_deleted' => 1,
            'deleted_by' => auth()->user() ? auth()->user()->username : 'System',
            'deleted_at' => now()
        ]);
        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function restore($id)
    {
        Karyawan::where('karyawan_id', $id)->update([
            'is_deleted' => 0,
            'deleted_by' => null,
            'deleted_at' => null
        ]);
        return back()->with('success', 'Data berhasil dipulihkan!');
    }

    public function simpanMutasi(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required|exists:md_karyawan,karyawan_id',
            'paket_id' => 'required|exists:md_paket,paket_id',
            'beg_date' => 'required',
        ]);

        // Simpan mutasi
        DB::table('paket_karyawan')->insert([
            'karyawan_id' => $request->karyawan_id,
            'paket_id' => $request->paket_id,
            'beg_date' => $request->beg_date,
        ]);

        // Auto-Calculate for the NEW Paket
        try {
            $calculatorService = app(\App\Services\ContractCalculatorService::class);
            
            // 1. Calculate for the Mutation Period
            $mutationPeriode = \Carbon\Carbon::parse($request->beg_date)->format('Y-m');
            $calculatorService->calculateForPaket($request->paket_id, $mutationPeriode);

            // 2. If Mutation Period is different from Current Period, calculate for Current as well
            // This ensures Dashboard (Current Month) and Detail Page (Mutation Month) are both up to date
            $currentPeriode = date('Y-m');
            if ($mutationPeriode !== $currentPeriode) {
                $calculatorService->calculateForPaket($request->paket_id, $currentPeriode);
            }

        } catch (\Exception $e) {
            \Log::error('Auto-calculation failed after mutation: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Mutasi paket berhasil disimpan, namun pembaharuan data kontrak gagal. Silakan buka detail paket untuk memuat ulang data. Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Mutasi paket berhasil disimpan.');
    }

    public function simpanShift(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required',
            'kode_harianshift' => 'required',
            'beg_date' => 'required',
        ]);


        // Simpan mutasi dengan tanggal sekarang
        DB::table('riwayat_shift')->insert([
            'karyawan_id' => $request->karyawan_id,
            'kode_harianshift' => $request->kode_harianshift,
            'beg_date' => $request->beg_date,
        ]);

        // Auto-Calculate for the Employee's Active Paket
        try {
            $activePaket = DB::table('paket_karyawan')
                ->where('karyawan_id', $request->karyawan_id)
                ->orderByDesc('beg_date')
                ->first();

            if ($activePaket) {
                $calculatorService = app(\App\Services\ContractCalculatorService::class);
                
                // Calculate for the Change Period
                $changePeriode = \Carbon\Carbon::parse($request->beg_date)->format('Y-m');
                $calculatorService->calculateForPaket($activePaket->paket_id, $changePeriode);

                // If Change Period is different from Current Period, calculate for Current as well
                $currentPeriode = date('Y-m');
                if ($changePeriode !== $currentPeriode) {
                    $calculatorService->calculateForPaket($activePaket->paket_id, $currentPeriode);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Auto-calculation failed after shift change: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Pergantian Harian/Shift berhasil disimpan, namun pembaharuan data kontrak gagal. Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pergantian Harian/Shift berhasil disimpan.');
    }

    public function simpanPromosi(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required|exists:md_karyawan,karyawan_id',
            'kode_jabatan' => 'required|exists:md_jabatan,kode_jabatan',
            'beg_date' => 'required',
        ]);

        // Simpan mutasi dengan tanggal sekarang
        DB::table('riwayat_jabatan')->insert([
            'karyawan_id' => $request->karyawan_id,
            'kode_jabatan' => $request->kode_jabatan,
            'beg_date' => $request->beg_date,
        ]);

        // Auto-Calculate for the Employee's Active Paket
        try {
            $activePaket = DB::table('paket_karyawan')
                ->where('karyawan_id', $request->karyawan_id)
                ->orderByDesc('beg_date')
                ->first();

            if ($activePaket) {
                $calculatorService = app(\App\Services\ContractCalculatorService::class);
                
                // Calculate for the Change Period
                $changePeriode = \Carbon\Carbon::parse($request->beg_date)->format('Y-m');
                $calculatorService->calculateForPaket($activePaket->paket_id, $changePeriode);

                // If Change Period is different from Current Period, calculate for Current as well
                $currentPeriode = date('Y-m');
                if ($changePeriode !== $currentPeriode) {
                    $calculatorService->calculateForPaket($activePaket->paket_id, $currentPeriode);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Auto-calculation failed after promotion: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Promosi jabatan berhasil disimpan, namun pembaharuan data kontrak gagal. Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Promosi jabatan berhasil disimpan.');
    }

    public function simpanArea(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required',
            'area_id' => 'required',
        ]);


        Karyawan::where('karyawan_id', $request->karyawan_id)
            ->update([
                'area_id' => $request->area_id,
            ]);

        // Auto-Calculate for the Employee's Active Paket
        try {
            $activePaket = DB::table('paket_karyawan')
                ->where('karyawan_id', $request->karyawan_id)
                ->orderByDesc('beg_date')
                ->first();

            if ($activePaket) {
                $calculatorService = app(\App\Services\ContractCalculatorService::class);
                
                // For Area change (which doesn't have beg_date input, assume immediate/current effect)
                // or we should calculate for current month.
                $currentPeriode = date('Y-m');
                $calculatorService->calculateForPaket($activePaket->paket_id, $currentPeriode);
            }
        } catch (\Exception $e) {
            \Log::error('Auto-calculation failed after area change: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Pergantian Area berhasil disimpan, namun pembaharuan data kontrak gagal. Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pergantian Area berhasil disimpan.');
    }

    public function simpanPakaian(Request $request)
    {
        // Validasi input
        $request->validate([
            'karyawan_id' => 'required',
            'ukuran_baju' => 'required',
            'ukuran_celana' => 'required',
            'beg_date' => 'required',
        ]);

        // Ambil nilai jatah global saat ini (dari data terakhir yang diinput)
        $currentNilai = \App\Models\Pakaian::where('is_deleted', 0)
            ->orderBy('beg_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->value('nilai_jatah') ?? 0;

        DB::table('md_pakaian')->insert([
            'karyawan_id' => $request->karyawan_id,
            'nilai_jatah' => $currentNilai,
            'ukuran_baju' => $request->ukuran_baju,
            'ukuran_celana' => $request->ukuran_celana,
            'beg_date' => $request->beg_date,
        ]);

        return redirect()->back()->with('success', 'Pergantian Pakaian berhasil disimpan.');
    }


}
