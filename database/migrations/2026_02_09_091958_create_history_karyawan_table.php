<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_karyawan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paket_id')->nullable();
            $table->unsignedBigInteger('karyawan_id'); // Karyawan Baru (Pengganti)
            $table->unsignedBigInteger('karyawan_sebelumnya_id')->nullable(); // Karyawan Lama (Yang digantikan)
            $table->date('tanggal_diberhentikan')->nullable();
            $table->unsignedBigInteger('diberhentikan_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Foreign keys if needed, but keeping it loose for now to avoid constraint issues during development
            // $table->foreign('paket_id')->references('paket_id')->on('md_paket');
            // $table->foreign('karyawan_id')->references('karyawan_id')->on('md_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_karyawan');
    }
}
