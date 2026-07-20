@extends('layouts.dashboard')

@section('page-title', 'Dashboard')
<!-- @section('page-subtitle', 'Ringkasan siste dan statistik penilaian risiko preeklampsia') -->

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
    <div class="stat-card fade-in" style="animation-delay: 0.1s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Total Penilaian</p>
                <p class="stat-value gradient-text">{{ $totalAssessments }}</p>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                    @if($growthPercent >= 0)
                    <span style="color: var(--color-medical-success); font-weight: 600;">↑ {{ $growthPercent }}%</span>
                    @else
                    <span style="color: var(--color-medical-danger); font-weight: 600;">↓ {{ abs($growthPercent) }}%</span>
                    @endif
                    dari bulan lalu
                </p>
            </div>
            <div class="medical-icon icon-primary">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card fade-in" style="animation-delay: 0.2s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Kasus Preeklampsia</p>
                <p class="stat-value" style="color: var(--color-medical-danger);">{{ $highRiskCount }}</p>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                    Terdeteksi oleh KNN
                </p>
            </div>
            <div class="medical-icon icon-danger">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card fade-in" style="animation-delay: 0.3s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Total Pasien</p>
                <p class="stat-value gradient-text">{{ $totalPatients }}</p>
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

    <div class="stat-card fade-in" style="animation-delay: 0.4s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p class="stat-label">Akurasi Model</p>
                <p class="stat-value" style="color: var(--color-medical-info);">{{ $cvAccuracy !== null ? $cvAccuracy . '%' : '--%' }}</p>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin-top: 0.5rem;">
                    Diuji pada data testing
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

