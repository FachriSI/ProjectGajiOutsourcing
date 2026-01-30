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
        // 1. Rename table if old name exists and new name doesn't
        if (Schema::hasTable('paket') && !Schema::hasTable('md_paket')) {
            Schema::rename('paket', 'md_paket');
        }

        // 2. Add columns to md_paket
        Schema::table('md_paket', function (Blueprint $table) {
            // Check individual columns to prevent overlap
            if (!Schema::hasColumn('md_paket', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('md_paket', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('md_paket', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable();
            }
            if (!Schema::hasColumn('md_paket', 'deleted_by')) {
                $table->string('deleted_by', 50)->nullable();
            }
            if (!Schema::hasColumn('md_paket', 'is_deleted')) {
                $table->integer('is_deleted')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('md_paket', function (Blueprint $table) {
             $table->dropColumn(['created_at', 'updated_at', 'deleted_at', 'deleted_by', 'is_deleted']);
        });

        if (Schema::hasTable('md_paket')) {
            Schema::rename('md_paket', 'paket');
        }
    }
};
