<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\FarmImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FarmController extends Controller
{
    public function index()
    {
        return view('farms.index');
    }

    public function showMap($block)
    {
        // Hardcoded coordinates for Davao del Sur blocks
        $blockCoords = [
            'A1' => ['lat' => 6.7500, 'lng' => 125.3500],
            'A2' => ['lat' => 6.7510, 'lng' => 125.3510],
            'A3' => ['lat' => 6.7520, 'lng' => 125.3520],
            'A4' => ['lat' => 6.7530, 'lng' => 125.3530],
            'B1' => ['lat' => 6.7540, 'lng' => 125.3540],
            'B2' => ['lat' => 6.7550, 'lng' => 125.3550],
            'B3' => ['lat' => 6.7560, 'lng' => 125.3560],
            'B4' => ['lat' => 6.7570, 'lng' => 125.3570],
            'C1' => ['lat' => 6.7580, 'lng' => 125.3580],
            'C2' => ['lat' => 6.7590, 'lng' => 125.3590],
            'C3' => ['lat' => 6.7600, 'lng' => 125.3600],
            'C4' => ['lat' => 6.7610, 'lng' => 125.3610],
            'D1' => ['lat' => 6.7620, 'lng' => 125.3620],
            'D2' => ['lat' => 6.7630, 'lng' => 125.3630],
            'D3' => ['lat' => 6.7640, 'lng' => 125.3640],
            'D4' => ['lat' => 6.7650, 'lng' => 125.3650],
        ];
        $coords = $blockCoords[$block] ?? ['lat' => 6.7510, 'lng' => 125.3510]; // default to A2 if not found
        return view('farms.map', [
            'block' => $block,
            'lat' => $coords['lat'],
            'lng' => $coords['lng'],
        ]);
    }

    public function show($id)
    {
        $farm = Farm::with('images')->findOrFail($id);
        return view('farms.show', compact('farm'));
    }

    public function uploadImage(Request $request, $id)
    {
        $farm = Farm::findOrFail($id);
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);
        $path = $request->file('image')->store('farm_images', 'public');
        FarmImage::create([
            'farm_id' => $farm->id,
            'user_id' => Auth::id(),
            'image' => $path,
        ]);
        return redirect()->route('farms.show', $farm->id)->with('success', 'Image uploaded successfully.');
    }
} 