<?php

namespace App\Http\Controllers;

use App\Models\PestAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PestAlertController extends Controller
{
    public function index()
    {
        $alerts = PestAlert::with('user')
            ->latest()
            ->paginate(10);

        return view('pest.alerts.index', compact('alerts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high',
            'location' => 'required|string|max:255',
            'pest_type' => 'required|in:cecid_fly,fruit_fly,unknown',
            'status' => 'required|in:active,resolved',
        ]);

        $alert = PestAlert::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'location' => $validated['location'],
            'pest_type' => $validated['pest_type'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('pest.alerts')
            ->with('success', 'Alert created successfully.');
    }

    public function show(PestAlert $alert)
    {
        return view('pest.alerts.show', compact('alert'));
    }

    public function update(Request $request, PestAlert $alert)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high',
            'location' => 'required|string|max:255',
            'pest_type' => 'required|in:cecid_fly,fruit_fly,unknown',
            'status' => 'required|in:active,resolved',
        ]);

        $alert->update($validated);

        return redirect()->route('pest.alerts')
            ->with('success', 'Alert updated successfully.');
    }

    public function destroy(PestAlert $alert)
    {
        $alert->delete();

        return redirect()->route('pest.alerts')
            ->with('success', 'Alert deleted successfully.');
    }
} 