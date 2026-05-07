<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PestData;
use App\Models\PestAlert;
use App\Models\PestType;
use App\Models\Trap;
use Carbon\Carbon;

class PestMonitoringController extends Controller
{
    public function index()
    {
        $recentData = PestData::with('pestType')
            ->latest()
            ->take(6)
            ->get();

        $alerts = PestAlert::with('pestType')
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        $pestTypes = PestType::with(['pestData' => function($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        }])->get();

        $statistics = [
            'totalPests' => PestData::whereDate('created_at', today())->sum('count'),
            'activeAlerts' => PestAlert::where('status', 'active')->count(),
            'pestSpecies' => PestData::whereDate('created_at', today())
                ->distinct('pest_type_id')
                ->count(),
            'activeTraps' => Trap::where('status', 'active')->count(),
            'totalTraps' => Trap::count()
        ];

        return view('dashboard', compact('recentData', 'alerts', 'statistics', 'pestTypes'));
    }

    public function getRealTimeData()
    {
        $data = PestData::with('pestType')
            ->latest()
            ->take(7)
            ->get()
            ->reverse()
            ->values();

        return response()->json($data);
    }

    public function getPestTrends($period = 'day')
    {
        $query = PestData::select(
            \DB::raw('DATE(created_at) as date'),
            \DB::raw('SUM(count) as total_pests')
        )
        ->groupBy('date')
        ->orderBy('date');

        switch ($period) {
            case 'week':
                $query->whereDate('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->whereDate('created_at', '>=', now()->subMonth());
                break;
            default:
                $query->whereDate('created_at', '>=', now()->subDay());
        }

        return response()->json($query->get());
    }
} 