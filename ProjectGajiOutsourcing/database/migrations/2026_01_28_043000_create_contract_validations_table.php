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
        Schema::create('contract_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nilai_kontrak_id');
            $table->string('validation_token', 255)->unique();
            $table->string('document_hash', 255);
            $table->string('pdf_path', 500)->nullable();
            $table->boolean('is_valid')->default(true);
            $table->datetime('generated_at');
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->datetime('validated_at')->nullable();
            $table->integer('validation_count')->default(0);
            $table->string('last_validated_ip', 45)->nullable();
            $table->datetime('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('nilai_kontrak_id')
                  ->references('id')
                  ->on('nilai_kontrak')
                  ->onDelete('cascade');
            
            $table->foreign('generated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Indexes
            $table->index('validation_token');
            $table->index('nilai_kontrak_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_validations');
    }
};
