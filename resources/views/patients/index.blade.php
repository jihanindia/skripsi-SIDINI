@extends('layouts.dashboard')

@section('title', 'Data Pasien - SIDINI')
@section('page-title', 'Riwayat Skrining Dini Preeklampsia')
@section('page-subtitle')
@if(auth()->user()->isPuskesmas())
    Data Skrining Dini Preeklampsia — {{ auth()->user()->puskesmas }}
@else
    Monitoring data skrining seluruh puskesmas
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
        📋 Data Hasil Skrining
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
                    <th>MAP</th>
                    <th>GDS</th>
                    <th>Hasil KNN</th>
                    <th>Aksi</th>
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
                    <td>{{ $assessment->map ?? '-' }}</td>
                    <td>{{ $assessment->gds ?? '-' }}</td>
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
                    <td>
                        <div style="display: flex; gap: 0.4rem;">
                            <button class="btn btn-outline btn-detail-patient"
                                style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 6px; font-weight: 600;"
                                data-id="{{ $assessment->id }}"
                                data-name="{{ $assessment->patient->name ?? '-' }}"
                                data-date="{{ $assessment->assessment_date?->format('d/m/Y') ?? '-' }}"
                                data-age="{{ $assessment->patient->age ?? '-' }}"
                                data-para="{{ $assessment->para }}"
                                data-systolic="{{ $assessment->systolic_bp }}"
                                data-diastolic="{{ $assessment->diastolic_bp }}"
                                data-weight="{{ $assessment->beratbadan ?? '-' }}"
                                data-height="{{ $assessment->tinggibadan ?? '-' }}"
                                data-bmi="{{ $assessment->imt ?? '-' }}"
                                data-protein="{{ $proteinLabels[$assessment->protein_urine] ?? $assessment->protein_urine }}"
                                data-protein-raw="{{ array_search($proteinLabels[$assessment->protein_urine] ?? $assessment->protein_urine, $proteinLabels) ?: '0' }}"
                                data-map="{{ $assessment->map ?? '-' }}"
                                data-gds="{{ $assessment->gds ?? '-' }}"
                                data-result="{{ $assessment->result ? $assessment->result->prediction_label : '-' }}"
                                data-is-preeklampsia="{{ $isPreeklampsia ? '1' : '0' }}"
                                data-recommendations="{{ json_encode($assessment->result->recommendations ?? []) }}"
                                data-urgency="{{ $assessment->result->urgency_level ?? '-' }}">
                                🔍 Detail
                            </button>
                            @if(auth()->user()->isPuskesmas())
                            <button class="btn btn-primary btn-edit-patient"
                                style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 6px; font-weight: 600; background: linear-gradient(135deg,#0ea5e9,#0369a1);"
                                data-id="{{ $assessment->id }}"
                                data-update-url="{{ route('assessments.update', $assessment->id) }}"
                                data-name="{{ $assessment->patient->name ?? '' }}"
                                data-date="{{ $assessment->assessment_date?->format('Y-m-d') ?? '' }}"
                                data-age="{{ $assessment->patient->age ?? '' }}"
                                data-para="{{ $assessment->para }}"
                                data-systolic="{{ $assessment->systolic_bp }}"
                                data-diastolic="{{ $assessment->diastolic_bp }}"
                                data-weight="{{ $assessment->beratbadan ?? '' }}"
                                data-height="{{ $assessment->tinggibadan ?? '' }}"
                                data-imt="{{ $assessment->imt ?? '' }}"
                                data-protein-raw="{{ array_search($assessment->protein_urine, [
                                    'negative'=>'negative', '+1'=>'+1', '+2'=>'+2', '+3'=>'+3', '+4'=>'+4'
                                ]) !== false ? (function() use ($assessment) {
                                    $map = ['negative'=>'0','+1'=>'1','+2'=>'2','+3'=>'3','+4'=>'4'];
                                    return $map[$assessment->protein_urine] ?? '0';
                                })() : '0' }}"
                                data-gds="{{ $assessment->gds ?? '' }}">
                                ✏️ Edit
                            </button>
                            @endif
                        </div>
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

