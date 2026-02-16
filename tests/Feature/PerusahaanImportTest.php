<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Perusahaan;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

class PerusahaanImportTest extends TestCase
{
    // use RefreshDatabase; // Don't use refresh to avoid wiping master data if not necessary, or use it if we have seeders. 
    // The previous tests didn't seem to use RefreshDatabase on the main DB?
    // Actually FeatureActionTest used DB::beginTransaction() or similar? No, it just inserted.
    // I'll manually clean up the test data.

    /** @test */
    public function can_import_perusahaan_with_template_baru()
    {
        $user = User::first(); // Assuming a user exists
        if (!$user) {
            $user = User::factory()->create();
        }

        $header = ['No', 'Nama Perusahaan', 'Alamat', 'Contact Person (CP)', 'Jabatan CP', 'No Telp CP', 'Email CP', 'ID Mesin (Fingerprint)', 'Deleted (0/1)', 'TKP', 'NPP'];
        $row = ['1', 'PT Import Test', 'Jl. Import', 'Budi Import', 'Mgr', '08123', 'budi@import.com', '999', '1', 'TKP-Test', 'NPP-Test'];

        // Create CSV in memory
        $content = implode(',', $header) . "\n" . implode(',', $row);
        $file = UploadedFile::fake()->createWithContent('import_perusahaan.csv', $content);

        $response = $this->actingAs($user)->post('/import-template-baru', [
            'file' => $file
        ]);

        if (session('error')) {
            dump("Session Error: " . session('error'));
        }
        if (session('errors')) {
            dump("Validation Errors: " . session('errors')->first('file'));
        }

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $perusahaan = Perusahaan::where('perusahaan', 'PT Import Test')->first();
        $this->assertNotNull($perusahaan, 'Perusahaan not found in DB');
        
        $this->assertEquals('Jl. Import', $perusahaan->alamat);
        $this->assertEquals('TKP-Test', $perusahaan->tkp);
        $this->assertEquals('NPP-Test', $perusahaan->npp);
        
        // This assertion is expected to fail currently
        // The import sets 'deleted_data' => 1, but model ignores it. 'is_deleted' defaults to 0.
        $this->assertEquals(1, $perusahaan->is_deleted, 'is_deleted should be 1');
        
        // Cleanup
        if ($perusahaan) $perusahaan->delete();
    }
}
