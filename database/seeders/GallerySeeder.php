<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;
use App\Models\User;

class GallerySeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        Gallery::insert([
            [
                'user_id' => $user->id,
                'title' => 'Cecid Fly Closeup',
                'description' => 'Macro photo of a cecid fly on a mango leaf.',
                'image_path' => 'gallery/cecid_fly.jpg',
                'pest_type' => 'cecid_fly',
                'location' => 'North Orchard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Fruit Fly in Trap',
                'description' => 'Fruit fly specimen caught in a monitoring trap.',
                'image_path' => 'gallery/fruit_fly.jpg',
                'pest_type' => 'fruit_fly',
                'location' => 'South Block',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Unknown Insect',
                'description' => 'Unidentified insect pest found in the field.',
                'image_path' => 'gallery/unknown.jpg',
                'pest_type' => 'unknown',
                'location' => 'East Field',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 