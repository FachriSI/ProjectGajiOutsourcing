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
        Schema::table('kuota_jam', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(0)->after('beg_date');
            $table->string('deleted_by')->nullable()->after('is_deleted');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by');
            
            // Adding timestamps if they are missing, based on user report they might be useful
            // The model shows $timestamps = false, but adding them to DB is safe if we want to enable later
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kuota_jam', function (Blueprint $table) {
            $table->dropColumn(['is_deleted', 'deleted_by', 'deleted_at', 'created_at', 'updated_at']);
        });
    }
};
