@if(!empty($metrics['confusion_matrix']) && is_array($metrics['confusion_matrix']))
@php
    $cm     = $metrics['confusion_matrix'];
    $labels = $metrics['confusion_matrix_labels'] ?? [];
    if (empty($labels)) {
        $labels = count($cm) === 2 ? ['Normal', 'Preeklampsia'] : array_map(fn($i) => "Kelas $i", range(0, count($cm) - 1));
    }
    $n = count($labels);
    $report = $metrics['classification_report'] ?? [];
    $classMetrics = [];
    foreach ($report as $key => $val) {
        if (is_array($val) && isset($val['precision']) && !str_contains(strtolower((string) $key), 'avg') && $key !== 'accuracy') {
            $classMetrics[$key] = $val;
        }
    }
    $macroRow    = $report['macro avg'] ?? null;
    $weightedRow = $report['weighted avg'] ?? null;
@endphp

<div class="medical-card fade-in" style="margin-top: {{ $marginTop ?? '2rem' }};">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
        <div>
            <h3 style="font-size:1.125rem; font-weight:700; color:var(--color-gray-800); margin:0 0 0.35rem;">
                {{ $title ?? '📊 Confusion Matrix & Classification Report' }}
            </h3>
            @if(!empty($subtitle))
            <p style="color:var(--color-gray-500); font-size:0.85rem; margin:0;">{{ $subtitle }}</p>
            @endif
        </div>
        @if(!empty($metrics['test_accuracy']))
        <div style="padding:0.5rem 1rem; background:#e0f2fe; color:#0369a1; border-radius:8px; font-weight:700; font-size:0.9rem;">
            Akurasi: {{ number_format($metrics['test_accuracy'], 1) }}%
        </div>
        @endif
    </div>

    @if(!empty($metrics['evaluated_at']))
    <p style="font-size:0.8rem; color:var(--color-gray-400); margin:-0.5rem 0 1.25rem;">
        Penilaian terakhir: {{ $metrics['evaluated_at'] }}
        @if(!empty($metrics['total_data']))
            · {{ $metrics['total_data'] }} data
        @endif
    </p>
    @endif

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:2rem; align-items:start;">
        <div>
            <p style="font-weight:600; color:var(--color-gray-700); margin:0 0 1rem; font-size:0.9rem;">Confusion Matrix</p>
            <div style="overflow-x:auto;">
                <table style="border-collapse:separate; border-spacing:4px; width:100%;">
                    <thead>
                        <tr>
                            <th style="padding:0.5rem; background:transparent;"></th>
                            <th colspan="{{ $n }}" style="padding:0.5rem; font-size:0.72rem; font-weight:700; text-align:center; background:#f1f5f9; border-radius:6px; color:var(--color-gray-700);">
                                Prediksi KNN
                            </th>
                        </tr>
                        <tr>
                            <th style="padding:0.4rem; font-size:0.68rem; font-weight:700; text-align:center; background:#f1f5f9; border-radius:6px; color:var(--color-gray-500);">Aktual</th>
                            @foreach($labels as $label)
                            <th style="padding:0.5rem 0.75rem; font-size:0.7rem; font-weight:700; text-align:center; background:#e2e8f0; border-radius:6px;">{{ ucfirst($label) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cm as $rowIdx => $row)
                        <tr>
                            <td style="padding:0.5rem 0.75rem; font-size:0.7rem; font-weight:700; text-align:center; background:#e2e8f0; border-radius:6px;">{{ ucfirst($labels[$rowIdx] ?? "Kelas $rowIdx") }}</td>
                            @foreach($row as $colIdx => $val)
                            @php $isDiag = ($rowIdx === $colIdx); @endphp
                            <td style="padding:0.85rem 1rem; text-align:center; font-size:1.15rem; font-weight:800; border-radius:8px; min-width:64px; background:{{ $isDiag ? '#d1fae5' : '#fee2e2' }}; color:{{ $isDiag ? '#065f46' : '#991b1b' }};">
                                {{ $val }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(!empty($classMetrics))
        <div>
            <p style="font-weight:600; color:var(--color-gray-700); margin:0 0 1rem; font-size:0.9rem;">Classification Report</p>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:0.6rem 0.75rem; text-align:left; border-bottom:2px solid #e2e8f0;">Kelas</th>
                            <th style="padding:0.6rem 0.75rem; text-align:right; border-bottom:2px solid #e2e8f0;">Precision</th>
                            <th style="padding:0.6rem 0.75rem; text-align:right; border-bottom:2px solid #e2e8f0;">Recall</th>
                            <th style="padding:0.6rem 0.75rem; text-align:right; border-bottom:2px solid #e2e8f0;">F1</th>
                            <th style="padding:0.6rem 0.75rem; text-align:right; border-bottom:2px solid #e2e8f0;">Support</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classMetrics as $className => $m)
                        @php $isPre = str_contains(strtolower($className), 'pre'); @endphp
                        <tr>
                            <td style="padding:0.55rem 0.75rem; border-bottom:1px solid #f1f5f9;">
                                <span style="padding:0.15rem 0.5rem; border-radius:99px; font-size:0.72rem; font-weight:700; background:{{ $isPre ? '#fee2e2' : '#d1fae5' }}; color:{{ $isPre ? '#991b1b' : '#065f46' }};">{{ ucfirst($className) }}</span>
                            </td>
                            <td style="padding:0.55rem 0.75rem; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">{{ number_format(($m['precision'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">{{ number_format(($m['recall'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">{{ number_format(($m['f1-score'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">{{ $m['support'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                        @if($macroRow)
                        <tr style="background:#f8fafc; font-weight:700;">
                            <td style="padding:0.55rem 0.75rem;">Macro Avg</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ number_format(($macroRow['precision'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ number_format(($macroRow['recall'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ number_format(($macroRow['f1-score'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ $macroRow['support'] ?? 0 }}</td>
                        </tr>
                        @endif
                        @if($weightedRow)
                        <tr style="background:#f8fafc; font-weight:700;">
                            <td style="padding:0.55rem 0.75rem;">Weighted Avg</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ number_format(($weightedRow['precision'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ number_format(($weightedRow['recall'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ number_format(($weightedRow['f1-score'] ?? 0) * 100, 1) }}%</td>
                            <td style="padding:0.55rem 0.75rem; text-align:right;">{{ $weightedRow['support'] ?? 0 }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endif
