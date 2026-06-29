@extends('layouts.app')

@section('title', 'Daftar - SIDINI')

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div class="glass-card fade-in" style="max-width: 600px; width: 100%;">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="medical-icon icon-primary" style="width: 70px; height: 70px; margin: 0 auto 1rem; font-size: 35px;">
                🏥
            </div>
            <h1 class="gradient-text" style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;">
                Daftar Akun Baru
            </h1>
            <p style="color: var(--color-gray-600); font-size: 0.95rem;">
                Buat akun untuk mengakses SIDINI
            </p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <!-- Name Input -->
                <div class="form-group" style="grid-column: span 2;">
                    <label for="name" class="form-label">
                        <svg style="width: 16px; height: 16px; display: inline; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Nama Lengkap
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        placeholder="Masukkan nama lengkap"
                        value="{{ old('name') }}"
                        required 
                        autofocus
                    >
                </div>

                <!-- Username Input -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <svg style="width: 16px; height: 16px; display: inline; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="Username unik"
                        value="{{ old('username') }}"
                        required
                    >
                </div>

                <!-- Email Input -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <svg style="width: 16px; height: 16px; display: inline; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="email@example.com"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <!-- Role Selection -->
                <div class="form-group" style="grid-column: span 2;">
                    <label for="role" class="form-label">
                        <svg style="width: 16px; height: 16px; display: inline; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Peran
                    </label>
                    <select id="role" name="role" class="form-input" required>
                        <option value="">Pilih peran...</option>
                        <option value="nurse" {{ old('role') == 'nurse' ? 'selected' : '' }}>Perawat / Bidan</option>
                        <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Dokter</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <svg style="width: 16px; height: 16px; display: inline; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Min. 8 karakter"
                        required
                    >
                </div>

                <!-- Password Confirmation -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <svg style="width: 16px; height: 16px; display: inline; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Konfirmasi Password
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input" 
                        placeholder="Ulangi password"
                        required
                    >
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-full" style="margin-top: 1.5rem;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Daftar Sekarang
            </button>
        </form>

        <!-- Divider -->
        <div style="margin: 2rem 0; text-align: center; position: relative;">
            <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: var(--color-gray-200);"></div>
            <span style="position: relative; background: rgba(255, 255, 255, 0.95); padding: 0 1rem; color: var(--color-gray-500); font-size: 0.875rem;">
                Sudah punya akun?
            </span>
        </div>

        <!-- Login Link -->
        <a href="{{ route('login') }}" class="btn btn-outline w-full">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Masuk ke Akun
        </a>
    </div>
</div>

<style>
    .glass-card {
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    select.form-input {
        cursor: pointer;
    }

    @media (max-width: 640px) {
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
        
        .form-group[style*="grid-column: span 2"] {
            grid-column: span 1 !important;
        }
    }
</style>
@endsection
