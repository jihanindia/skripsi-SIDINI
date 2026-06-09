<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'all');
        $risk = $request->query('risk', 'all');
        $puskesmas = $request->query('puskesmas', 'all');

        $assessments = $this->getFilteredAssessments($period, $risk, $puskesmas);
        $summary = $this->buildSummary($assessments);

        $puskesmasList = [
            'all' => 'Semua Puskesmas',
            User::PUSKESMAS_PULO_ARMYN => User::PUSKESMAS_PULO_ARMYN,
            User::PUSKESMAS_PANCASAN => User::PUSKESMAS_PANCASAN,
        ];

        return view('reports.index', compact('assessments', 'summary', 'period', 'risk', 'puskesmas', 'puskesmasList'));
    }

    public function export(Request $request): StreamedResponse
    {
        $period = $request->query('period', 'all');
        $risk = $request->query('risk', 'all');
        $puskesmas = $request->query('puskesmas', 'all');
        $assessments = $this->getFilteredAssessments($period, $risk, $puskesmas);

        $filename = 'laporan_screening_preeklampsia_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($assessments) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'No',
                'Puskesmas',
                'Nama Pasien',
                'Tgl. Penilaian',
                'Usia',
                'Paritas',
                'Sistolik (mmHg)',
                'Diastolik (mmHg)',
                'BB (kg)',
                'TB (cm)',
                'IMT',
                'Protein Urin',
                'HB',
                'GDS',
                'Riw. HT Keluarga',
                'Hasil KNN',
            ], ';');

            $no = 1;
            foreach ($assessments as $assessment) {
                fputcsv($handle, [
                    $no++,
                    $assessment->puskesmas ?? '-',
                    $assessment->patient->name ?? '-',
                    $assessment->assessment_date?->format('d/m/Y') ?? '-',
                    $assessment->patient->age ?? '-',
                    $assessment->para,
                    $assessment->systolic_bp,
                    $assessment->diastolic_bp,
                    $assessment->beratbadan ?? '-',
                    $assessment->tinggibadan ?? '-',
                    $assessment->imt ?? '-',
                    $this->formatProteinUrine($assessment->protein_urine),
                    $assessment->hb ?? '-',
                    $assessment->gds ?? '-',
                    $assessment->riw_ht_keluarga ? 'Ada' : 'Tidak ada',
                    $assessment->result?->prediction_label ?? '-',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function getFilteredAssessments(string $period, string $risk, string $puskesmas = 'all')
    {
        $query = Assessment::with(['patient', 'result'])
            ->forCurrentUser()
            ->orderByDesc('assessment_date')
            ->orderByDesc('created_at');

        $this->applyPeriodFilter($query, $period);
        $this->applyRiskFilter($query, $risk);

        if ($puskesmas !== 'all' && $puskesmas !== '') {
            $query->where('puskesmas', $puskesmas);
        }

        return $query->get();
    }

    private function applyPeriodFilter($query, string $period): void
    {
        match ($period) {
            'month' => $query->where('assessment_date', '>=', now()->startOfMonth()),
            '3months' => $query->where('assessment_date', '>=', now()->subMonths(3)),
            '6months' => $query->where('assessment_date', '>=', now()->subMonths(6)),
            'year' => $query->whereYear('assessment_date', now()->year),
            default => null,
        };
    }

    private function applyRiskFilter($query, string $risk): void
    {
        if ($risk === 'normal') {
            $query->whereHas('result', fn ($q) => $q->whereNotIn('risk_category', ['high_risk', 'severe_preeclampsia']));
        } elseif ($risk === 'preeklampsia') {
            $query->whereHas('result', fn ($q) => $q->whereIn('risk_category', ['high_risk', 'severe_preeclampsia']));
        }
    }

    private function buildSummary($assessments): array
    {
        $total = $assessments->count();
        $preeklampsia = $assessments->filter(function ($a) {
            return $a->result && in_array($a->result->risk_category, ['high_risk', 'severe_preeclampsia'], true);
        })->count();

        return [
            'total' => $total,
            'normal' => $total - $preeklampsia,
            'preeklampsia' => $preeklampsia,
        ];
    }

    private function formatProteinUrine(?string $value): string
    {
        return match ($value) {
            'negative' => 'Negatif',
            '+1' => '+1',
            '+2' => '+2',
            '+3' => '+3',
            '+4' => '+4',
            default => $value ?? '-',
        };
    }
}
