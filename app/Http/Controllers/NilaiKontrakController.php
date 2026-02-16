<?php

namespace App\Http\Controllers;

use App\Services\ContractCalculatorService;
use App\Models\NilaiKontrak;
use App\Models\KontrakHistory;
use App\Models\Paket;
use App\Exports\NilaiKontrakExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\ContractValidationService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class NilaiKontrakController extends Controller
{
    protected $calculatorService;

    public function __construct(ContractCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    /**
     * Halaman kalkulator kontrak utama
     */
    public function index(Request $request)
    {
        $pakets = Paket::with('unitKerja')->orderBy('paket')->get();
        $currentPeriode = Carbon::now()->format('Y-m');
        
        // Get available periods for filter
        $availablePeriods = NilaiKontrak::select('periode')
            ->distinct()
            ->orderBy('periode', 'desc')
            ->pluck('periode')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m');
            });

        $selectedPeriode = $request->query('filter_periode');

        // Load nilai kontrak data
        $nilaiKontrakData = [];
        foreach ($pakets as $paket) {
            if ($selectedPeriode) {
                // Get specific period
                $nilai = NilaiKontrak::where('paket_id', $paket->paket_id)
                    ->where('periode', $selectedPeriode)
                    ->first();
            } else {
                // Default: Latest
                $nilai = NilaiKontrak::getLatestForPaket($paket->paket_id);
            }
            $nilaiKontrakData[$paket->paket_id] = $nilai;
        }
        
        return view('kalkulator-kontrak', compact('pakets', 'currentPeriode', 'nilaiKontrakData', 'availablePeriods', 'selectedPeriode'));
    }

    /**
     * Calculate contract value untuk paket tertentu
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'paket_id' => 'required|exists:md_paket,paket_id',
            'periode' => 'required|date_format:Y-m'
        ]);

        try {
            $nilaiKontrak = $this->calculatorService->calculateForPaket(
                $request->paket_id,
                $request->periode,
                auth()->id() ?? null
            );

            return redirect()
                ->route('kalkulator.show', ['paket_id' => $request->paket_id, 'periode' => $request->periode])
                ->with('success', 'Nilai kontrak berhasil dihitung');

        } catch (\Exception $e) {
            Log::error('Error calculating contract', [
                'paket_id' => $request->paket_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal menghitung nilai kontrak: ' . $e->getMessage());
        }
    }

    /**
     * Show calculated contract value
     */
    public function show(Request $request)
    {
        $paketId = $request->paket_id;
        $periode = $request->periode ?? Carbon::now()->format('Y-m');

        $nilaiKontrak = NilaiKontrak::with('paket.unitKerja')
            ->where('paket_id', $paketId)
            ->where('periode', $periode)
            ->first();

        if (!$nilaiKontrak) {
            return redirect()
                ->route('kalkulator.index')
                ->with('error', 'Data nilai kontrak belum tersedia. Silakan hitung terlebih dahulu.');
        }

        // Get history untuk comparison
        $previousNilai = NilaiKontrak::where('paket_id', $paketId)
            ->where('periode', '<', $periode)
            ->orderBy('periode', 'desc')
            ->first();

        $history = KontrakHistory::getHistoryForPaket($paketId, 5);

        return view('kalkulator-show', compact('nilaiKontrak', 'previousNilai', 'history'));
    }

    /**
     * Recalculate semua paket
     */
    public function recalculateAll(Request $request)
    {
        $periode = $request->periode ?? Carbon::now()->format('Y-m');

        try {
            $summary = $this->calculatorService->recalculateAllPakets(
                $periode,
                auth()->id() ?? null
            );

            return back()->with('success', 
                "Recalculation selesai. Berhasil: {$summary['success']}, Gagal: {$summary['failed']}"
            );

        } catch (\Exception $e) {
            Log::error('Error recalculating all pakets', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal recalculate: ' . $e->getMessage());
        }
    }

    /**
     * View history perubahan nilai kontrak
     */
    public function history($paketId)
    {
        $paket = Paket::with('unitKerja')->findOrFail($paketId);
        $histories = KontrakHistory::with('nilaiKontrak')
            ->where('paket_id', $paketId)
            ->orderBy('changed_at', 'desc')
            ->paginate(20);

        return view('kontrak-history', compact('paket', 'histories'));
    }

    /**
     * API endpoint untuk AJAX calculation
     */
    public function apiCalculate($paketId, Request $request)
    {
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));

        try {
            $nilaiKontrak = $this->calculatorService->calculateForPaket(
                $paketId,
                $periode,
                auth()->id() ?? null
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'total_nilai_kontrak' => number_format($nilaiKontrak->total_nilai_kontrak, 0, ',', '.'),
                    'total_nilai_kontrak_raw' => $nilaiKontrak->total_nilai_kontrak,
                    'total_pengawas' => number_format($nilaiKontrak->total_pengawas, 0, ',', '.'),
                    'total_pelaksana' => number_format($nilaiKontrak->total_pelaksana, 0, ',', '.'),
                    'jumlah_pengawas' => $nilaiKontrak->jumlah_pengawas,
                    'jumlah_pelaksana' => $nilaiKontrak->jumlah_pelaksana,
                    'jumlah_karyawan_aktif' => $nilaiKontrak->jumlah_karyawan_aktif,
                    'jumlah_karyawan_total' => $nilaiKontrak->jumlah_karyawan_total,
                    'kuota_paket' => $nilaiKontrak->kuota_paket,
                    'ump_sumbar' => number_format($nilaiKontrak->ump_sumbar, 0, ',', '.'),
                    'breakdown' => $nilaiKontrak->breakdown_json
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nilai kontrak untuk periode tertentu (untuk print integration)
     */
    public function getNilaiKontrakForPrint($paketId, $periode = null)
    {
        if (!$periode) {
            $periode = Carbon::now()->format('Y-m');
        }

        // Cek apakah sudah ada data untuk periode ini
        $nilaiKontrak = NilaiKontrak::getForPeriode($paketId, $periode);

        // Jika belum ada, calculate dulu
        if (!$nilaiKontrak) {
            $nilaiKontrak = $this->calculatorService->calculateForPaket($paketId, $periode);
        }

        return $nilaiKontrak;
    }

    public function cetakThr(Request $request, $paket_id, ContractValidationService $validationService)
    {
        $query = NilaiKontrak::with(['paket.unitKerja', 'paket.paketKaryawan.karyawan.perusahaan'])
            ->where('paket_id', $paket_id);

        if ($request->has('periode')) {
            $query->where('periode', $request->periode);
        }

        $nilaiKontrak = $query->orderBy('periode', 'desc')
            ->firstOrFail();

        // 1. Get Lebaran Date
        $lebaran = \App\Models\Lebaran::where('tahun', $nilaiKontrak->tahun)->where('is_deleted', 0)->first();
        
        if (!$lebaran) {
            return back()->with('error', 'Tanggal Lebaran untuk tahun ' . $nilaiKontrak->tahun . ' belum diatur. Silakan atur di menu Data Lebaran.');
        }

        $tanggalLebaran = \Carbon\Carbon::parse($lebaran->tanggal);
        // Eligibility Cutoff: 1st day of the Eid month
        $cutoffDate = $tanggalLebaran->copy()->startOfMonth();

        // Document Date: H-14 from Lebaran
        $tanggalDokumen = $tanggalLebaran->copy()->subDays(14);

        $breakdown = $nilaiKontrak->breakdown_json ?? [];
        
        $totalBasicThr = 0;
        $filteredKaryawanCount = 0;
        $eligibleKaryawan = [];

        foreach (($breakdown['karyawan'] ?? []) as $karyawan) {
            // Check eligibility against current DB status
            $karyawanDb = \App\Models\Karyawan::find($karyawan['karyawan_id']);
            
            if (!$karyawanDb) continue;

            $isEligible = false;
            $status = $karyawanDb->status_aktif;

            // Rule: Aktif OR (Resigned but worked until >= 1st of Eid Month)
            if ($karyawanDb->status_aktif == 'Aktif') {
                $isEligible = true;
            } else {
                if ($karyawanDb->tanggal_berhenti) {
                    $tglBerhenti = \Carbon\Carbon::parse($karyawanDb->tanggal_berhenti);
                    if ($tglBerhenti->gte($cutoffDate)) {
                        $isEligible = true;
                        $status = 'Berhenti (masih berhak)';
                    }
                }
            }

            if ($isEligible) {
                $upah = $karyawan['upah_pokok'] ?? 0;
                $tjTetap = $karyawan['tj_tetap'] ?? 0;
                $tjLokasi = $karyawan['tj_lokasi'] ?? 0;
                $thrAmount = $upah + $tjTetap + $tjLokasi;

                // THR = 1 month salary (Upah + Tj Tetap + Tj Lokasi)
                $totalBasicThr += $thrAmount;
                $filteredKaryawanCount++;

                $eligibleKaryawan[] = [
                    'nama' => $karyawanDb->nama_tk ?? '-',
                    'upah_pokok' => $upah,
                    'tj_tetap' => $tjTetap,
                    'tj_lokasi' => $tjLokasi,
                    'thr_amount' => $thrAmount,
                    'status' => $status,
                ];
            }
        }

        // Fee THR 5%
        $feeThr = $totalBasicThr * 0.05;
        $totalNilaiThr = $totalBasicThr + $feeThr;

        // QR Code Validation Logic
        $validation = \App\Models\ContractValidation::where('nilai_kontrak_id', $nilaiKontrak->id)
            ->where('metadata->type', 'THR')
            ->where('metadata->tahun', $nilaiKontrak->tahun)
            ->first();

        if (!$validation) {
            $validation = $validationService->createValidation($nilaiKontrak, auth()->id(), null, [
                'type' => 'THR',
                'tahun' => $nilaiKontrak->tahun,
                'description' => 'Dokumen THR Tahun ' . $nilaiKontrak->tahun
            ]);
        }

        $validationUrl = route('contract.validate', $validation->validation_token);
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($validationUrl));

        // Find vendor details
        $vendorName = '-';
        $vendorLeader = '-';
        $vendorPosition = '-';

        // Iterate through paket employees to find the vendor (assuming consistent vendor per packet)
        foreach ($nilaiKontrak->paket->paketKaryawan as $pk) {
            if ($pk->karyawan && $pk->karyawan->perusahaan) {
                $vendorName = $pk->karyawan->perusahaan->perusahaan;
                $vendorLeader = $pk->karyawan->perusahaan->cp; // Contact Person
                $vendorPosition = $pk->karyawan->perusahaan->cp_jab; // Contact Person Jabatan
                break;
            }
        }

        $data = [
            'nama_perusahaan' => $vendorName,
            'pimpinan_vendor' => $vendorLeader,
            'jabatan_vendor' => $vendorPosition,
            'paket' => $nilaiKontrak->paket->paket,
            'periode_tagihan' => 'THR Tahun ' . $nilaiKontrak->tahun,
            'jumlah_pekerja' => $filteredKaryawanCount,
            'unit_kerja' => $nilaiKontrak->paket->unitKerja->unit_kerja ?? '-',
            'pekerjaan_pos' => $nilaiKontrak->paket->paket, 
            'nilai_thr' => $totalBasicThr,
            'fee_thr' => $feeThr,
            'total' => $totalNilaiThr,
            'qr_code' => $qrCode,
            'validation_url' => $validationUrl,
            'tanggal_lebaran' => $lebaran->tanggal,
            'tanggal_dokumen' => $tanggalDokumen,
            'karyawan_list' => $eligibleKaryawan,
            'cutoff_date' => $cutoffDate->format('d F Y'),
        ];

        $pdf = \PDF::loadView('pdf.thr', compact('data', 'nilaiKontrak'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('THR_' . $nilaiKontrak->paket->paket . '_' . $nilaiKontrak->tahun . '.pdf');
    }

    /**
     * Export Laporan Kontrak ke Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'scope' => 'required|in:all,single',
            'paket_id' => 'required_if:scope,single|nullable|exists:md_paket,paket_id',
            'periode' => 'required|date_format:Y-m',
            'columns' => 'required|array|min:1'
        ]);

        $periode = $request->periode;
        $scope = $request->scope;
        $paketId = $request->paket_id;
        $columns = $request->columns;

        // Query Data based on scope
        if ($scope === 'all') {
            // Get all pakets first to ensure we cover everything, then get latest/specific periode value
            $query = NilaiKontrak::with(['paket.unitKerja'])
                ->where('periode', $periode);
        } else {
            $query = NilaiKontrak::with(['paket.unitKerja'])
                ->where('paket_id', $paketId)
                ->where('periode', $periode);
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk periode terpilih.');
        }

        // Generate filename
        $timestamp = Carbon::now()->format('Ymd_His');
        $filename = "Laporan_Kontrak_{$periode}_{$timestamp}.xlsx";

        return Excel::download(new NilaiKontrakExport($data, $columns), $filename);
    }
}
