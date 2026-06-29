<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class TrainingDataController extends Controller
{
    public function index()
    {
        $trainingData = \App\Models\TrainingData::all();

        $metadata = [];
        if ($trainingData->count() > 0 && file_exists(storage_path('app/knn_metadata.json'))) {
            $metadata = json_decode(file_get_contents(storage_path('app/knn_metadata.json')), true);
        }

        return view('training-data.index', compact('trainingData', 'metadata'));
    }

    public function train(Request $request)
    {
        $request->validate([
            'dataset' => 'required|file|mimes:csv,txt'
        ], [
            'dataset.required' => 'File dataset CSV wajib diupload.',
            'dataset.mimes' => 'Format file tidak didukung. Pastikan file berformat CSV.'
        ]);

        $file = $request->file('dataset');
        if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
            return redirect()->back()->with('error', 'Hanya file dengan ekstensi .csv yang diizinkan.');
        }

        $fileName = 'dataset_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/datasets'), $fileName);
        $filePath = public_path('assets/datasets/' . $fileName);

        $pythonScriptPath = base_path('app/Python/knn.py');
        $command = ['python', $pythonScriptPath, $filePath];

        $env = [
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'PATH' => getenv('PATH')
        ];

        $process = new Process($command, base_path(), $env);
        $process->run();

        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            if (empty($errorOutput)) {
                $errorOutput = $process->getOutput();
            }
            return redirect()->back()->with('error', 'Gagal melatih model: ' . $errorOutput);
        }

        $output = $process->getOutput();

        $terminalOutput = preg_replace('/===== JSON DATA =====.*/s', '', $output);
        $console = new \Symfony\Component\Console\Output\ConsoleOutput();
        $console->writeln("\n<info>[HASIL PELATIHAN MODEL KNN]</info>");
        $console->writeln(trim($terminalOutput));
        $console->writeln("");

        $parsedData = $this->parsePythonOutput($output);

        if ($parsedData === null) {
            Log::warning('Gagal mem-parse output Python.', [
                'json_error' => json_last_error_msg(),
                'output_tail' => substr($output, -500),
            ]);
            return redirect()->back()->with(
                'error',
                'Model KNN selesai dijalankan, tetapi Laravel tidak dapat membaca hasil JSON. Periksa storage/logs/laravel.log.'
            );
        }

        $accuracy = $parsedData['test_accuracy'] ?? $parsedData['cv_accuracy'] ?? $parsedData['accuracy'] ?? 0;
        $best_k = $parsedData['best_k'] ?? 0;
        $jsonData = $parsedData['data'] ?? [];

        if (!is_array($jsonData) || count($jsonData) === 0) {
            return redirect()->back()->with('error', 'Tidak ada data yang dikembalikan dari skrip Python.');
        }

        try {
            DB::transaction(function () use ($jsonData, $accuracy, $best_k, $parsedData) {
                \App\Models\TrainingData::truncate();

                $insertData = [];
                foreach ($jsonData as $row) {
                    $insertData[] = [
                        'nama' => $row['nama'] ?? null,
                        'usia' => $row['usia'] ?? null,
                        'paritas' => $row['paritas'] ?? null,
                        'tinggibadan' => $row['tb'] ?? $row['tinggibadan'] ?? null,
                        'beratbadan' => $row['bb'] ?? $row['beratbadan'] ?? null,
                        'imt' => $row['imt'] ?? null,
                        'sistolik' => $row['sistol'] ?? $row['sistolik'] ?? null,
                        'diastolik' => $row['diastol'] ?? $row['diastolik'] ?? null,
                        'map' => $row['map'] ?? null,
                        'gds' => $row['gds'] ?? null,
                        'protein_urine' => $row['protein_urin'] ?? $row['protein_urine'] ?? null,
                        'diagnosis' => strtolower($row['status'] ?? 'normal'),
                        'prediksi_knn' => strtolower($row['prediksi_knn'] ?? 'normal'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach (array_chunk($insertData, 100) as $chunk) {
                    \App\Models\TrainingData::insert($chunk);
                }

                file_put_contents(storage_path('app/knn_metadata.json'), json_encode([
                    'accuracy'                 => $accuracy,
                    'best_k'                   => $best_k,
                    'confusion_matrix'         => $parsedData['confusion_matrix'] ?? null,
                    'confusion_matrix_labels'  => $parsedData['classification_report']
                                                    ? array_values(array_keys(array_filter(
                                                          $parsedData['classification_report'],
                                                          fn($v, $k) => is_array($v) && isset($v['precision'])
                                                                    && !str_contains(strtolower($k), 'avg'),
                                                          ARRAY_FILTER_USE_BOTH
                                                      )))
                                                    : null,
                    'classification_report'    => $parsedData['classification_report'] ?? null,
                    'trained_at'               => now()->toDateTimeString(),
                    'total_records'            => count($insertData),
                ]));
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan data training ke database: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Data KNN berhasil diolah, tetapi gagal disimpan ke database: ' . $e->getMessage()
                . ' — pastikan migrasi kolom string sudah dijalankan (php artisan migrate).'
            );
        }

        return redirect()->back()->with([
            'success' => 'Model KNN berhasil dilatih dan ' . count($jsonData) . ' data tersimpan ke database.',
            'accuracy' => $accuracy,
            'best_k' => $best_k,
        ]);
    }

    /**
     * Ambil JSON dari output Python (marker atau fallback blok JSON).
     */
    private function parsePythonOutput(string $output): ?array
    {
        if (str_contains($output, '===== JSON DATA =====')) {
            $jsonString = trim(explode('===== JSON DATA =====', $output, 2)[1]);
        } else {
            $start = strrpos($output, '{"best_k"');
            if ($start === false) {
                $start = strrpos($output, '{"data"');
            }
            if ($start === false) {
                return null;
            }
            $jsonString = substr($output, $start);
        }

        $parsedData = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($parsedData['data'])) {
            return null;
        }

        return $parsedData;
    }
}
