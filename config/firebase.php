<?php

return [
    'default' => env('FIREBASE_PROJECT', 'mangoguard'),  // Changed from 'app' to 'mangoguard'

    'projects' => [
        'mangoguard' => [
            'credentials' => storage_path('app/firebase/mangoguarddb-firebase-adminsdk-fbsvc-79941ca510.json'),
            'project_id' => env('FIREBASE_PROJECT_ID', 'mangoguarddb'),
            'database' => [
                'url' => env('FIREBASE_DATABASE_URL', 'https://mangoguarddb-default-rtdb.asia-southeast1.firebasedatabase.app'),
            ],
            'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        ],
    ],
];