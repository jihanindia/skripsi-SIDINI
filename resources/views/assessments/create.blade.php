@extends('layouts.dashboard')

@section('title', 'Penilaian Baru - SIDINI')
@section('page-title', 'Deteksi Dini Preeklampsia')
@section('page-subtitle', 'Form penilaian komprehensif berbasis algoritma K-Nearest Neighbors (KNN)')

@section('content')
<!-- Medical Disclaimer -->
<!-- <div class="disclaimer fade-in">
    <div class="disclaimer-content">
        <h3 style="font-weight: 700; color: var(--color-gray-800); margin: 0 0 0.5rem 0;">⚠️ Disclaimer Medis</h3>
        <p style="margin: 0; color: var(--color-gray-700); line-height: 1.6;">
            Hasil penilaian ini bersifat <strong>edukatif dan pendukung keputusan klinis</strong>. 
            Diagnosis dan penanganan medis hanya dapat dilakukan oleh tenaga kesehatan profesional yang berkompeten.
        </p>
    </div>
</div> -->

<!-- Assessment Form Card -->
<div class="medical-card fade-in" style="animation-delay: 0.1s;">
    <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 0.5rem 0;">
        📋 Form Skrining Pasien
    </h2>
    <p style="color: var(--color-gray-600); margin: 0 0 2rem 0;">
        Lengkapi semua data pasien dengan teliti untuk mendapatkan hasil prediksi yang akurat
    </p>

    <form id="assessmentForm" action="{{ route('assessments.store') }}" method="POST">
        @csrf
        
        <!-- Patient Selection -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-icon">👤</span>
                Informasi Pasien
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Pasien</label>
                    <input type="text" class="form-input" name="patient_name" value="{{ old('patient_name') }}" placeholder="Masukkan nama pasien" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tanggal Penilaian</label>
                    <input type="date" class="form-input" name="assessment_date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>

        <!-- Pregnancy Data -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-icon">🤰</span>
                Data Pasien
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Usia</label>
                    <input type="number" class="form-input" name="usia" min="1" placeholder="Contoh: 2" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Paritas</label>
                    <input type="number" class="form-input" name="paritas" min="0" placeholder="Contoh: 1" required>
                </div>
            </div>
        </div>

        <!-- Vital Signs -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-icon">💓</span>
                Tanda Vital
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Tekanan Darah Sistolik (mmHg)</label>
                    <input type="number" class="form-input" name="systolic_bp" min="70" max="250" placeholder="Contoh: 140" required>
                    <small style="color: var(--color-gray-500); font-size: 0.85rem;">Normal: 90-140 mmHg</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tekanan Darah Diastolik (mmHg)</label>
                    <input type="number" class="form-input" name="diastolic_bp" min="40" max="150" placeholder="Contoh: 90" required>
                    <small style="color: var(--color-gray-500); font-size: 0.85rem;">Normal: 60-90 mmHg</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Berat Badan (kg)</label>
                    <input type="number" step="0.1" class="form-input" name="beratbadan" placeholder="Contoh: 55.5" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tinggi Badan (cm)</label>
                    <input type="number" class="form-input" name="tinggibadan" placeholder="Contoh: 160" required>
                </div>

                <div class="form-group">
                    <label class="form-label">IMT</label>
                    <input type="number" step="0.1" class="form-input" name="imt" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">MAP (Mean Arterial Pressure)</label>
                    <input type="number" step="0.1" class="form-input" name="map" readonly>
                    <small style="color: var(--color-gray-500); font-size: 0.85rem;">Normal: 70-90 mmHg</small>
                </div>
            </div>
        </div>

        <!-- Lab Results -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-icon">🧪</span>
                Hasil Laboratorium
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Protein Urin</label>
                    <select class="form-input" name="protein_urine" required>
                        <option value="0">Negatif</option>
                        <option value="1">+1</option>
                        <option value="2">+2</option>
                        <option value="3">+3</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Glukosa</label>
                    <input type="number" class="form-input" name="gds" placeholder="">
                </div>
                
                <!-- <div class="form-group">
                    <label class="form-label">SGPT (U/L)</label>
                    <input type="number" class="form-input" name="sgpt" placeholder="Contoh: 30">
                </div> -->
                
                <!-- <div class="form-group">
                    <label class="form-label">Kreatinin (mg/dL)</label>
                    <input type="number" step="0.01" class="form-input" name="creatinine" placeholder="Contoh: 0.9">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Asam Urat (mg/dL)</label>
                    <input type="number" step="0.1" class="form-input" name="uric_acid" placeholder="Contoh: 5.5">
                </div> -->
            </div>
        </div>

        <!-- Clinical Symptoms -->
        <!-- <div class="form-section">
            <h3 class="section-title">
                <span class="section-icon">🩺</span>
                Gejala Klinis
            </h3>
            
            <div class="checkbox-grid">
                <label class="checkbox-item">
                    <input type="checkbox" name="severe_headache" value="1">
                    <span>Sakit Kepala Hebat</span>
                </label>
                
                <label class="checkbox-item">
                    <input type="checkbox" name="blurred_vision" value="1">
                    <span>Penglihatan Kabur</span>
                </label>
                
                <label class="checkbox-item">
                    <input type="checkbox" name="epigastric_pain" value="1">
                    <span>Nyeri Ulu Hati</span>
                </label>
                
                <label class="checkbox-item">
                    <input type="checkbox" name="nausea_vomiting" value="1">
                    <span>Mual Muntah Berat</span>
                </label>
                
                <label class="checkbox-item">
                    <input type="checkbox" name="facial_edema" value="1">
                    <span>Bengkak Wajah</span>
                </label>
                
                <label class="checkbox-item">
                    <input type="checkbox" name="hand_edema" value="1">
                    <span>Bengkak Tangan</span>
                </label>
                
                <label class="checkbox-item">
                    <input type="checkbox" name="decreased_consciousness" value="1">
                    <span>Penurunan Kesadaran</span>
                </label>
                
                <label class="checkbox-item">
                    <input type="checkbox" name="seizures" value="1">
                    <span>Kejang</span>
                </label>
            </div>
        </div> -->


        <!-- Submit Buttons -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="{{ route('dashboard') }}" class="btn btn-outline">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Batal
            </a>
            <button type="submit" id="btnSubmitAssessment" class="btn btn-primary">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Analisis
            </button>
        </div>
    </form>
