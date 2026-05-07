<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PestData;
use App\Models\PestType;
use App\Models\Trap;
use Carbon\Carbon;

class PestDataSeeder extends Seeder
{
    public function run()
    {
        $pestTypes = PestType::all();
        $traps = Trap::where('status', 'active')->get();

        // Generate data for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            foreach ($traps as $trap) {
                // Generate 1-3 pest detections per trap per day
                $detections = rand(1, 3);
                
                for ($j = 0; $j < $detections; $j++) {
                    $pestType = $pestTypes->random();
                    $count = rand(1, 15); // Random count of pests
                    
                    // Generate random temperature and humidity
                    $temperature = rand(20, 35) + (rand(0, 100) / 100);
                    $humidity = rand(40, 90) + (rand(0, 100) / 100);
                    
                    // Random time during the day
                    $detectedAt = $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                    
                    PestData::create([
                        'pest_type_id' => $pestType->id,
                        'trap_id' => $trap->id,
                        'count' => $count,
                        'location' => $trap->location,
                        'image_path' => $pestType->image_path,
                        'detected_at' => $detectedAt,
                        'temperature' => $temperature,
                        'humidity' => $humidity,
                        'notes' => 'Automatically generated sample data'
                    ]);
                }
            }
        }
    }
} 