<!-- Custom Layout: Tren, Distribusi, Hasil Pemeriksaan -->
<div class="dashboard-custom-grid">
    <!-- Kolom Kiri -->
    <div class="custom-grid-left">
        <!-- Tren Hasil Deteksi -->
        <div class="medical-card fade-in" style="animation-delay: 0.45s; margin-bottom: 1.25rem; padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 1.1rem;">Tren Hasil Deteksi</h3>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.25rem 0.6rem; font-size: 0.8rem; color: #64748b;">
                    7 Hari Terakhir
                </div>
            </div>
            
            <div style="position: relative; height: 220px; width: 100%;">
                <canvas id="newTrendChart"></canvas>
            </div>

            <!-- Summary Box -->
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem;">
                <div style="flex: 1; min-width: 150px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #8B5CF6;"></span>
                        <span style="color: #475569; font-size: 0.875rem; font-weight: 600;">Preeklampsia</span>
                    </div>
                    <div>
                        <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ $distPreeklampsia }}</span>
                        <span style="font-size: 0.875rem; color: #475569; font-weight: 600;">({{ $totalDist > 0 ? round(($distPreeklampsia / $totalDist) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>
                <div style="flex: 1; min-width: 150px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #10B981;"></span>
                        <span style="color: #475569; font-size: 0.875rem; font-weight: 600;">Normal</span>
                    </div>
                    <div>
                        <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ $distNormal }}</span>
                        <span style="font-size: 0.875rem; color: #475569; font-weight: 600;">({{ $totalDist > 0 ? round(($distNormal / $totalDist) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>
            </div>
            <p style="margin: 1rem 0 0 0; font-size: 0.75rem; color: #94a3b8;">Persentase dihitung dari seluruh hasil pemeriksaan pada periode yang dipilih.</p>
        </div>

        <!-- Banner Bawah -->
        <div class="medical-card fade-in" style="animation-delay: 0.55s; background: #f8f5ff; border: 1px solid #e9d5ff; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-radius: 12px; margin-bottom: 1.25rem;">
            <div style="background: #e9d5ff; color: #8B5CF6; padding: 0.6rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <p style="margin: 0; color: #334155; font-size: 0.85rem;">Sistem ini membantu mendeteksi risiko preeklampsia sejak dini.</p>
                <p style="margin: 0.15rem 0 0 0; color: #334155; font-size: 0.85rem;">Hasil deteksi: <span style="color: #8B5CF6; font-weight: 600;">Preeklampsia</span> atau <span style="color: #10B981; font-weight: 600;">Normal</span>.</p>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan -->
    <div class="custom-grid-right">
        <!-- Distribusi Hasil Deteksi -->
        <div class="medical-card fade-in" style="animation-delay: 0.5s; margin-bottom: 1.25rem; padding: 1.25rem;">
            <h3 style="margin: 0 0 1.25rem 0; font-weight: 700; color: #1e293b; font-size: 1.1rem;">Distribusi Hasil Deteksi</h3>
            <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
                <div style="position: relative; width: 110px; height: 110px;">
                    <canvas id="newDistributionChart"></canvas>
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #8B5CF6;"></span>
                            <span style="font-size: 0.875rem; color: #475569; font-weight: 500;">Preeklampsia</span>
                        </div>
                        <span style="font-size: 0.875rem; color: #1e293b; font-weight: 600;">{{ $distPreeklampsia }} ({{ $totalDist > 0 ? round(($distPreeklampsia / $totalDist) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10B981;"></span>
                            <span style="font-size: 0.875rem; color: #475569; font-weight: 500;">Normal</span>
                        </div>
                        <span style="font-size: 0.875rem; color: #1e293b; font-weight: 600;">{{ $distNormal }} ({{ $totalDist > 0 ? round(($distNormal / $totalDist) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>
            </div>

            <!-- Note Box -->
            <div style="margin-top: 1.5rem; background: #f8f5ff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 1rem; display: flex; gap: 0.75rem;">
                <div style="background: #a78bfa; color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.75rem; font-weight: bold;">
                    i
                </div>
                <div>
                    <p style="margin: 0; font-size: 0.875rem; color: #334155; margin-bottom: 0.5rem;">Label yang dihasilkan hanya terdiri dari:</p>
                    <div style="display: flex; gap: 0.5rem;">
                        <span style="background: #f3e8ff; color: #8B5CF6; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Preeklampsia</span>
                        <span style="background: #dcfce7; color: #10B981; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Normal</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hasil Pemeriksaan Terbaru -->
        <div class="medical-card fade-in" style="animation-delay: 0.6s; padding: 1.25rem; margin-bottom: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 1.1rem;">Hasil Pemeriksaan Terbaru</h3>
                <a href="{{ route('patients.index') }}" style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.2rem 0.6rem; font-size: 0.75rem; color: #64748b; text-decoration: none; font-weight: 500;">Lihat Semua</a>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 0.75rem 0; font-size: 0.75rem; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Pasien</th>
                            <th style="text-align: left; padding: 0.75rem 0; font-size: 0.75rem; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Hasil</th>
                            <th style="text-align: right; padding: 0.75rem 0; font-size: 0.75rem; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAssessments as $assessment)
                        <tr>
                            <td style="padding: 0.75rem 0; font-size: 0.875rem; color: #334155; border-bottom: 1px solid #f1f5f9;">{{ $assessment->patient->name }}</td>
                            <td style="padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                                @if(in_array($assessment->result->risk_category ?? '', ['high_risk', 'severe_preeclampsia']))
                                    <span style="background: #f3e8ff; color: #8B5CF6; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Preeklampsia</span>
                                @else
                                    <span style="background: #dcfce7; color: #10B981; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Normal</span>
                                @endif
                            </td>
                            <td style="text-align: right; padding: 0.75rem 0; font-size: 0.875rem; color: #64748b; border-bottom: 1px solid #f1f5f9; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($assessment->assessment_date)->format('d M Y') }}
                                <svg style="width: 14px; height: 14px; vertical-align: middle; margin-left: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 1rem 0; font-size: 0.875rem; color: #94a3b8;">Belum ada data pemeriksaan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Grafik perkembangan penilaian -->
<div class="medical-card fade-in chart-card" style="animation-delay: 0.5s;">
    <div class="chart-header">
        <h2 class="chart-title">{{ $chartTitle }}</h2>
        <div class="chart-filter">
            <span class="chart-filter-label">Rentang waktu:</span>
            <a href="{{ route('dashboard', ['range' => 1]) }}"
               class="chart-filter-btn {{ $chartRange === 1 ? 'active' : '' }}">
                1 Tahun
            </a>
            <a href="{{ route('dashboard', ['range' => 5]) }}"
               class="chart-filter-btn {{ $chartRange === 5 ? 'active' : '' }}">
                5 Tahun
            </a>
        </div>
    </div>
    <div class="chart-wrapper">
        <canvas id="assessmentTrendChart"></canvas>
    </div>
</div>

<style>
    .dashboard-custom-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    @media (min-width: 992px) {
        .dashboard-custom-grid {
            grid-template-columns: 1.6fr 1fr;
        }
    }

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

    .chart-card {
        padding: 1.5rem 1.75rem 2rem;
    }

    .chart-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--color-gray-800);
        margin: 0;
        flex: 1;
        min-width: 200px;
        text-align: left;
    }

    .chart-filter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .chart-filter-label {
        font-size: 0.875rem;
        color: var(--color-gray-500);
        font-weight: 500;
    }

    .chart-filter-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--color-gray-600);
        background: var(--color-gray-100);
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    .chart-filter-btn:hover {
        background: var(--color-gray-200);
        color: var(--color-gray-800);
    }

    .chart-filter-btn.active {
        background: var(--color-medical-primary);
        color: white;
        border-color: var(--color-medical-primary);
    }

    .chart-wrapper {
        position: relative;
        height: 380px;
        width: 100%;
        border: 1px solid var(--color-gray-200);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        background: #fff;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---- New Trend Chart (7 Hari Terakhir) ----
    const newTrendCtx = document.getElementById('newTrendChart').getContext('2d');
    const newTrendLabels = @json($trendLabels);
    const newTrendNormal = @json($trendNormal);
    const newTrendPreeklampsia = @json($trendPreeklampsia);

    new Chart(newTrendCtx, {
        type: 'line',
        data: {
            labels: newTrendLabels,
            datasets: [
                {
                    label: 'Preeklampsia',
                    data: newTrendPreeklampsia,
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#8B5CF6',
                    fill: false,
                    tension: 0.3,
                },
                {
                    label: 'Normal',
                    data: newTrendNormal,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#10B981',
                    fill: true,
                    tension: 0.3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'start',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        boxHeight: 8,
                        font: { size: 12, weight: '500' }
                    }
                }
            },
            scales: {
                y: { min: 0, ticks: { stepSize: 10 }, border: { dash: [4, 4] }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ---- New Distribution Chart (Doughnut) ----
    const newDistCtx = document.getElementById('newDistributionChart').getContext('2d');
    new Chart(newDistCtx, {
        type: 'doughnut',
        data: {
            labels: ['Preeklampsia', 'Normal'],
            datasets: [{
                data: [{{ $distPreeklampsia }}, {{ $distNormal }}],
                backgroundColor: ['#8B5CF6', '#10B981'],
                borderWidth: 0,
                cutout: '65%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' pasien';
                        }
                    }
                }
            }
        }
    });

    // ---- Old Assessment Trend Chart ----
    const labels = @json($chartLabels);
    const dataNormal = @json($chartDataNormal);
    const dataPreeklampsia = @json($chartDataPreeklampsia);
    const allValues = [...dataNormal, ...dataPreeklampsia];
    const maxVal = Math.max(...allValues, 0);
    let yMax = maxVal === 0 ? 10 : Math.ceil(maxVal / 10) * 10;
    if (yMax < 5) yMax = 5;
    const step = yMax <= 10 ? (yMax <= 5 ? 1 : 2) : 10;

    const ctx = document.getElementById('assessmentTrendChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Normal',
                    data: dataNormal,
                    borderColor: '#5B9BD5',
                    backgroundColor: 'rgba(91, 155, 213, 0.08)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#5B9BD5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0.1,
                },
                {
                    label: 'Preeklampsia',
                    data: dataPreeklampsia,
                    borderColor: '#DC2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.08)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#DC2626',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0.1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16,
                        font: { size: 12, weight: '600' },
                        color: '#475569',
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': ' + ctx.parsed.y + ' pasien';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11, weight: '500' },
                        color: '#64748b',
                        maxRotation: 45,
                        minRotation: 0,
                    },
                    border: { color: '#e2e8f0' }
                },
                y: {
                    min: 0,
                    max: yMax,
                    ticks: {
                        stepSize: step,
                        font: { size: 12 },
                        color: '#64748b'
                    },
                    grid: {
                        color: '#e2e8f0',
                        drawBorder: false
                    },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
