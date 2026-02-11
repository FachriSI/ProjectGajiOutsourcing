<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LebaranUnlimitedSeeder extends Seeder
{
    public function run()
    {
        // Anchor: 1456 H corresponds to Label Year 2035, Date 2034-12-12
        $anchorDate = Carbon::parse('2034-12-12');
        $anchorHijri = 1456;
        $anchorLabelYear = 2035;

        // Lunar Year Length in Days
        $lunarYearLength = 354.36708;
        
        $data = [];
        $currentDaysAdded = 0;

        // Generate from 2036 to 2100
        for ($year = 2036; $year <= 2100; $year++) {
            $diffYears = $year - $anchorLabelYear;
            $currentDaysAdded = $diffYears * $lunarYearLength;
            
            $newDate = $anchorDate->copy()->addDays((int)$currentDaysAdded);
            $newHijri = $anchorHijri + $diffYears;

            $data[] = [
                'tahun' => $year,
                'tahun_hijriyah' => $newHijri . ' H',
                'tanggal' => $newDate->format('Y-m-d'),
                'keterangan' => null, // Clean as requested
                'created_at' => now(),
                'updated_at' => now(),
                'is_deleted' => 0
            ];
        }

        foreach ($data as $item) {
            DB::table('md_lebaran')->updateOrInsert(
                ['tahun' => $item['tahun']],
                $item
            );
        }
    }
}
