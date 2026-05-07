<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PestAlert;
use App\Models\User;
use Carbon\Carbon;

class PestAlertSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        
        // Create alerts for the past 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Cecid Fly alerts
            PestAlert::create([
                'user_id' => $user->id,
                'title' => 'Cecid Fly Alert - Block ' . chr(65 + ($i % 5)),
                'description' => 'Cecid flies detected in ' . chr(65 + ($i % 5)) . ' block. Galls observed on new leaves.',
                'severity' => $this->getRandomSeverity(),
                'location' => 'Block ' . chr(65 + ($i % 5)) . ', Section ' . ($i % 3 + 1),
                'pest_type' => 'cecid_fly',
                'status' => $this->getRandomStatus(),
                'notes' => 'Regular monitoring required. Consider applying preventive measures.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Fruit Fly alerts
            PestAlert::create([
                'user_id' => $user->id,
                'title' => 'Fruit Fly Trap Alert - Area ' . ($i % 4 + 1),
                'description' => 'Increased fruit fly activity detected in trap ' . ($i % 10 + 1),
                'severity' => $this->getRandomSeverity(),
                'location' => 'Area ' . ($i % 4 + 1) . ', Trap ' . ($i % 10 + 1),
                'pest_type' => 'fruit_fly',
                'status' => $this->getRandomStatus(),
                'notes' => 'Trap needs maintenance. Consider increasing trap density.',
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Unknown pest alerts
            if ($i % 3 === 0) {
                PestAlert::create([
                    'user_id' => $user->id,
                    'title' => 'Unknown Pest Alert - Zone ' . ($i % 6 + 1),
                    'description' => 'Unidentified insect activity detected. Requires expert identification.',
                    'severity' => $this->getRandomSeverity(),
                    'location' => 'Zone ' . ($i % 6 + 1) . ', Plot ' . ($i % 8 + 1),
                    'pest_type' => 'unknown',
                    'status' => $this->getRandomStatus(),
                    'notes' => 'Sample collected for identification. Awaiting expert analysis.',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }

    private function getRandomSeverity()
    {
        $severities = ['low', 'medium', 'high'];
        return $severities[array_rand($severities)];
    }

    private function getRandomStatus()
    {
        $statuses = ['active', 'resolved', 'false_alarm'];
        return $statuses[array_rand($statuses)];
    }
} 