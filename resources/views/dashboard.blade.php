@extends('layouts.dashboard')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan sistem dan statistik penilaian risiko preeklampsia')

@section('content')
<!-- Disclaimer Medical -->
<div class="disclaimer fade-in">
    <div class="disclaimer-content">
        <h3 style="font-weight: 700; color: var(--color-gray-800); margin: 0 0 0.5rem 0;">Disclaimer Medis</h3>
        <p style="margin: 0; color: var(--color-gray-700); line-height: 1.6;">
            Hasil analisis sistem ini bersifat <strong>edukatif dan pendukung keputusan klinis</strong>. 
            Diagnosis dan penanganan medis hanya dapat dilakukan oleh tenaga kesehatan profesional yang berkompeten.
            Sistem ini menggunakan algoritma K-Nearest Neighbors (KNN) untuk prediksi risiko.
        </p>
    </div>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Total Assessments -->
    <div class="stat-card fade-in" style="animation-delay: 0.1s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Total Penilaian</p>
                <p class="stat-value gradient-text">0</p>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                    <span style="color: var(--color-medical-success); font-weight: 600;">↑ 0%</span> dari bulan lalu
                </p>
            </div>
            <div class="medical-icon icon-primary">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- High Risk Cases -->
    <div class="stat-card fade-in" style="animation-delay: 0.2s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Kasus Risiko Tinggi</p>
                <p class="stat-value" style="color: var(--color-medical-danger);">0</p>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                    Memerlukan perhatian segera
                </p>
            </div>
            <div class="medical-icon icon-danger">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Patients -->
    <div class="stat-card fade-in" style="animation-delay: 0.3s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Total Pasien</p>
                <p class="stat-value gradient-text">0</p>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                    Pasien terdaftar
                </p>
            </div>
            <div class="medical-icon icon-success">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- KNN Accuracy -->
    <div class="stat-card fade-in" style="animation-delay: 0.4s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Akurasi Model KNN</p>
                <p class="stat-value" style="color: var(--color-medical-info);">--%</p>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                    Berdasarkan data training
                </p>
            </div>
            <div class="medical-icon icon-warning">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="medical-card fade-in" style="margin-bottom: 2rem; animation-delay: 0.5s;">
    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 1.5rem 0;">
        Aksi Cepat
    </h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="#" class="btn btn-primary" style="justify-content: center;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Penilaian Baru
        </a>
        <a href="#" class="btn btn-outline" style="justify-content: center;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            Tambah Pasien
        </a>
        <a href="#" class="btn btn-outline" style="justify-content: center;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Lihat Laporan
        </a>
    </div>
</div>

<!-- Risk Distribution Chart -->
<div class="medical-card fade-in" style="margin-bottom: 2rem; animation-delay: 0.6s;">
    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 1.5rem 0;">
        Distribusi Risiko Preeklampsia
    </h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
        <div style="text-align: center; padding: 1.5rem; background: var(--color-gray-50); border-radius: 12px;">
            <div class="risk-badge risk-none" style="margin: 0 auto 0.75rem; width: fit-content;">
                <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Tidak Ada Risiko
            </div>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-risk-none); margin: 0;">0</p>
        </div>

        <div style="text-align: center; padding: 1.5rem; background: var(--color-gray-50); border-radius: 12px;">
            <div class="risk-badge risk-low" style="margin: 0 auto 0.75rem; width: fit-content;">
                Risiko Rendah
            </div>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-risk-low); margin: 0;">0</p>
        </div>

        <div style="text-align: center; padding: 1.5rem; background: var(--color-gray-50); border-radius: 12px;">
            <div class="risk-badge risk-moderate" style="margin: 0 auto 0.75rem; width: fit-content;">
                Risiko Sedang
            </div>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-risk-moderate); margin: 0;">0</p>
        </div>

        <div style="text-align: center; padding: 1.5rem; background: var(--color-gray-50); border-radius: 12px;">
            <div class="risk-badge risk-high" style="margin: 0 auto 0.75rem; width: fit-content;">
                Risiko Tinggi
            </div>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-risk-high); margin: 0;">0</p>
        </div>

        <div style="text-align: center; padding: 1.5rem; background: var(--color-gray-50); border-radius: 12px;">
            <div class="risk-badge risk-severe" style="margin: 0 auto 0.75rem; width: fit-content;">
                <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Preeklampsia Berat
            </div>
            <p style="font-size: 2rem; font-weight: 700; color: var(--color-risk-severe); margin: 0;">0</p>
        </div>
    </div>
</div>

<!-- Recent Assessments -->
<div class="medical-card fade-in" style="animation-delay: 0.7s;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-gray-800); margin: 0;">
            Penilaian Terbaru
        </h2>
        <a href="#" style="color: var(--color-medical-primary); font-weight: 600; text-decoration: none; font-size: 0.875rem;">
            Lihat Semua →
        </a>
    </div>

    <div style="text-align: center; padding: 3rem 1rem; color: var(--color-gray-400);">
        <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.5rem 0;">Belum Ada Penilaian</p>
        <p style="font-size: 0.875rem; margin: 0;">Mulai dengan membuat penilaian risiko preeklampsia pertama Anda</p>
        <a href="#" class="btn btn-primary" style="margin-top: 1.5rem;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Penilaian Baru
        </a>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }
</style>
@endsection
