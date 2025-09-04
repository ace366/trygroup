<?php
// database/seeders/ClientBaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ClientBaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $rows = [
            ['base_code' => '0001', 'base_name' => '北海道'],
            ['base_code' => '0002', 'base_name' => '東北'],
            ['base_code' => '0003', 'base_name' => '関東'],
            ['base_code' => '0004', 'base_name' => '中部'],
            ['base_code' => '0005', 'base_name' => '関西'],
            ['base_code' => '0006', 'base_name' => '中国'],
            ['base_code' => '0007', 'base_name' => '四国'],
            ['base_code' => '0008', 'base_name' => '九州'],
        ];

        foreach ($rows as $r) {
            // 既存チェック（再実行安全）
            $exists = DB::table('client_bases')->where('base_code', $r['base_code'])->exists();
            if (!$exists) {
                DB::table('client_bases')->insert([
                    'base_code'   => $r['base_code'],
                    'base_name'   => $r['base_name'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }
}
