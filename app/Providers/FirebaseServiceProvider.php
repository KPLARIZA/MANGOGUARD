<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Firestore;

class FirebaseServiceProvider extends ServiceProvider
{
    private function normalizePrivateKey(?string $privateKey): string
    {
        $normalized = trim((string) $privateKey);
        $normalized = trim($normalized, "\"'");
        $normalized = str_replace(["\\r\\n", "\\n"], "\n", $normalized);

        if (
            !str_contains($normalized, '-----BEGIN PRIVATE KEY-----') ||
            !str_contains($normalized, '-----END PRIVATE KEY-----')
        ) {
            throw new \RuntimeException('Invalid FIREBASE_PRIVATE_KEY format. Use full private_key from service account JSON with \\n escapes.');
        }

        return $normalized;
    }

    private function configureCaBundle(): void
    {
        $caBundlePath = (string) env('FIREBASE_CA_BUNDLE', '');
        $caBundlePath = trim($caBundlePath, "\"' ");
        $caBundlePath = str_replace('\\', DIRECTORY_SEPARATOR, $caBundlePath);

        if ($caBundlePath && file_exists($caBundlePath)) {
            @ini_set('curl.cainfo', $caBundlePath);
            @ini_set('openssl.cafile', $caBundlePath);
            @putenv('SSL_CERT_FILE=' . $caBundlePath);
            @putenv('CURL_CA_BUNDLE=' . $caBundlePath);
        } else {
            logger()->warning('FIREBASE_CA_BUNDLE is missing or invalid.', ['path' => $caBundlePath]);
        }
    }

    public function register()
    {
        $this->app->singleton(Firestore::class, function () {
            $this->configureCaBundle();

            $credentialsPath = config('firebase.projects.mangoguard.credentials');
            $serviceAccount = null;

            if ($credentialsPath && file_exists($credentialsPath)) {
                $serviceAccount = $credentialsPath;
            } elseif (
                env('FIREBASE_PROJECT_ID') &&
                env('FIREBASE_CLIENT_EMAIL') &&
                env('FIREBASE_PRIVATE_KEY')
            ) {
                $privateKey = $this->normalizePrivateKey(env('FIREBASE_PRIVATE_KEY'));

                $serviceAccount = [
                    'type' => 'service_account',
                    'project_id' => env('FIREBASE_PROJECT_ID'),
                    'private_key' => $privateKey,
                    'client_email' => env('FIREBASE_CLIENT_EMAIL'),
                    'token_uri' => 'https://oauth2.googleapis.com/token',
                ];
            } else {
                throw new \RuntimeException('Firebase credentials not configured. Set FIREBASE_CREDENTIALS or FIREBASE_PROJECT_ID/FIREBASE_CLIENT_EMAIL/FIREBASE_PRIVATE_KEY.');
            }

            if (!extension_loaded('grpc')) {
                throw new \RuntimeException('Firestore requires the PHP gRPC extension, which is not loaded.');
            }

            $factory = (new Factory)->withServiceAccount($serviceAccount);

            return $factory->createFirestore();
        });
    }
}
