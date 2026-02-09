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
        Schema::create('md_lebaran', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->string('tahun_hijriyah')->nullable(); // 1445 H
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->boolean('is_deleted')->default(false);
            $table->string('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('md_lebaran');
    }
};
