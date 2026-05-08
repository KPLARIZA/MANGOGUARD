<?php

namespace App\Http\Controllers;

use App\Models\CropHarvest;
use App\Models\PestAlert;
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
    public function getDashboardStats()
    {
        try {
            $last30Days = Carbon::now()->subDays(30);
            $totalReports = PestAlert::count();
            $cecidFlyAlerts = PestAlert::where('created_at', '>=', $last30Days)
                ->where(function ($query) {
                    $query->where('pest_type', 'cecid_fly')
                        ->orWhere('pest_type', 'Cecid Fly');
                })
                ->count();
            $fruitFlyAlerts = PestAlert::where('created_at', '>=', $last30Days)
                ->where(function ($query) {
                    $query->where('pest_type', 'fruit_fly')
                        ->orWhere('pest_type', 'Fruit Fly');
                })
                ->count();
            $totalHarvestVolume = (float) CropHarvest::sum('volume');

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
    public function getChartData($period = 'weekly')
    {
        try {
            $days = match ($period) {
                'yearly' => 365,
                'monthly' => 30,
                default => 7,
            };

            $alerts = PestAlert::query()
                ->where('created_at', '>=', Carbon::now()->subDays($days))
                ->get(['pest_type', 'created_at'])
                ->map(function ($alert) {
                    $type = strtolower(str_replace('_', ' ', (string) $alert->pest_type));

                    return [
                        'pest_type' => match ($type) {
                            'cecid fly' => 'Cecid Fly',
                            'fruit fly' => 'Fruit Fly',
                            'leaf hopper' => 'Leaf Hopper',
                            default => ucwords($type),
                        },
                        'date' => Carbon::parse($alert->created_at)->format('Y-m-d'),
                    ];
                });

            $grouped = $alerts->groupBy(['pest_type', 'date'])
                ->map(fn($dates) => $dates->count());

            return response()->json($grouped);
        } catch (\Exception $e) {
            Log::error('Chart data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch chart data'], 500);
        }
    }

    // Recent 5 reports
    public function getRecentReports()
    {
        try {
            $pestAlerts = PestAlert::query()
                ->latest('created_at')
                ->limit(5)
                ->get(['id', 'pest_type', 'severity', 'location', 'created_at']);

            $sorted = $pestAlerts->map(function ($report) {
                $type = ucwords(str_replace('_', ' ', (string) $report->pest_type));

                return [
                    'id' => $report->id,
                    'title' => trim($type . ' ' . ($report->severity ?? '')),
                    'location' => (string) ($report->location ?: 'Unknown'),
                    'severity' => (string) ($report->severity ?: 'low'),
                    'time_ago' => Carbon::parse($report->created_at)->diffForHumans(),
                ];
            });

            return response()->json($sorted);
        } catch (\Exception $e) {
            Log::error('Recent reports error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch recent reports'], 500);
        }
    }

    // Export CSV
    public function exportData()
    {
        try {
            $pestAlerts = PestAlert::query()
                ->latest('created_at')
                ->get(['created_at', 'pest_type', 'severity', 'location', 'notes']);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="pest_data_' . now()->format('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($pestAlerts) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Date', 'Pest Type', 'Severity', 'Location', 'Notes']);

                foreach ($pestAlerts as $data) {
                    $type = ucwords(str_replace('_', ' ', (string) $data->pest_type));
                    fputcsv($file, [
                        (string) ($data->created_at ?? ''),
                        $type,
                        (string) ($data->severity ?? ''),
                        (string) ($data->location ?? 'Unknown'),
                        (string) ($data->notes ?? ''),
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