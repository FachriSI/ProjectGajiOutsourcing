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
        Schema::create('nilai_kontrak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paket_id');
            $table->string('periode', 7); // Format: YYYY-MM (e.g., 2026-01)
            $table->year('tahun');
            $table->tinyInteger('bulan'); // 1-12
            
            // UMP & Employee Data (snapshot saat perhitungan)
            $table->decimal('ump_sumbar', 15, 2)->comment('UMP Sumbar yang digunakan');
            $table->integer('jumlah_karyawan_aktif')->default(0);
            $table->integer('jumlah_karyawan_total')->default(0)->comment('Total termasuk berhenti/diganti sampai kuota');
            $table->integer('kuota_paket')->comment('Snapshot kuota paket');
            
            // Calculated Values
            $table->decimal('total_nilai_kontrak', 15, 2)->comment('Total nilai kontrak paket');
            $table->decimal('total_pengawas', 15, 2)->default(0);
            $table->decimal('total_pelaksana', 15, 2)->default(0);
            $table->integer('jumlah_pengawas')->default(0);
            $table->integer('jumlah_pelaksana')->default(0);
            
            // Detailed Breakdown
            $table->json('breakdown_json')->nullable()->comment('Detail breakdown per karyawan');
            
            // Metadata
            $table->timestamp('calculated_at')->useCurrent();
            $table->unsignedBigInteger('calculated_by')->nullable()->comment('User ID yang trigger perhitungan');
            $table->timestamps();
            
            // Indexes
            $table->index('paket_id');
            $table->index('periode');
            $table->index(['tahun', 'bulan']);
            $table->unique(['paket_id', 'periode']); // Satu paket hanya punya satu nilai per periode
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_kontrak');
    }
};
