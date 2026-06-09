<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainingDataController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

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

    // Data pasien — dinas lihat semua, puskesmas lihat puskesmas sendiri
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');

    // Dinas Kesehatan Kota Bogor — laporan kasus preeklampsia
    Route::middleware('role:dinas')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Puskesmas — penilaian & training KNN
    Route::middleware('role:puskesmas')->group(function () {
        Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments/create', [AssessmentController::class, 'store'])->name('assessments.store');

        Route::get('/training-data', [TrainingDataController::class, 'index'])->name('training-data.index');
        Route::post('/training-data/train', [TrainingDataController::class, 'train'])->name('training-data.train');
    });
});
