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
        Schema::table('masa_kerja', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(0)->after('beg_date');
            $table->string('deleted_by')->nullable()->after('is_deleted');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by');
            
            // Adding timestamps if missing, to be consistent
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
        Schema::table('masa_kerja', function (Blueprint $table) {
            $table->dropColumn(['is_deleted', 'deleted_by', 'deleted_at', 'created_at', 'updated_at']);
        });
    }
};
