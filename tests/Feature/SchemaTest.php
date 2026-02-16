<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    /** @test */
    public function check_tables_schema()
    {
        $fungsi = \Illuminate\Support\Facades\DB::select("DESCRIBE md_fungsi");
        $unit = \Illuminate\Support\Facades\DB::select("DESCRIBE md_unit_kerja");
        file_put_contents('schema_dump.txt', "md_fungsi:\n" . print_r($fungsi, true) . "\n\nmd_unit_kerja:\n" . print_r($unit, true));
        $this->assertTrue(true);
    }
}
