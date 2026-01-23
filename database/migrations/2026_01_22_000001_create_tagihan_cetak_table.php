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
        Schema::create('tagihan_cetak', function (Blueprint $table) {
            $table->id('cetak_id');
            $table->unsignedBigInteger('paket_id');
            $table->string('token', 64)->unique();
            $table->decimal('total_boq', 15, 2);
            $table->integer('jumlah_pengawas')->default(0);
            $table->integer('jumlah_pelaksana')->default(0);
            $table->string('vendor', 255)->nullable();
            $table->timestamp('tanggal_cetak')->useCurrent();
            $table->timestamps();

            $table->index('paket_id');
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tagihan_cetak');
    }
};
