@extends('layouts.dashboard')

@section('title', 'Hasil Prediksi Data Testing - SIDINI')
@section('page-title', 'Hasil Prediksi Data Testing')
@section('page-subtitle', 'Hasil prediksi KNN untuk file: {{ $testFileName ?? "-" }}')

@section('content')

<style>
    /* ======== Summary Cards ======== */
    .result-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .result-stat-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        text-align: center;
        border-top: 4px solid;
        transition: transform 0.2s;
    }
    .result-stat-card:hover { transform: translateY(-3px); }
    .stat-number { font-size: 2rem; font-weight: 800; margin: 0.25rem 0; }
    .stat-desc { font-size: 0.78rem; color: #6b7280; font-weight: 500; }

    /* ======== Confusion Matrix ======== */
    .cm-wrapper {
        overflow-x: auto;
        margin: 1.25rem 0;
    }
    .cm-table {
        border-collapse: separate;
        border-spacing: 6px;
        margin: 0 auto;
    }
    .cm-table th {
        font-size: 0.8rem;
        font-weight: 700;
        color: #374151;
        padding: 0.5rem 1rem;
        text-align: center;
    }
    .cm-cell {
        width: 110px; height: 90px;
        border-radius: 12px;
        text-align: center;
        vertical-align: middle;
        font-size: 1.6rem;
        font-weight: 800;
        transition: transform 0.2s;
        cursor: default;
    }
    .cm-cell:hover { transform: scale(1.05); }
    .cm-cell-tp { background: linear-gradient(135deg,#bbf7d0,#86efac); color: #14532d; }
    .cm-cell-tn { background: linear-gradient(135deg,#bbf7d0,#86efac); color: #14532d; }
    .cm-cell-fp { background: linear-gradient(135deg,#fde68a,#fbbf24); color: #78350f; }
    .cm-cell-fn { background: linear-gradient(135deg,#fca5a5,#f87171); color: #7f1d1d; }
    .cm-cell-other { background: #f3f4f6; color: #374151; }
    .cm-label-row th { writing-mode: horizontal-tb; }
    .cm-axis-label {
        font-size: 0.72rem; color: #9ca3af; font-weight: 600; letter-spacing: 0.05em;
        text-transform: uppercase; padding: 0.35rem 0.5rem;
    }

    /* ======== Classification Report ======== */
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table th {
        background: linear-gradient(90deg, #D81B60, #880E4F);
        color: white; padding: 0.7rem 1rem; font-size: 0.8rem;
        font-weight: 600; text-align: right;
    }
    .report-table th:first-child { text-align: left; border-radius: 8px 0 0 0; }
    .report-table th:last-child  { border-radius: 0 8px 0 0; }
    .report-table td { padding: 0.65rem 1rem; font-size: 0.85rem; text-align: right; border-bottom: 1px solid #f3f4f6; }
    .report-table td:first-child { text-align: left; font-weight: 600; }
    .report-table tr:hover td { background: #fdf2f8; }
    .report-table tr.total-row td { background: #fdf2f8; font-weight: 700; border-top: 2px solid #e2b4c8; }

    /* ======== Progress bar for metric ======== */
    .metric-bar-wrap { display: flex; align-items: center; gap: 0.75rem; }
    .metric-bar-bg { flex: 1; height: 8px; background: #f3f4f6; border-radius: 99px; overflow: hidden; }
    .metric-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #D81B60, #ec4899); }

    /* ======== Results Table ======== */
    .pred-table { width: 100%; border-collapse: collapse; font-size: 0.825rem; }
    .pred-table th { background: #f9fafb; color: #374151; padding: 0.6rem 0.85rem; text-align: left; font-weight: 700; border-bottom: 2px solid #e5e7eb; white-space: nowrap; }
    .pred-table td { padding: 0.55rem 0.85rem; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .pred-table tr:hover td { background: #fdf2f8; }
    .badge-pred-pre  { padding: 0.2rem 0.65rem; border-radius: 99px; font-size: 0.72rem; font-weight: 700; background: #fee2e2; color: #991b1b; }
    .badge-pred-norm { padding: 0.2rem 0.65rem; border-radius: 99px; font-size: 0.72rem; font-weight: 700; background: #d1fae5; color: #065f46; }
    .badge-pred-other { padding: 0.2rem 0.65rem; border-radius: 99px; font-size: 0.72rem; font-weight: 700; background: #e0e7ff; color: #3730a3; }
    .conf-bar { height: 6px; border-radius: 99px; background: linear-gradient(90deg, #D81B60, #ec4899); }
    .correct-icon { color: #16a34a; }
    .wrong-icon   { color: #dc2626; }

    /* ======== Tabs ======== */
    .tab-buttons { display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
    .tab-btn {
        padding: 0.5rem 1.1rem; border-radius: 8px; border: 2px solid #e5e7eb;
        background: white; font-size: 0.85rem; font-weight: 600; cursor: pointer;
        color: #6b7280; transition: all 0.2s;
    }
    .tab-btn.active { border-color: #D81B60; color: #D81B60; background: #fdf2f8; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ======== Search ======== */
    .search-input {
        padding: 0.5rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px;
        font-size: 0.875rem; outline: none; transition: border-color 0.2s;
        width: 260px;
    }
    .search-input:focus { border-color: #D81B60; }

    /* ======== Donut Chart ======== */
    .donut-wrap { display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; }
    .donut-legend { display: flex; flex-direction: column; gap: 0.75rem; }
    .legend-item { display: flex; align-items: center; gap: 0.6rem; font-size: 0.875rem; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
</style>

<!-- Action Buttons -->
<div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; align-items:center;">
    <a href="{{ route('predictions.test.index') }}" style="display:inline-flex; align-items:center; gap:0.5rem; color:#ad1457; font-weight:600; text-decoration:none; font-size:0.9rem; padding:0.55rem 1.1rem; border:2px solid #f8bbd0; border-radius:9px; background:#fff0f6; transition:all 0.2s;" onmouseover="this.style.background='#fce4ec'" onmouseout="this.style.background='#fff0f6'">
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
    <button onclick="document.getElementById('uploadModal').style.display='flex'" style="display:inline-flex; align-items:center; gap:0.5rem; color:white; font-weight:600; font-size:0.9rem; padding:0.55rem 1.25rem; border:none; border-radius:9px; background:linear-gradient(135deg,#c2185b,#880e4f); cursor:pointer; box-shadow:0 2px 8px rgba(136,14,79,0.3); transition:all 0.2s;" onmouseover="this.style.background='linear-gradient(135deg,#880e4f,#560027)'" onmouseout="this.style.background='linear-gradient(135deg,#c2185b,#880e4f)'">
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Upload CSV Baru
    </button>
    <span style="font-size:0.8rem; color:#9ca3af; padding:0.4rem 0.75rem; background:#f9fafb; border-radius:8px; border:1px solid #e5e7eb;">
        💡 Data baru akan <strong>ditambahkan</strong> ke database
    </span>
</div>

<!-- Upload Modal -->
<div id="uploadModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:white; border-radius:20px; padding:2rem; max-width:480px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUp 0.3s ease;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="margin:0; font-size:1.1rem; font-weight:700; color:#1e293b;">📂 Upload CSV Data Uji Baru</h3>
            <button onclick="document.getElementById('uploadModal').style.display='none'" style="background:none; border:none; cursor:pointer; color:#9ca3af; font-size:1.5rem; line-height:1; padding:0.25rem;" title="Tutup">×</button>
        </div>
        <p style="font-size:0.85rem; color:#6b7280; margin:0 0 1.25rem;">Data hasil prediksi akan <strong>ditambahkan</strong> ke database (tidak menghapus data lama).</p>
        <form method="POST" action="{{ route('predictions.test.predict') }}" enctype="multipart/form-data">
            @csrf
            <div style="border:2px dashed #f8bbd0; border-radius:12px; padding:1.5rem; text-align:center; margin-bottom:1.25rem; background:#fff0f6; cursor:pointer;" onclick="document.getElementById('modalCsvInput').click()">
                <svg style="width:36px;height:36px;color:#c2185b;margin:0 auto 0.5rem;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <p style="font-weight:600; color:#c2185b; margin:0 0 0.25rem;">Klik untuk pilih file CSV</p>
                <p id="modalFileName" style="font-size:0.8rem; color:#9ca3af; margin:0;">Belum ada file dipilih</p>
            </div>
            <input type="file" id="modalCsvInput" name="test_dataset" accept=".csv" style="display:none;" onchange="document.getElementById('modalFileName').textContent = this.files[0]?.name || 'Belum ada file dipilih'">
            <div style="display:flex; gap:0.75rem;">
                <button type="button" onclick="document.getElementById('uploadModal').style.display='none'" style="flex:1; padding:0.65rem; border:2px solid #e5e7eb; border-radius:9px; background:white; font-weight:600; color:#6b7280; cursor:pointer; font-size:0.875rem;">Batal</button>
                <button type="submit" style="flex:2; padding:0.65rem; border:none; border-radius:9px; background:linear-gradient(135deg,#c2185b,#880e4f); color:white; font-weight:700; cursor:pointer; font-size:0.875rem; box-shadow:0 2px 8px rgba(136,14,79,0.3);">🚀 Proses Prediksi</button>
            </div>
        </form>
    </div>
</div>
<style>
@keyframes slideUp { from { transform:translateY(30px); opacity:0; } to { transform:translateY(0); opacity:1; } }
</style>

<!-- ===== SUMMARY STATS ===== -->
<div class="result-stats">
    <div class="result-stat-card" style="border-top-color:#D81B60;">
        <p class="stat-desc">Total Data Diprediksi</p>
        <p class="stat-number" style="color:#D81B60;">{{ $result['total_data'] }}</p>
        <p class="stat-desc">Baris</p>
    </div>

    @php
        $stats = $result['prediction_stats'] ?? [];
        $preCount  = 0; $normCount = 0;
        foreach ($stats as $label => $count) {
            if (str_contains(strtolower($label), 'pre')) $preCount  += $count;
            else                                          $normCount += $count;
        }
    @endphp

    <div class="result-stat-card" style="border-top-color:#ef4444;">
        <p class="stat-desc">Preeklampsia</p>
        <p class="stat-number" style="color:#ef4444;">{{ $preCount }}</p>
        <p class="stat-desc">{{ $result['total_data'] > 0 ? round($preCount / $result['total_data'] * 100, 1) : 0 }}% dari total</p>
    </div>

    <div class="result-stat-card" style="border-top-color:#16a34a;">
        <p class="stat-desc">Normal</p>
        <p class="stat-number" style="color:#16a34a;">{{ $normCount }}</p>
        <p class="stat-desc">{{ $result['total_data'] > 0 ? round($normCount / $result['total_data'] * 100, 1) : 0 }}% dari total</p>
    </div>

    <div class="result-stat-card" style="border-top-color:#7c3aed;">
        <p class="stat-desc">Nilai K</p>
        <p class="stat-number" style="color:#7c3aed;">{{ $result['best_k'] }}</p>
        <p class="stat-desc">KNN Neighbors</p>
    </div>

    @if($result['has_label'] && $result['test_accuracy'] !== null)
    <div class="result-stat-card" style="border-top-color:#0891b2;">
        <p class="stat-desc">Akurasi</p>
        <p class="stat-number" style="color:#0891b2;">{{ number_format($result['test_accuracy'], 1) }}%</p>
        <p class="stat-desc">Data Uji Berlabel</p>
    </div>
    @endif
</div>

<!-- ===== TABS ===== -->
<div class="tab-buttons">
    <button class="tab-btn active" onclick="switchTab('tab-dist')">📊 Distribusi Prediksi</button>
    @if($result['has_label'] && $result['confusion_matrix'])
    <button class="tab-btn" onclick="switchTab('tab-cm')">🔢 Confusion Matrix</button>
    <button class="tab-btn" onclick="switchTab('tab-report')">📈 Classification Report</button>
    @endif
    <button class="tab-btn" onclick="switchTab('tab-data')">📋 Data Hasil Prediksi</button>
</div>

<!-- ===== TAB: DISTRIBUSI ===== -->
<div id="tab-dist" class="tab-content active">
    <div class="medical-card fade-in">
        <h3 style="font-size:1.1rem; font-weight:700; color:var(--color-gray-800); margin:0 0 1.5rem;">
            📊 Distribusi Hasil Prediksi
        </h3>
        <div class="donut-wrap">
            <div>
                <canvas id="donutChart" width="200" height="200"></canvas>
            </div>
            <div class="donut-legend">
                @foreach($result['prediction_stats'] ?? [] as $label => $count)
                @php
                    $isPre = str_contains(strtolower($label), 'pre');
                    $pct   = $result['total_data'] > 0 ? round($count / $result['total_data'] * 100, 1) : 0;
                @endphp
                <div class="legend-item">
                    <div class="legend-dot" style="background: {{ $isPre ? '#ef4444' : '#16a34a' }};"></div>
                    <span style="font-weight:600; color:var(--color-gray-700);">{{ ucfirst($label) }}</span>
                    <span style="color:#9ca3af;">—</span>
                    <span style="font-weight:700;">{{ $count }}</span>
                    <span style="font-size:0.78rem; color:#9ca3af;">({{ $pct }}%)</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Bar Breakdown -->
        <div style="margin-top:2rem;">
            @foreach($result['prediction_stats'] ?? [] as $label => $count)
            @php
                $isPre = str_contains(strtolower($label), 'pre');
                $pct   = $result['total_data'] > 0 ? round($count / $result['total_data'] * 100, 1) : 0;
            @endphp
            <div style="margin-bottom:1rem;">
                <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem;">
                    <span style="font-weight:600; font-size:0.9rem; color:var(--color-gray-700);">{{ ucfirst($label) }}</span>
                    <span style="font-weight:700; font-size:0.9rem; color:{{ $isPre ? '#ef4444' : '#16a34a' }};">{{ $count }} ({{ $pct }}%)</span>
                </div>
                <div style="height:12px; background:#f3f4f6; border-radius:99px; overflow:hidden;">
                    <div style="height:100%; width:{{ $pct }}%; background:{{ $isPre ? 'linear-gradient(90deg,#ef4444,#dc2626)' : 'linear-gradient(90deg,#16a34a,#15803d)' }}; border-radius:99px; transition: width 1s ease;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ===== TAB: CONFUSION MATRIX ===== -->
@if($result['has_label'] && $result['confusion_matrix'])
<div id="tab-cm" class="tab-content">
    <div class="medical-card fade-in">
        <h3 style="font-size:1.1rem; font-weight:700; color:var(--color-gray-800); margin:0 0 0.5rem;">
            🔢 Confusion Matrix
        </h3>
        <p style="color:var(--color-gray-500); font-size:0.85rem; margin:0 0 1.5rem;">
            Perbandingan antara prediksi KNN dengan label asli pada data uji
        </p>

        @php
            $cm     = $result['confusion_matrix'];
            $labels = $result['confusion_matrix_labels'] ?? [];
            $n      = count($labels);
        @endphp

        <div class="cm-wrapper">
            <table class="cm-table">
                <!-- Header: Predicted -->
                <tr>
                    <td colspan="2" rowspan="2"></td>
                    <th colspan="{{ $n }}" style="text-align:center; color:#D81B60; padding-bottom:0.25rem;">
                        Prediksi KNN
                    </th>
                </tr>
                <tr>
                    @foreach($labels as $lbl)
                    <th style="text-align:center; min-width:110px;">{{ ucfirst($lbl) }}</th>
                    @endforeach
                </tr>

                <!-- Rows: Actual -->
                @foreach($labels as $i => $rowLabel)
                <tr>
                    @if($i === 0)
                    <th rowspan="{{ $n }}" style="writing-mode:vertical-rl; transform:rotate(180deg); color:#D81B60; padding-right:0.5rem;">
                        Label Asli
                    </th>
                    @endif
                    <th style="text-align:right;">{{ ucfirst($rowLabel) }}</th>

                    @foreach($labels as $j => $colLabel)
                    @php
                        $val = $cm[$i][$j] ?? 0;
                        if ($i === $j) {
                            $cellClass = 'cm-cell-tp';
                        } elseif ($j > $i) {
                            $cellClass = 'cm-cell-fp';
                        } else {
                            $cellClass = 'cm-cell-fn';
                        }
                        if ($i === $j) $tooltip = "Benar: {$val} prediksi " . ucfirst($colLabel) . " sesuai label asli";
                        elseif ($j > $i) $tooltip = "Salah: {$val} seharusnya " . ucfirst($rowLabel) . " diprediksi " . ucfirst($colLabel);
                        else $tooltip = "Salah: {$val} seharusnya " . ucfirst($rowLabel) . " diprediksi " . ucfirst($colLabel);
                    @endphp
                    <td class="cm-cell {{ $cellClass }}" title="{{ $tooltip }}">
                        {{ $val }}
                        <div style="font-size:0.6rem; font-weight:500; opacity:0.75; margin-top:2px;">
                            @if($i === $j) ✓ Benar @else ✗ Salah @endif
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Legend -->
        <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:1rem; justify-content:center;">
            <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.8rem;">
                <div style="width:18px;height:18px;border-radius:4px;background:#86efac;"></div>
                <span>Prediksi Benar (Diagonal)</span>
            </div>
            <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.8rem;">
                <div style="width:18px;height:18px;border-radius:4px;background:#fbbf24;"></div>
                <span>False Positive</span>
            </div>
            <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.8rem;">
                <div style="width:18px;height:18px;border-radius:4px;background:#f87171;"></div>
                <span>False Negative</span>
            </div>
        </div>

        <!-- Accuracy highlight -->
        @if($result['test_accuracy'] !== null)
        <div style="margin-top:1.5rem; padding:1rem 1.5rem; background:linear-gradient(135deg,rgba(216,27,96,0.08),rgba(136,14,79,0.04)); border-radius:12px; text-align:center;">
            <p style="margin:0; font-size:0.85rem; color:#6b7280;">Akurasi pada Data Uji</p>
            <p style="margin:0; font-size:2.25rem; font-weight:800; color:#D81B60;">
                {{ number_format($result['test_accuracy'], 2) }}%
            </p>
        </div>
        @endif
    </div>
</div>

<!-- ===== TAB: CLASSIFICATION REPORT ===== -->
<div id="tab-report" class="tab-content">
    <div class="medical-card fade-in">
        <h3 style="font-size:1.1rem; font-weight:700; color:var(--color-gray-800); margin:0 0 0.5rem;">
            📈 Classification Report
        </h3>
        <p style="color:var(--color-gray-500); font-size:0.85rem; margin:0 0 1.5rem;">
            Precision, Recall, dan F1-Score per kelas
        </p>

        @php
            $report = $result['classification_report'] ?? [];
            $classRows = array_filter($report, fn($v) => is_array($v) && isset($v['precision']), ARRAY_FILTER_USE_BOTH);
            unset($classRows['accuracy']);
            unset($classRows['macro avg']);
            unset($classRows['weighted avg']);
            $macroRow    = $report['macro avg']    ?? null;
            $weightedRow = $report['weighted avg'] ?? null;
        @endphp

        <div style="overflow-x:auto;">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Precision</th>
                        <th>Recall</th>
                        <th>F1-Score</th>
                        <th>Support</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classRows as $className => $metrics)
                    @php
                        $isPre = str_contains(strtolower($className), 'pre');
                    @endphp
                    <tr>
                        <td>
                            <span style="padding:0.2rem 0.6rem; border-radius:99px; font-size:0.75rem; font-weight:700; background:{{ $isPre ? '#fee2e2' : '#d1fae5' }}; color:{{ $isPre ? '#991b1b' : '#065f46' }};">
                                {{ ucfirst($className) }}
                            </span>
                        </td>
                        <td>
                            <div class="metric-bar-wrap">
                                <div class="metric-bar-bg">
                                    <div class="metric-bar-fill" style="width:{{ round($metrics['precision']*100) }}%;"></div>
                                </div>
                                <span style="font-weight:700; min-width:42px;">{{ number_format($metrics['precision']*100, 1) }}%</span>
                            </div>
                        </td>
                        <td>
                            <div class="metric-bar-wrap">
                                <div class="metric-bar-bg">
                                    <div class="metric-bar-fill" style="width:{{ round($metrics['recall']*100) }}%;"></div>
                                </div>
                                <span style="font-weight:700; min-width:42px;">{{ number_format($metrics['recall']*100, 1) }}%</span>
                            </div>
                        </td>
                        <td>
                            <div class="metric-bar-wrap">
                                <div class="metric-bar-bg">
                                    <div class="metric-bar-fill" style="width:{{ round($metrics['f1-score']*100) }}%;"></div>
                                </div>
                                <span style="font-weight:700; min-width:42px;">{{ number_format($metrics['f1-score']*100, 1) }}%</span>
                            </div>
                        </td>
                        <td style="font-weight:700;">{{ $metrics['support'] }}</td>
                    </tr>
                    @endforeach

                    @if($macroRow)
                    <tr class="total-row">
                        <td>Macro Avg</td>
                        <td>{{ number_format($macroRow['precision']*100, 1) }}%</td>
                        <td>{{ number_format($macroRow['recall']*100, 1) }}%</td>
                        <td>{{ number_format($macroRow['f1-score']*100, 1) }}%</td>
                        <td>{{ $macroRow['support'] }}</td>
                    </tr>
                    @endif

                    @if($weightedRow)
                    <tr class="total-row">
                        <td>Weighted Avg</td>
                        <td>{{ number_format($weightedRow['precision']*100, 1) }}%</td>
                        <td>{{ number_format($weightedRow['recall']*100, 1) }}%</td>
                        <td>{{ number_format($weightedRow['f1-score']*100, 1) }}%</td>
                        <td>{{ $weightedRow['support'] }}</td>
                    </tr>
                    @endif

                    @if(isset($report['accuracy']))
                    <tr class="total-row">
                        <td colspan="3" style="font-weight:700; color:#D81B60;">Accuracy</td>
                        <td style="font-weight:800; color:#D81B60;">{{ number_format($report['accuracy']*100, 1) }}%</td>
                        <td>—</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.5rem; padding:1rem; background:#f9fafb; border-radius:10px;">
            <p style="font-size:0.8rem; font-weight:700; color:#374151; margin:0 0 0.5rem;">📖 Keterangan Metrik:</p>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:0.5rem;">
                <p style="font-size:0.78rem; color:#6b7280; margin:0;"><strong>Precision</strong> — Proporsi prediksi positif yang benar</p>
                <p style="font-size:0.78rem; color:#6b7280; margin:0;"><strong>Recall</strong> — Proporsi positif aktual yang terdeteksi</p>
                <p style="font-size:0.78rem; color:#6b7280; margin:0;"><strong>F1-Score</strong> — Rata-rata harmonik Precision & Recall</p>
                <p style="font-size:0.78rem; color:#6b7280; margin:0;"><strong>Support</strong> — Jumlah sampel per kelas</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ===== TAB: DATA PREDIKSI ===== -->
<div id="tab-data" class="tab-content">
    <div class="medical-card fade-in">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:1rem;">
            <h3 style="font-size:1.1rem; font-weight:700; color:var(--color-gray-800); margin:0;">
                📋 Data Hasil Prediksi ({{ count($result['predictions'] ?? []) }} baris)
            </h3>
            <input type="text" id="tableSearch" class="search-input" placeholder="🔍 Cari data..." onkeyup="filterTable()">
        </div>

        <div style="overflow-x:auto;">
            <table class="pred-table" id="predTable">
                <thead>
                    <tr>
                        <th>#</th>
                        @if(isset($result['predictions'][0]['nama'])) <th>Nama</th> @endif
                        <th>Usia</th>
                        <th>Paritas</th>
                        <th>TB</th>
                        <th>BB</th>
                        <th>IMT</th>
                        <th>Sistolik</th>
                        <th>Diastolik</th>
                        <th>MAP</th>
                        <th>GDS</th>
                        <th>Protein Urin</th>
                        @if($result['has_label']) <th>Label Asli</th> @endif
                        <th>Prediksi KNN</th>
                        <!-- <th>Confidence</th> -->
                        @if($result['has_label']) <th>Status</th> @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($result['predictions'] ?? [] as $i => $row)
                    @php
                        $pred   = strtolower($row['prediksi_knn'] ?? '');
                        $isPre  = str_contains($pred, 'pre');
                        $conf   = round($row['confidence'] ?? 0, 1);
                        $isCorrect = $result['has_label'] ? (strtolower($row['label_asli'] ?? '') === $pred) : null;
                    @endphp
                    <tr>
                        <td style="color:#9ca3af; font-weight:500;">{{ $i + 1 }}</td>
                        @if(isset($result['predictions'][0]['nama']))
                        <td style="font-weight:600;">{{ $row['nama'] ?? '-' }}</td>
                        @endif
                        <td>{{ $row['usia'] ?? '-' }}</td>
                        <td>{{ $row['paritas'] ?? '-' }}</td>
                        <td>{{ $row['tb'] ?? '-' }}</td>
                        <td>{{ $row['bb'] ?? '-' }}</td>
                        <td>{{ isset($row['imt']) ? number_format($row['imt'],1) : '-' }}</td>
                        <td>{{ $row['sistolik'] ?? '-' }}</td>
                        <td>{{ $row['diastolik'] ?? '-' }}</td>
                        <td>{{ isset($row['map']) ? number_format($row['map'],1) : '-' }}</td>
                        <td>{{ $row['gds'] ?? '-' }}</td>
                        <td>{{ $row['protein_urin'] ?? '-' }}</td>
                        @if($result['has_label'])
                        <td>
                            @php $la = strtolower($row['label_asli'] ?? ''); @endphp
                            <span class="{{ str_contains($la,'pre') ? 'badge-pred-pre' : 'badge-pred-norm' }}">
                                {{ ucfirst($row['label_asli'] ?? '-') }}
                            </span>
                        </td>
                        @endif
                        <td>
                            <span class="{{ $isPre ? 'badge-pred-pre' : 'badge-pred-norm' }}">
                                {{ ucfirst($row['prediksi_knn'] ?? '-') }}
                            </span>
                        </td>
                        <!-- <td>
                            <div style="min-width:100px;">
                                <div style="display:flex; justify-content:space-between; font-size:0.75rem; font-weight:600; margin-bottom:2px;">
                                    <span>{{ $conf }}%</span>
                                </div>
                                <div style="height:5px; background:#f3f4f6; border-radius:99px; overflow:hidden;">
                                    <div class="conf-bar" style="width:{{ $conf }}%;"></div>
                                </div>
                            </div>
                        </td> -->
                        @if($result['has_label'])
                        <td style="text-align:center;">
                            @if($isCorrect)
                                <span class="correct-icon" title="Prediksi benar">✔</span>
                            @else
                                <span class="wrong-icon" title="Prediksi salah">✘</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Donut Chart Script -->
<script>
    // Tab switching
    function switchTab(id) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        const idx = ['tab-dist','tab-cm','tab-report','tab-data'].indexOf(id);
        document.querySelectorAll('.tab-btn')[idx]?.classList.add('active');
    }

    // Table search
    function filterTable() {
        const q = document.getElementById('tableSearch').value.toLowerCase();
        document.querySelectorAll('#predTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    // Donut Chart
    @php
        $chartLabels  = [];
        $chartData    = [];
        $chartColors  = [];
        foreach ($result['prediction_stats'] ?? [] as $label => $count) {
            $chartLabels[] = ucfirst($label);
            $chartData[]   = $count;
            $chartColors[] = str_contains(strtolower($label), 'pre') ? '#ef4444' : '#16a34a';
        }
    @endphp

    const labels = @json($chartLabels);
    const data   = @json($chartData);
    const colors = @json($chartColors);

    const canvas = document.getElementById('donutChart');
    if (canvas && data.length > 0) {
        const ctx = canvas.getContext('2d');
        const total = data.reduce((a,b) => a+b, 0);
        const cx = 100, cy = 100, r = 80, innerR = 50;
        let startAngle = -Math.PI / 2;

        data.forEach((val, i) => {
            const slice = (val / total) * 2 * Math.PI;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, startAngle, startAngle + slice);
            ctx.fillStyle = colors[i];
            ctx.fill();
            startAngle += slice;
        });

        // Inner circle
        ctx.beginPath();
        ctx.arc(cx, cy, innerR, 0, 2 * Math.PI);
        ctx.fillStyle = 'white';
        ctx.fill();

        // Center text
        ctx.fillStyle = '#374151';
        ctx.font = 'bold 22px Inter, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(total, cx, cy - 8);
        ctx.font = '11px Inter, sans-serif';
        ctx.fillStyle = '#9ca3af';
        ctx.fillText('total', cx, cy + 12);
    }
</script>

@endsection
