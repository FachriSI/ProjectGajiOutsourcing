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
        $tables = [
            'md_departemen', 'md_fungsi', 'md_jabatan', 'md_unit_kerja', 
            'md_lokasi', 'md_resiko', 'md_pakaian', 'md_ump', 
            'md_perusahaan', 'md_harianshift', 'md_penyesuaian', 
            'md_bidang', 'md_area', 'md_karyawan'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'created_at')) {
                        $table->timestamp('created_at')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'updated_at')) {
                        $table->timestamp('updated_at')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'deleted_at')) {
                        $table->timestamp('deleted_at')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'deleted_by')) {
                        $table->string('deleted_by')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'is_deleted')) {
                        $table->boolean('is_deleted')->default(0);
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'md_departemen', 'md_fungsi', 'md_jabatan', 'md_unit_kerja', 
            'md_lokasi', 'md_resiko', 'md_pakaian', 'md_ump', 
            'md_perusahaan', 'md_harianshift', 'md_penyesuaian', 
            'md_bidang', 'md_area', 'md_karyawan'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['created_at', 'updated_at', 'deleted_at', 'deleted_by', 'is_deleted']);
            });
        }
    }
};
