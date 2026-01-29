<?php

namespace App\Services;

use App\Models\NilaiKontrak;
use App\Models\Karyawan;
use App\Models\Riwayat_jabatan;
use App\Models\Riwayat_shift;
use App\Models\Riwayat_resiko;
use App\Models\Riwayat_lokasi;
use App\Models\Riwayat_fungsi;
use App\Models\Masakerja;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractPdfService
{
    protected $validationService;
    protected $qrCodeService;

    public function __construct(
        ContractValidationService $validationService,
        QrCodeService $qrCodeService
    ) {
        $this->validationService = $validationService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Generate contract PDF with complete BOQ from all master data
     * 
     * @param int $nilaiKontrakId
     * @param int|null $userId
     * @param int|null $expiryDays
     * @return array
     */
    public function generateContractPdf($nilaiKontrakId, $userId = null, $expiryDays = 365)
    {
        // 1. Load nilai kontrak dengan relasi
        $nilaiKontrak = NilaiKontrak::with([
            'paket.unitKerja',
            'paket.paketKaryawan.karyawan.perusahaan',
            'calculator'
        ])->findOrFail($nilaiKontrakId);

        // 2. Create validation record
        $validation = $this->validationService->createValidation($nilaiKontrak, $userId, $expiryDays);

        // 3. Build complete BOQ data from all master data
        $boqData = $this->buildCompleteBoqData($nilaiKontrak);

        // 4. Generate QR code URL
        $qrCodeUrl = route('contract.validate', ['token' => $validation->validation_token]);
        $qrCodeSvg = $this->qrCodeService->generate($qrCodeUrl, 200);

        // 5. Compile data for PDF
        $pdfData = [
            'nilai_kontrak' => $nilaiKontrak,
            'paket' => $nilaiKontrak->paket,
            'unit_kerja' => $nilaiKontrak->paket->unitKerja,
            'breakdown_pengawas' => $boqData['pengawas'],
            'breakdown_pelaksana' => $boqData['pelaksana'],
            'karyawan_detail' => $boqData['karyawan'],
            'ump_sumbar' => $nilaiKontrak->ump_sumbar,
            'periode' => $nilaiKontrak->periode,
            'periode_formatted' => $this->formatPeriode($nilaiKontrak->tahun, $nilaiKontrak->bulan),
            'qr_code_svg' => $qrCodeSvg,
            'validation_token' => $validation->validation_token,
            'validation_url' => $qrCodeUrl,
            'generated_at' => now(),
            'generated_by_name' => optional($nilaiKontrak->calculator)->name ?? 'System',
            'contract_number' => $this->generateContractNumber($nilaiKontrak)
        ];

        // 6. Generate PDF
        $pdf = Pdf::loadView('contracts.pdf-template', $pdfData)
            ->setPaper('a4', 'portrait');

        // 7. Save PDF to storage
        $filename = $this->generateFilename($nilaiKontrak, $validation);
        $pdfPath = "contracts/{$filename}";
        
        // Ensure directory exists
        Storage::disk('public')->makeDirectory('contracts');
        
        // Save PDF
        $pdfContent = $pdf->output();
        Storage::disk('public')->put($pdfPath, $pdfContent);

        // 8. Generate document hash
        $documentHash = hash('sha256', $pdfContent);

        // 9. Update validation with path and hash
        $validation->update([
            'pdf_path' => $pdfPath,
            'document_hash' => $documentHash
        ]);

        Log::info("Contract PDF generated", [
            'nilai_kontrak_id' => $nilaiKontrakId,
            'validation_id' => $validation->id,
            'pdf_path' => $pdfPath
        ]);

        return [
            'pdf' => $pdf,
            'path' => $pdfPath,
            'validation' => $validation,
            'filename' => $filename,
            'url' => Storage::disk('public')->url($pdfPath)
        ];
    }

    /**
     * Build complete BOQ data from all master data
     * 
     * @param NilaiKontrak $nilaiKontrak
     * @return array
     */
    protected function buildCompleteBoqData(NilaiKontrak $nilaiKontrak)
    {
        $breakdown = $nilaiKontrak->breakdown_json;
        $karyawanData = $breakdown['karyawan'] ?? [];
        
        $enrichedKaryawan = [];

        foreach ($karyawanData as $data) {
            $karyawanId = $data['karyawan_id'];
            
            // Load karyawan dengan relasi
            $karyawan = Karyawan::with('perusahaan')->find($karyawanId);
            
            if (!$karyawan) {
                continue;
            }

            // Ambil semua master data untuk karyawan ini
            $jabatan = Riwayat_jabatan::with('jabatan')
                ->where('karyawan_id', $karyawanId)
                ->latest('beg_date')
                ->first();
            
            $shift = Riwayat_shift::with('harianshift')
                ->where('karyawan_id', $karyawanId)
                ->latest('beg_date')
                ->first();
                
            $resiko = Riwayat_resiko::with('resiko')
                ->where('karyawan_id', $karyawanId)
                ->latest('beg_date')
                ->first();
                
            $lokasi = Riwayat_lokasi::with(['lokasi', 'lokasi.ump' => function($q) use ($nilaiKontrak) {
                $q->where('tahun', $nilaiKontrak->tahun);
            }])
                ->where('karyawan_id', $karyawanId)
                ->latest('beg_date')
                ->first();
                
            $fungsi = Riwayat_fungsi::with('fungsi')
                ->where('karyawan_id', $karyawanId)
                ->latest('beg_date')
                ->first();
                
            $masakerja = Masakerja::where('karyawan_id', $karyawanId)
                ->latest('beg_date')
                ->first();

            // Enrich data dengan detail master data
            $enrichedData = $data;
            $enrichedData['karyawan'] = $karyawan;
            $enrichedData['master_data'] = [
                'jabatan' => $jabatan ? [
                    'kode' => optional($jabatan->jabatan)->kode_jabatan ?? '',
                    'nama' => optional($jabatan->jabatan)->jabatan ?? '',
                    'tunjangan' => optional($jabatan->jabatan)->tunjangan_jabatan ?? 0
                ] : null,
                'shift' => $shift ? [
                    'kode' => optional($shift->harianshift)->kode_shift ?? '',
                    'nama' => optional($shift->harianshift)->nama_shift ?? '',
                    'tunjangan' => optional($shift->harianshift)->tunjangan_shift ?? 0
                ] : null,
                'resiko' => $resiko ? [
                    'kode' => optional($resiko->resiko)->kode_resiko ?? '',
                    'nama' => optional($resiko->resiko)->nama_resiko ?? '',
                    'tunjangan' => optional($resiko->resiko)->tunjangan_resiko ?? 0
                ] : null,
                'lokasi' => $lokasi ? [
                    'kode' => optional($lokasi->lokasi)->kode_lokasi ?? '',
                    'nama' => optional($lokasi->lokasi)->nama_lokasi ?? '',
                    'ump' => optional(optional($lokasi->lokasi)->ump)->ump ?? 0
                ] : null,
                'fungsi' => $fungsi ? [
                    'kode' => optional($fungsi->fungsi)->kode_fungsi ?? '',
                    'nama' => optional($fungsi->fungsi)->fungsi ?? '',
                    'tunjangan' => optional($fungsi->fungsi)->tunjangan_fungsi ?? 0
                ] : null,
                'masa_kerja' => $masakerja ? [
                    'tahun' => $masakerja->tahun ?? 0,
                    'tunjangan' => $masakerja->tunjangan_masakerja ?? 0
                ] : null
            ];

            // Breakdown komponen gaji lebih detail
            $enrichedData['breakdown_detail'] = $this->buildGajiBreakdown(
                $data, 
                $nilaiKontrak->ump_sumbar,
                $enrichedData['master_data']
            );

            $enrichedKaryawan[] = $enrichedData;
        }

        return [
            'pengawas' => $breakdown['pengawas'] ?? [],
            'pelaksana' => $breakdown['pelaksana'] ?? [],
            'karyawan' => $enrichedKaryawan
        ];
    }

    /**
     * Build detailed salary breakdown
     * 
     * @param array $karyawanData
     * @param float $umpSumbar
     * @param array $masterData
     * @return array
     */
    protected function buildGajiBreakdown($karyawanData, $umpSumbar, $masterData)
    {
        return [
            'upah_pokok' => $karyawanData['upah_pokok'] ?? 0,
            'tj_umum' => round($umpSumbar * 0.08),
            'tj_jabatan' => $masterData['jabatan']['tunjangan'] ?? 0,
            'tj_masa_kerja' => $masterData['masa_kerja']['tunjangan'] ?? 0,
            'tj_shift' => $masterData['shift']['tunjangan'] ?? 0,
            'tj_lokasi' => $karyawanData['tj_lokasi'] ?? 0,
            'tj_resiko' => $masterData['resiko']['tunjangan'] ?? 0,
            'tj_fungsi' => $masterData['fungsi']['tunjangan'] ?? 0,
            'tj_penyesuaian' => $karyawanData['karyawan']->tunjangan_penyesuaian ?? 0,
            'tj_presensi' => round(($karyawanData['upah_pokok'] ?? 0) * 0.08),
            'bpjs_kesehatan' => $karyawanData['bpjs_kesehatan'] ?? 0,
            'bpjs_ketenagakerjaan' => $karyawanData['bpjs_ketenagakerjaan'] ?? 0,
            'kompensasi' => $karyawanData['kompensasi'] ?? 0,
            'total' => $karyawanData['total'] ?? 0
        ];
    }

    /**
     * Generate contract number
     * 
     * @param NilaiKontrak $nilaiKontrak
     * @return string
     */
    protected function generateContractNumber(NilaiKontrak $nilaiKontrak)
    {
        $tahun = $nilaiKontrak->tahun;
        $bulan = str_pad($nilaiKontrak->bulan, 2, '0', STR_PAD_LEFT);
        $paketId = str_pad($nilaiKontrak->paket_id, 3, '0', STR_PAD_LEFT);
        
        return "CTR-{$tahun}-{$bulan}-PKG{$paketId}";
    }

    /**
     * Generate filename for PDF
     * 
     * @param NilaiKontrak $nilaiKontrak
     * @param ContractValidation $validation
     * @return string
     */
    protected function generateFilename(NilaiKontrak $nilaiKontrak, $validation)
    {
        $contractNumber = $this->generateContractNumber($nilaiKontrak);
        $tokenShort = substr($validation->validation_token, 0, 8);
        
        return "contract_{$contractNumber}_{$tokenShort}.pdf";
    }

    /**
     * Format periode for display
     * 
     * @param int $tahun
     * @param int $bulan
     * @return string
     */
    protected function formatPeriode($tahun, $bulan)
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $bulanNames[$bulan] . ' ' . $tahun;
    }

    /**
     * Download existing contract PDF
     * 
     * @param int $validationId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadPdf($validationId)
    {
        $validation = ContractValidation::findOrFail($validationId);
        
        if (!$validation->pdf_path || !Storage::disk('public')->exists($validation->pdf_path)) {
            throw new \Exception('File PDF tidak ditemukan');
        }

        return Storage::disk('public')->download($validation->pdf_path);
    }
}
