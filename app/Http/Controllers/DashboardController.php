<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $chartRange = in_array((int) $request->query('range'), [1, 5], true)
            ? (int) $request->query('range')
            : 5;

        $baseQuery = Assessment::query()->forCurrentUser();

        $totalAssessments = (clone $baseQuery)->count();
        $totalPatients = (clone $baseQuery)->distinct('patient_id')->count('patient_id');

        $highRiskCount = AssessmentResult::whereIn('risk_category', [
            'high_risk',
            'severe_preeclampsia',
        ])->whereHas('assessment', fn ($q) => $q->forCurrentUser())->count();

        $lastMonthCount = (clone $baseQuery)->where('created_at', '>=', now()->subMonth())->count();
        $prevMonthCount = (clone $baseQuery)->whereBetween('created_at', [
            now()->subMonths(2),
            now()->subMonth(),
        ])->count();

        $growthPercent = 0;
        if ($prevMonthCount > 0) {
            $growthPercent = round((($lastMonthCount - $prevMonthCount) / $prevMonthCount) * 100);
        } elseif ($lastMonthCount > 0) {
            $growthPercent = 100;
        }

        $knnAccuracy = null;
        $metadataPath = storage_path('app/knn_metadata.json');
        if (file_exists($metadataPath)) {
            $metadata = json_decode(file_get_contents($metadataPath), true);
            $knnAccuracy = $metadata['accuracy'] ?? null;
        }

        $chart = $this->buildChartData($chartRange);

        return view('dashboard', compact(
            'totalAssessments',
            'totalPatients',
            'highRiskCount',
            'growthPercent',
            'knnAccuracy',
            'chartRange',
        ) + $chart);
    }

    private function buildChartData(int $range): array
    {
        $labels = [];
        $dataNormal = [];
        $dataPreeklampsia = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        if ($range === 1) {
            $chartTitle = 'Perkembangan Penilaian Normal vs Preeklampsia (Perbulan)';

            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $monthNames[$date->month - 1] . ' ' . $date->year;
                $dataNormal[] = $this->countAssessmentsByResult($date->year, $date->month, false);
                $dataPreeklampsia[] = $this->countAssessmentsByResult($date->year, $date->month, true);
            }
        } else {
            $chartTitle = 'Perkembangan Penilaian Normal vs Preeklampsia (Pertahun)';
            $currentYear = (int) now()->format('Y');

            for ($year = $currentYear - 4; $year <= $currentYear; $year++) {
                $labels[] = (string) $year;
                $dataNormal[] = $this->countAssessmentsByResult($year, null, false);
                $dataPreeklampsia[] = $this->countAssessmentsByResult($year, null, true);
            }
        }

        return [
            'chartLabels' => $labels,
            'chartDataNormal' => $dataNormal,
            'chartDataPreeklampsia' => $dataPreeklampsia,
            'chartTitle' => $chartTitle,
        ];
    }

    private function countAssessmentsByResult(int $year, ?int $month, bool $preeklampsia): int
    {
        $query = Assessment::query()
            ->forCurrentUser()
            ->whereYear('assessment_date', $year)
            ->whereHas('result', function ($q) use ($preeklampsia) {
                if ($preeklampsia) {
                    $q->whereIn('risk_category', ['high_risk', 'severe_preeclampsia']);
                } else {
                    $q->whereNotIn('risk_category', ['high_risk', 'severe_preeclampsia']);
                }
            });

        if ($month !== null) {
            $query->whereMonth('assessment_date', $month);
        }

        return $query->count();
    }
}
