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
        Schema::table('karyawan', function (Blueprint $table) {
            $table->string('cp')->nullable();
            $table->string('cp_jab')->nullable();
            $table->string('cp_telp')->nullable();
            $table->string('cp_email')->nullable();
            $table->string('id_mesin')->nullable();
            $table->string('deleted_data')->nullable(); // Correspond to 'Deleted' column
            $table->string('tkp')->nullable();
            $table->string('npp')->nullable();
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
            //
        });
    }
};
