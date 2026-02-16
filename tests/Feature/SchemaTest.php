<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class SchemaTest extends TestCase
{
    /** @test */
    public function check_tables_schema()
    {
            $tables = ['md_unit_kerja', 'md_paket', 'md_karyawan'];
            file_put_contents('schema_dump.txt', "");
            foreach ($tables as $t) {
                $result = \Illuminate\Support\Facades\DB::select("DESCRIBE $t");
                file_put_contents('schema_dump.txt', "$t:\n" . print_r($result, true), FILE_APPEND);
            }
            file_put_contents('schema_dump.txt', "Error: " . $e->getMessage());
        }
        $this->assertTrue(true);
    }
}
