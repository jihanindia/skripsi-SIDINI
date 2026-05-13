@extends('layouts.dashboard')

@section('title', 'Data Pasien - Preeklampsia CDSS')
@section('page-title', 'Data Pasien')
@section('page-subtitle', 'Manajemen data pasien dan riwayat penilaian')

@section('content')
<!-- Action Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div style="flex: 1; max-width: 400px;">
        <input type="text" class="form-input" placeholder="🔍 Cari pasien..." style="margin: 0;">
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Pasien Baru
    </a>
</div>

<!-- Patients Table -->
<div class="medical-card fade-in">
    <div style="overflow-x: auto;">
        <table class="medical-table">
            <thead>
                <tr>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Usia</th>
                    <th>Golongan Darah</th>
                    <th>Total Penilaian</th>
                    <th>Risiko Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>{{ $patient->medical_record_number }}</td>
                    <td>{{ $patient->name }}</td>
                    <td>{{ $patient->age }} Thn</td>
                    <td>{{ $patient->blood_type ?? '-' }}</td>
                    <td>{{ $patient->assessments_count }}</td>
                    <td>
                        @php
                            $latestResult = $patient->latestAssessment?->result;
                        @endphp
                        @if($latestResult)
                            <span class="badge badge-{{ $latestResult->risk_color }}">
                                {{ $latestResult->risk_category_label }}
                            </span>
                        @else
                            <span style="color: var(--color-gray-400);">Belum Ada</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="#" class="btn btn-sm btn-outline" title="Detail">👁️</a>
                            <a href="{{ route('assessments.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-primary" title="Penilaian Baru">➕</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--color-gray-400);">
                        <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.5rem 0;">Belum Ada Data Pasien</p>
                        <p style="font-size: 0.875rem; margin: 0;">Tambahkan pasien baru untuk memulai penilaian risiko preeklampsia</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
