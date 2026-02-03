<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_karyawan', function (Blueprint $table) {
            // ID unik untuk riwayat
            $table->id('riwayat_id');

            // Snapshot semua data karyawan lama
            $table->unsignedBigInteger('karyawan_id'); // ID karyawan yang diganti
            $table->string('osis_id')->nullable();
            $table->string('ktp')->nullable();
            $table->string('nama_tk');
            $table->unsignedBigInteger('perusahaan_id');
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 1)->nullable();
            $table->string('agama')->nullable();
            $table->string('status', 1)->nullable();
            $table->text('alamat')->nullable();
            $table->string('asal')->nullable();
            $table->date('tanggal_bekerja')->nullable();
            $table->date('tahun_pensiun')->nullable();
            $table->date('tanggal_pensiun')->nullable();

            // Info penggantian
            $table->text('catatan_berhenti')->nullable(); // Alasan berhenti
            $table->date('tanggal_berhenti'); // Kapan diberhentikan
            $table->string('diganti_dengan_nama'); // Nama karyawan pengganti
            $table->unsignedBigInteger('diganti_oleh')->nullable(); // User yang melakukan penggantian

            $table->timestamps();

            // Index untuk performa query
            $table->index('karyawan_id');
            $table->index('tanggal_berhenti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_karyawan');
    }
};
