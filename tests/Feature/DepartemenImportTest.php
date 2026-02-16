<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\UploadedFile;

class DepartemenImportTest extends TestCase
{
    /** @test */
    public function can_import_departemen()
    {
        $user = User::first(); 
        if (!$user) {
            $user = User::factory()->create();
        }

        // Header must match TemplateController
        // We use quotes to handle the comma in the header name
        $header = ['"Nama Departemen"', '"Is SI (1=Ya, 0=Tidak)"'];
        $row1 = ['"Dept Import Test 1"', '"1"'];
        $row2 = ['"Dept Import Test 2"', '"0"'];

        // Create CSV manually with verification of quotes
        $content = implode(',', $header) . "\n" . implode(',', $row1) . "\n" . implode(',', $row2);
        $file = UploadedFile::fake()->createWithContent('import_departemen.csv', $content);

        // Submitting to the route found in ImportController
        $response = $this->actingAs($user)->post('/import-departemen', [
            'file' => $file
        ]);

        if (session('error')) {
             dump("Session Error: " . session('error'));
        }

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Check DB
        $dept1 = Departemen::where('departemen', 'Dept Import Test 1')->first();
        $this->assertNotNull($dept1, 'Dept 1 not found');
        $this->assertEquals(1, $dept1->is_si);
        $this->assertEquals(0, $dept1->is_deleted);

        $dept2 = Departemen::where('departemen', 'Dept Import Test 2')->first();
        $this->assertNotNull($dept2, 'Dept 2 not found');
        $this->assertEquals(0, $dept2->is_si);

        // Cleanup
        $dept1->delete();
        $dept2->delete();
    }
}
