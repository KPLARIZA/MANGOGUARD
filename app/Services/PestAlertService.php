<?php

namespace App\Services;

use App\Models\PestData;
use App\Models\PestAlert;
use App\Models\Trap;
use App\Models\User;
use App\Notifications\PestAlertNotification;
use Carbon\Carbon;

class PestAlertService
{
    // Threshold values for different pest types
    private $thresholds = [
        'cecid_fly' => [
            'low' => 10,
            'medium' => 30,
            'high' => 50
        ],
        'fruit_fly' => [
            'low' => 5,
            'medium' => 15,
            'high' => 30
        ],
        'unknown' => [
            'low' => 3,
            'medium' => 8,
            'high' => 15
        ]
    ];

    public function processPestData(PestData $pestData)
    {
        $trap = $pestData->trap;
        $pestType = $pestData->pest_type;
        $count = $pestData->count;

        // Check if count exceeds thresholds
        $severity = $this->determineSeverity($pestType, $count);
        
        if ($severity) {
            $this->createAlert($trap, $pestType, $count, $severity);
        }

        // Check for sudden increase in pest population
        $this->checkSuddenIncrease($trap, $pestType, $count);

        // Check for continuous high activity
        $this->checkContinuousActivity($trap, $pestType);
    }

    private function determineSeverity($pestType, $count)
    {
        if (!isset($this->thresholds[$pestType])) {
            return null;
        }

        if ($count >= $this->thresholds[$pestType]['high']) {
            return 'high';
        } elseif ($count >= $this->thresholds[$pestType]['medium']) {
            return 'medium';
        } elseif ($count >= $this->thresholds[$pestType]['low']) {
            return 'low';
        }

        return null;
    }

    private function createAlert($trap, $pestType, $count, $severity, $customMessage = null)
    {
        $title = ucfirst($pestType) . ' Alert';
        $description = $customMessage ?? sprintf(
            'High %s activity detected at %s. Count: %d. Severity: %s',
            str_replace('_', ' ', $pestType),
            $trap->location,
            $count,
            $severity
        );

        $alert = PestAlert::create([
            'trap_id' => $trap->id,
            'title' => $title,
            'description' => $description,
            'severity' => $severity,
            'location' => $trap->location,
            'pest_type' => $pestType,
            'status' => 'active'
        ]);

        // Send notifications to all users
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new PestAlertNotification($alert));
        }

        return $alert;
    }

    private function checkSuddenIncrease($trap, $pestType, $currentCount)
    {
        // Get average count for the last 3 days
        $threeDaysAgo = Carbon::now()->subDays(3);
        $averageCount = PestData::where('trap_id', $trap->id)
            ->where('pest_type', $pestType)
            ->where('recorded_at', '>=', $threeDaysAgo)
            ->avg('count');

        // If current count is more than 3 times the average, create an alert
        if ($averageCount > 0 && $currentCount > ($averageCount * 3)) {
            $this->createAlert(
                $trap,
                $pestType,
                $currentCount,
                'high',
                'Sudden increase in pest population detected'
            );
        }
    }

    private function checkContinuousActivity($trap, $pestType)
    {
        // Check if there's been continuous high activity for the last 7 days
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $highActivityDays = PestData::where('trap_id', $trap->id)
            ->where('pest_type', $pestType)
            ->where('recorded_at', '>=', $sevenDaysAgo)
            ->where('count', '>=', $this->thresholds[$pestType]['medium'])
            ->count();

        if ($highActivityDays >= 5) {
            $this->createAlert(
                $trap,
                $pestType,
                0,
                'high',
                'Continuous high pest activity detected over the last week'
            );
        }
    }
} 