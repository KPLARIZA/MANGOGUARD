<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PestAdvice;
use App\Models\User;

class PestAdviceSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        PestAdvice::insert([
            [
                'user_id' => $user->id,
                'title' => 'Cecid Fly Management',
                'content' => 'Monitor new growth for galls. Prune and destroy infested leaves. Apply systemic insecticides if needed.',
                'pest_type' => 'cecid_fly',
                'severity' => 'high',
                'preventive_measures' => 'Regular monitoring, proper tree spacing.',
                'control_methods' => 'Pruning, insecticide application.',
                'chemical_treatments' => 'Use recommended systemic insecticides.',
                'organic_treatments' => 'Neem oil spray.',
                'monitoring_tips' => 'Inspect early morning.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Fruit Fly Control',
                'content' => 'Use traps and bagging. Remove fallen fruits. Apply bait sprays as needed.',
                'pest_type' => 'fruit_fly',
                'severity' => 'medium',
                'preventive_measures' => 'Sanitation, regular trapping.',
                'control_methods' => 'Traps, bagging, bait sprays.',
                'chemical_treatments' => 'Protein bait sprays.',
                'organic_treatments' => 'Vinegar traps.',
                'monitoring_tips' => 'Check traps weekly.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Unknown Pest Response',
                'content' => 'Document and collect samples. Consult with experts for identification.',
                'pest_type' => 'unknown',
                'severity' => 'low',
                'preventive_measures' => 'Regular scouting.',
                'control_methods' => 'Isolation and monitoring.',
                'chemical_treatments' => null,
                'organic_treatments' => null,
                'monitoring_tips' => 'Photograph and record findings.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 