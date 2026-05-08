<?php

namespace App\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Auth as KreaitFirebaseAuth;
use Kreait\Firebase\Contract\Auth as FirebaseAuthContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureFirebaseCertificates();
        $this->registerFirebaseAuthConcreteRedirect();
    }

    /**
     * Kreait Laravel only binds Firebase\Auth as an interface singleton. Code that incorrectly
     * resolves app(Kreait\Firebase\Auth::class) would try to instantiate ApiClient without a
     * project id — do not patch vendor; redirect the concrete class name to the same singleton.
     */
    private function registerFirebaseAuthConcreteRedirect(): void
    {
        if (! $this->app->bound(FirebaseAuthContract::class)) {
            return;
        }

        $this->app->singleton(KreaitFirebaseAuth::class, function (Container $app): FirebaseAuthContract {
            /** @var FirebaseAuthContract */
            return $app->make(FirebaseAuthContract::class);
        });
    }

    /**
     * Ensure PHP cURL/OpenSSL trusts a CA bundle before any Firebase/Google HTTP calls (Windows/dev).
     */
    private function configureFirebaseCertificates(): void
    {
        $caBundlePath = (string) env('FIREBASE_CA_BUNDLE', '');
        $caBundlePath = trim($caBundlePath, "\"' ");
        $caBundlePath = str_replace('\\', DIRECTORY_SEPARATOR, $caBundlePath);

        if ($caBundlePath !== '' && file_exists($caBundlePath)) {
            @ini_set('curl.cainfo', $caBundlePath);
            @ini_set('openssl.cafile', $caBundlePath);
            @putenv('SSL_CERT_FILE='.$caBundlePath);
            @putenv('CURL_CA_BUNDLE='.$caBundlePath);
        }
    }
}
