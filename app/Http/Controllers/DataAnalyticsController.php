<?php

namespace App\Http\Controllers;

use App\Models\PestData;
use App\Models\Trap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;

class DataAnalyticsController extends Controller
{
    public function index()
    {
        return view('analytics.index');
    }

    public function getAnalytics($period = 'monthly')
    {
        $subDays = 0;
        $groupBy = 'date';
        $dateFormat = '%Y-%m-%d';
        $labels = [];
        $cecidFlyData = [];
        $fruitFlyData = [];
        $leafHopperData = [];

        switch ($period) {
            case 'weekly':
                $subDays = 7 * 4; // Last 4 weeks
                $groupBy = 'week';
                $dateFormat = '%Y-%W';
                for ($i = 3; $i >= 0; $i--) {
                    $week = Carbon::now()->subWeeks($i);
                    $labels[] = 'Week ' . $week->weekOfYear; // Format for week
                }
                break;
            case 'monthly':
                $subDays = 30 * 6; // Last 6 months
                $groupBy = 'month';
                $dateFormat = '%Y-%m';
                for ($i = 5; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $labels[] = $month->format('M'); // e.g., Jan, Feb
                }
                break;
            case 'yearly':
                $subDays = 365 * 3; // Last 3 years
                $groupBy = 'year';
                $dateFormat = '%Y';
                for ($i = 2; $i >= 0; $i--) {
                    $year = Carbon::now()->subYears($i);
                    $labels[] = $year->format('Y'); // e.g., 2021, 2022
                }
                break;
            default: // monthly as default
                $subDays = 30 * 6; // Last 6 months
                $groupBy = 'month';
                $dateFormat = '%Y-%m';
                for ($i = 5; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $labels[] = $month->format('M'); // e.g., Jan, Feb
                }
                break;
        }

        $rawData = PestData::query()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period_label"),
                'pest_type',
                DB::raw('SUM(pest_count) as total_pest_count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($subDays))
            ->groupBy('period_label', 'pest_type')
            ->orderBy('period_label')
            ->get();

        foreach ($labels as $label) {
            $cecidFlyCount = $rawData->where('period_label', $label)->where('pest_type', 'Cecid Fly')->sum('total_pest_count');
            $fruitFlyCount = $rawData->where('period_label', $label)->where('pest_type', 'Fruit Fly')->sum('total_pest_count');
            $leafHopperCount = $rawData->where('period_label', $label)->where('pest_type', 'Leaf Hopper')->sum('total_pest_count');
            
            $cecidFlyData[] = $cecidFlyCount;
            $fruitFlyData[] = $fruitFlyCount;
            $leafHopperData[] = $leafHopperCount;
        }

        return response()->json([
            'labels' => $labels,
            'cecidFlyData' => $cecidFlyData,
            'fruitFlyData' => $fruitFlyData,
            'leafHopperData' => $leafHopperData,
        ]);
    }

    public function exportData($period, $format)
    {
        $data = $this->getAnalyticsData($period);

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($data, $period);
            case 'pdf':
                return $this->exportToPdf($data, $period);
            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }

    private function getAnalyticsData($period)
    {
        $query = PestData::query()
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('WEEK(created_at) as week'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(pest_count) as total_pests'),
                DB::raw('COUNT(DISTINCT pest_type) as unique_species'),
                DB::raw('AVG(temperature) as avg_temperature'),
                'pest_type',
                DB::raw('SUM(pest_count) as pest_count')
            ])
            ->groupBy('date', 'week', 'month', 'pest_type')
            ->orderBy('date', 'desc');

        switch ($period) {
            case 'week':
                $data = $query->where('created_at', '>=', Carbon::now()->subWeeks(4))->get();
                break;
            case 'month':
                $data = $query->where('created_at', '>=', Carbon::now()->subMonths(6))->get();
                break;
            default:
                $data = $query->where('created_at', '>=', Carbon::now()->subDays(30))->get();
        }

        return $data;
    }

    private function exportToCsv($data, $period)
    {
        $csv = Writer::createFromString('');
        
        // Add headers
        $csv->insertOne([
            'Date',
            'Total Pests',
            'Unique Species',
            'Average Temperature',
            'Pest Type',
            'Pest Count'
        ]);

        // Add data rows
        foreach ($data as $row) {
            $csv->insertOne([
                $row->date ?? $row->week ?? $row->month,
                $row->total_pests,
                $row->unique_species,
                $row->avg_temperature,
                $row->pest_type,
                $row->pest_count
            ]);
        }

        $filename = "pest-analytics-{$period}-" . Carbon::now()->format('Y-m-d') . '.csv';
        
        return response($csv->toString())
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function exportToPdf($data, $period)
    {
        $pdf = new \TCPDF();
        $pdf->SetCreator('Mango Farm Management System');
        $pdf->SetAuthor('System');
        $pdf->SetTitle("Pest Analytics - {$period}");

        $pdf->AddPage();

        // Add title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, "Pest Analytics Report - {$period}", 0, 1, 'C');
        $pdf->Ln(10);

        // Add summary
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Summary Statistics', 0, 1);
        $pdf->SetFont('helvetica', '', 10);

        $totalPests = $data->sum('total_pests');
        $avgTemp = $data->avg('avg_temperature');
        $uniqueSpecies = $data->unique('pest_type')->count();

        $pdf->Cell(0, 10, "Total Pests: {$totalPests}", 0, 1);
        $pdf->Cell(0, 10, "Average Temperature: {$avgTemp}°C", 0, 1);
        $pdf->Cell(0, 10, "Unique Species: {$uniqueSpecies}", 0, 1);
        $pdf->Ln(10);

        // Add detailed data
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Detailed Data', 0, 1);
        $pdf->SetFont('helvetica', '', 10);

        // Table header
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(40, 7, 'Date', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Total Pests', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Pest Type', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Count', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Temperature', 1, 1, 'C', true);

        // Table data
        foreach ($data as $row) {
            $pdf->Cell(40, 7, $row->date ?? $row->week ?? $row->month, 1);
            $pdf->Cell(30, 7, $row->total_pests, 1);
            $pdf->Cell(40, 7, $row->pest_type, 1);
            $pdf->Cell(30, 7, $row->pest_count, 1);
            $pdf->Cell(40, 7, $row->avg_temperature . '°C', 1);
            $pdf->Ln();
        }

        $filename = "pest-analytics-{$period}-" . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return response($pdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function getTrapPerformance()
    {
        $traps = Trap::with(['pestData' => function ($query) {
            $query->select('trap_id', DB::raw('SUM(pest_count) as total_catch'))
                ->groupBy('trap_id');
        }])->get();

        return response()->json($traps);
    }

    public function getPestDistribution()
    {
        $distribution = PestData::select('pest_type', DB::raw('SUM(pest_count) as total'))
            ->groupBy('pest_type')
            ->orderBy('total', 'desc')
            ->get();

        $labels = $distribution->pluck('pest_type')->toArray();
        $data = $distribution->pluck('total')->toArray();

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
} 