{{-- ======== MODAL DETAIL ======== --}}
<div id="modalPatientDetail" class="patient-modal-overlay" style="display: none;" aria-hidden="true">
    <div class="patient-modal-box" style="max-width: 600px;">
        <div class="patient-modal-header">
            <h2>🔍 Detail Screening Pasien</h2>
            <button type="button" id="btnCloseDetailModal" class="modal-close-btn">&times;</button>
        </div>
        <div class="patient-modal-col">
            <div class="modal-section-box">
                <h3 class="modal-section-title">👤 Profil Pasien</h3>
                <div class="modal-info-grid2">
                    <div><span class="info-label">Nama Pasien</span><strong id="detName">-</strong></div>
                    <div><span class="info-label">Tanggal Penilaian</span><strong id="detDate">-</strong></div>
                    <div><span class="info-label">Usia</span><span id="detAge">-</span> Tahun</div>
                    <div><span class="info-label">Paritas</span><span id="detPara">-</span></div>
                </div>
            </div>
            <div class="modal-section-box">
                <h3 class="modal-section-title">🩺 Tanda Vital & Lab</h3>
                <div class="modal-info-grid3">
                    <div><span class="info-label">Tekanan Darah</span><strong id="detBP">-</strong> mmHg</div>
                    <div><span class="info-label">MAP</span><strong id="detMAP">-</strong> mmHg</div>
                    <div><span class="info-label">Protein Urin</span><strong id="detProtein">-</strong></div>
                    <div><span class="info-label">Berat Badan</span><span id="detWeight">-</span> kg</div>
                    <div><span class="info-label">Tinggi Badan</span><span id="detHeight">-</span> cm</div>
                    <div><span class="info-label">IMT</span><span id="detBMI">-</span></div>
                    <div style="grid-column: span 3;"><span class="info-label">GDS</span><span id="detGDS">-</span> mg/dL</div>
                </div>
            </div>
            <div class="modal-section-box">
                <h3 class="modal-section-title">💡 Hasil & Rekomendasi</h3>
                <div style="margin-bottom: 0.75rem;">
                    <span class="info-label">Hasil Diagnosis KNN</span>
                    <span id="detResultBadge" class="risk-badge" style="padding: 0.35rem 0.85rem; font-size: 0.85rem; font-weight: 700;">-</span>
                </div>
                <div>
                    <span class="info-label">Rekomendasi Medis</span>
                    <ul id="detRecommendations" style="margin: 0.25rem 0 0; padding-left: 1.25rem; font-size: 0.85rem; color: var(--color-gray-700); line-height: 1.6;"></ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" id="btnModalDetailClose">Tutup Detail</button>
        </div>
    </div>
</div>


