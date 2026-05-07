<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

class AuthController extends Controller
{
    protected $auth;
    protected $firestore;

    public function __construct()
    {
        try {
        $factory = (new Factory)
           ->withServiceAccount(storage_path('app/firebase/mangoguarddb-firebase-adminsdk-fbsvc-79941ca510.json'));
        $this->auth = $factory->createAuth();
        $this->firestore = $factory->createFirestore()->database();
     //   dd(storage_path('app/firebase/mangoguarddb-firebase-adminsdk-fbsvc-79941ca510.json'));

    } catch (\Throwable $e) {
        Log::error('Firebase init failed', ['error' => $e->getMessage()]);
       $this->auth = null;
$this->firestore = null;
    }
}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    //  LOGIN
   public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    try {
        
        $signInResult = $this->auth->signInWithEmailAndPassword(
            $request->email,
            $request->password
        );

        $uid = $signInResult->firebaseUserId();

        
        $doc = $this->firestore
            ->collection('Users')
            ->document($uid)
            ->snapshot();

        if (!$doc->exists()) {
            return back()->withErrors([
                'general' => 'User profile not found.'
            ]);
        }

        $userData = $doc->data();

        //  Create session
        session([
            'firebase_user_id' => $uid,
            'email' => $userData['email'],
            'name' => $userData['name'],
        ]);

        return redirect('/dashboard');

    } catch (\Throwable $e) {
        Log::error('Login error', ['message' => $e->getMessage()]);

        return back()->withErrors([
            'general' => 'Invalid email or password.'
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
    if (!$this->auth || !$this->firestore) {

        Log::error('Firebase connection failed.');

        return back()
            ->withInput()
            ->withErrors([
                'general' => 'Firebase connection failed.'
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

        // Save user to Firestore
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