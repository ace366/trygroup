<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FiscalYearSeeder extends Seeder
{
    public function run(): void
    {
        // 既に1件でもあれば何もしない
        if (DB::table('fiscal_years')->exists()) {
            return;
        }

        // 今日基準で日本の会計年度（4/1〜翌3/31）を1件投入
        $today = Carbon::today();
        $year  = (int) ($today->month >= 4 ? $today->year : $today->year - 1);

        DB::table('fiscal_years')->insert([
            'year'       => $year,
            'start_date' => Carbon::create($year, 4, 1)->toDateString(),
            'end_date'   => Carbon::create($year + 1, 3, 31)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
