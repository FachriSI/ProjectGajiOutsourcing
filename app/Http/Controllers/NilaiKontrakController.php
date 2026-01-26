<?php

namespace App\Http\Controllers;

use App\Services\ContractCalculatorService;
use App\Models\NilaiKontrak;
use App\Models\KontrakHistory;
use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
    public function index()
    {
        $pakets = Paket::with('unitKerja')->orderBy('paket')->get();
        $currentPeriode = Carbon::now()->format('Y-m');
        
        // Load latest nilai kontrak for each paket
        $nilaiKontrakData = [];
        foreach ($pakets as $paket) {
            $latestNilai = NilaiKontrak::getLatestForPaket($paket->paket_id);
            $nilaiKontrakData[$paket->paket_id] = $latestNilai;
        }
        
        return view('kalkulator-kontrak', compact('pakets', 'currentPeriode', 'nilaiKontrakData'));
    }

    /**
     * Calculate contract value untuk paket tertentu
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'paket_id' => 'required|exists:paket,paket_id',
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
}