{{-- ======== MODAL EDIT ======== --}}
<div id="modalEditPatient" class="patient-modal-overlay" style="display: none;" aria-hidden="true">
    <div class="patient-modal-box" style="max-width: 620px;">
        <div class="patient-modal-header">
            <h2>✏️ Edit Data Penilaian</h2>
            <button type="button" id="btnCloseEditModal" class="modal-close-btn">&times;</button>
        </div>
        <div id="editLoadingOverlay" style="display:none; text-align:center; padding: 2rem;">
            <div class="modal-spinner" style="margin: 0 auto 1rem;"></div>
            <p style="color:var(--color-gray-600);">Sedang menghitung ulang prediksi KNN...</p>
        </div>
        <div id="editFormContent">
            <p style="font-size:0.85rem;color:var(--color-gray-500);margin:0 0 1.25rem;">
                Pasien: <strong id="editPatientName">-</strong> &nbsp;|&nbsp; Ubah data lalu klik <strong>Simpan & Prediksi Ulang</strong> untuk mendapatkan hasil KNN terbaru.
            </p>
            <form id="editAssessmentForm">
                @csrf
                <div class="edit-form-grid">
                    <div class="form-group">
                        <label class="form-label">Tanggal Penilaian</label>
                        <input type="date" name="assessment_date" id="editDate" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Usia (Tahun)</label>
                        <input type="number" name="usia" id="editUsia" class="form-input" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Paritas</label>
                        <input type="number" name="paritas" id="editParitas" class="form-input" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sistolik (mmHg)</label>
                        <input type="number" name="systolic_bp" id="editSistolik" class="form-input" min="70" max="250" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Diastolik (mmHg)</label>
                        <input type="number" name="diastolic_bp" id="editDiastolik" class="form-input" min="40" max="150" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="beratbadan" id="editBB" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" name="tinggibadan" id="editTB" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">IMT (auto)</label>
                        <input type="number" step="0.1" name="imt" id="editIMT" class="form-input" readonly style="background:var(--color-gray-100);">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Protein Urin</label>
                        <select name="protein_urine" id="editProtein" class="form-input" required>
                            <option value="0">Negatif</option>
                            <option value="1">+1</option>
                            <option value="2">+2</option>
                            <option value="3">+3</option>
                            <option value="4">+4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">GDS (mg/dL)</label>
                        <input type="number" name="gds" id="editGDS" class="form-input" placeholder="Opsional">
                    </div>
                </div>
                <div id="editResultBanner" style="display:none; margin:1rem 0; padding:0.75rem 1rem; border-radius:10px; font-weight:700; font-size:0.95rem; text-align:center;"></div>
                <div class="modal-footer" style="margin-top:1rem;">
                    <button type="button" class="btn btn-outline" id="btnCancelEdit">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveEdit">
                        💾 Simpan & Prediksi Ulang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* === Modal Overlay === */