</div>

<!-- Modal 1: Proses analisis -->
<div id="modalLoading" class="assessment-modal-overlay" style="display: none;" aria-hidden="true">
    <div class="assessment-modal">
        <div class="modal-spinner"></div>
        <h3 style="margin: 1rem 0 0.5rem; font-size: 1.25rem; color: var(--color-gray-800);">Sedang Menganalisis...</h3>
        <p style="margin: 0; color: var(--color-gray-600); text-align: center;">
            Data sedang diproses dengan algoritma K-Nearest Neighbors (KNN). Mohon tunggu.
        </p>
    </div>
</div>

<!-- Modal 2: Hasil deteksi -->
<div id="modalResult" class="assessment-modal-overlay" style="display: none;" aria-hidden="true">
    <div class="assessment-modal" id="modalResultBox" style="max-width: 500px;">
        <div id="modalResultIcon" style="font-size: 3rem; margin-bottom: 0.5rem;"></div>
        <h3 id="modalResultTitle" style="margin: 0 0 0.5rem; font-size: 1.5rem;"></h3>
        <p id="modalResultMessage" style="margin: 0 0 1rem; color: var(--color-gray-600); text-align: center; line-height: 1.6;"></p>
        <p id="modalResultPatient" style="margin: 0 0 1rem; font-weight: 600; color: var(--color-gray-700);"></p>
        <!-- Rekomendasi Rujukan - hanya tampil saat preeklampsia -->
        <div id="modalReferralBox" style="display:none; background:#fef2f2; border:1.5px solid #fca5a5; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.25rem; text-align:left;">
            <div style="display:flex; align-items:flex-start; gap:0.6rem;">
                <span style="font-size:1.3rem; flex-shrink:0;">🏥</span>
                <div>
                    <p style="font-weight:700; color:#b91c1c; margin:0 0 0.3rem; font-size:0.95rem;">Perlu Dirujuk ke Rumah Sakit</p>
                    <p style="font-size:0.82rem; color:#7f1d1d; margin:0; line-height:1.55;">Pasien dengan risiko preeklampsia memerlukan penanganan lebih lanjut oleh dokter spesialis. <strong>Segera rujuk ke rumah sakit</strong> untuk pemeriksaan dan tatalaksana yang tepat.</p>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
            <button type="button" class="btn btn-outline" id="btnModalClose">Tutup</button>
            <a href="{{ route('patients.index') }}" class="btn btn-primary" id="btnModalToPatients">Daftar Pasien</a>
            <a href="#" class="btn btn-success" id="btnModalDetailAkurasi">Lihat Detail</a>
        </div>
    </div>
</div>

<style>
.assessment-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1rem;
}

.assessment-modal {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    max-width: 440px;
    width: 100%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    text-align: center;
}

.assessment-modal.modal-preeklampsia {
    border-top: 6px solid #dc2626;
}

.assessment-modal.modal-normal {
    border-top: 6px solid #059669;
}

.modal-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--color-gray-200);
    border-top-color: var(--color-medical-primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid var(--color-gray-100);
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-gray-800);
    margin: 0 0 1.5rem 0;
}

.section-icon {
    font-size: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--color-gray-50);
    border: 2px solid var(--color-gray-200);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.checkbox-item:hover {
    background: white;
    border-color: var(--color-medical-primary);
}

.checkbox-item input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: var(--color-medical-primary);
}

