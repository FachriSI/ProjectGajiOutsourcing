<?php

namespace App\Http\Controllers;

use App\Models\ContractValidation;
use App\Services\ContractValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContractValidationController extends Controller
{
    protected $validationService;

    public function __construct(ContractValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Show validation page (accessed from QR code)
     * 
     * @param string $token
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showValidation($token, Request $request)
    {
        $ipAddress = $request->ip();
        
        // Validate contract by token
        $result = $this->validationService->validateByToken($token, $ipAddress);

        Log::info("Contract validation accessed", [
            'token' => $token,
            'ip' => $ipAddress,
            'valid' => $result['valid']
        ]);

        return view('contracts.validation', [
            'result' => $result,
            'token' => $token
        ]);
    }

    /**
     * API endpoint for validation (JSON response)
     * 
     * @param string $token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateApi($token, Request $request)
    {
        $ipAddress = $request->ip();
        
        $result = $this->validationService->validateByToken($token, $ipAddress);

        return response()->json($result);
    }

    /**
     * Show validation statistics
     * 
     * @param int $nilaiKontrakId
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats($nilaiKontrakId)
    {
        $stats = $this->validationService->getValidationStats($nilaiKontrakId);

        return response()->json($stats);
    }

    /**
     * Invalidate a validation
     * 
     * @param int $validationId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invalidate($validationId, Request $request)
    {
        try {
            $userId = auth()->id();
            
            $this->validationService->invalidateValidation($validationId, $userId);

            return back()->with('success', 'Validasi berhasil di-nonaktifkan');

        } catch (\Exception $e) {
            Log::error("Failed to invalidate validation", [
                'validation_id' => $validationId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Gagal menonaktifkan validasi');
        }
    }
}