.patient-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, 0.6);
    display: flex; align-items: center; justify-content: center;
    z-index: 9999; padding: 1rem;
}
.patient-modal-box {
    background: white; border-radius: 20px; padding: 2rem;
    max-width: 940px; width: 100%; max-height: 92vh; overflow-y: auto;
    box-shadow: 0 25px 60px -12px rgba(0,0,0,0.3);
    animation: fadeIn 0.25s ease-out;
}
.patient-modal-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 2px solid var(--color-gray-200);
    padding-bottom: 1rem; margin-bottom: 1.5rem;
}
.patient-modal-header h2 { font-size: 1.3rem; font-weight: 700; color: var(--color-gray-800); margin: 0; }
.modal-close-btn { background: none; border: none; font-size: 1.6rem; cursor: pointer; color: var(--color-gray-500); line-height: 1; }
.modal-close-btn:hover { color: var(--color-gray-800); }
.patient-modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.patient-modal-col { display: flex; flex-direction: column; gap: 1rem; }
.modal-section-box { background: var(--color-gray-50); border: 1px solid var(--color-gray-200); border-radius: 12px; padding: 1.25rem; text-align: left; }
.modal-section-title { font-size: 1rem; font-weight: 700; color: var(--color-gray-800); margin: 0 0 0.85rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--color-gray-200); }
.modal-info-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.7rem; font-size: 0.9rem; }
.modal-info-grid3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.65rem; font-size: 0.85rem; }
.info-label { color: var(--color-gray-500); font-size: 0.75rem; display: block; margin-bottom: 0.1rem; }
.knn-metric-card { background: white; border: 1px solid var(--color-gray-200); border-radius: 8px; padding: 0.75rem; text-align: center; display: flex; flex-direction: column; gap: 0.15rem; }
.metric-row { display: flex; justify-content: space-between; margin-bottom: 0.4rem; padding-bottom: 0.25rem; border-bottom: 1px dashed var(--color-gray-200); }
.metric-row:last-child { border-bottom: none; margin-bottom: 0; }
/* Confusion Matrix */
.cm-table { width: 100%; border-collapse: collapse; text-align: center; font-size: 0.8rem; margin-top: 0.5rem; }
.cm-axis-label {
    background: var(--color-gray-300); color: var(--color-gray-800); font-weight: 700;
    padding: 0.5rem; width: 10%; writing-mode: vertical-rl; transform: rotate(180deg);
    border-radius: 6px 0 0 6px; border: none; font-size: 0.7rem; text-transform: uppercase;
}
.cm-cell { border: 2px solid white; padding: 0.85rem 0.5rem; font-size: 1.3rem; font-weight: 800; }
.cm-cell-label { font-size: 0.6rem; font-weight: 500; margin-top: 0.2rem; }
.cm-tn { background: #e8f5e9; color: #2e7d32; }
.cm-tn .cm-cell-label { color: #4caf50; }
.cm-fp { background: #fff3e0; color: #e65100; }
.cm-fp .cm-cell-label { color: #ff9800; }
.cm-fn { background: #ffebee; color: #c62828; }
.cm-fn .cm-cell-label { color: #f44336; }
.cm-tp { background: #e8f5e9; color: #2e7d32; }
.cm-tp .cm-cell-label { color: #4caf50; }
/* Edit Form */
.edit-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.modal-footer { display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-gray-200); }
/* Modal spinner */
.modal-spinner {
    width: 40px; height: 40px;
    border: 4px solid var(--color-gray-200); border-top-color: var(--color-medical-primary);
    border-radius: 50%; animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
@media (max-width: 768px) {
    .patient-modal-grid, .edit-form-grid { grid-template-columns: 1fr; }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ——— Search ———
    document.getElementById('searchPatient')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#patientsTable tbody tr[data-name]').forEach(row => {
            row.style.display = row.dataset.name.includes(q) ? '' : 'none';
        });
    });

    // ——— DETAIL MODAL ———
    const modalDetail = document.getElementById("modalPatientDetail");
    document.getElementById("btnCloseDetailModal")?.addEventListener("click", () => modalDetail.style.display = "none");
    document.getElementById("btnModalDetailClose")?.addEventListener("click", () => modalDetail.style.display = "none");

    function fillDetailModal(data) {
        document.getElementById("detName").textContent     = data.name;
        document.getElementById("detDate").textContent     = data.date;
        document.getElementById("detAge").textContent      = data.age;
        document.getElementById("detPara").textContent     = data.para;
        document.getElementById("detBP").textContent       = data.systolic + "/" + data.diastolic;
        document.getElementById("detMAP").textContent      = data.map;
        document.getElementById("detProtein").textContent  = data.protein;
        document.getElementById("detWeight").textContent   = data.weight;
        document.getElementById("detHeight").textContent   = data.height;
        document.getElementById("detBMI").textContent      = data.bmi;
        document.getElementById("detGDS").textContent      = data.gds || '-';

        const isP = data.isPreeklampsia === '1';
        const badge = document.getElementById("detResultBadge");
        badge.textContent = data.result;
        badge.style.backgroundColor = isP ? "#fee2e2" : "#d1fae5";
        badge.style.color = isP ? "#991b1b" : "#065f46";

        const recList = document.getElementById("detRecommendations");
        recList.innerHTML = "";
        try {
            const recs = JSON.parse(data.recommendations || '[]');
            if (Array.isArray(recs) && recs.length > 0) {
                recs.forEach(rec => { const li = document.createElement("li"); li.textContent = rec; recList.appendChild(li); });
            } else {
                recList.innerHTML = "<li>Tidak ada rekomendasi spesifik.</li>";
            }
        } catch(e) { recList.innerHTML = "<li>Tidak dapat memuat rekomendasi.</li>"; }
    }

    document.querySelectorAll(".btn-detail-patient").forEach(btn => {
        btn.addEventListener("click", function () {
            fillDetailModal(this.dataset);
            modalDetail.style.display = "flex";
            modalDetail.setAttribute("aria-hidden", "false");
        });
    });

    // Auto-open from URL ?open_detail=ID
    const openDetailId = new URLSearchParams(window.location.search).get("open_detail");
    if (openDetailId) {
        const btn = document.querySelector(`.btn-detail-patient[data-id="${openDetailId}"]`);
        if (btn) btn.click();
    }

    // ——— EDIT MODAL ———
    const modalEdit = document.getElementById("modalEditPatient");
    const editForm  = document.getElementById("editAssessmentForm");
    let currentUpdateUrl = '';

    document.getElementById("btnCloseEditModal")?.addEventListener("click", () => modalEdit.style.display = "none");
    document.getElementById("btnCancelEdit")?.addEventListener("click",      () => modalEdit.style.display = "none");

    // Auto-calculate IMT when BB / TB changes
    function calcIMT() {
        const bb = parseFloat(document.getElementById('editBB').value);
        const tb = parseFloat(document.getElementById('editTB').value);
        if (bb > 0 && tb > 0) {
            const tbM = tb / 100;
            document.getElementById('editIMT').value = (bb / (tbM * tbM)).toFixed(1);
        }
    }
    document.getElementById('editBB')?.addEventListener('input', calcIMT);
    document.getElementById('editTB')?.addEventListener('input', calcIMT);

    document.querySelectorAll(".btn-edit-patient").forEach(btn => {
        btn.addEventListener("click", function () {
            const d = this.dataset;
            currentUpdateUrl = d.updateUrl;

            document.getElementById("editPatientName").textContent = d.name;
            document.getElementById("editDate").value    = d.date;
            document.getElementById("editUsia").value    = d.age;
            document.getElementById("editParitas").value = d.para;
            document.getElementById("editSistolik").value  = d.systolic;
            document.getElementById("editDiastolik").value = d.diastolic;
            document.getElementById("editBB").value   = d.weight;
            document.getElementById("editTB").value   = d.height;
            document.getElementById("editIMT").value  = d.imt;
            document.getElementById("editGDS").value  = d.gds || '';

            // Set protein urin dropdown
            const proteinMap = {'negative':'0','+1':'1','+2':'2','+3':'3','+4':'4'};
            const proteinVal = proteinMap[d.proteinRaw] ?? d.proteinRaw ?? '0';
            document.getElementById("editProtein").value = proteinVal;

            document.getElementById("editResultBanner").style.display = 'none';
            document.getElementById("editFormContent").style.display = 'block';
            document.getElementById("editLoadingOverlay").style.display = 'none';
            document.getElementById("btnSaveEdit").disabled = false;

            modalEdit.style.display = "flex";
            modalEdit.setAttribute("aria-hidden", "false");
        });
    });

    editForm?.addEventListener("submit", async function(e) {
        e.preventDefault();

        document.getElementById("editLoadingOverlay").style.display = 'block';
        document.getElementById("editFormContent").style.display = 'none';

        const formData = new FormData(editForm);
        formData.append('_method', 'PUT');

        try {
            const res = await fetch(currentUpdateUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            const data = await res.json();

            document.getElementById("editLoadingOverlay").style.display = 'none';
            document.getElementById("editFormContent").style.display = 'block';

            if (data.success) {
                const isP = data.prediction === 'preeklampsia';
                const banner = document.getElementById("editResultBanner");
                banner.textContent = '✅ Prediksi Baru: ' + data.prediction_label;
                banner.style.backgroundColor = isP ? '#fee2e2' : '#d1fae5';
                banner.style.color = isP ? '#991b1b' : '#065f46';
                banner.style.display = 'block';
                document.getElementById("btnSaveEdit").textContent = '✔ Tersimpan';
                document.getElementById("btnSaveEdit").disabled = true;

                // Refresh halaman setelah 2 detik agar tabel terupdate
                setTimeout(() => { window.location.reload(); }, 2000);
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan.');
                document.getElementById("btnSaveEdit").disabled = false;
            }
        } catch(err) {
            document.getElementById("editLoadingOverlay").style.display = 'none';
            document.getElementById("editFormContent").style.display = 'block';
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
        }
    });

});
</script>
@endsection
