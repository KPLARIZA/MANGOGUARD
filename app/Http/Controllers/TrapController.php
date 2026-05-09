<?php

namespace App\Http\Controllers;

use App\Models\Trap;
use App\Models\PestAlert;
use App\Services\DetectedLogsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrapController extends Controller
{
    public function dashboard(DetectedLogsService $detectedLogs)
    {
        $error = null;
        $logs = [];

        try {
            $logs = $detectedLogs->fetchDetectedLogs(250);
        } catch (\Throwable $e) {
            Log::warning('DetectedLogs fetch failed', ['message' => $e->getMessage()]);
            $error = 'Could not load Firestore detectedLogs: '.$e->getMessage();
        }

        $stats = $detectedLogs->summarizeForDashboard($logs);

        $columnKeys = [];
        foreach ($logs as $row) {
            foreach (array_keys($row['fields'] ?? []) as $k) {
                $columnKeys[$k] = true;
            }
        }
        $columnKeys = array_keys($columnKeys);
        sort($columnKeys);

        $dynamicKeys = array_values(array_filter($columnKeys, static function (string $k): bool {
            $norm = strtolower(str_replace('_', '', $k));

            return ! in_array($norm, ['trapid', 'weight'], true);
        }));
        sort($dynamicKeys);

        return view('traps.dashboard', [
            'logs' => $logs,
            'stats' => $stats,
            'firestoreError' => $error,
            'columnKeys' => $columnKeys,
            'dynamicKeys' => $dynamicKeys,
        ]);
    }

    public function index()
    {
        $traps = Trap::with(['pestData', 'pestAlerts'])->get();
        return view('traps.index', compact('traps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'battery_level' => 'required|numeric|min:0|max:100',
            'storage_volume' => 'required|numeric|min:0',
            'storage_threshold' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $trap = Trap::create($validated);

        // Check for alerts
        $this->checkTrapAlerts($trap);

        return redirect()->route('traps.index')
            ->with('success', 'Trap created successfully.');
    }

    public function update(Request $request, Trap $trap)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'battery_level' => 'required|numeric|min:0|max:100',
            'storage_volume' => 'required|numeric|min:0',
            'storage_threshold' => 'required|numeric|min:0',
            'status' => 'required|in:active,maintenance,inactive',
            'notes' => 'nullable|string'
        ]);

        $trap->update($validated);

        // Check for alerts
        $this->checkTrapAlerts($trap);

        return redirect()->route('traps.index')
            ->with('success', 'Trap updated successfully.');
    }

    private function checkTrapAlerts(Trap $trap)
    {
        // Check battery level
        if ($trap->battery_level < 20) {
            PestAlert::create([
                'trap_id' => $trap->id,
                'title' => 'Low Battery Alert',
                'description' => "Trap {$trap->name} has low battery ({$trap->battery_level}%). Please replace or charge the battery.",
                'severity' => 'high',
                'location' => $trap->location,
                'pest_type' => 'system',
                'status' => 'active'
            ]);
        }

        // Check storage volume
        if ($trap->storage_volume >= $trap->storage_threshold) {
            PestAlert::create([
                'trap_id' => $trap->id,
                'title' => 'Storage Full Alert',
                'description' => "Trap {$trap->name} storage is full ({$trap->storage_volume}kg). Please empty the trap.",
                'severity' => 'high',
                'location' => $trap->location,
                'pest_type' => 'system',
                'status' => 'active'
            ]);
        }

        // Check if trap needs maintenance
        if ($trap->needsMaintenance()) {
            $trap->update(['status' => 'maintenance']);
        }
    }

    public function maintenance(Trap $trap)
    {
        $trap->update([
            'battery_level' => 100,
            'storage_volume' => 0,
            'last_maintenance' => now(),
            'status' => 'active'
        ]);

        return redirect()->route('traps.index')
            ->with('success', 'Trap maintenance completed successfully.');
    }

    public function destroy(Trap $trap)
    {
        $trap->delete();

        return redirect()->route('traps.index')
            ->with('success', 'Trap deleted successfully.');
    }
} 