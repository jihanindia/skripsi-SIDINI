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

        $cvAccuracy = null;
        $testAccuracy = null;
        $metricsPath = storage_path('app/last_training_prediction_metrics.json');
        if (file_exists($metricsPath)) {
            $metrics = json_decode(file_get_contents($metricsPath), true);
            $testAccuracy = isset($metrics['test_accuracy'])
                ? round($metrics['test_accuracy'], 2)
                : null;
            $cvAccuracy = $testAccuracy;
        }

        $chart = $this->buildChartData($chartRange);

        // Fetch Recent Assessments
        $recentAssessments = Assessment::query()
            ->forCurrentUser()
            ->with(['patient', 'result'])
            ->orderBy('assessment_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(2)
            ->get();

        // 7 Days Trend Logic
        $trendLabels = [];
        $trendNormal = [];
        $trendPreeklampsia = [];
        
        $distNormal = 0;
        $distPreeklampsia = 0;

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayMonth = $date->format('j M');
            $trendLabels[] = $dayMonth;

            // count Normal
            $countNormal = Assessment::query()
                ->forCurrentUser()
                ->whereDate('assessment_date', $date->toDateString())
                ->whereHas('result', function ($q) {
                    $q->whereNotIn('risk_category', ['high_risk', 'severe_preeclampsia']);
                })->count();
            
            // count Preeklampsia
            $countPreeklampsia = Assessment::query()
                ->forCurrentUser()
                ->whereDate('assessment_date', $date->toDateString())
                ->whereHas('result', function ($q) {
                    $q->whereIn('risk_category', ['high_risk', 'severe_preeclampsia']);
                })->count();

            $trendNormal[] = $countNormal;
            $trendPreeklampsia[] = $countPreeklampsia;
            
            $distNormal += $countNormal;
            $distPreeklampsia += $countPreeklampsia;
        }

        $totalDist = $distNormal + $distPreeklampsia;

        return view('dashboard', compact(
            'totalAssessments',
            'totalPatients',
            'highRiskCount',
            'growthPercent',
            'cvAccuracy',
            'testAccuracy',
            'chartRange',
            'recentAssessments',
            'trendLabels',
            'trendNormal',
            'trendPreeklampsia',
            'distNormal',
            'distPreeklampsia',
            'totalDist'
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
