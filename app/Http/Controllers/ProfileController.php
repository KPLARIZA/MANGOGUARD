<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = (object) [
            'name' => session('name', 'User'),
            'email' => session('email', ''),
            'profile_picture' => session('profile_picture'),
        ];

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profilePicture = session('profile_picture');

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($profilePicture) {
                Storage::disk('public')->delete($profilePicture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profilePicture = $path;
        }

        $request->session()->put('name', $request->name);
        $request->session()->put('profile_picture', $profilePicture);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
} 