<?php

namespace App\Services;

use App\Models\ContractValidation;
use App\Models\NilaiKontrak;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContractValidationService
{
    /**
     * Create a new validation record
     * 
     * @param NilaiKontrak $nilaiKontrak
     * @param int|null $userId
     * @param int|null $expiryDays
     * @return ContractValidation
     */
    public function createValidation(NilaiKontrak $nilaiKontrak, $userId = null, $expiryDays = null)
    {
        $token = ContractValidation::generateToken();
        
        $validationData = [
            'nilai_kontrak_id' => $nilaiKontrak->id,
            'validation_token' => $token,
            'document_hash' => '', // Will be updated after PDF generation
            'is_valid' => true,
            'generated_at' => now(),
            'generated_by' => $userId,
            'expires_at' => $expiryDays ? now()->addDays($expiryDays) : null,
            'metadata' => $this->buildMetadata($nilaiKontrak, $userId)
        ];

        $validation = ContractValidation::create($validationData);

        Log::info("Contract validation created", [
            'validation_id' => $validation->id,
            'token' => $token,
            'nilai_kontrak_id' => $nilaiKontrak->id
        ]);

        return $validation;
    }

    /**
     * Build metadata snapshot for validation
     * 
     * @param NilaiKontrak $nilaiKontrak
     * @param int|null $userId
     * @return array
     */
    protected function buildMetadata(NilaiKontrak $nilaiKontrak, $userId = null)
    {
        $paket = $nilaiKontrak->paket;
        $breakdown = $nilaiKontrak->breakdown_json;
        
        return [
            'snapshot' => [
                'paket_id' => $nilaiKontrak->paket_id,
                'paket_nama' => $paket->paket ?? 'N/A',
                'unit_kerja' => $paket->unitKerja->unit_kerja ?? 'N/A',
                'periode' => $nilaiKontrak->periode,
                'tahun' => $nilaiKontrak->tahun,
                'bulan' => $nilaiKontrak->bulan,
                'ump_sumbar' => $nilaiKontrak->ump_sumbar,
                'total_nilai_kontrak' => $nilaiKontrak->total_nilai_kontrak,
                'jumlah_karyawan_aktif' => $nilaiKontrak->jumlah_karyawan_aktif,
                'jumlah_karyawan_total' => $nilaiKontrak->jumlah_karyawan_total,
                'jumlah_pengawas' => $nilaiKontrak->jumlah_pengawas,
                'jumlah_pelaksana' => $nilaiKontrak->jumlah_pelaksana,
                'total_pengawas' => $nilaiKontrak->total_pengawas,
                'total_pelaksana' => $nilaiKontrak->total_pelaksana,
                'karyawan_count' => count($breakdown['karyawan'] ?? []),
                'generated_by_id' => $userId,
                'generated_at' => now()->toDateTimeString()
            ],
            'checksum' => $this->generateChecksum($nilaiKontrak)
        ];
    }

    /**
     * Generate checksum from contract data
     * 
     * @param NilaiKontrak $nilaiKontrak
     * @return string
     */
    protected function generateChecksum(NilaiKontrak $nilaiKontrak)
    {
        $data = [
            $nilaiKontrak->id,
            $nilaiKontrak->paket_id,
            $nilaiKontrak->periode,
            $nilaiKontrak->total_nilai_kontrak,
            $nilaiKontrak->jumlah_karyawan_total,
            json_encode($nilaiKontrak->breakdown_json)
        ];

        return md5(implode('|', $data));
    }

    /**
     * Validate a contract by token
     * 
     * @param string $token
     * @param string|null $ipAddress
     * @return array
     */
    public function validateByToken($token, $ipAddress = null)
    {
        $validation = ContractValidation::with(['nilaiKontrak.paket.unitKerja', 'generator'])
            ->where('validation_token', $token)
            ->first();

        if (!$validation) {
            return [
                'valid' => false,
                'error' => 'Token tidak ditemukan',
                'message' => 'QR Code tidak valid atau tidak terdaftar dalam sistem.'
            ];
        }

        // Check if expired
        if ($validation->isExpired()) {
            return [
                'valid' => false,
                'error' => 'Token sudah expired',
                'message' => 'QR Code sudah melewati masa berlaku.',
                'validation' => $validation
            ];
        }

        // Check if marked as invalid
        if (!$validation->is_valid) {
            return [
                'valid' => false,
                'error' => 'Dokumen ditandai tidak valid',
                'message' => 'Dokumen ini telah ditandai sebagai tidak valid oleh sistem.',
                'validation' => $validation
            ];
        }

        // Increment validation count
        $validation->incrementValidationCount($ipAddress);

        // Verify checksum
        $currentChecksum = $this->generateChecksum($validation->nilaiKontrak);
        $storedChecksum = $validation->metadata['checksum'] ?? '';
        
        $checksumMatch = ($currentChecksum === $storedChecksum);

        return [
            'valid' => true,
            'validation' => $validation,
            'nilai_kontrak' => $validation->nilaiKontrak,
            'checksum_match' => $checksumMatch,
            'message' => $checksumMatch 
                ? 'Dokumen valid dan belum dimodifikasi' 
                : 'Dokumen valid namun data kontrak telah berubah sejak PDF dibuat'
        ];
    }

    /**
     * Invalidate a validation
     * 
     * @param int $validationId
     * @param int|null $userId
     * @return bool
     */
    public function invalidateValidation($validationId, $userId = null)
    {
        $validation = ContractValidation::findOrFail($validationId);
        
        $validation->update([
            'is_valid' => false
        ]);

        Log::info("Contract validation invalidated", [
            'validation_id' => $validationId,
            'invalidated_by' => $userId
        ]);

        return true;
    }

    /**
     * Get validation statistics
     * 
     * @param int $nilaiKontrakId
     * @return array
     */
    public function getValidationStats($nilaiKontrakId)
    {
        $validations = ContractValidation::where('nilai_kontrak_id', $nilaiKontrakId)->get();

        return [
            'total_validations' => $validations->count(),
            'total_scans' => $validations->sum('validation_count'),
            'active_validations' => $validations->where('is_valid', true)->count(),
            'expired_validations' => $validations->filter->isExpired()->count(),
            'latest_scan' => $validations->max('validated_at')
        ];
    }
}
