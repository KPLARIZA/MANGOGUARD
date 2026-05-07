<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // Dashboard page
    public function index()
    {
        return view('dashboard');
    }

    // Get overall stats
    public function getDashboardStats(SupabaseService $supabase)
    {
        try {
            $pestAlerts = $supabase->from('pest_alerts')->get() ?? [];
            $harvestData = $supabase->from('crop_harvest')->get() ?? [];

            $totalReports = count($pestAlerts);
            $cecidFlyAlerts = collect($pestAlerts)
                ->filter(fn($a) => isset($a['pest_type'], $a['created_at']) && $a['pest_type'] === 'Cecid Fly' && Carbon::parse($a['created_at'])->gte(Carbon::now()->subDays(30)))
                ->count();
            $fruitFlyAlerts = collect($pestAlerts)
                ->filter(fn($a) => isset($a['pest_type'], $a['created_at']) && $a['pest_type'] === 'Fruit Fly' && Carbon::parse($a['created_at'])->gte(Carbon::now()->subDays(30)))
                ->count();
            $totalHarvestVolume = collect($harvestData)->filter(fn($h) => is_numeric($h['volume'] ?? null))->sum('volume');

            return response()->json([
                'totalReports' => $totalReports,
                'cecidFlyAlerts' => $cecidFlyAlerts,
                'fruitFlyAlerts' => $fruitFlyAlerts,
                'totalHarvestVolume' => number_format($totalHarvestVolume, 1),
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch stats'], 500);
        }
    }

    // Chart data (with period logic)
    public function getChartData(SupabaseService $supabase, $period = 'weekly')
    {
        try {
            $pestAlerts = $supabase->from('pest_alerts')->get() ?? [];

            $alerts = collect($pestAlerts)
                ->filter(fn($a) => isset($a['pest_type'], $a['created_at']))
                ->map(fn($a) => [
                    'pest_type' => $a['pest_type'],
                    'date' => Carbon::parse($a['created_at'])->format('Y-m-d'),
                ]);

            // Implement period filtering (e.g., last 7 days for 'weekly')
            if ($period === 'weekly') {
                $alerts = $alerts->filter(fn($a) => Carbon::parse($a['date'])->gte(Carbon::now()->subDays(7)));
            }

            $grouped = $alerts->groupBy(['pest_type', 'date'])
                ->map(fn($dates) => $dates->count());

            return response()->json($grouped);
        } catch (\Exception $e) {
            Log::error('Chart data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch chart data'], 500);
        }
    }

    // Recent 5 reports
    public function getRecentReports(SupabaseService $supabase)
    {
        try {
            $pestAlerts = $supabase->from('pest_alerts')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get() ?? [];

            $sorted = collect($pestAlerts)
                ->filter(fn($a) => isset($a['created_at'], $a['pest_type'], $a['severity'], $a['location']))
                ->map(fn($report) => [
                    'id' => $report['id'] ?? null,
                    'title' => $report['pest_type'] . ' ' . $report['severity'],
                    'location' => (isset($report['location']['block']) ? $report['location']['block'] : 'Unknown') . ', Section ' . ($report['location']['section'] ?? 'Unknown'),
                    'severity' => $report['severity'],
                    'time_ago' => Carbon::parse($report['created_at'])->diffForHumans(),
                ]);

            return response()->json($sorted);
        } catch (\Exception $e) {
            Log::error('Recent reports error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch recent reports'], 500);
        }
    }

    // Export CSV
    public function exportData(SupabaseService $supabase)
    {
        try {
            $pestAlerts = $supabase->from('pest_alerts')->get() ?? [];

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="pest_data_' . now()->format('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($pestAlerts) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Date', 'Pest Type', 'Severity', 'Location', 'Notes']);

                foreach ($pestAlerts as $data) {
                    $location = is_array($data['location'] ?? null) 
                        ? (($data['location']['block'] ?? 'Unknown') . ', Section ' . ($data['location']['section'] ?? 'Unknown'))
                        : ($data['location'] ?? 'Unknown');
                    
                    fputcsv($file, [
                        $data['created_at'] ?? '',
                        $data['pest_type'] ?? '',
                        $data['severity'] ?? '',
                        $location,
                        $data['notes'] ?? '',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export data'], 500);
        }
    }
}