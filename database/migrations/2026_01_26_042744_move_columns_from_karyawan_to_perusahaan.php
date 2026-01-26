<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns to perusahaan
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->string('cp')->nullable();
            $table->string('cp_jab')->nullable();
            $table->string('cp_telp')->nullable();
            $table->string('cp_email')->nullable();
            $table->string('id_mesin')->nullable();
            $table->string('deleted_data')->nullable();
            $table->string('tkp')->nullable();
            $table->string('npp')->nullable();
            $table->string('alamat')->nullable(); // Alamat might be needed if not present
        });

        // Drop columns from karyawan
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn([
                'cp',
                'cp_jab',
                'cp_telp',
                'cp_email',
                'id_mesin',
                'deleted_data',
                'tkp',
                'npp'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->string('cp')->nullable();
            $table->string('cp_jab')->nullable();
            $table->string('cp_telp')->nullable();
            $table->string('cp_email')->nullable();
            $table->string('id_mesin')->nullable();
            $table->string('deleted_data')->nullable();
            $table->string('tkp')->nullable();
            $table->string('npp')->nullable();
        });

        Schema::table('perusahaan', function (Blueprint $table) {
            $table->dropColumn([
                'cp',
                'cp_jab',
                'cp_telp',
                'cp_email',
                'id_mesin',
                'deleted_data',
                'tkp',
                'npp',
                'alamat'
            ]);
        });
    }
};
