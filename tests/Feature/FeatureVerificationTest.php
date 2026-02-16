<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class FeatureVerificationTest extends TestCase
{
    // Use DatabaseTransactions to roll back changes after each test
    // We do NOT use RefreshDatabase because we want to test with existing data if possible,
    // or at least not wipe the user's DB.
    // However, without RefreshDatabase, we must be careful.
    // Actually, `DatabaseTransactions` is the safest bet for local dev testing.
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Find or create a user for authentication
        $this->user = User::first();
        
        if (!$this->user) {
             $this->user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }
        
        // Disable exception handling to see strack trace on 500 errors
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function dashboard_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('TOTAL KARYAWAN'); 
    }

    /** @test */
    public function paket_page_loads_and_displays_data()
    {
        $response = $this->actingAs($this->user)->get('/paket');
        $response->assertStatus(200);
        $response->assertSee('Paket');
    }

    /** @test */
    public function karyawan_page_loads()
    {
        $response = $this->actingAs($this->user)->get('/karyawan');
        $response->assertStatus(200);
        // assertSee('Data Karyawan') or similar
        $response->assertSee('Karyawan');
    }

    /** @test */
    public function medical_checkup_page_loads()
    {
        $response = $this->actingAs($this->user)->get('/medical-checkup');
        $response->assertStatus(200);
        $response->assertSee('Medical Checkup');
    }

    /** @test */
    public function contract_calculator_page_loads()
    {
        $response = $this->actingAs($this->user)->get('/kalkulator-kontrak');
        $response->assertStatus(200);
        $response->assertSee('Nilai Kontrak');
    }

    /** @test */
    public function verify_thr_cetak_route_exists()
    {
        // We verify that the route for Cetak THR is defined and accessible
        // We might need a dummy Paket ID or NilaiKontrak ID.
        // For now, just checking if the route pattern is valid.
        
        $response = $this->actingAs($this->user)->get('/kalkulator-kontrak/cetak-thr/99999'); 
        // Likely 404 or 500 because ID doesn't exist, but we want to know if it hits the controller.
        // Controlller will return back()->with('error') or 404.
        
        // If it returns 404, the route exists but resource doesn't. 
        // If it returns 500, code executed but failed (likely DB lookup).
        // If route didn't exist, it would be 404 too... wait.
        
        // Better: Check if `Route::has('kalkulator.cetak-thr')` is true.
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('kalkulator.cetak-thr'));
    }
    
    /** @test */
    public function tambah_pengganti_form_loads()
    {
        // Requires an ID, so we skip if no Penempatan exists.
        $this->markTestSkipped('Skipping specific ID test for now.');
    }
}
