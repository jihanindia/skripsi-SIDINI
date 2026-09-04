@extends('layouts.dashboard')

@section('title', 'Laporan - SIDINI')
@section('page-title', 'Laporan Deteksi Dini Preeklampsia')
@section('page-subtitle', 'Data Pasien Skrining Dini Preeklampsia - cetak dan ekspor Excel')

@section('content')
@php
    $proteinLabels = [
        'negative' => 'Negatif',
        '+1' => '+1',
        '+2' => '+2',
        '+3' => '+3',
        '+4' => '+4',
    ];
@endphp

<!-- Filter -->
<form method="GET" action="{{ route('reports.index') }}" class="no-print" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <label class="form-label" style="margin-bottom: 0.5rem;">Periode</label>
        <select class="form-input" name="period" style="margin: 0;">
            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Data</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="3months" {{ $period === '3months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
            <option value="6months" {{ $period === '6months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Tahun Ini</option>
        </select>
    </div>

    <div class="stat-card">
        <label class="form-label" style="margin-bottom: 0.5rem;">Kategori</label>
        <select class="form-input" name="risk" style="margin: 0;">
            <option value="all" {{ $risk === 'all' ? 'selected' : '' }}>Semua</option>
            <option value="normal" {{ $risk === 'normal' ? 'selected' : '' }}>Normal</option>
            <option value="preeklampsia" {{ $risk === 'preeklampsia' ? 'selected' : '' }}>Preeklampsia</option>
        </select>
    </div>

    <div class="stat-card">
        <label class="form-label" style="margin-bottom: 0.5rem;">Puskesmas</label>
        <select class="form-input" name="puskesmas" style="margin: 0;">
            @foreach($puskesmasList as $value => $label)
            <option value="{{ $value }}" {{ $puskesmas === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="stat-card">
        <button type="submit" class="btn btn-primary w-full" style="margin: 1.8rem 0 0 0;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Filter
        </button>
    </div>
</form>

<!-- Ringkasan -->
<div class="no-print" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card" style="text-align: center;">
        <p class="stat-label">Total Data</p>
        <p class="stat-value gradient-text">{{ $summary['total'] }}</p>
    </div>
    <div class="stat-card" style="text-align: center;">
        <p class="stat-label">Normal</p>
        <p class="stat-value" style="color: #059669;">{{ $summary['normal'] }}</p>
    </div>
    <div class="stat-card" style="text-align: center;">
        <p class="stat-label">Preeklampsia</p>
        <p class="stat-value" style="color: #dc2626;">{{ $summary['preeklampsia'] }}</p>
    </div>
</div>

<!-- Laporan -->
<div class="medical-card fade-in" id="reportPrintArea">
    <div class="report-header no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-gray-800); margin: 0;">
            📊 Data Pasien Skrining Dini Preeklampsia {{ $puskesmasList[$puskesmas] ?? $puskesmas }}
        </h2>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-outline">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export Excel
            </a>
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Header cetak -->
    <div class="print-only print-header">
        <h1 style="font-size: 1.25rem; margin: 0 0 0.25rem;">Data Pasien Skrining Dini Preeklampsia {{ $puskesmasList[$puskesmas] ?? $puskesmas }}</h1>
        <p style="margin: 0; font-size: 0.875rem; color: #475569;">
            Dicetak: {{ now()->format('d/m/Y H:i') }} |
            Total: {{ $summary['total'] }} |
            Normal: {{ $summary['normal'] }} |
            Preeklampsia: {{ $summary['preeklampsia'] }}
        </p>
    </div>

    @if($assessments->count() > 0)
    <div style="overflow-x: auto;">
        <table class="medical-table report-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Puskesmas</th>
                    <th>Nama Pasien</th>
                    <th>Tgl. Penilaian</th>
                    <th>Usia</th>
                    <th>Paritas</th>
                    <th>Sistolik</th>
                    <th>Diastolik</th>
                    <th>BB (kg)</th>
                    <th>TB (cm)</th>
                    <th>IMT</th>
                    <th>Protein Urin</th>
                    <th>MAP</th>
                    <th>GDS</th>
                    <th>Hasil KNN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assessments as $index => $assessment)
                @php
                    $isPreeklampsia = $assessment->result && in_array($assessment->result->risk_category, ['high_risk', 'severe_preeklampsia']);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $assessment->puskesmas ?? '-' }}</td>
                    <td><strong>{{ $assessment->patient->name ?? '-' }}</strong></td>
                    <td>{{ $assessment->assessment_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $assessment->patient->age ?? '-' }}</td>
                    <td>{{ $assessment->para }}</td>
                    <td>{{ $assessment->systolic_bp }}</td>
                    <td>{{ $assessment->diastolic_bp }}</td>
                    <td>{{ $assessment->beratbadan ?? '-' }}</td>
                    <td>{{ $assessment->tinggibadan ?? '-' }}</td>
                    <td>{{ $assessment->imt ?? '-' }}</td>
                    <td>{{ $proteinLabels[$assessment->protein_urine] ?? $assessment->protein_urine }}</td>
                    <td>{{ $assessment->map ?? '-' }}</td>
                    <td>{{ $assessment->gds ?? '-' }}</td>
                    <td>
                        @if($assessment->result)
                        <span class="report-badge {{ $isPreeklampsia ? 'badge-danger' : 'badge-success' }}">
                            {{ $assessment->result->prediction_label }}
                        </span>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 4rem 2rem; color: var(--color-gray-400);">
        <p style="font-size: 1.25rem; font-weight: 600; margin: 0 0 0.5rem 0;">Belum Ada Data Laporan</p>
        <p style="font-size: 0.95rem; margin: 0 0 1.5rem 0;">Lakukan penilaian pasien untuk melihat laporan</p>
        <a href="{{ route('assessments.create') }}" class="btn btn-primary no-print">Buat Penilaian Baru</a>
    </div>
    @endif
</div>

<style>
.print-only { display: none; }

.report-badge {
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-success { background: #d1fae5; color: #065f46; }
.badge-danger { background: #fee2e2; color: #991b1b; }

@media print {
    body * { visibility: hidden; }
    #reportPrintArea, #reportPrintArea * { visibility: visible; }
    #reportPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 0;
        margin: 0;
        box-shadow: none !important;
        border: none !important;
    }
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    .print-header { margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #333; }

    #sidebar { display: none !important; }
    body > div[style*="margin-left: 280px"] {
        margin-left: 0 !important;
        width: 100% !important;
    }
    body > div[style*="margin-left: 280px"] > div:first-child {
        display: none !important;
    }
    body > div[style*="margin-left: 280px"] > div:last-child {
        padding: 0.5rem !important;
    }

    .report-table { font-size: 9px; width: 100%; }
    .report-table th, .report-table td {
        padding: 4px 6px !important;
        border: 1px solid #ccc !important;
    }
    .report-badge {
        background: transparent !important;
        color: #000 !important;
        padding: 0 !important;
    }
}
</style>
@endsection
