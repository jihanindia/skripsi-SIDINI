@extends('layouts.dashboard')

@section('title', 'Data Training - SIDINI')
@section('page-title', 'Data Training KNN')
@section('page-subtitle', 'Manajemen dataset untuk algoritma K-Nearest Neighbors')

@section('content')
<!-- Info Alert -->
<div class="alert alert-info" style="margin-bottom: 2rem;">
    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <div>
        <strong>Tentang Data Training:</strong> Dataset ini digunakan untuk melatih algoritma KNN dalam memprediksi risiko preeklampsia. Semakin banyak data yang valid, semakin akurat prediksi sistem.
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 2rem; background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem;">
    <strong>Sukses: </strong> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger" style="margin-bottom: 2rem; background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem;">
    <strong>Error: </strong> {{ session('error') }}
</div>
@endif

<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <p class="stat-label">Total Data</p>
        <p class="stat-value" style="color: var(--color-medical-primary);">{{ isset($trainingData) ? $trainingData->count() : 0 }}</p>
    </div>
    
    <div class="stat-card">
        <p class="stat-label">Data Tervalidasi</p>
        <p class="stat-value" style="color: var(--color-medical-success);">{{ isset($trainingData) ? $trainingData->count() : 0 }}</p>
    </div>
    
    <div class="stat-card">
        <p class="stat-label">Akurasi Model</p>
        <p class="stat-value" style="color: var(--color-medical-info);">
            {{ session('accuracy') ?? $metadata['accuracy'] ?? '--' }}%
        </p>
    </div>
    
    <div class="stat-card">
        <p class="stat-label">K Value</p>
        <p class="stat-value" style="color: var(--color-medical-warning);">
            {{ session('best_k') ?? $metadata['best_k'] ?? '--' }}
        </p>
    </div>
</div>

<!-- Action Bar -->
<form action="{{ route('training-data.train') }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
    @csrf
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <input type="file" name="dataset" id="dataset" accept=".csv" style="display: none;" onchange="document.getElementById('file-name').innerText = this.files[0] ? this.files[0].name : 'Belum ada file dipilih'">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('dataset').click()">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Pilih File CSV
            </button>
            <span id="file-name" style="color: var(--color-gray-600); font-size: 0.875rem; font-weight: 500;">Belum ada file dipilih</span>
        </div>
        
        <button type="submit" class="btn btn-success">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
            </svg>
            Upload & Train Model KNN
        </button>
    </div>
</form>

