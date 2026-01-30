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
        if (Schema::hasTable('perusahaan') && !Schema::hasTable('md_perusahaan')) {
            Schema::rename('perusahaan', 'md_perusahaan');
        }
        if (Schema::hasTable('harianshift') && !Schema::hasTable('md_harianshift')) {
            Schema::rename('harianshift', 'md_harianshift');
        }
        if (Schema::hasTable('penyesuaian') && !Schema::hasTable('md_penyesuaian')) {
            Schema::rename('penyesuaian', 'md_penyesuaian');
        }
        if (Schema::hasTable('bidang') && !Schema::hasTable('md_bidang')) {
            Schema::rename('bidang', 'md_bidang');
        }
        if (Schema::hasTable('area') && !Schema::hasTable('md_area')) {
            Schema::rename('area', 'md_area');
        }
    }

    public function down()
    {
        if (Schema::hasTable('md_perusahaan')) Schema::rename('md_perusahaan', 'perusahaan');
        if (Schema::hasTable('md_harianshift')) Schema::rename('md_harianshift', 'harianshift');
        if (Schema::hasTable('md_penyesuaian')) Schema::rename('md_penyesuaian', 'penyesuaian');
        if (Schema::hasTable('md_bidang')) Schema::rename('md_bidang', 'bidang');
        if (Schema::hasTable('md_area')) Schema::rename('md_area', 'area');
    }
};
