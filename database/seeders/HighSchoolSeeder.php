<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HighSchool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class HighSchoolSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = base_path('database/seeders/data/high_schools.csv');
        $csv = array_map('str_getcsv', file($csvFile));
        $header = array_map('trim', $csv[0]);

        foreach (array_slice($csv, 1) as $row) {
            $data = array_combine($header, $row);
            HighSchool::create([
                'id' => $data['id'],
                'name' => $data['name'],
                'department' => $data['department'],
                'deviation_value' => $data['deviation_value'],
                'school_report_ratio' => $data['school_report_ratio'] ?? null,
                'selection_criteria_url' => $data['selection_criteria_url'] ?? null,
                'has_school_selection_exam' => $data['has_school_selection_exam'] ?? 0,
                'created_at' => Carbon::parse($data['created_at']),
                'updated_at' => Carbon::parse($data['updated_at']),
                'type' => $data['type'],
            ]);
        }
    }
}
