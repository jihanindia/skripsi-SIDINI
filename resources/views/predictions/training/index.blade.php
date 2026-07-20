@extends('layouts.dashboard')

@section('title', 'Prediksi Data Training - SIDINI')
@section('page-title', 'Prediksi Data Training CSV')
@section('page-subtitle', 'Upload file CSV data training untuk diprediksi menggunakan model KNN')

@section('content')

<style>
    .upload-zone {
        border: 2px dashed #e2b4c8;
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        background: linear-gradient(135deg, rgba(216,27,96,0.04), rgba(136,14,79,0.02));
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: var(--color-medical-primary);
        background: linear-gradient(135deg, rgba(216,27,96,0.08), rgba(136,14,79,0.04));
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(216,27,96,0.15);
    }
    .upload-icon-wrap {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, #D81B60, #880E4F);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
        box-shadow: 0 4px 20px rgba(216,27,96,0.3);
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .info-item {
        background: white;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border-left: 4px solid var(--color-medical-primary);
        display: flex; align-items: center; gap: 1rem;
    }
    .info-item-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .format-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        background: rgba(216,27,96,0.1);
        color: var(--color-medical-primary);
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 600;
        margin: 0.15rem;
    }
    .column-list {
        display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem;
    }
    .col-badge {
        padding: 0.25rem 0.6rem;
        background: #f0f4ff;
        color: #3b5bdb;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        font-family: monospace;
    }
    .submit-btn {
        background: linear-gradient(135deg, #D81B60, #880E4F);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex; align-items: center; gap: 0.6rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(216,27,96,0.3);
    }
    .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(216,27,96,0.4); }
    .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .model-meta-badge {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 1rem; border-radius: 8px;
        background: #d1fae5; color: #065f46;
        font-weight: 600; font-size: 0.875rem;
    }
    .no-model-badge {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 1rem; border-radius: 8px;
        background: #fee2e2; color: #991b1b;
        font-weight: 600; font-size: 0.875rem;
    }
    .loading-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        flex-direction: column; align-items: center; justify-content: center;
        color: white;
    }
    .spinner {
        width: 56px; height: 56px;
        border: 5px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.9s linear infinite;
        margin-bottom: 1.25rem;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p style="font-size: 1.1rem; font-weight: 600; margin: 0;">Memproses prediksi KNN data latih...</p>
    <p style="font-size: 0.875rem; opacity: 0.8; margin: 0.5rem 0 0;">Mohon tunggu, proses ini memerlukan beberapa detik</p>
</div>

<!-- Alerts -->
@if(session('error'))
<div style="background:#fee2e2; color:#991b1b; padding:1rem 1.25rem; border-radius:10px; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem;">
    <svg style="width:20px;height:20px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div><strong>Error:</strong> {{ session('error') }}</div>
</div>
@endif

<!-- Model Status -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h2 style="font-size:1.1rem; font-weight:700; color:var(--color-gray-800); margin:0 0 0.25rem;">Status Model KNN</h2>
        <p style="font-size:0.85rem; color:var(--color-gray-500); margin:0;">Model yang digunakan untuk prediksi batch</p>
    </div>
    @if(!empty($metadata))
        <div class="model-meta-badge">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Model Tersedia &nbsp;|&nbsp; K={{ $metadata['best_k'] ?? '?' }} &nbsp;|&nbsp; Akurasi CV={{ $metadata['cv_accuracy'] ?? $metadata['accuracy'] ?? '?' }}%
        </div>
    @else
        <div class="no-model-badge">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Model belum dilatih — silakan latih dulu di halaman Training
        </div>
    @endif
</div>

<!-- Info Grid -->
<div class="info-grid">
    <div class="info-item">
        <div class="info-item-icon" style="background:#ede9fe;">📊</div>
        <div>
            <p style="font-weight:700; margin:0 0 0.2rem; color:var(--color-gray-800);">Format File</p>
            <p style="font-size:0.83rem; color:var(--color-gray-500); margin:0;">CSV (Comma Separated Values)</p>
            <div style="margin-top:0.4rem;">
                <span class="format-badge">.csv</span>
            </div>
        </div>
    </div>
    <div class="info-item">
        <div class="info-item-icon" style="background:#dcfce7;">🏷️</div>
        <div>
            <p style="font-weight:700; margin:0 0 0.2rem; color:var(--color-gray-800);">Tanpa Label</p>
            <p style="font-size:0.83rem; color:var(--color-gray-500); margin:0;">Kolom <code>status</code> tidak diperlukan (opsional untuk confusion matrix)</p>
        </div>
    </div>
    <div class="info-item">
        <div class="info-item-icon" style="background:#fef9c3;">🤖</div>
        <div>
            <p style="font-weight:700; margin:0 0 0.2rem; color:var(--color-gray-800);">Algoritma</p>
            <p style="font-size:0.83rem; color:var(--color-gray-500); margin:0;">K-Nearest Neighbors · Euclidean Distance</p>
        </div>
    </div>
    <div class="info-item">
        <div class="info-item-icon" style="background:#fee2e2;">📋</div>
        <div>
            <p style="font-weight:700; margin:0 0 0.2rem; color:var(--color-gray-800);">Confusion Matrix</p>
            <p style="font-size:0.83rem; color:var(--color-gray-500); margin:0;">Otomatis tampil jika kolom <code>status</code> ada</p>
        </div>
    </div>
</div>

<!-- Required Columns Info -->
<div class="medical-card fade-in" style="margin-bottom:2rem;">
    <h3 style="font-size:1rem; font-weight:700; color:var(--color-gray-800); margin:0 0 0.75rem;">
        📝 Kolom yang Dibutuhkan dalam CSV
    </h3>
    <div class="column-list">
        <span class="col-badge">usia</span>
        <span class="col-badge">paritas</span>
        <span class="col-badge">tb</span>
        <span class="col-badge">bb</span>
        <span class="col-badge">imt</span>
        <span class="col-badge">sistolik</span>
        <span class="col-badge">diastolik</span>
        <span class="col-badge">map <em style="font-weight:400;">(opsional, dihitung otomatis)</em></span>
        <span class="col-badge">gds</span>
        <span class="col-badge">protein_urin</span>
        <span class="col-badge" style="background:#fef3c7; color:#92400e;">status <em style="font-weight:400;">(opsional)</em></span>
    </div>
    <p style="font-size:0.8rem; color:var(--color-gray-400); margin:0.75rem 0 0;">
        💡 Nama kolom bisa menggunakan alternatif: <code>tinggibadan</code>→<code>tb</code>, <code>beratbadan</code>→<code>bb</code>, <code>sistol</code>→<code>sistolik</code>, <code>diastol</code>→<code>diastolik</code>, <code>protein_urine</code>→<code>protein_urin</code>
    </p>
</div>

<!-- Upload Form -->
<div class="medical-card fade-in">
    <h3 style="font-size:1.125rem; font-weight:700; color:var(--color-gray-800); margin:0 0 1.5rem;">
        📤 Upload File Data Latih
    </h3>

    <form action="{{ route('predictions.training.predict') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf

        <!-- Drop Zone -->
        <div class="upload-zone" id="dropZone" onclick="document.getElementById('training_dataset').click()">
            <div class="upload-icon-wrap">
                <svg style="width:36px;height:36px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <h3 style="font-size:1.1rem; font-weight:700; color:var(--color-gray-700); margin:0 0 0.5rem;">
                Klik atau Seret File CSV ke Sini
            </h3>
            <p style="color:var(--color-gray-400); font-size:0.875rem; margin:0 0 1rem;">
                File data latih (kolom <code>status</code> opsional)
            </p>
            <div id="filePreview" style="display:none; background:white; border-radius:10px; padding:0.75rem 1.25rem; display:inline-flex; align-items:center; gap:0.75rem; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                <svg style="width:20px;height:20px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="fileName" style="font-weight:600; color:var(--color-gray-700); font-size:0.875rem;">—</span>
                <span id="fileSize" style="font-size:0.75rem; color:var(--color-gray-400);">—</span>
            </div>
            <input type="file" name="training_dataset" id="training_dataset" accept=".csv" style="display:none;" onchange="handleFileSelect(this)">
        </div>

        @error('training_dataset')
        <p style="color:#dc2626; font-size:0.875rem; margin:0.5rem 0 0;">{{ $message }}</p>
        @enderror

        <div style="display:flex; justify-content:flex-end; margin-top:1.5rem;">
            <button type="submit" class="submit-btn" id="submitBtn" disabled>
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Jalankan Prediksi KNN
            </button>
        </div>
    </form>
</div>

@include('partials.knn-evaluation-metrics', [
    'metrics' => $lastMetrics ?? [],
    'title' => '📊 Hasil Penilaian Terakhir (Data Training)',
    'subtitle' => 'Confusion matrix & classification report dari prediksi data latih terakhir',
])

<script>
function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;

    const preview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');

    fileName.textContent = file.name;
    fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
    preview.style.display = 'inline-flex';
    submitBtn.disabled = false;
}

// Drag & Drop
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const dt = e.dataTransfer;
    if (dt.files.length) {
        const input = document.getElementById('training_dataset');
        input.files = dt.files;
        handleFileSelect(input);
    }
});

// Show loading on submit
document.getElementById('uploadForm').addEventListener('submit', function() {
    document.getElementById('loadingOverlay').style.display = 'flex';
});
</script>
@endsection
