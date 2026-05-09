<?php

return [
    'default' => env('FIREBASE_PROJECT', 'mangoguard'),

    'projects' => [
        'mangoguard' => [
            'credentials' => env(
                'FIREBASE_CREDENTIALS',
                storage_path('app/firebase/mangoguarddb-firebase-adminsdk-fbsvc-79941ca510.json')
            ),
            'project_id' => env('FIREBASE_PROJECT_ID', 'mangoguarddb'),
            'database' => [
                'url' => env('FIREBASE_DATABASE_URL', 'https://mangoguarddb-default-rtdb.asia-southeast1.firebasedatabase.app'),
            ],
            'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),

            'firestore' => [
                /** Firestore collection name for trap insect detection logs */
                'detected_logs_collection' => env('FIRESTORE_DETECTED_LOGS_COLLECTION', 'detectedLogs'),
            ],
        ],
    ],
];