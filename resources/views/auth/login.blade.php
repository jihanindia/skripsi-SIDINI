@extends('layouts.app')

@section('title', 'Login - Preeklampsia CDSS')

@section('content')
<!-- Animated Background -->
<div class="animated-bg">
    <div class="gradient-orb orb-1"></div>
    <div class="gradient-orb orb-2"></div>
    <div class="gradient-orb orb-3"></div>
</div>

<div class="login-container">
    <div class="login-card">
        <!-- Medical Branding -->
        <div class="login-header">
            <div class="logo-container">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4.5 16.5c-1.5 1.25-2 5-2 5s3.75-.5 5-2c.625-.625 1-1.5 1-2.5V16h-1.5c-1 0-1.875.375-2.5 1zm0 0c1.5 1.5 4 1.5 5.5 0M12 15l-3-3m0 0a5.002 5.002 0 007.467.005M9 12l-3.5 3.5M9 12V7.5m0 0a5 5 0 019.5 1.5M9 7.5L5.5 4M19.5 12h-7"></path>
                    </svg>
                </div>
            </div>
            <h1 class="login-title">Preeklampsia CDSS</h1>
            <p class="login-subtitle">Clinical Decision Support System</p>
            <div class="title-underline"></div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger" style="animation: shake 0.5s;">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <!-- Username/Email Input -->
            <div class="input-group">
                <label for="login" class="input-label">
                    <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Username atau Email
                </label>
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        class="modern-input" 
                        placeholder="Masukkan username atau email"
                        value="{{ old('login') }}"
                        required 
                        autofocus
                    >
                    <div class="input-border"></div>
                </div>
            </div>

            <!-- Password Input -->
            <div class="input-group">
                <label for="password" class="input-label">
                    <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Password
                </label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="modern-input" 
                        placeholder="Masukkan password"
                        required
                    >
                    <div class="input-border"></div>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="form-options">
                <label class="remember-me">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="modern-checkbox"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <span class="checkbox-label">Ingat saya</span>
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="login-button">
                <span class="button-content">
                    <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Masuk ke Sistem
                </span>
                <div class="button-shine"></div>
            </button>
        </form>

        <!-- <p style="text-align: center; font-size: 0.875rem; color: var(--color-gray-500); margin: 1.5rem 0 0;">
            Akun diberikan oleh administrator. Hubungi Dinas Kesehatan jika belum memiliki akses.
        </p> -->

        <!-- Footer -->
        <div class="login-footer">
            <p>© 2026 Preeklampsia CDSS</p>
            <p>Sistem Pendukung Keputusan Klinis Berbasis KNN</p>
        </div>
    </div>
</div>

<style>
/* Animated Background */
.animated-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #EC407A 0%, #880E4F 100%);
    overflow: hidden;
    z-index: 0;
}

.gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.6;
    animation: float 20s infinite ease-in-out;
}

.orb-1 {
    width: 500px;
    height: 500px;
    background: linear-gradient(135deg, #EC407A, #880E4F);
    top: -250px;
    left: -250px;
    animation-delay: 0s;
}

.orb-2 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #f093fb, #f5576c);
    bottom: -200px;
    right: -200px;
    animation-delay: 7s;
}

.orb-3 {
    width: 350px;
    height: 350px;
    background: linear-gradient(135deg, #FF80AB, #FF4081);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: 14s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

/* Login Container */
.login-container {
    position: relative;
    z-index: 1;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.login-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(30px);
    border-radius: 32px;
    padding: 3rem;
    max-width: 480px;
    width: 100%;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
    animation: cardSlideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes cardSlideUp {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Header */
.login-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.logo-container {
    margin-bottom: 1.5rem;
}

.logo-icon {
    width: 90px;
    height: 90px;
    margin: 0 auto;
    background: linear-gradient(135deg, #EC407A, #880E4F);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(216, 27, 96, 0.4);
    animation: logoFloat 3s ease-in-out infinite;
}

.logo-icon svg {
    width: 50px;
    height: 50px;
    color: white;
}

@keyframes logoFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.login-title {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #EC407A, #880E4F);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.5px;
}

.login-subtitle {
    color: var(--color-gray-600);
    font-size: 1rem;
    margin: 0;
    font-weight: 500;
}

.title-underline {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #EC407A, #880E4F);
    margin: 1rem auto 0;
    border-radius: 2px;
}

/* Form */
.login-form {
    margin-bottom: 2rem;
}

.input-group {
    margin-bottom: 1.75rem;
}

.input-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--color-gray-700);
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.label-icon {
    width: 18px;
    height: 18px;
    color: var(--color-medical-primary);
}

.input-wrapper {
    position: relative;
}

.modern-input {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid var(--color-gray-200);
    border-radius: 16px;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    color: var(--color-gray-800);
}

.modern-input:focus {
    outline: none;
    border-color: transparent;
    box-shadow: 0 0 0 3px rgba(216, 27, 96, 0.15);
    transform: translateY(-2px);
}

.modern-input:focus + .input-border {
    transform: scaleX(1);
}

.input-border {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #EC407A, #880E4F);
    border-radius: 16px;
    transform: scaleX(0);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-input::placeholder {
    color: var(--color-gray-400);
}

/* Form Options */
.form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.modern-checkbox {
    width: 20px;
    height: 20px;
    border: 2px solid var(--color-gray-300);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    accent-color: #D81B60;
}

.checkbox-label {
    font-weight: 500;
    color: var(--color-gray-700);
    font-size: 0.9rem;
}

.forgot-link {
    color: var(--color-medical-primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.forgot-link:hover {
    color: #880E4F;
    text-decoration: underline;
}

/* Login Button */
.login-button {
    width: 100%;
    padding: 1.125rem 2rem;
    background: linear-gradient(135deg, #EC407A 0%, #D81B60 100%);
    border: none;
    border-radius: 16px;
    color: white;
    font-weight: 700;
    font-size: 1.05rem;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 25px rgba(216, 27, 96, 0.3);
}

.login-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(216, 27, 96, 0.4);
}

.login-button:active {
    transform: translateY(-1px);
}

.button-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.button-icon {
    width: 22px;
    height: 22px;
}

.button-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.login-button:hover .button-shine {
    left: 100%;
}

/* Divider */
.divider {
    position: relative;
    text-align: center;
    margin: 2rem 0;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--color-gray-300), transparent);
}

.divider-text {
    position: relative;
    background: rgba(255, 255, 255, 0.98);
    padding: 0 1.5rem;
    color: var(--color-gray-500);
    font-size: 0.875rem;
    font-weight: 600;
}

/* Register Link */
.register-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border: 2px solid var(--color-gray-300);
    border-radius: 16px;
    color: var(--color-gray-700);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
}

.register-link:hover {
    border-color: #D81B60;
    color: #D81B60;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(216, 27, 96, 0.15);
}

.register-icon {
    width: 20px;
    height: 20px;
}

/* Footer */
.login-footer {
    margin-top: 2.5rem;
    text-align: center;
    color: var(--color-gray-500);
    font-size: 0.85rem;
    line-height: 1.6;
}

.login-footer p {
    margin: 0.25rem 0;
}

/* Shake Animation for Errors */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

/* Responsive */
@media (max-width: 640px) {
    .login-card {
        padding: 2rem 1.5rem;
        border-radius: 24px;
    }
    
    .login-title {
        font-size: 1.75rem;
    }
    
    .logo-icon {
        width: 70px;
        height: 70px;
    }
    
    .logo-icon svg {
        width: 40px;
        height: 40px;
    }
}
</style>
@endsection
