<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class FirebaseAuthController extends Controller
{
    public function verify(Request $request, SupabaseService $supabase)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            $user = $supabase->getUser($request->idToken);

            return response()->json([
                'message' => 'Authenticated',
                'uid' => $user['id'],
                'email' => $user['email'],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Invalid token',
            ], 401);
        }
    }
}
