<?php

namespace App\Http\Controllers;

use App\Models\PestAdvice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PestAdviceController extends Controller
{
    public function index()
    {
        $advice = PestAdvice::with('user')
            ->latest()
            ->paginate(10);

        return view('pest.advice.index', compact('advice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'pest_type' => 'required|in:cecid_fly,fruit_fly,unknown',
            'severity' => 'required|in:low,medium,high',
            'preventive_measures' => 'required|string',
            'control_methods' => 'required|string',
            'chemical_treatments' => 'nullable|string',
            'organic_treatments' => 'nullable|string',
            'monitoring_tips' => 'required|string',
        ]);

        $advice = PestAdvice::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'pest_type' => $validated['pest_type'],
            'severity' => $validated['severity'],
            'preventive_measures' => $validated['preventive_measures'],
            'control_methods' => $validated['control_methods'],
            'chemical_treatments' => $validated['chemical_treatments'],
            'organic_treatments' => $validated['organic_treatments'],
            'monitoring_tips' => $validated['monitoring_tips'],
        ]);

        return redirect()->route('pest.advice')
            ->with('success', 'Control advice added successfully.');
    }

    public function show(PestAdvice $advice)
    {
        return view('pest.advice.show', compact('advice'));
    }

    public function update(Request $request, PestAdvice $advice)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'pest_type' => 'required|in:cecid_fly,fruit_fly,unknown',
            'severity' => 'required|in:low,medium,high',
            'preventive_measures' => 'required|string',
            'control_methods' => 'required|string',
            'chemical_treatments' => 'nullable|string',
            'organic_treatments' => 'nullable|string',
            'monitoring_tips' => 'required|string',
        ]);

        $advice->update($validated);

        return redirect()->route('pest.advice')
            ->with('success', 'Control advice updated successfully.');
    }

    public function destroy(PestAdvice $advice)
    {
        $advice->delete();

        return redirect()->route('pest.advice')
            ->with('success', 'Control advice deleted successfully.');
    }
} 