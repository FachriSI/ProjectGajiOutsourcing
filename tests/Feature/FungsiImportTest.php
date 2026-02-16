<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Fungsi;
use Illuminate\Http\UploadedFile;

class FungsiImportTest extends TestCase
{
    /** @test */
    public function can_import_fungsi()
    {
        $user = User::first(); 
        if (!$user) {
            $user = User::factory()->create();
        }

        // Header from TemplateController: 'Nama Fungsi', 'Keterangan'
        $header = ['"Nama Fungsi"', '"Keterangan"'];
        $row1 = ['"Fungsi Test 1"', '"Ket 1"'];

        $content = implode(',', $header) . "\n" . implode(',', $row1);
        $file = UploadedFile::fake()->createWithContent('import_fungsi.csv', $content);

        $response = $this->actingAs($user)->post('/import-fungsi', [
            'file' => $file
        ]);

        if (session('error')) {
             dump("Session Error: " . session('error'));
        }

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $fungsi = Fungsi::where('fungsi', 'Fungsi Test 1')->first();
        $this->assertNotNull($fungsi, 'Fungsi not found');
        $this->assertEquals('Ket 1', $fungsi->keterangan);

        $fungsi->delete();
    }
}
