<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeatureActionTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::first();
        if (!$this->user) {
            $this->user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test_action@example.com',
                'password' => bcrypt('password'),
            ]);
        }
        
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function test_can_create_employee()
    {
        // 1. Setup Master Data
        $deptId = rand(1000, 9999);
        DB::table('md_departemen')->insert([
            'departemen_id' => $deptId,
            'departemen' => 'Dept Test',
            'is_si' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $perusahaanId = DB::table('md_perusahaan')->insertGetId([
            'id_pt' => rand(1, 99),
            'perusahaan' => 'PT Test',
            'alamat' => 'Jl. Test',
            'cp' => 'Budi',
            'cp_jab' => 'Manager',
            'cp_telp' => '08123456789',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $unitId = rand(1000, 9999);
        DB::table('md_unit_kerja')->insert([
            'unit_id' => $unitId,
            'unit_kerja' => 'Unit Test',
            'departemen_id' => $deptId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $paketId = DB::table('md_paket')->insertGetId([
            'paket' => 'Paket Test',
            'kuota_paket' => 10,
            'unit_id' => $unitId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // 2. Data to Submit
        $data = [
            'osis_id' => '9999',
            'ktp' => '1234567890123456',
            'nama' => 'Test Employee',
            'perusahaan' => $perusahaanId,
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status' => 'T',
            'alamat' => 'Jl. Test',
            'paket_id' => $paketId,
        ];

        // 3. Post Request
        $response = $this->actingAs($this->user)->post('/tambah-karyawan', $data);

        // 4. Assert
        $response->assertStatus(302);
        $response->assertRedirect('/karyawan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('md_karyawan', [
            'osis_id' => '9999',
            'ktp' => '1234567890123456',
            'nama_tk' => 'Test Employee'
        ]);
    }

    /** @test */
    public function test_cannot_create_employee_with_invalid_osis_or_ktp()
    {
        $this->withExceptionHandling(); // Enable normal error handling for validation

        $data = [
            'osis_id' => '123', // Invalid: needs 4 digits
            'ktp' => '123', // Invalid: needs 16 digits
            'nama' => 'Test Invalid',
            // other fields omitted
        ];

        $response = $this->actingAs($this->user)->post('/tambah-karyawan', $data);

        $response->assertSessionHasErrors(['osis_id', 'ktp']);
    }

    /** @test */
    public function test_cannot_add_pengganti_with_invalid_osis_or_ktp()
    {
        $this->withExceptionHandling();

        $perusahaanId = DB::table('md_perusahaan')->insertGetId([
            'id_pt' => rand(1, 99),
            'perusahaan' => 'PT Test Val',
            'alamat' => 'Alamat',
            'created_at' => now(),
             'updated_at' => now()
        ]);

        $karyawanId = DB::table('md_karyawan')->insertGetId([
            'nama_tk' => 'Old Employee',
            'status_aktif' => 'Aktif',
            'osis_id' => '8888',
            'ktp' => '8888888888888888',
            'perusahaan_id' => $perusahaanId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $data = [
            'osis_id' => '12', 
            'ktp' => '12',
            'nama' => 'Pengganti Invalid',
            'tanggal_lahir' => '2000-01-01',
            'tanggal_bekerja' => now()->format('Y-m-d')
        ];

        $response = $this->actingAs($this->user)->post("/simpan-pengganti/{$karyawanId}", $data);

        $response->assertSessionHasErrors(['osis_id', 'ktp']);
    }

    /** @test */
    public function test_can_generate_pdf_route()
    {
        $this->withoutExceptionHandling(); 

        $deptId = rand(1000, 9999);
        DB::table('md_departemen')->insert([
            'departemen_id' => $deptId,
            'departemen' => 'Dept PDF',
            'is_si' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $unitId = rand(1000, 9999);
        DB::table('md_unit_kerja')->insert([
            'unit_id' => $unitId,
            'unit_kerja' => 'Unit PDF',
             'departemen_id' => $deptId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $paketId = DB::table('md_paket')->insertGetId([
            'paket' => 'Paket PDF Test',
            'kuota_paket' => 5,
            'unit_id' => $unitId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $tahun = date('Y');
        DB::table('nilai_kontrak')->insert([
            'paket_id' => $paketId,
            'periode' => date('Y-m'),
            'tahun' => $tahun,
            'bulan' => date('m'),
            'ump_sumbar' => 2500000,
            'kuota_paket' => 5,
            'total_nilai_kontrak' => 1000000,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
         $perusahaanId = DB::table('md_perusahaan')->insertGetId([
            'id_pt' => rand(1, 99),
            'perusahaan' => 'PT PDF',
            'alamat' => 'Alamat PDF',
            'created_at' => now(),
             'updated_at' => now()
        ]);
        
        $karyawanId = DB::table('md_karyawan')->insertGetId([
            'nama_tk' => 'Emp PDF',
            'status_aktif' => 'Aktif',
            'osis_id' => '1234',
            'ktp' => '1234567890123456',
            'perusahaan_id' => $perusahaanId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('paket_karyawan')->insert([
            'paket_id' => $paketId,
            'karyawan_id' => $karyawanId,
            'beg_date' => now()->format('Y-m-d')
        ]);

        $response = $this->actingAs($this->user)->get("/paket/{$paketId}/pdf"); 
        
        $status = $response->status();
        $this->assertTrue(in_array($status, [200, 302])); 
    }

    /** @test */
    public function test_can_access_thr_route()
    {
         $deptId = rand(1000, 9999);
         DB::table('md_departemen')->insert([
            'departemen_id' => $deptId,
            'departemen' => 'Dept THR',
            'is_si' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

         $unitId = rand(1000, 9999);
         DB::table('md_unit_kerja')->insert([
            'unit_id' => $unitId,
            'unit_kerja' => 'Unit THR',
            'departemen_id' => $deptId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $paketId = DB::table('md_paket')->insertGetId([
            'paket' => 'Paket THR Test',
            'kuota_paket' => 10,
            'unit_id' => $unitId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $tahun = date('Y');
        
        DB::table('nilai_kontrak')->insert([
            'paket_id' => $paketId,
            'periode' => date('Y-m'),
            'tahun' => $tahun,
            'bulan' => date('m'),
            'ump_sumbar' => 2500000,
            'kuota_paket' => 10,
            'total_nilai_kontrak' => 1000000,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('md_lebaran')->insert([
            'tahun' => $tahun,
            'tanggal' => date('Y-m-d'),
            'is_deleted' => 0,
            'created_at' => now()
        ]);

        $response = $this->actingAs($this->user)->get("/kalkulator-kontrak/cetak-thr/{$paketId}");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
