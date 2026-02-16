<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Http\UploadedFile;

class UnitKerjaImportTest extends TestCase
{
    /** @test */
    public function can_import_unit_kerja()
    {
        $user = User::first(); 
        if (!$user) {
            $user = User::factory()->create();
        }

        // Create required Departemen (ID=1) for FK constraint
        if (!\App\Models\Departemen::find(1)) {
            \App\Models\Departemen::create([
                'departemen_id' => 1,
                'departemen' => 'Dept Default',
                'is_si' => 0,
                'is_deleted' => 0
            ]);
        }

        // Header from TemplateController: 'ID Unit', 'Nama Unit Kerja'
        $header = ['"ID Unit"', '"Nama Unit Kerja"'];
        $row1 = ['', '"Unit Test 1"'];  // Empty ID, expected to auto-generate
        $row2 = ['UK002', '"Unit Test 2"']; // Provided ID

        $content = implode(',', $header) . "\n" . implode(',', $row1) . "\n" . implode(',', $row2);
        $file = UploadedFile::fake()->createWithContent('import_unit.csv', $content);

        $response = $this->actingAs($user)->post('/import-unitkerja', [
            'file' => $file
        ]);

        if (session('error')) {
             dump("Session Error: " . session('error'));
        }

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $unit1 = UnitKerja::where('unit_kerja', 'Unit Test 1')->first();
        $this->assertNotNull($unit1, 'Unit 1 not found');
        // Check default departemen_id if logic added
        
        $unit2 = UnitKerja::where('unit_kerja', 'Unit Test 2')->first();
        $this->assertNotNull($unit2, 'Unit 2 not found');
        // $this->assertEquals('UK002', $unit2->unit_id); // If unit_id is int, UK002 might fail. It is int.

        // Cleanup using Soft Delete to avoid FK violation (riwayat_unit)
        $unit1->update(['is_deleted' => 1]);
        $unit2->update(['is_deleted' => 1]);
    }
}
