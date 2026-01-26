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
        Schema::create('kontrak_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nilai_kontrak_id')->nullable()->comment('Reference ke nilai_kontrak jika ada');
            $table->unsignedBigInteger('paket_id');
            
            // Change Information
            $table->enum('change_type', [
                'ump_change',
                'kuota_change', 
                'employee_change',
                'manual_recalculation'
            ])->comment('Tipe perubahan yang trigger recalculation');
            
            // Old vs New Values
            $table->json('old_value')->nullable()->comment('Nilai sebelum perubahan');
            $table->json('new_value')->nullable()->comment('Nilai setelah perubahan');
            $table->text('change_description')->nullable()->comment('Deskripsi detail perubahan');
            
            // Delta/Difference
            $table->decimal('old_total', 15, 2)->nullable();
            $table->decimal('new_total', 15, 2)->nullable();
            $table->decimal('delta', 15, 2)->nullable()->comment('Selisih nilai kontrak');
            
            // Metadata
            $table->timestamp('changed_at')->useCurrent();
            $table->unsignedBigInteger('changed_by')->nullable()->comment('User ID yang trigger perubahan');
            $table->timestamps();
            
            // Indexes
            $table->index('paket_id');
            $table->index('nilai_kontrak_id');
            $table->index('change_type');
            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kontrak_history');
    }
};
