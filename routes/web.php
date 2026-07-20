<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainingDataController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PredictionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data pasien — dinas & puskesmas dapat melihat (dinas semua, puskesmas puskesmas sendiri)
    Route::middleware('role:dinas,puskesmas')->group(function () {
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    });

    // Puskesmas — skrining pasien
    Route::middleware('role:puskesmas')->group(function () {
        Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments/create', [AssessmentController::class, 'store'])->name('assessments.store');
        Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
    });

    // Dinas Kesehatan — laporan kasus preeklampsia
    Route::middleware('role:dinas')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Admin — tuning KNN & prediksi data
    Route::middleware('role:admin')->group(function () {
        Route::get('/training-data', [TrainingDataController::class, 'index'])->name('training-data.index');
        Route::post('/training-data/train', [TrainingDataController::class, 'train'])->name('training-data.train');

        // Prediksi data uji CSV
        Route::get('/predictions', function() { return redirect()->route('predictions.test.index'); })->name('predictions.index');
        Route::get('/predictions/test', [PredictionController::class, 'indexTest'])->name('predictions.test.index');
        Route::post('/predictions/test/predict', [PredictionController::class, 'predictTest'])->name('predictions.test.predict');
        Route::get('/predictions/test/result', [PredictionController::class, 'resultTest'])->name('predictions.test.result');

        // Prediksi data latih CSV
        Route::get('/predictions/training', [PredictionController::class, 'indexTraining'])->name('predictions.training.index');
        Route::post('/predictions/training/predict', [PredictionController::class, 'predictTraining'])->name('predictions.training.predict');
        Route::get('/predictions/training/result', [PredictionController::class, 'resultTraining'])->name('predictions.training.result');
    });
});
