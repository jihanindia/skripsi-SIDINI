@extends('layouts.dashboard')

@section('title', 'Data Pasien - Preeklampsia CDSS')
@section('page-title', 'Data Pasien & Hasil Screening')
@section('page-subtitle')
@if(auth()->user()->isPuskesmas())
    Data screening — {{ auth()->user()->puskesmas }}
@else
    Monitoring data screening seluruh puskesmas
@endif
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1.5rem; background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem;">
    <strong>Sukses:</strong> {{ session('success') }}
    @if(session('prediction_label'))
        — Hasil deteksi: <strong>{{ session('prediction_label') }}</strong>
    @endif
</div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div style="flex: 1; max-width: 400px;">
        <input type="text" id="searchPatient" class="form-input" placeholder="🔍 Cari nama pasien..." style="margin: 0;">
    </div>
    @if(auth()->user()->isPuskesmas())
    <a href="{{ route('assessments.create') }}" class="btn btn-primary">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Penilaian Baru
    </a>
    @endif
</div>

<div class="medical-card fade-in">
    <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 1.5rem 0;">
        📋 Data Hasil Screening
    </h3>
    <div style="overflow-x: auto;">
        <table class="medical-table" id="patientsTable">
            <thead>
                <tr>
                    @if($showPuskesmasColumn)
                    <th>Puskesmas</th>
                    @endif
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
                    <th>HB</th>
                    <th>GDS</th>
                    <th>Riw. HT Keluarga</th>
                    <th>Hasil KNN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assessments as $assessment)
                @php
                    $isPreeklampsia = $assessment->result && in_array($assessment->result->risk_category, ['high_risk', 'severe_preeclampsia']);
                    $proteinLabels = [
                        'negative' => 'Negatif',
                        '+1' => '+1',
                        '+2' => '+2',
                        '+3' => '+3',
                        '+4' => '+4',
                    ];
                @endphp
                <tr data-name="{{ strtolower($assessment->patient->name ?? '') }}">
                    @if($showPuskesmasColumn)
                    <td>{{ $assessment->puskesmas ?? '-' }}</td>
                    @endif
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
                    <td>{{ $assessment->hb ?? '-' }}</td>
                    <td>{{ $assessment->gds ?? '-' }}</td>
                    <td>{{ $assessment->riw_ht_keluarga ? 'Ada' : 'Tidak ada' }}</td>
                    <td>
                        @if($assessment->result)
                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
                            background-color: {{ $isPreeklampsia ? '#fee2e2' : '#d1fae5' }};
                            color: {{ $isPreeklampsia ? '#991b1b' : '#065f46' }};">
                            {{ $assessment->result->prediction_label }}
                        </span>
                        @else
                        <span style="color: var(--color-gray-400);">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $showPuskesmasColumn ? 15 : 14 }}" style="text-align: center; padding: 3rem; color: var(--color-gray-400);">
                        <p style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.5rem 0;">Belum Ada Data Screening</p>
                        <p style="font-size: 0.875rem; margin: 0 0 1rem 0;">Lakukan penilaian risiko preeklampsia untuk menyimpan data pasien</p>
                        <a href="{{ route('assessments.create') }}" class="btn btn-primary">Mulai Penilaian</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchPatient')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#patientsTable tbody tr[data-name]').forEach(row => {
        row.style.display = row.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>
@endsection
