<?php

namespace App\Http\Controllers;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Contract\Auth as FirebaseAuthContract;
use Kreait\Firebase\Factory;

class AuthController extends Controller
{
    protected $auth;
    protected $firestore;
    protected $firebaseInitError = null;

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

    private function getServiceAccountForRest(): array
    {
        $credentialsPath = config('firebase.projects.mangoguard.credentials');

        if ($credentialsPath && file_exists($credentialsPath)) {
            $json = json_decode((string) file_get_contents($credentialsPath), true);
            if (is_array($json) && isset($json['client_email'], $json['private_key'])) {
                return $json;
            }
        }

        if (
            env('FIREBASE_PROJECT_ID') &&
            env('FIREBASE_CLIENT_EMAIL') &&
            env('FIREBASE_PRIVATE_KEY')
        ) {
            return [
                'type' => 'service_account',
                'project_id' => env('FIREBASE_PROJECT_ID'),
                'private_key' => $this->normalizePrivateKey(env('FIREBASE_PRIVATE_KEY')),
                'client_email' => env('FIREBASE_CLIENT_EMAIL'),
                'token_uri' => 'https://oauth2.googleapis.com/token',
            ];
        }

        throw new \RuntimeException('Service account credentials unavailable for Firestore REST fallback.');
    }

