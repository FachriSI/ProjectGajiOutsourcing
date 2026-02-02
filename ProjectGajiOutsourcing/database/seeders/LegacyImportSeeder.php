<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyImportSeeder extends Seeder
{
    public function run()
    {
        // Data from 'positions' table in magangspnew dump
        // Format: [position_id, unit_id, name, parent_id, level, description]
        $positions = [
            [1, NULL, 'Kepala', 10, 2, NULL],
            [2, 44, 'Staff Learning ', 1, 3, NULL],
            [4, 43, 'Kepala', NULL, 2, NULL],
            [8, 44, 'Staff KM & Inovasi', 1, 3, NULL],
            [9, NULL, 'Pelaksana', 2, 4, NULL],
            [10, NULL, 'Departemen SDM', NULL, 1, NULL],
        ];

        // 1. Import Level 1 (or 'Departemen' logic) into md_departemen
        foreach ($positions as $pos) {
            if ($pos[4] == 1) { // Level 1 is Department
                // Check if exists
                $exists = DB::table('md_departemen')->where('departemen_id', $pos[0])->exists();
                if (!$exists) {
                    DB::table('md_departemen')->insert([
                        'departemen_id' => $pos[0],
                        'departemen' => $pos[2],
                        'is_si' => 0, // Default
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Start of Selection
                    DB::table('md_departemen')->where('departemen_id', $pos[0])->update([
                        'departemen' => $pos[2],
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Level > 1 is Jabatan
                // Map to md_jabatan
                // position_id -> kode_jabatan
                // name -> jabatan
                
                $exists = DB::table('md_jabatan')->where('kode_jabatan', $pos[0])->exists();
                if (!$exists) {
                    DB::table('md_jabatan')->insert([
                        'kode_jabatan' => $pos[0],
                        'jabatan' => $pos[2],
                        'tunjangan_jabatan' => 0, // Default
                        'is_deleted' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('md_jabatan')->where('kode_jabatan', $pos[0])->update([
                        'jabatan' => $pos[2],
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
