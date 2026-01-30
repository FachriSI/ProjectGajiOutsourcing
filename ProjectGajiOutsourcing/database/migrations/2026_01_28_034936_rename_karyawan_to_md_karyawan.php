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
        if (Schema::hasTable('karyawan') && !Schema::hasTable('md_karyawan')) {
            Schema::rename('karyawan', 'md_karyawan');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('md_karyawan')) {
            Schema::rename('md_karyawan', 'karyawan');
        }
    }
};