    private function toFirestoreFields(array $data): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => (string) $value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } else {
                $fields[$key] = ['stringValue' => (string) $value];
            }
        }

        return $fields;
    }

    private function saveUserProfileToFirestoreRest(string $uid, array $profile): void
    {
        $serviceAccount = $this->getServiceAccountForRest();
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/datastore'],
            $serviceAccount
        );

        $token = $credentials->fetchAuthToken();
        $accessToken = $token['access_token'] ?? null;

        if (!$accessToken) {
            throw new \RuntimeException('Unable to obtain Google access token for Firestore REST write.');
        }

        $projectId = (string) ($serviceAccount['project_id'] ?? env('FIREBASE_PROJECT_ID'));
        if ($projectId === '') {
            throw new \RuntimeException('Missing Firebase project ID for Firestore REST write.');
        }

        $client = new Client([
            'timeout' => 15,
        ]);

        $url = sprintf(
            'https://firestore.googleapis.com/v1/projects/%s/databases/(default)/documents/Users?documentId=%s',
            rawurlencode($projectId),
            rawurlencode($uid)
        );

        $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'fields' => $this->toFirestoreFields($profile),
            ],
        ]);
    }

    /**
     * Service account payload for Kreait Factory (GRPC Firestore only).
     *
     * @return array<string, mixed>|string|null
     */
    private function resolveServiceAccountForFactory(): array|string|null
    {
        $credentialsPath = config('firebase.projects.mangoguard.credentials');

        if ($credentialsPath && file_exists($credentialsPath)) {
            return $credentialsPath;
        }

        if (
            env('FIREBASE_PROJECT_ID') &&
            env('FIREBASE_CLIENT_EMAIL') &&
            env('FIREBASE_PRIVATE_KEY')
        ) {
            return [
                'type' => 'service_account',
                'project_id' => env('FIREBASE_PROJECT_ID'),
                'private_key' => $this->normalizePrivateKey(env('FIREBASE_PRIVATE_KEY')),
                'client_email' => env('FIREBASE_CLIENT_EMAIL'),
                'token_uri' => 'https://oauth2.googleapis.com/token',
            ];
        }

        return null;
    }

    public function __construct()
    {
        $this->auth = null;
        $this->firestore = null;

        try {
            $this->auth = app(FirebaseAuthContract::class);
        } catch (\Throwable $e) {
            $this->firebaseInitError = $e->getMessage();
            Log::error('Firebase Auth init failed', ['error' => $e->getMessage()]);

            return;
        }

        if (! extension_loaded('grpc')) {
            return;
        }

        $serviceAccount = $this->resolveServiceAccountForFactory();
        if ($serviceAccount === null) {
            return;
        }

        try {
            $factory = (new Factory)->withServiceAccount($serviceAccount);
            $this->firestore = $factory->createFirestore()->database();
        } catch (\Throwable $firestoreError) {
            Log::warning('Firestore unavailable, continuing with Firebase Auth only.', [
                'error' => $firestoreError->getMessage(),
            ]);
        }
    }

    public function showLogin()
    {
        if (session()->has('firebase_user_id')) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! $this->auth) {
            Log::error('Login failed: Firebase Auth is unavailable.', ['reason' => $this->firebaseInitError]);

            return back()->withErrors([
                'general' => 'Authentication service is temporarily unavailable.',
            ]);
        }

        try {
            $signInResult = $this->auth->signInWithEmailAndPassword(
                $request->email,
                $request->password
            );

            $uid = $signInResult->firebaseUserId();

            if (! is_string($uid) || $uid === '') {
                Log::error('Login failed: missing Firebase uid after sign-in.', [
                    'email' => $request->email,
                ]);

                return back()->withErrors([
                    'general' => 'Could not complete sign-in. Please try again.',
                ]);
            }

            $userData = null;

            if ($this->firestore) {
                try {
                    $doc = $this->firestore
                        ->collection('Users')
                        ->document($uid)
                        ->snapshot();

                    if ($doc->exists()) {
                        $userData = $doc->data();
                    }
                } catch (\Throwable $docError) {
                    Log::warning('Firestore read failed during login (ignored).', [
                        'uid' => $uid,
                        'error' => $docError->getMessage(),
                    ]);
                }
            }

            if (! $userData) {
                $firebaseUser = $this->auth->getUser($uid);
                $userData = [
                    'email' => $firebaseUser->email ?? $request->email,
                    'name' => $firebaseUser->displayName ?? explode('@', $request->email)[0],
                ];
            }

            $request->session()->regenerate();
            $request->session()->put([
                'firebase_user_id' => $uid,
                'email' => $userData['email'],
                'name' => $userData['name'],
            ]);
            $request->session()->save();

            return redirect('/dashboard');

        } catch (FailedToSignIn $e) {
            Log::warning('Firebase sign-in rejected.', ['message' => $e->getMessage()]);

            return back()->withErrors([
                'general' => 'Invalid email or password.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Login error', ['message' => $e->getMessage()]);

            return back()->withErrors([
                'general' => 'Invalid email or password.',
            ]);
        }
    }

    // //  REGISTER
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //         'name' => 'required|string|max:255',
    //     ]);

    //     $createdUser = null;

    //     try {
    //         $email = strtolower(trim($request->email));

            
    //         $createdUser = $this->auth->createUser([
    //             'email' => $email,
    //             'password' => $request->password,
    //             'displayName' => $request->name,
    //         ]);

            
    //         try {
    //             $this->firestore->collection('Users')
    //                 ->document($createdUser->uid)
    //                 ->set([
    //                     'name' => $request->name,
    //                     'email' => $email,
    //                     'gender' => $request->gender ?? 'male',
    //                     'phone' => $request->phone ?? null,
    //                     'created_at' => date('Y-m-d H:i:s'),
    //                 ]);

    //         } catch (\Throwable $firestoreError) {

                
    //             $this->auth->deleteUser($createdUser->uid);

    //             Log::error('Firestore error', [
    //                 'message' => $firestoreError->getMessage()
    //             ]);

    //             return back()->withErrors([
    //                 'general' => 'Failed to save user data.'
    //             ]);
    //         }

    //         return redirect('/login')
    //             ->with('success', 'Registration successful! Please log in.');

    //     } catch (\Throwable $e) {

    //         Log::error('Registration error', [
    //             'message' => $e->getMessage()
    //         ]);

    //         return back()->withErrors([
    //             'general' => 'Registration failed. ' . $e->getMessage()
    //         ]);
    //     }
    // }
    public function register(Request $request)
{
    // Check Firebase connection
    if (!$this->auth) {

        Log::error('Firebase connection failed.', [
            'reason' => $this->firebaseInitError,
            'credentials_path' => config('firebase.projects.mangoguard.credentials'),
        ]);

        return back()
            ->withInput()
            ->withErrors([
                'general' => 'Firebase Auth connection failed. Check Firebase credentials and SSL certificate settings.'
            ]);
    }

    // Validate form
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $createdUser = null;

    try {

        $email = strtolower(trim($request->email));

        // Create Firebase Auth user
        $createdUser = $this->auth->createUser([
            'email' => $email,
            'password' => $request->password,
            'displayName' => $request->name,
        ]);

        if ($this->firestore) {
            $this->firestore
                ->collection('Users')
                ->document($createdUser->uid)
                ->set([
                    'name' => $request->name,
                    'email' => $email,
                    'gender' => $request->gender ?? 'male',
                    'phone' => $request->phone ?? null,
                    'created_at' => now()->toDateTimeString(),
                ]);
        } else {
            $this->saveUserProfileToFirestoreRest($createdUser->uid, [
                'name' => $request->name,
                'email' => $email,
                'gender' => $request->gender ?? 'male',
                'phone' => $request->phone ?? null,
                'created_at' => now()->toDateTimeString(),
            ]);
        }

        // Success
        return redirect('/login')->with('success', 'Registration successful!');

    } catch (\Kreait\Firebase\Exception\Auth\EmailExists $e) {

        Log::error('Email already exists', [
            'message' => $e->getMessage()
        ]);

        return back()
            ->withInput()
            ->withErrors([
                'email' => 'This email is already registered.'
            ]);

    } catch (\Kreait\Firebase\Exception\Auth\WeakPassword $e) {

        Log::error('Weak password', [
            'message' => $e->getMessage()
        ]);

        return back()
            ->withInput()
            ->withErrors([
                'password' => 'Password is too weak.'
            ]);

    } catch (\Kreait\Firebase\Exception\FirebaseException $e) {

        Log::error('Firebase error', [
            'message' => $e->getMessage()
        ]);

        // Delete user if Firestore failed after Auth creation
        if ($createdUser) {
            try {
                $this->auth->deleteUser($createdUser->uid);
            } catch (\Throwable $deleteError) {
                Log::error('Failed to rollback Firebase user', [
                    'message' => $deleteError->getMessage()
                ]);
            }
        }

        return back()
            ->withInput()
            ->withErrors([
                'general' => 'Firebase error: ' . $e->getMessage()
            ]);

    } catch (\Throwable $e) {

        Log::error('Registration failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Rollback created Firebase user
        if ($createdUser) {
            try {
                $this->auth->deleteUser($createdUser->uid);
            } catch (\Throwable $deleteError) {
                Log::error('Rollback failed', [
                    'message' => $deleteError->getMessage()
                ]);
            }
        }

        return back()
            ->withInput()
            ->withErrors([
                'general' => 'Registration failed: ' . $e->getMessage()
            ]);
    }
}


    //  LOGOUT
    public function logout(Request $request)
    {
        session()->forget(['firebase_user_id', 'email', 'name', ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully.');
    }
}