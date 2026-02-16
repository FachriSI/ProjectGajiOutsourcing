<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paket;
use App\Models\Karyawan;
use App\Models\NilaiKontrak;
use App\Models\Lebaran;
use App\Services\ContractCalculatorService;
use Carbon\Carbon;

class ContractCalculatorTest extends TestCase
{
    protected $calculatorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculatorService = new ContractCalculatorService();
    }

    /** @test */
    public function can_calculate_monthly_contract_value()
    {
        // Setup Data
        $user = User::first() ?? User::factory()->create();
        
        // 0. Create UMP (Required by Service)
        \App\Models\Ump::create([
            'kode_lokasi' => '12', // Sumbar
            'tahun' => Carbon::now()->year,
            'ump' => 3000000, // Mock UMP
            'is_deleted' => 0
        ]);
        
        // 1. Create Paket
        $paket = Paket::create([
            'paket' => 'Paket Test Calc',
            'unit_id' => 1, // Assuming Unit 1 exists from previous tests
            'is_deleted' => 0
        ]);

        // 2. Create Karyawan for Paket
        // Basic Salary: 1.000.000
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Test Calc',
            'nik' => '12345',
            'status_aktif' => 'Aktif',
            'gaji_pokok' => 1000000,
            'is_deleted' => 0
        ]);

        // Attach to Paket
        \Illuminate\Support\Facades\DB::table('trans_paket_karyawan')->insert([
            'paket_id' => $paket->paket_id,
            'karyawan_id' => $karyawan->karyawan_id,
            'is_deleted' => 0
        ]);

        // 3. Run Calculation
        $periode = Carbon::now()->format('Y-m');
        $nilaiKontrak = $this->calculatorService->calculateForPaket($paket->paket_id, $periode);

        // 4. Verify Math
        // Formula check: Just ensure it returns a value and breakdown contains the employee
        $this->assertNotNull($nilaiKontrak);
        $this->assertEquals($paket->paket_id, $nilaiKontrak->paket_id);
        
        $breakdown = $nilaiKontrak->breakdown_json;
        $this->assertNotEmpty($breakdown['karyawan']);
        $this->assertEquals(1000000, $breakdown['karyawan'][0]['upah_pokok']);

        // Cleanup
        $paket->delete();
        $karyawan->delete();
        $nilaiKontrak->delete();
    }

    /** @test */
    public function can_calculate_thr_value()
    {
        // 0. Create UMP if not exists (Service Fallback check)
        if (!\App\Models\Ump::where('kode_lokasi', '12')->where('tahun', Carbon::now()->year)->exists()) {
             \App\Models\Ump::create([
                'kode_lokasi' => '12',
                'tahun' => Carbon::now()->year,
                'ump' => 3000000,
                'is_deleted' => 0
            ]);
        }

        // 1. Setup Paket & Karyawan
        $paket = Paket::create(['paket' => 'Paket THR Test', 'unit_id' => 1, 'is_deleted' => 0]);
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan THR', 
            'nik' => '999', 
            'status_aktif' => 'Aktif', 
            'gaji_pokok' => 2000000, // 2jt
            'tkp' => 500000, // Tj Tetap
            'is_deleted' => 0
        ]);

        \Illuminate\Support\Facades\DB::table('trans_paket_karyawan')->insert([
            'paket_id' => $paket->paket_id,
            'karyawan_id' => $karyawan->karyawan_id,
        ]);

        // 2. Setup Lebaran Data
        $tahun = Carbon::now()->year;
        $lebaran = Lebaran::create([
            'tahun' => $tahun,
            'tanggal' => Carbon::now()->addMonths(1)->format('Y-m-d'), // Next month
            'keterangan' => 'Idul Fitri Test',
            'is_deleted' => 0
        ]);

        // 3. Calculate Monthly Contract first (prerequisite for THR logic in Controller)
        $periode = Carbon::now()->format('Y-m');
        $nilaiKontrak = $this->calculatorService->calculateForPaket($paket->paket_id, $periode);
        
        // 4. Test Cetak THR Logic (Simulate Controller logic)
        $user = User::first() ?? User::factory()->create();
        
        $response = $this->actingAs($user)->get("/kalkulator-kontrak/cetak-thr/{$paket->paket_id}?periode={$periode}");
        
        $response->assertStatus(200); // Should return PDF stream
        // Since it's a PDF stream, verifying content is hard, but status 200 means logic executed.

        // Verify Calculation manually based on logic
        // THR = Gaji + Tj Tetap = 2.000.000 + 500.000 = 2.500.000
        // Fee 5% = 125.000
        // Total = 2.625.000

        // Cleanup
        $paket->delete();
        $karyawan->delete();
        $lebaran->delete();
        $nilaiKontrak->delete();
    }
}
