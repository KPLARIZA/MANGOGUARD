<?php

namespace App\Http\Controllers;

use App\Models\PestData;
use App\Models\Trap;
use App\Services\PestAlertService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PestDataController extends Controller
{
    protected $alertService;

    public function __construct(PestAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        $query = PestData::with(['pestType', 'trap']);

        // Basic filtering and searching (can be expanded)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('pestType', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('trap', function($sq) use ($search) {
                    $sq->where('location', 'like', '%' . $search . '%');
                })
                ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $pestData = $query->latest('detected_at')->paginate(10); // Paginate with 10 items per page

        // Manually attach severity and status for demonstration based on pest_type
        $pestData->getCollection()->transform(function ($item) {
            $severity = 'Low';
            $status = 'Resolved';

            if (str_contains($item->pestType->name, 'Cecid Fly')) {
                $severity = 'High';
                $status = 'In Progress';
            } elseif (str_contains($item->pestType->name, 'Leaf Hopper')) {
                $severity = 'Medium';
                $status = 'In Progress';
            }
            
            // You would replace this with actual logic to fetch severity/status from PestAlerts
            // For example, find the latest active alert for this pest data entry's trap/pest type

            return [
                'id' => 'R-' . sprintf('%04d', $item->id),
                'date' => $item->detected_at->format('Y-m-d'),
                'pest_type' => $item->pestType->name,
                'location' => $item->trap->location,
                'severity' => $severity,
                'status' => $status,
            ];
        });

        return response()->json($pestData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trap_id' => 'required|exists:traps,id',
            'pest_type' => 'required|in:cecid_fly,fruit_fly,unknown',
            'count' => 'required|integer|min:0',
            'recorded_at' => 'nullable|date'
        ]);

        // Set recorded_at to current time if not provided
        if (!isset($validated['recorded_at'])) {
            $validated['recorded_at'] = Carbon::now();
        }

        $pestData = PestData::create($validated);

        // The alert will be automatically generated through the model's boot method
        // which calls the PestAlertService

        return response()->json([
            'message' => 'Pest data recorded successfully',
            'data' => $pestData
        ], 201);
    }

    public function show(PestData $pestData)
    {
        return view('pest.data.show', compact('pestData'));
    }

    public function destroy(PestData $pestData)
    {
        $pestData->delete();

        return response()->json(['message' => 'Pest data deleted successfully']);
    }

    public function getTrapData(Trap $trap)
    {
        $data = PestData::where('trap_id', $trap->id)
            ->where('recorded_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('recorded_at')
            ->get()
            ->groupBy('pest_type')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'count' => $item->count,
                        'recorded_at' => $item->recorded_at->format('Y-m-d H:i:s')
                    ];
                });
            });

        return response()->json($data);
    }

    public function getLatestData()
    {
        $data = PestData::with(['pestType', 'trap'])
            ->latest('detected_at')
            ->take(4) // Get top 4 for recent reports
            ->get()
            ->map(function ($item) {
                $title = $item->pestType->name . ' Infestation';
                $severity = 'Low';
                $status = 'Resolved';

                if (str_contains($item->pestType->name, 'Cecid Fly')) {
                    $severity = 'High';
                    $status = 'In Progress';
                } elseif (str_contains($item->pestType->name, 'Leaf Hopper')) {
                    $severity = 'Medium';
                    $status = 'In Progress';
                }
                
                // You would replace this with actual logic to fetch severity/status from PestAlerts

                return [
                    'title' => $title,
                    'location' => $item->trap->location . ', Section ' . $item->trap->section, // Assuming trap has a section
                    'severity' => $severity,
                    'time_ago' => Carbon::parse($item->detected_at)->diffForHumans(),
                    'status' => $status, // Include status if needed for more complex display
                ];
            });

        return response()->json($data);
    }
} 