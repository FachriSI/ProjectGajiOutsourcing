<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MasterUkuran;

class MasterUkuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'];

        foreach ($sizes as $size) {
            MasterUkuran::firstOrCreate(['nama_ukuran' => $size]);
        }
    }
}
