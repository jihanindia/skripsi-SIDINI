<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Patient;
use Symfony\Component\Process\Process;

class AssessmentController extends Controller
{
    public function create()
    {
        return view('assessments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'assessment_date' => 'required|date',
            'usia' => 'required|numeric',
            'paritas' => 'required|numeric',
            'systolic_bp' => 'required|numeric',
            'diastolic_bp' => 'required|numeric',
            'beratbadan' => 'required|numeric',
            'tinggibadan' => 'required|numeric',
            'imt' => 'required|numeric',
            'protein_urine' => 'required',
            'hb' => 'required|numeric',
            'gds' => 'nullable|numeric',
            'riw_ht_keluarga' => 'required',
        ]);

        $patient = Patient::firstOrCreate(
            ['name' => $validated['patient_name']],
            [
                'medical_record_number' => 'RM-' . time(),
                'age' => (int) $validated['usia'],
                'date_of_birth' => now()->subYears((int) $validated['usia'])->toDateString(),
            ]
        );

        $patient->update(['age' => (int) $validated['usia']]);
        $validated['patient_id'] = $patient->id;

        $prediction = $this->runKnnPrediction($validated);
        $predictionLabel = $prediction === 'preeklampsia' ? 'Preeklampsia' : 'Normal';

        $assessment = Assessment::create([
            'patient_id' => $patient->id,
            'user_id' => auth()->id(),
            'puskesmas' => auth()->user()->puskesmas,
            'gravida' => (int) $validated['paritas'],
            'para' => (int) $validated['paritas'],
            'gestational_age' => 0,
            'systolic_bp' => (int) $validated['systolic_bp'],
            'diastolic_bp' => (int) $validated['diastolic_bp'],
            'beratbadan' => $validated['beratbadan'],
            'tinggibadan' => (int) $validated['tinggibadan'],
            'imt' => $validated['imt'],
            'protein_urine' => $this->mapProteinUrine($validated['protein_urine']),
            'hb' => $validated['hb'],
            'gds' => $validated['gds'] ?? null,
            'riw_ht_keluarga' => (bool) $validated['riw_ht_keluarga'],
            'family_history_preeclampsia' => (bool) $validated['riw_ht_keluarga'],
            'assessment_date' => $validated['assessment_date'],
        ]);

        $assessment->result()->create([
            'risk_category' => $prediction === 'preeklampsia' ? 'high_risk' : 'no_risk',
            'risk_score' => 100,
            'k_value' => 5,
            'severity_level' => $prediction === 'preeklampsia' ? 'moderate' : 'none',
            'supporting_indicators' => [],
            'knn_neighbors' => [],
            'recommendations' => $prediction === 'preeklampsia'
                ? ['Segera konsultasi dokter spesialis obstetri', 'Monitor tekanan darah secara rutin']
                : ['Lanjutkan pemeriksaan kehamilan rutin', 'Pantau tanda vital secara berkala'],
            'urgency_level' => $prediction === 'preeklampsia' ? 'urgent' : 'routine',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'prediction' => $prediction,
                'prediction_label' => $predictionLabel,
                'patient_name' => $patient->name,
                'redirect' => route('patients.index'),
            ]);
        }

        return redirect()->route('patients.index')->with([
            'success' => 'Penilaian berhasil disimpan.',
            'prediction' => $prediction,
            'prediction_label' => $predictionLabel,
        ]);
    }

    private function runKnnPrediction(array $validated): string
    {
        $datasetFile = $this->getLatestDataset();
        if ($datasetFile === '') {
            return 'normal';
        }

        $datasetPath = public_path('assets/datasets/' . $datasetFile);
        $pythonScriptPath = base_path('app/Python/predict.py');
        $inputJson = json_encode($validated);

        $process = new Process(
            ['python', $pythonScriptPath, $datasetPath, $inputJson],
            base_path(),
            [
                'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
                'PATH' => getenv('PATH'),
            ]
        );
        $process->run();

        if (!$process->isSuccessful()) {
            return 'normal';
        }

        $output = json_decode($process->getOutput(), true);
        if (isset($output['prediction'])) {
            return strtolower($output['prediction']);
        }

        return 'normal';
    }

    private function getLatestDataset(): string
    {
        $files = glob(public_path('assets/datasets/dataset_*.csv'));
        if (empty($files)) {
            return '';
        }
        usort($files, fn ($a, $b) => filemtime($b) - filemtime($a));

        return basename($files[0]);
    }

    private function mapProteinUrine($value): string
    {
        return match ($value) {
            '0' => 'negative',
            '1' => '+1',
            '2' => '+2',
            '3' => '+3',
            '4' => '+4',
            default => 'negative',
        };
    }
}
