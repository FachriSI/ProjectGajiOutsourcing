<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('aksi', 50); // 'Dibuat', 'Diubah', 'Dihapus'
            $table->string('diubah_oleh', 100)->nullable(); // user who made the change
            $table->text('detail')->nullable(); // description of changes
            $table->json('data_lama')->nullable(); // old data snapshot
            $table->json('data_baru')->nullable(); // new data snapshot
            $table->timestamp('waktu')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
