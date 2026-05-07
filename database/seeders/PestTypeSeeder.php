<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PestType;

class PestTypeSeeder extends Seeder
{
    public function run()
    {
        $pests = [
            [
                'name' => 'Mango Fruit Fly',
                'scientific_name' => 'Bactrocera dorsalis',
                'description' => 'A major pest of mango that causes fruit damage and premature dropping.',
                'threshold_value' => 5,
                'control_methods' => [
                    'Use protein bait sprays',
                    'Implement male annihilation technique',
                    'Practice orchard sanitation',
                    'Apply spinosad-based baits'
                ],
                'image_path' => 'pests/mango-fruit-fly.jpg'
            ],
            [
                'name' => 'Mango Weevil',
                'scientific_name' => 'Sternochetus mangiferae',
                'description' => 'Attacks mango seeds and can cause significant yield loss.',
                'threshold_value' => 3,
                'control_methods' => [
                    'Apply soil drenching with insecticides',
                    'Use pheromone traps',
                    'Maintain proper orchard hygiene',
                    'Implement quarantine measures'
                ],
                'image_path' => 'pests/mango-weevil.jpg'
            ],
            [
                'name' => 'Mango Scale',
                'scientific_name' => 'Aulacaspis tubercularis',
                'description' => 'Sucks plant sap and can cause leaf yellowing and fruit damage.',
                'threshold_value' => 10,
                'control_methods' => [
                    'Apply horticultural oil',
                    'Use systemic insecticides',
                    'Prune infested branches',
                    'Maintain tree health'
                ],
                'image_path' => 'pests/mango-scale.jpg'
            ],
            [
                'name' => 'Mango Leafhopper',
                'scientific_name' => 'Idioscopus clypealis',
                'description' => 'Causes leaf curling and reduces fruit quality.',
                'threshold_value' => 8,
                'control_methods' => [
                    'Apply neem-based sprays',
                    'Use yellow sticky traps',
                    'Maintain proper tree spacing',
                    'Implement biological control'
                ],
                'image_path' => 'pests/mango-leafhopper.jpg'
            ],
            [
                'name' => 'Mango Mealybug',
                'scientific_name' => 'Drosicha mangiferae',
                'description' => 'Sucks plant sap and secretes honeydew, leading to sooty mold.',
                'threshold_value' => 7,
                'control_methods' => [
                    'Apply soap solution',
                    'Use systemic insecticides',
                    'Implement banding technique',
                    'Maintain natural predators'
                ],
                'image_path' => 'pests/mango-mealybug.jpg'
            ]
        ];

        foreach ($pests as $pest) {
            PestType::create($pest);
        }
    }
} 