.checkbox-item span {
    font-weight: 500;
    color: var(--color-gray-700);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .checkbox-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const bbInput = document.querySelector('[name="beratbadan"]');
    const tbInput = document.querySelector('[name="tinggibadan"]');
    const imtInput = document.querySelector('[name="imt"]');

    function hitungIMT() {
        let bb = parseFloat(bbInput.value);
        let tb = parseFloat(tbInput.value);

        if (!bb || !tb || tb === 0) {
            imtInput.value = '';
            return;
        }

        let tbMeter = tb / 100;
        let imt = bb / (tbMeter * tbMeter);

        imtInput.value = imt.toFixed(1);
    }

    bbInput.addEventListener('input', hitungIMT);
    tbInput.addEventListener('input', hitungIMT);

    const sistolikInput = document.querySelector('[name="systolic_bp"]');
    const diastolikInput = document.querySelector('[name="diastolic_bp"]');
    const mapInput = document.querySelector('[name="map"]');

    function hitungMAP() {
        let sistolik = parseFloat(sistolikInput.value);
        let diastolik = parseFloat(diastolikInput.value);

        if (!sistolik || !diastolik) {
            mapInput.value = '';
            return;
        }

        let map = ((2 * diastolik) + sistolik) / 3;
        mapInput.value = map.toFixed(1);
    }

    sistolikInput.addEventListener('input', hitungMAP);
    diastolikInput.addEventListener('input', hitungMAP);

    const form = document.getElementById('assessmentForm');
    const modalLoading = document.getElementById('modalLoading');
    const modalResult = document.getElementById('modalResult');
    const modalResultBox = document.getElementById('modalResultBox');
    const btnSubmit = document.getElementById('btnSubmitAssessment');

    function showModal(el) {
        el.style.display = 'flex';
        el.setAttribute('aria-hidden', 'false');
    }

    function hideModal(el) {
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
    }

    function showResultModal(prediction, patientName, assessmentId) {
        const isPreeklampsia = prediction === 'preeklampsia';
        modalResultBox.classList.remove('modal-normal', 'modal-preeklampsia');
        modalResultBox.classList.add(isPreeklampsia ? 'modal-preeklampsia' : 'modal-normal');

        document.getElementById('modalResultIcon').textContent = isPreeklampsia ? '⚠️' : '✅';
        document.getElementById('modalResultTitle').textContent = isPreeklampsia
            ? 'Terdeteksi Preeklampsia'
            : 'Hasil Normal';
        document.getElementById('modalResultTitle').style.color = isPreeklampsia ? '#991b1b' : '#065f46';
        let message = isPreeklampsia
            ? 'Berdasarkan analisis KNN, pasien berisiko mengalami preeklampsia. Segera lakukan evaluasi medis lebih lanjut.'
            : 'Berdasarkan analisis KNN, pasien dalam kondisi normal. Tetap lakukan pemantauan kehamilan secara rutin.';

        if (!isPreeklampsia) {
            const sistolik = parseInt(document.querySelector('[name="systolic_bp"]').value) || 0;
            const diastolik = parseInt(document.querySelector('[name="diastolic_bp"]').value) || 0;
            if (sistolik > 140 || diastolik > 90) {
                message += ' Cek tekanan darah secara berkala.';
            }
        }
        document.getElementById('modalResultMessage').textContent = message;
        document.getElementById('modalResultPatient').textContent = 'Pasien: ' + patientName;

        // Tampilkan/sembunyikan rekomendasi rujukan
        const referralBox = document.getElementById('modalReferralBox');
        if (referralBox) {
            referralBox.style.display = isPreeklampsia ? 'block' : 'none';
        }

        const detailAkurasiBtn = document.getElementById('btnModalDetailAkurasi');
        if (detailAkurasiBtn) {
            detailAkurasiBtn.href = "{{ route('patients.index') }}?open_detail=" + assessmentId;
        }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        btnSubmit.disabled = true;
        hideModal(modalResult);
        showModal(modalLoading);

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            hideModal(modalLoading);

            if (!response.ok) {
                let msg = 'Gagal menyimpan penilaian.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('\n');
                } else if (data.message) {
                    msg = data.message;
                }
                alert(msg);
                btnSubmit.disabled = false;
                return;
            }

            if (!data.success) {
                alert(data.message || 'Gagal menyimpan penilaian.');
                btnSubmit.disabled = false;
                return;
            }

            showResultModal(data.prediction, data.patient_name, data.assessment_id);
            showModal(modalResult);
        } catch (err) {
            hideModal(modalLoading);
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            btnSubmit.disabled = false;
        }
    });

    document.getElementById('btnModalClose').addEventListener('click', function () {
        hideModal(modalResult);
        btnSubmit.disabled = false;
    });

    document.getElementById('btnModalToPatients').addEventListener('click', function () {
        window.location.href = this.href;
    });
});
</script>
@endsection
