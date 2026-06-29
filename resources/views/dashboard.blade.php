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
                <p class="stat-label">Akurasi Model KNN</p>
                <p class="stat-value" style="color: var(--color-medical-info);">{{ $knnAccuracy !== null ? $knnAccuracy . '%' : '--%' }}</p>
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

<!-- Gambar Dashboard KNN -->
<!-- <div class="medical-card fade-in" style="animation-delay: 0.45s; margin-bottom: 1.5rem; padding: 1rem;">
    <img 
        src="{{ asset('assets/dashboard_knn.png') }}" 
        alt="Dashboard KNN"
        style="
            width: 100%;
            height: auto;
            border-radius: 12px;
            display: block;
            object-fit: cover;
        "
    >
</div> -->

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
