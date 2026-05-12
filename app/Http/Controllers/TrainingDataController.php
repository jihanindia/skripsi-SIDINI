<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TrainingDataController extends Controller
{
    public function index()
    {
        $trainingData = \App\Models\TrainingData::all();
        return view('training-data.index', compact('trainingData'));
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
            return redirect()->back()->with('error', 'Gagal melatih model: ' . $process->getErrorOutput());
        }

        $output = $process->getOutput();
        
        // Menampilkan output dari Python ke terminal tempat 'php artisan serve' berjalan
        $terminalOutput = preg_replace('/===== JSON DATA =====.*/s', '', $output);
        
        $console = new \Symfony\Component\Console\Output\ConsoleOutput();
        $console->writeln("\n<info>[HASIL PELATIHAN MODEL KNN]</info>");
        $console->writeln(trim($terminalOutput));
        $console->writeln("");
        
        $accuracy = 0;
        $best_k = 0;

        // Parse JSON output from Python script and insert into DB
        if (preg_match('/===== JSON DATA =====\s*({.*})/s', $output, $jsonMatches)) {
            $parsedData = json_decode($jsonMatches[1], true);
            
            if (isset($parsedData['data']) && is_array($parsedData['data']) && count($parsedData['data']) > 0) {
                $accuracy = $parsedData['test_accuracy'] ?? $parsedData['cv_accuracy'] ?? $parsedData['accuracy'] ?? 0;
                $best_k = $parsedData['best_k'] ?? 0;
                $jsonData = $parsedData['data'];

                // Hapus data lama (opsional) atau update
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
                        'riw_ht_keluarga' => $row['riw_ht_keluarga'] ?? null,
                        'hb' => $row['hb'] ?? null,
                        'gds' => $row['gds'] ?? null,
                        'protein_urine' => $row['protein_urin'] ?? $row['protein_urine'] ?? null,
                        'diagnosis' => strtolower($row['status'] ?? 'normal'),
                        'prediksi_knn' => strtolower($row['prediksi_knn'] ?? 'normal'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                \App\Models\TrainingData::insert($insertData);
            }
        }

        return redirect()->back()->with([
            'success' => 'Model KNN berhasil dilatih!',
            'accuracy' => $accuracy,
            'output' => $output
        ]);
    }
}
