<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Firestore;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Firestore::class, function () {
            return (new Factory)
                ->withServiceAccount(
                    storage_path('app/firebase/mangoguarddb-firebase-adminsdk-fbsvc-79941ca510.json')
                )
                ->createFirestore();
        });
    }
}
