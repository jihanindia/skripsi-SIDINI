@extends('layouts.dashboard')

@section('title', 'Laporan - Preeklampsia CDSS')
@section('page-title', 'Laporan & Statistik')
@section('page-subtitle', 'Analisis data dan laporan penilaian risiko preeklampsia')

@section('content')
<!-- Filter Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <label class="form-label" style="margin-bottom: 0.5rem;">Periode</label>
        <select class="form-input" style="margin: 0;">
            <option>Bulan Ini</option>
            <option>3 Bulan Terakhir</option>
            <option>6 Bulan Terakhir</option>
            <option>Tahun Ini</option>
            <option>Custom</option>
        </select>
    </div>
    
    <div class="stat-card">
        <label class="form-label" style="margin-bottom: 0.5rem;">Kategori Risiko</label>
        <select class="form-input" style="margin: 0;">
            <option>Semua Kategori</option>
            <option>Tidak Ada Risiko</option>
            <option>Risiko Rendah</option>
            <option>Risiko Sedang</option>
            <option>Risiko Tinggi</option>
            <option>Preeklampsia Berat</option>
        </select>
    </div>
    
    <div class="stat-card">
        <button class="btn btn-primary w-full" style="margin: 1.8rem 0 0 0;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Filter
        </button>
    </div>
</div>

<!-- Report Cards -->
<div class="medical-card fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-gray-800); margin: 0;">
            📊 Ringkasan Laporan
        </h2>
        <div style="display: flex; gap: 0.5rem;">
            <button class="btn btn-outline">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export Excel
            </button>
            <button class="btn btn-outline">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>
    
    <div style="text-align: center; padding: 4rem 2rem; color: var(--color-gray-400);">
        <svg style="width: 80px; height: 80px; margin: 0 auto 1.5rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <p style="font-size: 1.25rem; font-weight: 600; margin: 0 0 0.5rem 0;">Belum Ada Data Laporan</p>
        <p style="font-size: 0.95rem; margin: 0 0 1.5rem 0;">Lakukan penilaian pasien untuk melihat laporan dan statistik</p>
        <a href="{{ route('assessments.create') }}" class="btn btn-primary">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Penilaian Baru
        </a>
    </div>
</div>
@endsection
