<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class FirebaseAuthController extends Controller
{
    public function verify(Request $request, FirebaseAuth $firebaseAuth)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            $verifiedToken = $firebaseAuth->verifyIdToken($request->idToken);
            $uid = (string) $verifiedToken->claims()->get('sub');
            $user = $firebaseAuth->getUser($uid);

            return response()->json([
                'message' => 'Authenticated',
                'uid' => $uid,
                'email' => $user->email,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Invalid token',
            ], 401);
        }
    }
}
