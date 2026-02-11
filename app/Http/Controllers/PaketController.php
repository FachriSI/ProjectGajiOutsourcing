<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\Riwayat_fungsi;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_penyesuaian;
use App\Models\Riwayat_lokasi;
use App\Models\PaketKaryawan;
use App\Models\Paket;
use App\Models\Ump;
use App\Models\Kuotajam;
use App\Models\Masakerja;
use App\Models\TagihanCetak;
use Illuminate\Http\Request;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaketController extends Controller
{

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'paket_id_add' => 'required|exists:md_paket,paket_id',
            'karyawan_id' => 'required|exists:md_karyawan,karyawan_id'
        ]);

        PaketKaryawan::create([
            'paket_id' => $request->paket_id_add,
            'karyawan_id' => $request->karyawan_id,
            'beg_date' => now()
        ]);

        // Auto-Calculate to ensure data appears immediately
        try {
            $calculatorService = app(\App\Services\ContractCalculatorService::class);
            $calculatorService->calculateForPaket($request->paket_id_add, date('Y-m'));
        } catch (\Exception $e) {
            \Log::error('Auto-calculation failed after adding employee: ' . $e->getMessage());
        }

        return redirect('/paket/' . $request->paket_id_add)->with('success', 'Karyawan berhasil ditambahkan ke paket');
    }

    public function index()
    {
        // Initialize totals
        $total_jml_fix_cost = 0;
        $total_seluruh_variabel = 0;
        $total_kontrak_all = 0;
        $total_kontrak_tahunan_all = 0;
        $total_thr_bln = 0;
        $total_thr_thn = 0;
        $total_pakaian_all = 0;
        $total_active_employees_all = 0;
        $total_mcu_all = 0;

        $currentYear = date('Y');
        $currentMonth = date('Y-m');
        $umpSumbar = Ump::where('kode_lokasi', '12')->where('tahun', $currentYear)->value('ump');

        $allPakets = Paket::with(['paketKaryawan.karyawan.perusahaan'])->get();
        
        $calculatorService = app(\App\Services\ContractCalculatorService::class);

        // Collection to store all active employee IDs found in contracts (for bulk querying Pakaian)
        $activeEmployeeIds = collect();

        foreach ($allPakets as $paket) {
            // Get or Calculate NilaiKontrak for CURRENT month
            $nilaiKontrak = \App\Models\NilaiKontrak::where('paket_id', $paket->paket_id)
                ->where('periode', $currentMonth)
                ->first();

            // Auto-calculate if missing (Consistency with Detail View)
            if (!$nilaiKontrak) {
                try {
                    $calculatorService->calculateForPaket($paket->paket_id, $currentMonth);
                    // Re-fetch
                    $nilaiKontrak = \App\Models\NilaiKontrak::where('paket_id', $paket->paket_id)
                        ->where('periode', $currentMonth)
                        ->first();
                } catch (\Exception $e) {
                    \Log::error("Auto-calc failed for Index Paket {$paket->paket_id}: " . $e->getMessage());
                    continue; // Skip meaningful data for this packet if calc fails
                }
            }

            if ($nilaiKontrak) {
                // Aggregate Totals from Calculated Data
                $breakdown = $nilaiKontrak->breakdown_json;
                
                // Total Kontrak (Monthly) - Directly from stored value
                $total_kontrak_all += $nilaiKontrak->total_nilai_kontrak;
                
                $karyawanData = $breakdown['karyawan'] ?? [];
                
                foreach ($karyawanData as $k) {
                    $total_active_employees_all++;
                    
                    // Collect ID for Pakaian query
                    if (isset($k['karyawan_id'])) {
                        $activeEmployeeIds->push($k['karyawan_id']);
                    }
                    
                    // Fix Cost: 'fix_cost' key from service
                    $total_jml_fix_cost += ($k['fix_cost'] ?? 0);
                    
                    // Variable Cost: 'lembur' key from service (which represents Total Variabel)
                    $total_seluruh_variabel += ($k['lembur'] ?? 0);
                    
                    // THR Calculation: Upah Pokok + Tunjangan Tetap + Tunjangan Lokasi
                    // Note: 'tj_tetap' includes Jabatan + Masa Kerja
                    $upah = $k['upah_pokok'] ?? 0;
                    $tjTetap = $k['tj_tetap'] ?? 0;
                    $tjLokasi = $k['tj_lokasi'] ?? 0;
                    
                    $thr_amount = $upah + $tjTetap + $tjLokasi;
                    $total_thr_thn += $thr_amount;
                }
            }
        }

        // Yearly Totals based on Monthly Sums
        $total_kontrak_tahunan_all = $total_kontrak_all * 12; // Total Contract/Year
        
        // THR Monthly Average
        $total_thr_bln = $total_thr_thn / 12;

        // Pakaian Calculation: Sum 'nilai_jatah' for all unique active employees found
        if ($activeEmployeeIds->isNotEmpty()) {
            $total_pakaian_all = \App\Models\Pakaian::whereIn('karyawan_id', $activeEmployeeIds->unique())
                ->where('is_deleted', 0)
                ->sum('nilai_jatah');
        }

        // MCU Calculation: Cost * Number of Active Employees
        $mcu = \App\Models\MedicalCheckup::where('is_deleted', 0)->latest()->first();
        $mcu_cost = $mcu->biaya ?? 0;
        $total_mcu_all = $total_active_employees_all * $mcu_cost;

        // Filter Karyawan: Only those who are NOT in any PaketKaryawan record (Strictly 'Free')
        $assignedKaryawanIds = PaketKaryawan::pluck('karyawan_id')->unique();
        $availableKaryawan = Karyawan::whereNotIn('karyawan_id', $assignedKaryawanIds)
            ->where(function ($q) {
                $q->where('status_aktif', 'Aktif')
                    ->orWhereNull('status_aktif')
                    ->orWhere('status_aktif', '');
            })
            ->orderBy('nama_tk')
            ->get();

        $data = $allPakets;
        $hasDeleted = \App\Models\Paket::where('is_deleted', 1)->exists();

        return view('paket', compact(
            'data',
            'hasDeleted',
            'total_jml_fix_cost',
            'total_seluruh_variabel',
            'total_kontrak_all',
            'total_kontrak_tahunan_all',
            'total_thr_bln',
            'total_thr_thn',
            'total_pakaian_all',
            'availableKaryawan',
            'total_mcu_all'
        ));
    }

    public function show($id)
    {
        // Old Index Logic: Detail for a specific package
        $paketId = $id;
        $data = [];
        $errorLog = [];
        $totalExpected = 0;
        $totalActual = 0;

        $selectedPeriode = request('periode');
        
        // Determine correct period
        // If not specified, default to current month Y-m
        if (!$selectedPeriode) {
             $selectedPeriode = date('Y-m');
        }

        // Parse year from selected period
        $currentYear = \Carbon\Carbon::parse($selectedPeriode)->year;

        // Check for existing Calculation (NilaiKontrak)
        $nilaiKontrak = \App\Models\NilaiKontrak::where('paket_id', $paketId)
            ->where('periode', $selectedPeriode)
            ->first();

        // Data for Chart: Contract History
        $contractHistory = \App\Models\NilaiKontrak::where('paket_id', $paketId)
            ->orderBy('periode', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => \Carbon\Carbon::parse($item->periode)->format('F Y'),
                    'total' => $item->total_nilai_kontrak
                ];
            });

        // Filter: Only for this package
        $paketList = Paket::withoutGlobalScopes()->where('paket_id', $paketId)->with(['paketKaryawan.karyawan.perusahaan'])->get();

        if ($paketList->isEmpty()) {
            return redirect('/paket')->with('error', 'Paket tidak ditemukan');
        }

        // If NO calculation exists for this period, AUTO-CALCULATE to ensure data consistency
        if (!$nilaiKontrak) {
            try {
                // Auto-calculate on the fly
                $calculatorService = app(\App\Services\ContractCalculatorService::class);
                $calculatorService->calculateForPaket($paketId, $selectedPeriode);

                // Re-fetch the newly created calculation
                $nilaiKontrak = \App\Models\NilaiKontrak::where('paket_id', $paketId)
                    ->where('periode', $selectedPeriode)
                    ->first();
            } catch (\Exception $e) {
                // Determine if failure is due to empty package or real error
                // If package has no employees, it might return empty/null or throw error depending on service.
                // Fallback to empty view if calculation fails or still no result.
                \Log::error("Auto-calc failed for Paket {$paketId}: " . $e->getMessage());
                
                $total_mcu_paket = 0;
                return view('paket_detail', compact('data', 'paketList', 'contractHistory', 'selectedPeriode', 'total_mcu_paket'))
                    ->with('warning', 'Data belum tersedia dan perhitungan otomatis gagal: ' . $e->getMessage());
            }
        }
        
        // Double check if re-fetch worked
        if (!$nilaiKontrak) {
             $total_mcu_paket = 0;
             return view('paket_detail', compact('data', 'paketList', 'contractHistory', 'selectedPeriode', 'total_mcu_paket'));
        }

        // If Calculation EXISTS, use the IDs from the calculation to ensure consistency
        $calculatedKaryawanIds = collect($nilaiKontrak->breakdown_json['karyawan'] ?? [])->pluck('karyawan_id')->toArray();
        
        // Use UMP stored in calculation to maintain history accuracy
        $umpSumbar = $nilaiKontrak->ump_sumbar;

        // Ambil semua data di awal untuk efisiensi
        // Only fetch for IDs that were in the calculation
        $kuotaJamAll = Kuotajam::whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->keyBy('karyawan_id');
        $jabatanAll = Riwayat_jabatan::with('jabatan')->whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->groupBy('karyawan_id');
        $shiftAll = Riwayat_shift::with('harianshift')->whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->groupBy('karyawan_id');
        $resikoAll = Riwayat_resiko::with('resiko')->whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->groupBy('karyawan_id');
        $fungsiAll = Riwayat_fungsi::with('fungsi')->whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->groupBy('karyawan_id');
        
        // For Lokasi, we try to match historical year, but data might be scant. 
        // Using 'latest' logic similar to original but scoped to IDs.
        $lokasiAll = Riwayat_lokasi::with([
            'lokasi.ump' => function ($query) use ($currentYear) {
                $query->where('tahun', $currentYear);
            }
        ])->whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->groupBy('karyawan_id');
        
        $masakerjaAll = Masakerja::whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->keyBy('karyawan_id');
        $pakaianAll = \App\Models\Pakaian::whereIn('karyawan_id', $calculatedKaryawanIds)->latest('beg_date')->get()->unique('karyawan_id')->keyBy('karyawan_id');
        $mcu = \App\Models\MedicalCheckup::where('is_deleted', 0)->latest()->first();

        foreach ($paketList as $paket) {
            $kuota = (int) $paket->kuota_paket;
            $totalExpected += $kuota;

            // Instead of filtering active/active logic again, 
            // We iterating ONLY the employees that were explicitly saved in the Contract Calculation.
            // This ensures "What you see is what you calculated".
            
            // Need to fetch Karyawan models for these IDs to get names/dates etc
            $terpilih = Karyawan::whereIn('karyawan_id', $calculatedKaryawanIds)
                        ->with('perusahaan')
                        // We might want to preserve the order or just standard sort
                        ->orderBy('nama_tk')
                        ->get();

            $totalActual += $terpilih->count();

            foreach ($terpilih as $karyawan) {
                $id = $karyawan->karyawan_id;

                $jabatan = optional($jabatanAll[$id] ?? collect())->first();
                $shift = optional($shiftAll[$id] ?? collect())->first();
                $resiko = optional($resikoAll[$id] ?? collect())->first();
                $lokasi = optional($lokasiAll[$id] ?? collect())->first();
                $fungsi = optional($fungsiAll[$id] ?? collect())->first();
                $kuota_jam = $kuotaJamAll[$id] ?? null;
                $masakerja = $masakerjaAll[$id] ?? null;
                $pakaian_data = $pakaianAll[$id] ?? null;

                $data[] = (object) array_merge(
                    // Default values for potentially missing relations
                    [
                        'kode_lokasi' => 12, 
                        'kode_resiko' => 2,
                        'lokasi' => [],
                        'harianshift' => [],
                        'resiko' => [],
                        'kuota' => 0
                    ], 
                    $pakaian_data?->toArray() ?? [], // Merge Pakaian fields (nilai_jatah)
                    $kuota_jam?->toArray() ?? [],
                    $karyawan->toArray(),
                    ['perusahaan' => $karyawan->perusahaan->perusahaan ?? null],
                    ['aktif_mulai' => \Carbon\Carbon::parse($karyawan->tanggal_bekerja)->translatedFormat('F Y')],
                    $jabatan?->toArray() ?? [],
                    [
                        'jabatan' => optional($jabatan?->jabatan)->jabatan ?? null,
                        'tunjangan_jabatan' => optional($jabatan?->jabatan)->tunjangan_jabatan ?? 0,
                    ],
                    ['fungsi' => $fungsi?->fungsi?->fungsi ?? null],
                    $shift?->toArray() ?? [],
                    $resiko?->toArray() ?? [],
                    $lokasi?->toArray() ?? [],
                    ['ump_sumbar' => $umpSumbar], // Use UMP from Contract History
                    $paket->toArray(),
                    $masakerja?->toArray() ?? [],
                    ['mcu' => $mcu->biaya ?? 0]
                );
            }
        }


        // MCU Calculation for this package
        $mcu = \App\Models\MedicalCheckup::where('is_deleted', 0)->latest()->first();
        $mcu_cost = $mcu->biaya ?? 0;
        $total_mcu_paket = $totalActual * $mcu_cost; 
        
        return view('paket_detail', compact('data', 'paketList', 'contractHistory', 'selectedPeriode', 'total_mcu_paket'));
    }

    public function getTambah()
    {
        $unit = DB::table('md_unit_kerja')
            ->select('md_unit_kerja.*')
            ->get();
        // Get existing package names for client-side validation
        $existingPakets = \App\Models\Paket::pluck('paket')->toArray();
        return view('tambah-paket', ['unit' => $unit, 'existingPakets' => $existingPakets]);
    }

    public function setTambah(Request $request)
    {
        // Handle paket naming with prefix
        $paketName = $request->paket;
        if ($request->has('paket_suffix')) {
            $paketName = 'Paket ' . $request->paket_suffix;
        }

        // Merge the constructed name back into request for validation if needed, 
        // or just validate manually. 
        // We also need to ensure quota is > 0.

        $request->merge(['full_paket_name' => $paketName]);

        $request->validate([
            'paket_suffix' => 'required|integer|min:1',
            'full_paket_name' => 'unique:md_paket,paket', // Check if "Paket X" exists
            'kuota_paket' => 'required|integer|min:1',
            'unit_kerja' => 'required'
        ], [
            'full_paket_name.unique' => 'Nama paket sudah ada.',
            'kuota_paket.min' => 'Kuota paket harus lebih dari 0.'
        ]);

        $paket = Paket::create([
            'paket' => $paketName,
            'kuota_paket' => $request->kuota_paket,
            'unit_id' => $request->unit_kerja
        ]);

        return redirect('/paket/' . $paket->paket_id)->with('success', 'Data Berhasil Tersimpan');
    }

    public function getUpdate($id)
    {
        $dataP = DB::table('md_paket')
            ->where('paket_id', '=', $id)
            ->first();
        $unit = DB::table('md_unit_kerja')
            ->select('md_unit_kerja.*')
            ->get();

        return view('update-paket', ['dataP' => $dataP, 'unit' => $unit]);
    }

    public function setUpdate(Request $request, $id)
    {
        $request->validate([
            'paket' => 'required',
            'kuota_paket' => 'required'
        ]);


        Paket::where('paket_id', $id)
            ->update([
                'paket' => $request->paket,
                'kuota_paket' => $request->kuota_paket
            ]);

        return redirect('/paket')->with('success', 'Data Berhasil Tersimpan');
    }

    public function destroy($id)
    {
        $hapus = Paket::findorfail($id);
        $hapus->is_deleted = 1;
        $hapus->deleted_by = session('user_name');
        $hapus->deleted_at = now();
        $hapus->save();

        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function trash()
    {
        $data = DB::table('md_paket')
            ->where('md_paket.is_deleted', 1)
            ->join('md_unit_kerja', 'md_unit_kerja.unit_id', '=', 'md_paket.unit_id')
            ->select('md_paket.*', 'md_unit_kerja.*')
            ->get();
        return view('paket-sampah', ['data' => $data]);
    }

    public function restore($id)
    {
        $data = Paket::findorfail($id);
        $data->is_deleted = 0;
        $data->deleted_by = null;
        $data->deleted_at = null;
        $data->save();

        return redirect('/paket')->with('success', 'Data berhasil dipulihkan!');
    }

    /**
     * Calculate BOQ (Bill of Quantity) for a specific paket
     * Menghitung total tagihan dengan breakdown Pengawas dan Pelaksana
     */
    public function calculateBOQ($paketId)
    {
        $currentYear = date('Y');

        // Use logic from ContractCalculatorService to ensure consistency
        $calculatorService = app(\App\Services\ContractCalculatorService::class);
        $periode = now()->format('Y-m');

        // Calculate (will use updated logic from Service)
        $nilaiKontrak = $calculatorService->calculateForPaket($paketId, $periode);

        // Extract breakdown data
        $breakdown = $nilaiKontrak->breakdown_json;
        $pengawas = $breakdown['pengawas'];
        $pelaksana = $breakdown['pelaksana'];
        $karyawanData = $breakdown['karyawan'];

        $paket = Paket::with(['paketKaryawan.karyawan.perusahaan', 'unitKerja'])->findOrFail($paketId);

        // Find vendor (first found among employees)
        $vendor = null;
        foreach ($karyawanData as $kd) {
            // Note: karyawanData from service doesn't have perusahaan name directly, 
            // but we can fetch it or just re-loop from packet if strictly needed.
            // For safety/speed, let's just grab it from the packet relation we loaded above.
            // Or better, iterate unique employees from packet to find vendor.
            // Since service logic for "terpilih" matches fairly well, we can trust the counts.
        }

        // Re-determine vendor from the packet employees for display consistency
        foreach ($paket->paketKaryawan as $pk) {
            if ($pk->karyawan && $pk->karyawan->perusahaan) {
                $vendor = $pk->karyawan->perusahaan->perusahaan;
                break;
            }
        }

        $totalBOQ = $pengawas['total'] + $pelaksana['total'];
        $totalBulanan = $totalBOQ;
        $totalTahunan = $totalBOQ * 12;

        return [
            'paket' => $paket,
            'vendor' => $vendor,
            'jumlah_pekerja' => $pengawas['count'] + $pelaksana['count'],
            'pengawas' => $pengawas,
            'pelaksana' => $pelaksana,
            'karyawan' => $karyawanData,
            'total_bulanan' => $totalBulanan,
            'total_tahunan' => $totalTahunan,
            'total_boq' => $totalBOQ,
            'ump_sumbar' => $nilaiKontrak->ump_sumbar,
            'tahun' => $currentYear
        ];
    }

    /**
     * Lihat Tagihan - Preview BOQ sebelum download PDF
     * UPDATED: Menyimpan data ke nilai_kontrak untuk tracking
     */
    public function lihatTagihan(Request $request, $id)
    {
        // Calculate BOQ dan simpan ke nilai_kontrak
        $calculatorService = app(\App\Services\ContractCalculatorService::class);

        // Determine periode: 
        // 1. From request 'periode' (e.g. "2026-02")
        // 2. Or latest existing contract
        // 3. Or current month
        $periode = $request->periode;

        if (!$periode) {
            $latestNilai = \App\Models\NilaiKontrak::where('paket_id', $id)
                ->orderBy('periode', 'desc')
                ->first();
            $periode = $latestNilai ? \Carbon\Carbon::parse($latestNilai->periode)->format('Y-m') : \Carbon\Carbon::now()->format('Y-m');
        }

        // Calculate dan simpan (re-calculate to ensure fresh data)
        $nilaiKontrak = $calculatorService->calculateForPaket($id, $periode);

        // Tetap gunakan calculateBOQ untuk compatibility dengan view yang ada, 
        // TAPI kita perlu adjust calculateBOQ agar bisa terima periode atau kita override datanya di sini.
        // Masalahnya calculateBOQ() di controller ini hardcoded `now()->format('Y-m')` atau logic lain?
        // Let's check calculateBOQ implementation again. 
        // It uses `now()->format('Y-m')`. We should temporarily override logic or pass periode if we refactor `calculateBOQ`.
        // Ideally `calculateBOQ` should accept `$periode`. 
        // Since I cannot easily change `calculateBOQ` signature without checking all usages (it might be used elsewhere), 
        // I will rely on `$nilaiKontrak` which IS calculated with correct period, and populate `$boqData` manually or correct it.

        // Actually `calculateBOQ` calls `$calculatorService->calculateForPaket($paketId, $periode)` but `$periode` is `now()`.
        // So `calculateBOQ` returns data for CURRENT month. 
        // We want data for `$periode`.

        // BEST FIX: Refactor `calculateBOQ` to accept optional `$periode`.
        // But to be safe and quick, let's just use the service directly here and construct the data structure expected by the view,
        // OR pass the correct data from `$nilaiKontrak`.

        // breakdown_json in $nilaiKontrak has everything `calculateBOQ` returns except 'paket' object and 'vendor'.

        $breakdown = $nilaiKontrak->breakdown_json;
        $pengawas = $breakdown['pengawas'];
        $pelaksana = $breakdown['pelaksana'];
        $karyawanData = $breakdown['karyawan'];

        $paket = Paket::with(['paketKaryawan.karyawan.perusahaan', 'unitKerja'])->findOrFail($id);

        $vendor = null;
        foreach ($paket->paketKaryawan as $pk) {
            if ($pk->karyawan && $pk->karyawan->perusahaan) {
                $vendor = $pk->karyawan->perusahaan->perusahaan;
                break;
            }
        }

        $totalBOQ = $pengawas['total'] + $pelaksana['total'];

        $boqData = [
            'paket' => $paket,
            'vendor' => $vendor,
            'jumlah_pekerja' => $pengawas['count'] + $pelaksana['count'],
            'pengawas' => $pengawas,
            'pelaksana' => $pelaksana,
            'karyawan' => $karyawanData,
            'total_bulanan' => $totalBOQ,
            'total_tahunan' => $totalBOQ * 12,
            'total_boq' => $totalBOQ,
            'ump_sumbar' => $nilaiKontrak->ump_sumbar,
            'tahun' => \Carbon\Carbon::parse($periode)->format('Y'),
            'nilai_kontrak' => $nilaiKontrak // Important
        ];

        return view('lihat-tagihan', [
            'boq' => $boqData
        ]);
    }

    /**
     * Generate PDF Tagihan untuk paket tertentu
     * Sesuai dengan Activity Diagram dan Sequence Diagram
     * UPDATED: Menggunakan NilaiKontrak untuk menyimpan data perhitungan
     */
    public function generatePDF($id)
    {
        try {
            // Clear all output buffers completely
            while (ob_get_level()) {
                ob_end_clean();
            }

            // 1. Get latest periode dari nilai_kontrak yang sudah ada
            $latestNilai = \App\Models\NilaiKontrak::where('paket_id', $id)
                ->orderBy('periode', 'desc')
                ->first();

            if (!$latestNilai) {
                throw new \Exception('Data kontrak belum tersedia. Silakan hitung kontrak terlebih dahulu.');
            }

            $calculatorService = app(\App\Services\ContractCalculatorService::class);
            $periode = \Carbon\Carbon::parse($latestNilai->periode)->format('Y-m');
            $nilaiKontrak = $calculatorService->calculateForPaket($id, $periode);

            // 2. Ambil data paket dari database (untuk compatibility)
            $boqData = $this->calculateBOQ($id);

            // Tambahkan data nilai_kontrak ke boqData
            $boqData['nilai_kontrak'] = $nilaiKontrak;

            // 3. Generate unique token
            $token = TagihanCetak::generateToken();

            // 4. Generate QR Code URL
            $verifyUrl = url('/verify-tagihan/' . $token);

            // 5. Generate QR Code using QRServer.com API (more reliable than Google Charts)
            // QRServer.com is actively maintained and handles long URLs better
            $qrSize = 120;

            // Use QRServer.com API instead of Google Charts (deprecated)
            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
                'size' => $qrSize . 'x' . $qrSize,
                'data' => $verifyUrl,
                'format' => 'png'
            ]);

            // Download QR image from Google API
            try {
                // Log attempt
                \Log::info('Attempting to download QR code from: ' . $qrApiUrl);

                // Use context to set proper headers
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => 'User-Agent: Mozilla/5.0',
                        'timeout' => 10
                    ]
                ]);

                $qrImageData = @file_get_contents($qrApiUrl, false, $context);

                if ($qrImageData !== false && strlen($qrImageData) > 0) {
                    // Convert to base64 for PDF embedding
                    $qrCodeBase64 = base64_encode($qrImageData);
                    $qrCodeImg = '<img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="QR Code" style="width:120px;height:120px;display:block;margin:0 auto;" />';
                    \Log::info('QR code downloaded successfully, size: ' . strlen($qrImageData) . ' bytes');
                } else {
                    // Fallback if download fails
                    \Log::warning('QR code download returned empty or false');
                    $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;line-height:1.3;">
                        <strong>Verifikasi Online</strong><br/><br/>
                        Kunjungi:<br/>
                        <span style="font-size:6px;word-break:break-all;">' . $verifyUrl . '</span>
                    </div>';
                }
            } catch (\Exception $e) {
                // Fallback on error with detailed logging
                \Log::error('QR code generation failed: ' . $e->getMessage());
                \Log::error('Error details: ' . print_r([
                    'allow_url_fopen' => ini_get('allow_url_fopen'),
                    'openssl_loaded' => extension_loaded('openssl'),
                    'url' => $qrApiUrl
                ], true));

                $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;line-height:1.3;">
                    <strong>Verifikasi Online</strong><br/><br/>
                    Kunjungi:<br/>
                    <span style="font-size:6px;word-break:break-all;">' . $verifyUrl . '</span>
                </div>';
            }

            // 6. Simpan ke tagihan_cetak dengan reference ke nilai_kontrak
            $tagihan = TagihanCetak::create([
                'paket_id' => $id,
                'token' => $token,
                'total_boq' => $nilaiKontrak->total_nilai_kontrak, // Gunakan dari nilai_kontrak
                'jumlah_pengawas' => $nilaiKontrak->jumlah_pengawas,
                'jumlah_pelaksana' => $nilaiKontrak->jumlah_pelaksana,
                'vendor' => $boqData['vendor'],
                'tanggal_cetak' => now()
            ]);


            // 6. Generate PDF dengan DomPDF
            // Extract year from contract period for signature
            $contractYear = \Carbon\Carbon::parse($nilaiKontrak->periode)->format('Y');

            $pdf = Pdf::loadView('tagihan-pdf', [
                'boq' => $boqData,
                'qrCode' => $qrCodeImg,
                'token' => $token,
                'tanggal_cetak' => now()->format('d F Y'),
                'cetak_id' => $tagihan->cetak_id,
                'contract_year' => $contractYear  // Year from contract period for signature
            ]);

            $pdf->setPaper('A4', 'portrait');

            // 7. Direct download PDF (no preview in browser)
            $filename = 'BOQ_' . str_replace(' ', '_', $boqData['paket']->paket) . '_' . date('Ymd') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi keaslian tagihan melalui QR Code
     */
    /**
     * Verify tagihan from QR code scan
     * Public access - no auth required
     * Directly shows PDF in browser (can be printed)
     */
    public function verifyTagihan($token)
    {
        // Find tagihan with related data
        $tagihan = TagihanCetak::where('token', $token)
            ->with(['paket.unitKerja'])
            ->first();

        if (!$tagihan) {
            // Show error page if invalid token
            return view('verify-tagihan-error', [
                'message' => 'Token tagihan tidak ditemukan atau tidak valid.'
            ]);
        }

        // Regenerate PDF to show in browser
        // Get nilai kontrak untuk regenerate
        $nilaiKontrak = \App\Models\NilaiKontrak::where('paket_id', $tagihan->paket_id)
            ->orderBy('periode', 'desc')
            ->first();

        if (!$nilaiKontrak) {
            return view('verify-tagihan-error', [
                'message' => 'Data kontrak tidak ditemukan.'
            ]);
        }

        // Calculate BOQ data
        $boqData = $this->calculateBOQ($tagihan->paket_id);

        // Generate QR Code
        $verifyUrl = url('/verify-tagihan/' . $token);
        $qrSize = 120;
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
            'size' => $qrSize . 'x' . $qrSize,
            'data' => $verifyUrl,
            'format' => 'png'
        ]);

        // Download and encode QR
        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'User-Agent: Mozilla/5.0',
                    'timeout' => 10
                ]
            ]);

            $qrImageData = @file_get_contents($qrApiUrl, false, $context);

            if ($qrImageData !== false && strlen($qrImageData) > 0) {
                $qrCodeBase64 = base64_encode($qrImageData);
                $qrCodeImg = '<img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="QR Code" style="width:120px;height:120px;display:block;margin:0 auto;" />';
            } else {
                $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;">QR Code</div>';
            }
        } catch (\Exception $e) {
            $qrCodeImg = '<div style="border:2px solid #000;padding:10px;width:120px;height:120px;text-align:center;font-size:7px;">QR Code</div>';
        }

        // Extract year from contract period
        $contractYear = \Carbon\Carbon::parse($nilaiKontrak->periode)->format('Y');

        // Generate PDF and stream to browser (not download)
        $pdf = Pdf::loadView('tagihan-pdf', [
            'boq' => $boqData,
            'qrCode' => $qrCodeImg,
            'token' => $token,
            'tanggal_cetak' => now()->format('d F Y'),
            'cetak_id' => $tagihan->cetak_id,
            'contract_year' => $contractYear
        ]);

        $pdf->setPaper('A4', 'portrait');

        // Stream PDF to browser (can view and print)
        return $pdf->stream('Tagihan_' . $tagihan->cetak_id . '.pdf');
    }

    /**
     * Download PDF from verification page
     * Public access - requires valid token
     */
    public function downloadVerifiedPDF($token)
    {
        $tagihan = TagihanCetak::where('token', $token)->first();

        if (!$tagihan) {
            abort(404, 'Tagihan tidak ditemukan');
        }

        // Regenerate PDF sama seperti generatePDF method
        // Atau redirect ke route generatePDF jika lebih prefer
        return redirect()->route('paket.pdf.download', $tagihan->paket_id);
    }

    /**
     * Hitung Ulang Nilai Kontrak Manual
     */
    public function hitung(Request $request, $id)
    {
        $calculatorService = app(\App\Services\ContractCalculatorService::class);
        
        $periode = $request->periode ?: date('Y-m');
        
        try {
            $calculatorService->calculateForPaket($id, $periode);
            return redirect()->back()->with('success', 'Perhitungan kontrak periode ' . \Carbon\Carbon::parse($periode)->format('F Y') . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghitung kontrak: ' . $e->getMessage());
        }
    }

}
