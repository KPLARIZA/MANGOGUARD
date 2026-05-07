<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = Gallery::latest()->paginate(12);
        return view('gallery.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pest_type' => 'required|string|max:255',
            'location' => 'required|string|max:255'
        ]);

        $imagePath = $request->file('image')->store('gallery', 'public');

        Gallery::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => $imagePath,
            'pest_type' => $request->pest_type,
            'location' => $request->location,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('gallery')->with('success', 'Image uploaded successfully');
    }

    public function show(Gallery $gallery)
    {
        return view('gallery.show', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pest_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($gallery->image_path);
            
            // Store new image
            $imagePath = $request->file('image')->store('gallery', 'public');
            $gallery->image_path = $imagePath;
        }

        $gallery->update([
            'title' => $request->title,
            'description' => $request->description,
            'pest_type' => $request->pest_type,
            'location' => $request->location
        ]);

        return redirect()->route('gallery')->with('success', 'Image updated successfully');
    }

    public function destroy(Gallery $gallery)
    {
        // Delete image file
        Storage::disk('public')->delete($gallery->image_path);
        
        // Delete record
        $gallery->delete();

        return redirect()->route('gallery')->with('success', 'Image deleted successfully');
    }
} 