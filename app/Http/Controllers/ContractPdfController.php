<?php

namespace App\Http\Controllers;

use App\Models\NilaiKontrak;
use App\Models\ContractValidation;
use App\Services\ContractPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContractPdfController extends Controller
{
    protected $pdfService;

    public function __construct(ContractPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate and download contract PDF
     * 
     * @param Request $request
     * @param int $nilaiKontrakId
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request, $nilaiKontrakId)
    {
        try {
            $userId = Auth::id();
            $expiryDays = $request->input('expiry_days', 365); // Default 1 year

            // Generate PDF
            $result = $this->pdfService->generateContractPdf($nilaiKontrakId, $userId, $expiryDays);

            // Return PDF download
            return $result['pdf']->download($result['filename']);

        } catch (\Exception $e) {
            Log::error("Failed to generate contract PDF", [
                'nilai_kontrak_id' => $nilaiKontrakId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal generate PDF kontrak: ' . $e->getMessage());
        }
    }

    /**
     * Stream PDF (view in browser)
     * 
     * @param int $nilaiKontrakId
     * @return \Illuminate\Http\Response
     */
    public function stream($nilaiKontrakId)
    {
        try {
            $userId = Auth::id();

            // Generate PDF
            $result = $this->pdfService->generateContractPdf($nilaiKontrakId, $userId);

            // Return PDF stream
            return $result['pdf']->stream($result['filename']);

        } catch (\Exception $e) {
            Log::error("Failed to stream contract PDF", [
                'nilai_kontrak_id' => $nilaiKontrakId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal menampilkan PDF kontrak: ' . $e->getMessage());
        }
    }

    /**
     * Download existing PDF from validation
     * 
     * @param int $validationId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($validationId)
    {
        try {
            return $this->pdfService->downloadPdf($validationId);

        } catch (\Exception $e) {
            Log::error("Failed to download contract PDF", [
                'validation_id' => $validationId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'File PDF tidak ditemukan');
        }
    }

    /**
     * Show list of generated PDFs for a contract
     * 
     * @param int $nilaiKontrakId
     * @return \Illuminate\View\View
     */
    public function index($nilaiKontrakId)
    {
        $nilaiKontrak = NilaiKontrak::with(['paket', 'validations' => function($query) {
            $query->orderBy('generated_at', 'desc');
        }])->findOrFail($nilaiKontrakId);

        return view('contracts.index', compact('nilaiKontrak'));
    }
}
