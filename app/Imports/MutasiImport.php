<?php

namespace App\Imports;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Paket;
use App\Models\PaketKaryawan;
use App\Models\Riwayat_jabatan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\ContractCalculatorService;

class MutasiImport implements ToCollection
{

    private $total = 0;
    private $gagal = 0;
    protected $log = [];
    
    // Store affected pakets to recalculate later: [paket_id => [periode1, periode2]]
    protected $affectedPakets = [];

    public function getTotal()
    {
        return $this->total;
    }

    public function getGagal()
    {
        return $this->gagal;
    }

    /**
     * Parse date from Excel (Serial or String)
     */
    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            // Check if it's numeric (Excel Serial Date)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            
            // Try parsing as string (YYYY-MM-DD or other formats supported by Carbon)
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Register affected paket for recalculation
     */
    private function registerForRecalculation($paketId, $dateStr)
    {
        if (!$paketId || !$dateStr) return;

        $periode = Carbon::parse($dateStr)->format('Y-m');
        $currentPeriode = date('Y-m');

        if (!isset($this->affectedPakets[$paketId])) {
            $this->affectedPakets[$paketId] = [];
        }

        // Add mutation period
        if (!in_array($periode, $this->affectedPakets[$paketId])) {
            $this->affectedPakets[$paketId][] = $periode;
        }

        // Add current period if different (to ensure dashboard is updated)
        if ($periode !== $currentPeriode && !in_array($currentPeriode, $this->affectedPakets[$paketId])) {
            $this->affectedPakets[$paketId][] = $currentPeriode;
        }
    }

    public function collection(Collection $rows)
    {
        unset($rows[0]); // Lewati header

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $this->total++;

                $osis_id = $row[0] ?? null;
                $paket_excel = $row[1] ?? null;
                $tanggal_mutasi_excel = $row[2] ?? null;
                
                // Fix index for Jabatan & Promosi Date (based on TemplateController: 3=Kode Jabatan, 4=Tanggal Promosi)
                // But wait, standard might be different. Let's assume the user uses the template from TemplateController.
                // TemplateController::downloadMutasi says:
                // 0: OSIS ID
                // 1: Paket
                // 2: Tanggal Mutasi
                // 3: Kode Jabatan
                // 4: Tanggal Promosi
                
                $jabatan_excel = $row[3] ?? null;
                $tanggal_promosi_excel = $row[4] ?? null;

                // Jika OSIS ID kosong → skip
                if (empty($osis_id)) {
                    $this->log[] = "Baris " . ($index + 1) . ": SKIP - OSIS ID kosong";
                    $this->gagal++;
                    continue;
                }

                $karyawan = Karyawan::where('osis_id', $osis_id)->first();

                if (!$karyawan) {
                    $this->log[] = "Baris " . ($index + 1) . ": SKIP - Karyawan dengan OSIS ID $osis_id tidak ditemukan";
                    $this->gagal++;
                    continue;
                }

                $inserted_mutasi = false;
                $inserted_promosi = false;

                // ===== Mutasi Paket =====
                if (!empty($paket_excel) && !empty($tanggal_mutasi_excel)) {
                    $paket = Paket::where('paket', $paket_excel)->first();

                    if ($paket) {
                        $beg_date_mutasi = $this->parseDate($tanggal_mutasi_excel);

                        if ($beg_date_mutasi) {
                            PaketKaryawan::create([
                                'karyawan_id' => $karyawan->karyawan_id,
                                'paket_id' => $paket->paket_id,
                                'beg_date' => $beg_date_mutasi,
                            ]);

                            $inserted_mutasi = true;
                            $this->registerForRecalculation($paket->paket_id, $beg_date_mutasi);
                        } else {
                            $this->log[] = "Baris " . ($index + 1) . ": GAGAL Mutasi - Format Tanggal Salah";
                        }
                    } else {
                        $this->log[] = "Baris " . ($index + 1) . ": GAGAL Mutasi - Paket '$paket_excel' tidak ditemukan";
                    }
                }

                // ===== Promosi Jabatan =====
                if (!empty($jabatan_excel) && !empty($tanggal_promosi_excel)) {
                    $jabatan = Jabatan::where('kode_jabatan', $jabatan_excel)->first();

                    if ($jabatan) {
                        $beg_date_promosi = $this->parseDate($tanggal_promosi_excel);

                        if ($beg_date_promosi) {
                            Riwayat_jabatan::create([
                                'karyawan_id' => $karyawan->karyawan_id,
                                'kode_jabatan' => $jabatan->kode_jabatan,
                                'beg_date'      => $beg_date_promosi,
                            ]);

                            $inserted_promosi = true;
                            
                            // For promotion, we must find the employee's ACTIVE PACKAGE at that time to recalculate it
                            // Or just simple approach: check their latest package
                            $activePaket = DB::table('paket_karyawan')
                                ->where('karyawan_id', $karyawan->karyawan_id)
                                ->where('beg_date', '<=', $beg_date_promosi)
                                ->orderByDesc('beg_date')
                                ->first();
                                
                            if ($activePaket) {
                                $this->registerForRecalculation($activePaket->paket_id, $beg_date_promosi);
                            }
                        } else {
                            $this->log[] = "Baris " . ($index + 1) . ": GAGAL Promosi - Format Tanggal Salah";
                        }
                    } else {
                        $this->log[] = "Baris " . ($index + 1) . ": GAGAL Promosi - Jabatan '$jabatan_excel' tidak ditemukan";
                    }
                }

                // Logging hasil
                if ($inserted_mutasi && $inserted_promosi) {
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Mutasi & Promosi";
                } elseif ($inserted_mutasi) {
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Hanya Mutasi";
                } elseif ($inserted_promosi) {
                    $this->log[] = "Baris " . ($index + 1) . ": SUKSES - Hanya Promosi";
                } else {
                    if (!$inserted_mutasi && !$inserted_promosi) {
                        // Only count as fail if intended data was present but failed logic
                        if ((!empty($paket_excel) || !empty($jabatan_excel))) {
                           $this->gagal++;
                        } else {
                           $this->log[] = "Baris " . ($index + 1) . ": SKIP - Data Kosong";
                        }
                    }
                }
            }

            DB::commit();

            // === PROCESS RECALCULATION AFTER IMPORT ===
            if (!empty($this->affectedPakets)) {
                $calculator = app(ContractCalculatorService::class);
                foreach ($this->affectedPakets as $paketId => $periodes) {
                    foreach ($periodes as $periode) {
                        try {
                            $calculator->calculateForPaket($paketId, $periode);
                        } catch (\Exception $e) {
                            $this->log[] = "WARNING: Gagal hitung ulang Paket ID $paketId (Per: $periode): " . $e->getMessage();
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getLog()
    {
        return $this->log;
    }

}
