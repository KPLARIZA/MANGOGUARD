<?php

namespace App\Http\Controllers;

use App\Models\PestAlert;
use Illuminate\Http\Request;

class PestReportController extends Controller
{
    public function create()
    {
        return view('pest.reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pest_type' => 'required|string',
            'severity' => 'required|in:low,medium,high',
            'location' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $report = PestAlert::create([
            'pest_type' => $validated['pest_type'],
            'severity' => $validated['severity'],
            'location' => $validated['location'],
            'notes' => $validated['notes'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('pest-reports.show', $report)
            ->with('success', 'Pest report created successfully.');
    }

    public function show(PestAlert $report)
    {
        return view('pest.reports.show', compact('report'));
    }

    public function edit(PestAlert $report)
    {
        return view('pest.reports.edit', compact('report'));
    }

    public function update(Request $request, PestAlert $report)
    {
        $validated = $request->validate([
            'pest_type' => 'required|string',
            'severity' => 'required|in:low,medium,high',
            'location' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $report->update($validated);

        return redirect()->route('pest-reports.show', $report)
            ->with('success', 'Pest report updated successfully.');
    }

    public function destroy(PestAlert $report)
    {
        $report->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Pest report deleted successfully.');
    }

    public function index()
    {
        $reports = \App\Models\PestAlert::latest()->paginate(10);
        return view('pest.reports.index', compact('reports'));
    }
} 