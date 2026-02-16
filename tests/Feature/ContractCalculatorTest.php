<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Paket;
use App\Models\Karyawan;
use App\Models\NilaiKontrak;
use App\Models\Lebaran;
use App\Services\ContractCalculatorService;
use Carbon\Carbon;

class ContractCalculatorTest extends TestCase
{
    use DatabaseTransactions;

    protected $calculatorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculatorService = new ContractCalculatorService();
    }

    /** @test */
    public function can_calculate_monthly_contract_value()
    {
        // 0. Create UMP (Required by Service)
        \App\Models\Ump::create([
            'kode_lokasi' => '12',
            'tahun' => Carbon::now()->year,
            'ump' => 3000000,
            'is_deleted' => 0
        ]);

        // 0.5 Create Perusahaan (Required by Karyawan)
        $perusahaan = \App\Models\Perusahaan::create([
            'perusahaan' => 'PT Test Calc',
            'id_pt' => 1,
            'is_deleted' => 0
        ]);

        // 1. Create Paket
        $paketId = \Illuminate\Support\Facades\DB::table('md_paket')->insertGetId([
            'paket' => 'Paket Test Calc',
            'unit_id' => 1,
            'kuota_paket' => 5,
            'is_deleted' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $paket = Paket::find($paketId);

        // 2. Create Karyawan (Basic Salary: 1.000.000)
        $karyawan = Karyawan::create([
            'nama_tk' => 'Karyawan Test Calc',
            'nik' => '12345',
            'ktp' => '1234567890123456',
            'osis_id' => 12345,
            'perusahaan_id' => $perusahaan->perusahaan_id,
            'status_aktif' => 'Aktif',
            'gaji_pokok' => 1000000,
            'is_deleted' => 0
        ]);

        // 3. Attach Karyawan to Paket
        \Illuminate\Support\Facades\DB::table('paket_karyawan')->insert([
            'paket_id' => $paket->paket_id,
            'karyawan_id' => $karyawan->karyawan_id,
            'beg_date' => now()->format('Y-m-d'),
        ]);

        // 4. Run Calculation
        $periode = Carbon::now()->format('Y-m');
        $nilaiKontrak = $this->calculatorService->calculateForPaket($paket->paket_id, $periode);

        // 5. Verify: calculation produces a result with correct paket_id and employee data
        $this->assertNotNull($nilaiKontrak, 'NilaiKontrak should not be null');
        $this->assertEquals($paket->paket_id, $nilaiKontrak->paket_id);

        $breakdown = $nilaiKontrak->breakdown_json;
        $this->assertNotEmpty($breakdown['karyawan'], 'Breakdown should contain karyawan data');
        $this->assertGreaterThan(0, count($breakdown['karyawan']), 'Should have at least 1 karyawan in breakdown');
    }

    /** @test */
    public function can_calculate_thr_value()
    {
        // 0. Create UMP if not exists
        if (!\App\Models\Ump::where('kode_lokasi', '12')->where('tahun', Carbon::now()->year)->exists()) {
             \App\Models\Ump::create([
                'kode_lokasi' => '12',
                'tahun' => Carbon::now()->year,
                'ump' => 3000000,
                'is_deleted' => 0
            ]);
        }

        // 1. Setup Paket
        $paketId = \Illuminate\Support\Facades\DB::table('md_paket')->insertGetId([
            'paket' => 'Paket THR Test',
            'unit_id' => 1,
            'kuota_paket' => 5,
            'is_deleted' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $paket = Paket::find($paketId);

        // 2. Setup Perusahaan & Karyawan
        $perusahaan = \App\Models\Perusahaan::create(['perusahaan' => 'PT THR', 'id_pt' => 2, 'is_deleted' => 0]);

        $karyawan = Karyawan::create([
            'nama_tk' => 'Karyawan THR',
            'nik' => '999',
            'ktp' => '9999999999999999',
            'osis_id' => 99999,
            'perusahaan_id' => $perusahaan->perusahaan_id,
            'status_aktif' => 'Aktif',
            'gaji_pokok' => 2000000,
            'tkp' => 500000,
            'is_deleted' => 0
        ]);

        \Illuminate\Support\Facades\DB::table('paket_karyawan')->insert([
            'paket_id' => $paket->paket_id,
            'karyawan_id' => $karyawan->karyawan_id,
            'beg_date' => now()->format('Y-m-d'),
        ]);

        // 3. Setup Lebaran Data
        $tahun = Carbon::now()->year;
        $lebaran = Lebaran::create([
            'tahun' => $tahun,
            'tanggal' => Carbon::now()->addMonths(1)->format('Y-m-d'),
            'keterangan' => 'Idul Fitri Test',
            'is_deleted' => 0
        ]);

        // 4. Calculate Monthly Contract first (prerequisite for THR)
        $periode = Carbon::now()->format('Y-m');
        $nilaiKontrak = $this->calculatorService->calculateForPaket($paket->paket_id, $periode);

        // 5. Test Cetak THR Logic
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get("/kalkulator-kontrak/cetak-thr/{$paket->paket_id}?periode={$periode}");

        // Status 200 means the THR logic executed and returned a PDF stream
        $response->assertStatus(200);
    }
}
