<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class PredictionController extends Controller
{
    /**
     * Tampilkan halaman upload & prediksi data uji.
     */
    public function indexTest()
    {
        $metadata = $this->loadKnnMetadata();
        $lastMetrics = $this->loadPredictionMetrics('test');

        if (\App\Models\TestPrediction::exists()) {
            return redirect()->route('predictions.test.result');
        }

        return view('predictions.test.index', compact('metadata', 'lastMetrics'));
    }

    /**
     * Proses upload CSV data uji dan jalankan prediksi KNN.
     */
    public function predictTest(Request $request)
    {
        $request->validate([
            'test_dataset' => 'required|file|mimes:csv,txt',
        ], [
            'test_dataset.required' => 'File data uji CSV wajib diupload.',
            'test_dataset.mimes'    => 'Format file tidak didukung. Pastikan file berformat CSV.',
        ]);

        $file = $request->file('test_dataset');
        if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
            return redirect()->back()->with('error', 'Hanya file dengan ekstensi .csv yang diizinkan.');
        }

        // Simpan file uji
        $testFileName = 'test_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/datasets'), $testFileName);
        $testFilePath = public_path('assets/datasets/' . $testFileName);

        // Ambil dataset training terbaru
        $trainingFile = $this->getLatestDataset();
        if (empty($trainingFile)) {
            return redirect()->back()->with('error', 'Belum ada dataset training. Silakan upload dan latih model KNN terlebih dahulu di halaman Training.');
        }
        $trainingFilePath = public_path('assets/datasets/' . $trainingFile);

        // Jalankan Python script
        $pythonScriptPath = base_path('app/Python/predict_csv.py');
        $command = ['python', $pythonScriptPath, $trainingFilePath, $testFilePath];

        $env = [
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'PATH'       => getenv('PATH'),
        ];

        $process = new Process($command, base_path(), $env);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            $err = $process->getErrorOutput() ?: $process->getOutput();
            Log::error('predict_csv.py error (Test): ' . $err);
            return redirect()->back()->with('error', 'Gagal menjalankan prediksi data uji: ' . $err);
        }

        $output = $process->getOutput();
        $parsedData = $this->parsePythonOutput($output);

        if ($parsedData === null) {
            Log::warning('Gagal parse output predict_csv.py (Test)', ['tail' => substr($output, -500)]);
            return redirect()->back()->with('error', 'Prediksi data uji selesai, namun Laravel tidak dapat membaca hasilnya. Periksa log.');
        }

        // Simpan hasil ke database tabel test_predictions
        try {
            DB::transaction(function () use ($parsedData) {
                if (!empty($parsedData['predictions'])) {
                    $insertData = [];
                    foreach ($parsedData['predictions'] as $row) {
                        $insertData[] = [
                            'nama'          => $row['nama'] ?? null,
                            'usia'          => isset($row['usia']) ? intval($row['usia']) : 0,
                            'paritas'       => isset($row['paritas']) ? intval($row['paritas']) : 0,
                            'tinggibadan'   => isset($row['tb']) ? floatval($row['tb']) : (isset($row['tinggibadan']) ? floatval($row['tinggibadan']) : 0),
                            'beratbadan'    => isset($row['bb']) ? floatval($row['bb']) : (isset($row['beratbadan']) ? floatval($row['beratbadan']) : 0),
                            'imt'           => isset($row['imt']) ? floatval($row['imt']) : 0,
                            'sistolik'      => isset($row['sistolik']) ? intval($row['sistolik']) : (isset($row['sistol']) ? intval($row['sistol']) : 0),
                            'diastolik'     => isset($row['diastolik']) ? intval($row['diastolik']) : (isset($row['diastol']) ? intval($row['diastol']) : 0),
                            'map'           => isset($row['map']) ? floatval($row['map']) : null,
                            'gds'           => isset($row['gds']) ? intval($row['gds']) : 0,
                            'protein_urine' => $row['protein_urin'] ?? $row['protein_urine'] ?? null,
                            'diagnosis'     => $row['label_asli'] ?? $row['status'] ?? null,
                            'prediksi_knn'  => $row['prediksi_knn'] ?? 'Normal',
                            'confidence'    => isset($row['confidence']) ? floatval($row['confidence']) : null,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }

                    foreach (array_chunk($insertData, 100) as $chunk) {
                        \App\Models\TestPrediction::insert($chunk);
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan hasil prediksi data uji: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan hasil prediksi ke database: ' . $e->getMessage());
        }

        $this->savePredictionMetrics('test', $parsedData);

        return redirect()->route('predictions.test.result')
            ->with('prediction_result', $parsedData)
            ->with('test_file_name', $file->getClientOriginalName());
    }

    /**
     * Tampilkan halaman hasil prediksi data uji.
     */
    public function resultTest()
    {
        $result       = session('prediction_result');
        $testFileName = session('test_file_name');

        // Jika session kosong, rekonstruksi dari database
        if (!$result) {
            $records = \App\Models\TestPrediction::orderBy('id', 'desc')->get();
            if ($records->isEmpty()) {
                return redirect()->route('predictions.test.index')
                    ->with('error', 'Tidak ada hasil prediksi data uji. Silakan upload file data uji terlebih dahulu.');
            }
            $result = $this->buildResultFromDb($records, 'test');
            $testFileName = 'Data tersimpan di database';
        }

        $result = $this->mergeResultWithSavedMetrics($result, 'test');

        $metadata = $this->loadKnnMetadata();

        return view('predictions.test.result', compact('result', 'testFileName', 'metadata'));
    }

    /**
     * Tampilkan halaman upload & prediksi data latih.
     */
    public function indexTraining()
    {
        $metadata = $this->loadKnnMetadata();
        $lastMetrics = $this->loadPredictionMetrics('training');

        if (\App\Models\TrainingPrediction::exists()) {
            return redirect()->route('predictions.training.result');
        }

        return view('predictions.training.index', compact('metadata', 'lastMetrics'));
    }

    /**
     * Proses upload CSV data latih dan jalankan prediksi KNN.
     */
    public function predictTraining(Request $request)
    {
        $request->validate([
            'training_dataset' => 'required|file|mimes:csv,txt',
        ], [
            'training_dataset.required' => 'File data latih CSV wajib diupload.',
            'training_dataset.mimes'    => 'Format file tidak didukung. Pastikan file berformat CSV.',
        ]);

        $file = $request->file('training_dataset');
        if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
            return redirect()->back()->with('error', 'Hanya file dengan ekstensi .csv yang diizinkan.');
        }

        // Simpan file latihan
        $trainingFileName = 'training_predict_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/datasets'), $trainingFileName);
        $trainingFilePath = public_path('assets/datasets/' . $trainingFileName);

        // Ambil dataset training terbaru
        $trainingFile = $this->getLatestDataset();
        if (empty($trainingFile)) {
            return redirect()->back()->with('error', 'Belum ada dataset training. Silakan upload dan latih model KNN terlebih dahulu di halaman Training.');
        }
        $trainingFilePathRef = public_path('assets/datasets/' . $trainingFile);

        // Jalankan Python script
        $pythonScriptPath = base_path('app/Python/predict_csv.py');
        $command = ['python', $pythonScriptPath, $trainingFilePathRef, $trainingFilePath];

        $env = [
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'PATH'       => getenv('PATH'),
        ];

        $process = new Process($command, base_path(), $env);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            $err = $process->getErrorOutput() ?: $process->getOutput();
            Log::error('predict_csv.py error (Training): ' . $err);
            return redirect()->back()->with('error', 'Gagal menjalankan prediksi data latih: ' . $err);
        }

        $output = $process->getOutput();
        $parsedData = $this->parsePythonOutput($output);

        if ($parsedData === null) {
            Log::warning('Gagal parse output predict_csv.py (Training)', ['tail' => substr($output, -500)]);
            return redirect()->back()->with('error', 'Prediksi data latih selesai, namun Laravel tidak dapat membaca hasilnya. Periksa log.');
        }

        // Simpan hasil ke database tabel training_predictions
        try {
            DB::transaction(function () use ($parsedData) {
                if (!empty($parsedData['predictions'])) {
                    $insertData = [];
                    foreach ($parsedData['predictions'] as $row) {
                        $insertData[] = [
                            'nama'          => $row['nama'] ?? null,
                            'usia'          => isset($row['usia']) ? intval($row['usia']) : 0,
                            'paritas'       => isset($row['paritas']) ? intval($row['paritas']) : 0,
                            'tinggibadan'   => isset($row['tb']) ? floatval($row['tb']) : (isset($row['tinggibadan']) ? floatval($row['tinggibadan']) : 0),
                            'beratbadan'    => isset($row['bb']) ? floatval($row['bb']) : (isset($row['beratbadan']) ? floatval($row['beratbadan']) : 0),
                            'imt'           => isset($row['imt']) ? floatval($row['imt']) : 0,
                            'sistolik'      => isset($row['sistolik']) ? intval($row['sistolik']) : (isset($row['sistol']) ? intval($row['sistol']) : 0),
                            'diastolik'     => isset($row['diastolik']) ? intval($row['diastolik']) : (isset($row['diastol']) ? intval($row['diastol']) : 0),
                            'map'           => isset($row['map']) ? floatval($row['map']) : null,
                            'gds'           => isset($row['gds']) ? intval($row['gds']) : 0,
                            'protein_urine' => $row['protein_urin'] ?? $row['protein_urine'] ?? null,
                            'diagnosis'     => $row['label_asli'] ?? $row['status'] ?? null,
                            'prediksi_knn'  => $row['prediksi_knn'] ?? 'Normal',
                            'confidence'    => isset($row['confidence']) ? floatval($row['confidence']) : null,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }

                    foreach (array_chunk($insertData, 100) as $chunk) {
                        \App\Models\TrainingPrediction::insert($chunk);
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan hasil prediksi data latih: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan hasil prediksi data latih ke database: ' . $e->getMessage());
        }

        $this->savePredictionMetrics('training', $parsedData);

        return redirect()->route('predictions.training.result')
            ->with('prediction_result', $parsedData)
            ->with('training_file_name', $file->getClientOriginalName());
    }

    /**
     * Tampilkan halaman hasil prediksi data latih.
     */
    public function resultTraining()
    {
        $result           = session('prediction_result');
        $trainingFileName = session('training_file_name');

        // Jika session kosong, rekonstruksi dari database
        if (!$result) {
            $records = \App\Models\TrainingPrediction::orderBy('id', 'desc')->get();
            if ($records->isEmpty()) {
                return redirect()->route('predictions.training.index')
                    ->with('error', 'Tidak ada hasil prediksi data latih. Silakan upload file data latih terlebih dahulu.');
            }
            $result = $this->buildResultFromDb($records, 'training');
            $trainingFileName = 'Data tersimpan di database';
        }

        $result = $this->mergeResultWithSavedMetrics($result, 'training');

        $metadata = $this->loadKnnMetadata();

        return view('predictions.training.result', compact('result', 'trainingFileName', 'metadata'));
    }

    /**
     * Ambil dataset training terbaru dari folder datasets.
     */
    private function getLatestDataset(): string
    {
        $files = glob(public_path('assets/datasets/dataset_*.csv'));
        if (empty($files)) {
            return '';
        }
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        return basename($files[0]);
    }

    /**
     * Rekonstruksi array $result dari koleksi record database.
     * Digunakan saat session kosong agar halaman hasil tetap tampil.
     */
    private function buildResultFromDb($records, string $type): array
    {
        $predictions    = [];
        $predictionStats = [];
        $hasLabel       = false;

        foreach ($records as $row) {
            $pred = $row->prediksi_knn ?? 'Normal';
            $label = $row->diagnosis ?? null;
            if ($label) $hasLabel = true;

            $predictionStats[$pred] = ($predictionStats[$pred] ?? 0) + 1;

            $predictions[] = [
                'nama'         => $row->nama,
                'usia'         => $row->usia,
                'paritas'      => $row->paritas,
                'tb'           => $row->tinggibadan,
                'bb'           => $row->beratbadan,
                'imt'          => $row->imt,
                'sistolik'     => $row->sistolik,
                'diastolik'    => $row->diastolik,
                'map'          => $row->map,
                'gds'          => $row->gds,
                'protein_urin' => $row->protein_urine,
                'label_asli'   => $row->diagnosis,
                'prediksi_knn' => $pred,
                'confidence'   => $row->confidence,
            ];
        }

        // Hitung akurasi jika ada label
        $accuracy = null;
        if ($hasLabel && count($predictions) > 0) {
            $correct = 0;
            foreach ($predictions as $p) {
                if ($p['label_asli'] !== null &&
                    strtolower($p['label_asli']) === strtolower($p['prediksi_knn'])) {
                    $correct++;
                }
            }
            $accuracy = round(($correct / count($predictions)) * 100, 2);
        }

        // Ambil best_k dari metadata jika ada
        $bestK = 5;
        if (file_exists(storage_path('app/knn_metadata.json'))) {
            $meta  = json_decode(file_get_contents(storage_path('app/knn_metadata.json')), true);
            $bestK = $meta['best_k'] ?? 5;
        }

        return $this->mergeResultWithSavedMetrics([
            'total_data'              => count($predictions),
            'predictions'             => $predictions,
            'prediction_stats'        => $predictionStats,
            'has_label'               => $hasLabel,
            'test_accuracy'           => $accuracy,
            'best_k'                  => $bestK,
            'confusion_matrix'        => null,
            'confusion_matrix_labels' => [],
            'classification_report'   => [],
        ], $type);
    }

    private function loadKnnMetadata(): array
    {
        if (!file_exists(storage_path('app/knn_metadata.json'))) {
            return [];
        }

        return json_decode(file_get_contents(storage_path('app/knn_metadata.json')), true) ?? [];
    }

    private function savePredictionMetrics(string $type, array $parsedData): void
    {
        $payload = [
            'confusion_matrix'        => $parsedData['confusion_matrix'] ?? null,
            'confusion_matrix_labels' => $parsedData['confusion_matrix_labels'] ?? [],
            'classification_report'   => $parsedData['classification_report'] ?? [],
            'test_accuracy'           => $parsedData['test_accuracy'] ?? null,
            'has_label'               => $parsedData['has_label'] ?? false,
            'total_data'              => $parsedData['total_data'] ?? count($parsedData['predictions'] ?? []),
            'evaluated_at'            => now()->format('d/m/Y H:i'),
        ];

        file_put_contents(
            storage_path("app/last_{$type}_prediction_metrics.json"),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function loadPredictionMetrics(string $type): array
    {
        $path = storage_path("app/last_{$type}_prediction_metrics.json");
        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function mergeResultWithSavedMetrics(array $result, string $type): array
    {
        $saved = $this->loadPredictionMetrics($type);

        if (empty($saved['confusion_matrix']) && !empty($result['predictions']) && ($result['has_label'] ?? false)) {
            $computed = $this->computeMetricsFromPredictions($result['predictions']);
            $saved = array_merge($saved, $computed);
        }

        foreach (['confusion_matrix', 'confusion_matrix_labels', 'classification_report', 'test_accuracy', 'has_label', 'evaluated_at', 'total_data'] as $key) {
            if (empty($result[$key]) && !empty($saved[$key])) {
                $result[$key] = $saved[$key];
            }
        }

        if (!empty($saved['confusion_matrix'])) {
            $result['has_label'] = true;
        }

        return $result;
    }

    private function computeMetricsFromPredictions(array $predictions): array
    {
        $labels = [];
        foreach ($predictions as $p) {
            if (!empty($p['label_asli'])) {
                $labels[] = $this->normalizeLabel($p['label_asli']);
            }
            if (!empty($p['prediksi_knn'])) {
                $labels[] = $this->normalizeLabel($p['prediksi_knn']);
            }
        }
        $labels = array_values(array_unique($labels));
        sort($labels);

        if (count($labels) < 2) {
            return [];
        }

        $index = array_flip($labels);
        $matrix = array_fill(0, count($labels), array_fill(0, count($labels), 0));
        $correct = 0;
        $total = 0;

        foreach ($predictions as $p) {
            if (empty($p['label_asli'])) {
                continue;
            }
            $actual = $this->normalizeLabel($p['label_asli']);
            $pred   = $this->normalizeLabel($p['prediksi_knn'] ?? '');
            if (!isset($index[$actual], $index[$pred])) {
                continue;
            }
            $matrix[$index[$actual]][$index[$pred]]++;
            $total++;
            if ($actual === $pred) {
                $correct++;
            }
        }

        if ($total === 0) {
            return [];
        }

        $report = [];
        foreach ($labels as $label) {
            $tp = $matrix[$index[$label]][$index[$label]];
            $fp = 0;
            $fn = 0;
            foreach ($labels as $other) {
                if ($other !== $label) {
                    $fp += $matrix[$index[$other]][$index[$label]];
                    $fn += $matrix[$index[$label]][$index[$other]];
                }
            }
            $precision = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0;
            $recall    = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0;
            $f1        = ($precision + $recall) > 0 ? 2 * $precision * $recall / ($precision + $recall) : 0;
            $support   = 0;
            foreach ($predictions as $p) {
                if (!empty($p['label_asli']) && $this->normalizeLabel($p['label_asli']) === $label) {
                    $support++;
                }
            }

            $report[$label] = [
                'precision' => $precision,
                'recall'    => $recall,
                'f1-score'  => $f1,
                'support'   => $support,
            ];
        }

        $report['accuracy'] = $correct / $total;

        return [
            'confusion_matrix'        => $matrix,
            'confusion_matrix_labels' => $labels,
            'classification_report'   => $report,
            'test_accuracy'           => round(($correct / $total) * 100, 2),
            'has_label'               => true,
        ];
    }

    private function normalizeLabel(string $label): string
    {
        $label = strtolower(trim($label));
        if (str_contains($label, 'preeklampsia') || str_contains($label, 'pre')) {
            return 'Preeklampsia';
        }

        return 'Normal';
    }

    /**
     * Parse JSON dari output Python (setelah marker).
     */
    private function parsePythonOutput(string $output): ?array
    {
        if (str_contains($output, '===== JSON DATA =====')) {
            $jsonString = trim(explode('===== JSON DATA =====', $output, 2)[1]);
        } else {
            $start = strrpos($output, '{"best_k"');
            if ($start === false) {
                $start = strrpos($output, '{"total_data"');
            }
            if ($start === false) {
                return null;
            }
            $jsonString = substr($output, $start);
        }

        $parsedData = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $parsedData;
    }
}
