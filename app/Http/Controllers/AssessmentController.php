<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Patient;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AssessmentController extends Controller
{
    public function create()
    {
        $patients = Patient::all();
        return view('assessments.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required',
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

        // Handle Patient
        if ($validated['patient_id'] === 'new') {
            $patient = Patient::create([
                'name' => 'Pasien Baru ' . date('His'),
                'medical_record_number' => 'RM-' . time(),
                'age' => $validated['usia'],
            ]);
            $validated['patient_id'] = $patient->id;
        } else {
            $patient = Patient::find($validated['patient_id']);
        }

        // Run KNN Prediction via Python
        $datasetPath = public_path('assets/datasets/' . $this->getLatestDataset());
        $pythonScriptPath = base_path('app/Python/predict.py');
        $inputJson = json_encode($validated);
        
        $command = ['python', $pythonScriptPath, $datasetPath, $inputJson];
        $process = new Process($command, base_path(), [
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'PATH' => getenv('PATH')
        ]);
        $process->run();

        $prediction = 'normal';
        $neighbors = [];
        
        if ($process->isSuccessful()) {
            $output = json_decode($process->getOutput(), true);
            if (isset($output['prediction'])) {
                $prediction = strtolower($output['prediction']);
                $neighbors = $output['neighbors'] ?? [];
            }
        }

        // Save Assessment
        $assessment = Assessment::create([
            'patient_id' => $validated['patient_id'],
            'user_id' => auth()->id(),
            'gravida' => $validated['paritas'], // Mapping paritas to gravida for now as placeholder
            'para' => $validated['paritas'],
            'gestational_age' => 0, // Placeholder
            'systolic_bp' => $validated['systolic_bp'],
            'diastolic_bp' => $validated['diastolic_bp'],
            'protein_urine' => $this->mapProteinUrine($validated['protein_urine']),
            'assessment_date' => $validated['assessment_date'],
        ]);

        // Save Assessment Result
        $assessment->result()->create([
            'risk_category' => ($prediction === 'preeklampsia') ? 'high_risk' : 'no_risk',
            'risk_score' => 100, // Placeholder
            'k_value' => 5,
            'knn_neighbors' => $neighbors,
        ]);

        return redirect()->route('patients.index')->with('success', 'Penilaian berhasil dilakukan! Hasil: ' . strtoupper($prediction));
    }

    private function getLatestDataset()
    {
        $files = glob(public_path('assets/datasets/dataset_*.csv'));
        if (empty($files)) return '';
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        return basename($files[0]);
    }

    private function mapProteinUrine($value)
    {
        return match($value) {
            '0' => 'negative',
            '1' => '+1',
            '2' => '+2',
            '3' => '+3',
            '4' => '+4',
            default => 'negative'
        };
    }
}
