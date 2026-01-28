<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('departemen', 'md_departemen');
        Schema::rename('fungsi', 'md_fungsi');
        Schema::rename('jabatan', 'md_jabatan');
        Schema::rename('unit_kerja', 'md_unit_kerja');
        Schema::rename('lokasi', 'md_lokasi');
        Schema::rename('resiko', 'md_resiko');
        Schema::rename('pakaian', 'md_pakaian');
        Schema::rename('ump', 'md_ump');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('md_departemen', 'departemen');
        Schema::rename('md_fungsi', 'fungsi');
        Schema::rename('md_jabatan', 'jabatan');
        Schema::rename('md_unit_kerja', 'unit_kerja');
        Schema::rename('md_lokasi', 'lokasi');
        Schema::rename('md_resiko', 'resiko');
        Schema::rename('md_pakaian', 'pakaian');
        Schema::rename('md_ump', 'ump');
    }
};