<!-- Training Data Table -->
<div class="medical-card fade-in">
    <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 1.5rem 0;">
        📚 Dataset Training
    </h3>
    
    <div style="overflow-x: auto;">
        <table class="medical-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Usia</th>
                    <th>Paritas</th>
                    <th>BB (kg)</th>
                    <th>TB (cm)</th>
                    <th>IMT</th>
                    <th>Sistolik</th>
                    <th>Diastolik</th>
                    <th>MAP</th>
                    <th>GDS</th>
                    <th>Protein Urin</th>
                    <th>Diagnosis Asli</th>
                    <th>Prediksi KNN</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($trainingData) && $trainingData->count() > 0)
                    @foreach($trainingData as $index => $data)
                    <tr>
                        <td>{{ $data->id }}</td>
                        <td>{{ $data->nama }}</td>
                        <td>{{ $data->usia }}</td>
                        <td>{{ $data->paritas }}</td>
                        <td>{{ $data->beratbadan ?? '-' }}</td>
                        <td>{{ $data->tinggibadan ?? '-' }}</td>
                        <td>{{ $data->imt ?? '-' }}</td>
                        <td>{{ $data->sistolik }}</td>
                        <td>{{ $data->diastolik }}</td>
                        <td>{{ $data->map }}</td>
                        <td>{{ $data->gds }}</td>
                        <td>{{ $data->protein_urine }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: {{ strtolower($data->diagnosis) === 'preeklampsia' ? '#fee2e2' : '#d1fae5' }}; color: {{ strtolower($data->diagnosis) === 'preeklampsia' ? '#991b1b' : '#065f46' }};">
                                {{ ucfirst($data->diagnosis) }}
                            </span>
                        </td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: {{ strtolower($data->prediksi_knn) === 'preeklampsia' ? '#fee2e2' : '#d1fae5' }}; color: {{ strtolower($data->prediksi_knn) === 'preeklampsia' ? '#991b1b' : '#065f46' }};">
                                {{ ucfirst($data->prediksi_knn ?? '-') }}
                            </span>
                        </td>
                        <td>
                            <!-- Aksi bisa dimasukkan di sini jika ada -->
                        </td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="15" style="text-align: center; padding: 3rem; color: var(--color-gray-400);">
                        <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <p style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.5rem 0;">Belum Ada Data Training</p>
                        <p style="font-size: 0.875rem; margin: 0;">Import data CSV atau tambahkan data secara manual untuk melatih model KNN</p>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Model Info -->
<div class="medical-card fade-in" style="margin-top: 2rem; animation-delay: 0.1s;">
    <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 1rem 0;">
        🤖 Informasi Model KNN
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div>
            <p style="font-weight: 600; color: var(--color-gray-700); margin: 0 0 0.5rem 0;">Algoritma</p>
            <p style="color: var(--color-gray-600); margin: 0;">K-Nearest Neighbors (KNN)</p>
        </div>
        <div>
            <p style="font-weight: 600; color: var(--color-gray-700); margin: 0 0 0.5rem 0;">Distance Metric</p>
            <p style="color: var(--color-gray-600); margin: 0;">Euclidean Distance</p>
        </div>
        <div>
            <p style="font-weight: 600; color: var(--color-gray-700); margin: 0 0 0.5rem 0;">Features</p>
            <p style="color: var(--color-gray-600); margin: 0;">10 Parameter Medis</p>
        </div>
        <div>
            <p style="font-weight: 600; color: var(--color-gray-700); margin: 0 0 0.5rem 0;">Target Classes</p>
            <p style="color: var(--color-gray-600); margin: 0;">2 Kategori</p>
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- CONFUSION MATRIX & EVALUASI --}}
{{-- ============================== --}}
@if(isset($metadata['confusion_matrix']) && is_array($metadata['confusion_matrix']))
<div class="medical-card fade-in" style="margin-top: 2rem; animation-delay: 0.2s;">
    <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 1.5rem 0;">
        📊 Confusion Matrix & Evaluasi Model
    </h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">

        {{-- CONFUSION MATRIX GRID --}}
        <div>
            <p style="font-weight: 600; color: var(--color-gray-700); margin: 0 0 1rem 0; font-size: 0.9rem;">Confusion Matrix</p>

            @php
                $cm     = $metadata['confusion_matrix'];
                $labels = $metadata['confusion_matrix_labels'] ?? [];

                // Fallback labels jika kosong
                if (empty($labels)) {
                    $labels = count($cm) === 2 ? ['Normal', 'Preeklampsia'] : array_map(fn($i) => "Kelas $i", range(0, count($cm)-1));
                }

                $n = count($labels);

                // Warna sel berdasarkan diagonal (TP) vs off-diagonal (FP/FN)
                $cellColors = [
                    'diagonal'    => ['bg' => '#d1fae5', 'color' => '#065f46'],
                    'off_diagonal'=> ['bg' => '#fee2e2', 'color' => '#991b1b'],
                ];
            @endphp

            <div style="overflow-x: auto;">
                <table style="border-collapse: separate; border-spacing: 4px; width: 100%;">
                    <thead>
                        <tr>
                            <th style="padding: 0.5rem; font-size: 0.7rem; color: var(--color-gray-500); text-align: center; background: transparent;"></th>
                            <th colspan="{{ $n }}" style="padding: 0.5rem 0.25rem; font-size: 0.72rem; font-weight: 700; color: var(--color-gray-700); text-align: center; background: #f1f5f9; border-radius: 6px;">
                                Prediksi
                            </th>
                        </tr>
                        <tr>
                            <th style="padding: 0.4rem; font-size: 0.68rem; color: var(--color-gray-500); text-align: center; font-weight: 700; background: #f1f5f9; border-radius: 6px; writing-mode: vertical-rl; transform: rotate(180deg); min-width: 40px;">
                                Aktual
                            </th>
                            @foreach($labels as $label)
                            <th style="padding: 0.5rem 0.75rem; font-size: 0.7rem; font-weight: 700; color: var(--color-gray-700); text-align: center; background: #e2e8f0; border-radius: 6px; max-width: 100px; word-break: break-word;">
                                {{ ucfirst($label) }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cm as $rowIdx => $row)
                        <tr>
                            <td style="padding: 0.5rem 0.75rem; font-size: 0.7rem; font-weight: 700; color: var(--color-gray-700); text-align: center; background: #e2e8f0; border-radius: 6px; max-width: 100px; word-break: break-word;">
                                {{ ucfirst($labels[$rowIdx] ?? "Kelas $rowIdx") }}
                            </td>
                            @foreach($row as $colIdx => $val)
                            @php
                                $isDiag = ($rowIdx === $colIdx);
                                $bgColor    = $isDiag ? '#d1fae5' : '#fee2e2';
                                $textColor  = $isDiag ? '#065f46' : '#991b1b';
                                $label_hint = $isDiag ? 'TP' : ($rowIdx < $colIdx ? 'FN' : 'FP');
                            @endphp
                            <td title="{{ $label_hint }}: {{ $val }}" style="
                                    padding: 0.85rem 1rem;
                                    text-align: center;
                                    font-size: 1.25rem;
                                    font-weight: 800;
                                    background-color: {{ $bgColor }};
                                    color: {{ $textColor }};
                                    border-radius: 8px;
                                    min-width: 70px;
                                    position: relative;
                                ">
                                {{ $val }}
                                <span style="display: block; font-size: 0.6rem; font-weight: 500; opacity: 0.7; margin-top: 2px;">
                                    {{ $isDiag ? '✓ Benar' : '✗ Salah' }}
                                </span>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.72rem; color: var(--color-gray-600);">
                    <span style="width: 14px; height: 14px; border-radius: 3px; background: #d1fae5; display:inline-block;"></span> Prediksi Benar (Diagonal)
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.72rem; color: var(--color-gray-600);">
                    <span style="width: 14px; height: 14px; border-radius: 3px; background: #fee2e2; display:inline-block;"></span> Prediksi Salah
                </div>
            </div>
        </div>

        {{-- PRECISION, RECALL, F1-SCORE --}}
        @if(isset($metadata['classification_report']) && is_array($metadata['classification_report']))
        @php
            $report = $metadata['classification_report'];
            $classMetrics = [];
            foreach ($report as $key => $val) {
                if (is_array($val) && isset($val['precision']) && !str_contains(strtolower($key), 'avg')) {
                    $classMetrics[$key] = $val;
                }
            }
            $avgKeys = ['macro avg', 'weighted avg', 'accuracy'];
        @endphp
        <div>
            <p style="font-weight: 600; color: var(--color-gray-700); margin: 0 0 1rem 0; font-size: 0.9rem;">Metrik per Kelas</p>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($classMetrics as $className => $metrics)
                @php
                    $precision = round(($metrics['precision'] ?? 0) * 100, 1);
                    $recall    = round(($metrics['recall'] ?? 0) * 100, 1);
                    $f1        = round(($metrics['f1-score'] ?? 0) * 100, 1);
                    $support   = $metrics['support'] ?? 0;
                    $isPreek   = str_contains(strtolower($className), 'preeklampsia') || str_contains(strtolower($className), 'pre');
                    $badgeColor = $isPreek ? '#fee2e2' : '#d1fae5';
                    $badgeText  = $isPreek ? '#991b1b' : '#065f46';
                @endphp
                <div style="background: #f8fafc; border-radius: 10px; padding: 1rem 1.25rem; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="
                            padding: 0.2rem 0.65rem;
                            border-radius: 9999px;
                            font-size: 0.72rem;
                            font-weight: 700;
                            background-color: {{ $badgeColor }};
                            color: {{ $badgeText }};
                        ">{{ ucwords($className) }}</span>
                        <span style="font-size: 0.7rem; color: var(--color-gray-500);">Support: {{ $support }}</span>
                    </div>

                    {{-- Precision --}}
                    <div style="margin-bottom: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                            <span style="font-size: 0.72rem; color: var(--color-gray-600); font-weight: 600;">Precision</span>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #1e40af;">{{ $precision }}%</span>
                        </div>
                        <div style="background: #e2e8f0; border-radius: 999px; height: 6px;">
                            <div style="width: {{ $precision }}%; background: linear-gradient(90deg, #3b82f6, #1d4ed8); border-radius: 999px; height: 6px; transition: width 0.6s ease;"></div>
                        </div>
                    </div>

                    {{-- Recall --}}
                    <div style="margin-bottom: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                            <span style="font-size: 0.72rem; color: var(--color-gray-600); font-weight: 600;">Recall</span>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #059669;">{{ $recall }}%</span>
                        </div>
                        <div style="background: #e2e8f0; border-radius: 999px; height: 6px;">
                            <div style="width: {{ $recall }}%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 999px; height: 6px; transition: width 0.6s ease;"></div>
                        </div>
                    </div>

                    {{-- F1-Score --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                            <span style="font-size: 0.72rem; color: var(--color-gray-600); font-weight: 600;">F1-Score</span>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #7c3aed;">{{ $f1 }}%</span>
                        </div>
                        <div style="background: #e2e8f0; border-radius: 999px; height: 6px;">
                            <div style="width: {{ $f1 }}%; background: linear-gradient(90deg, #8b5cf6, #6d28d9); border-radius: 999px; height: 6px; transition: width 0.6s ease;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Macro & Weighted Avg --}}
            @if(isset($report['macro avg']) || isset($report['weighted avg']))
            <div style="margin-top: 1rem; background: linear-gradient(135deg, #eff6ff, #f5f3ff); border-radius: 10px; padding: 0.85rem 1.25rem; border: 1px solid #c7d2fe;">
                <p style="font-size: 0.75rem; font-weight: 700; color: var(--color-gray-700); margin: 0 0 0.6rem 0;">📈 Rata-Rata Keseluruhan</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                    @foreach(['macro avg', 'weighted avg'] as $avgKey)
                    @if(isset($report[$avgKey]))
                    @php $avg = $report[$avgKey]; @endphp
                    <div style="background: white; border-radius: 8px; padding: 0.6rem 0.75rem;">
                        <p style="font-size: 0.65rem; font-weight: 700; color: var(--color-gray-500); margin: 0 0 0.35rem 0; text-transform: uppercase; letter-spacing: 0.04em;">{{ ucwords($avgKey) }}</p>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-size: 0.68rem; color: #1e40af; font-weight: 600;">P: {{ round(($avg['precision'] ?? 0)*100, 1) }}%</span>
                            <span style="font-size: 0.68rem; color: #059669; font-weight: 600;">R: {{ round(($avg['recall'] ?? 0)*100, 1) }}%</span>
                            <span style="font-size: 0.68rem; color: #7c3aed; font-weight: 600;">F1: {{ round(($avg['f1-score'] ?? 0)*100, 1) }}%</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

        </div>
        @endif

    </div>
</div>
@endif

@endsection

