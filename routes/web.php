<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainingDataController;

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
    Route::get('/assessments/create', function () {
        return view('assessments.create');
    })->name('assessments.create');
    
    Route::get('/assessments', function () {
        return view('assessments.index');
    })->name('assessments.index');
    
    // Patients
    Route::get('/patients', function () {
        return view('patients.index');
    })->name('patients.index');
    
    Route::get('/patients/create', function () {
        return view('patients.create');
    })->name('patients.create');
    
    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
    
    // Training Data
    Route::get('/training-data', [TrainingDataController::class, 'index'])->name('training-data.index');
    Route::post('/training-data/train', [TrainingDataController::class, 'train'])->name('training-data.train');
});
