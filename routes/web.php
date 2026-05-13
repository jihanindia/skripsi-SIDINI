<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainingDataController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\PatientController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Assessments
    Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments/create', [AssessmentController::class, 'store'])->name('assessments.store');
    
    Route::get('/assessments', function () {
        return view('assessments.index');
    })->name('assessments.index');
    
    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    
    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
    
    // Training Data
    Route::get('/training-data', [TrainingDataController::class, 'index'])->name('training-data.index');
    Route::post('/training-data/train', [TrainingDataController::class, 'train'])->name('training-data.train');
});
