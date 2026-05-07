<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PestMonitoringSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $data = [];
        foreach (range(0, 14) as $i) {
            $data[] = [
                'trap_id' => 1,
                'pest_type' => 'cecid_fly',
                'count' => rand(10, 50),
                'recorded_at' => $now->copy()->subDays($i),
                'created_at' => $now->copy()->subDays($i),
                'updated_at' => $now->copy()->subDays($i),
            ];
            $data[] = [
                'trap_id' => 2,
                'pest_type' => 'fruit_fly',
                'count' => rand(5, 30),
                'recorded_at' => $now->copy()->subDays($i),
                'created_at' => $now->copy()->subDays($i),
                'updated_at' => $now->copy()->subDays($i),
            ];
            $data[] = [
                'trap_id' => 3,
                'pest_type' => 'unknown',
                'count' => rand(0, 5),
                'recorded_at' => $now->copy()->subDays($i),
                'created_at' => $now->copy()->subDays($i),
                'updated_at' => $now->copy()->subDays($i),
            ];
        }
        DB::table('pest_data')->insert($data);
    }
} 