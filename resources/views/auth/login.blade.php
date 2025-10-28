@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-green: #2d7a32;
        --light-green: #4caf50;
        --dark-green: #1b5e20;
    }

    body {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        min-height: 100vh;
    }

    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
        width: 100%;
        max-width: 420px;
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        color: white;
        padding: 30px 30px 40px;
        text-align: center;
        position: relative;
    }

    .card-header::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 40px;
        background: inherit;
        border-radius: 50%;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .logo-container {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .logo-container img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .card-subtitle {
        opacity: 0.9;
        font-size: 0.9rem;
        margin-top: 8px;
    }

    .card-body {
        padding: 50px 30px 30px;
    }

    .form-group {
        margin-bottom: 24px;
        position: relative;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fff;
        outline: none;
    }

    .form-control:focus {
        border-color: var(--light-green);
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        transform: translateY(-1px);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .text-danger {
        display: block;
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 6px;
        font-weight: 500;
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 38px;
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: var(--primary-green);
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
    }

    .form-check-input {
        margin-right: 8px;
        transform: scale(1.1);
    }

    .form-check-label {
        color: #666;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-login {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--primary-green), var(--light-green));
        border: none;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-login::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-login:hover::before {
        left: 100%;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(45, 122, 50, 0.3);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .register-link {
        text-align: center;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e9ecef;
    }

    .register-link a {
        color: var(--primary-green);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .register-link a:hover {
        color: var(--dark-green);
        text-decoration: underline;
    }

    .loading-state {
        opacity: 0.7;
        pointer-events: none;
    }

    .loading-state .btn-login {
        background: #6c757d;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 10px;
        }
        
        .card-header,
        .card-body {
            padding-left: 20px;
            padding-right: 20px;
        }
    }
</style>

<x-auth-session-status class="mb-4" :status="session('status')" />

<div class="login-container">
    <div class="login-card">
        <div class="card-header">
            <div class="logo">
                    <img src="{{ asset('images/vitarich-logo.png') }}" alt="Logo" style="max-height: 80px;">
                </div>
            <h1 class="card-title">Welcome Back</h1>
            <p class="card-subtitle">Sign in to ASF Monitoring System</p>
        </div>
        
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required 
                        autofocus
                        placeholder="Enter your email"
                    >
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        class="form-control @error('password') is-invalid @enderror"
                        required
                        placeholder="Enter your password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </button>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">
                        <i class="fas fa-bookmark me-2"></i>Remember Me
                    </label>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Password toggle functionality
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Form submission handling
    document.getElementById('loginForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        const form = this;
        
        // Add loading state
        form.classList.add('loading-state');
        submitBtn.innerHTML = '<span class="spinner"></span>Signing In...';
        submitBtn.disabled = true;
    });

    // Real-time validation feedback
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const feedback = this.parentElement.querySelector('.text-danger');
                if (feedback) feedback.style.display = 'none';
            }
        });
    });

    // Auto-focus enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        if (emailInput && !emailInput.value) {
            setTimeout(() => emailInput.focus(), 100);
        }
    });

    // Remember me enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const rememberCheckbox = document.getElementById('remember');
        const emailInput = document.getElementById('email');
        
        // Load remembered email if available
        const rememberedEmail = localStorage.getItem('rememberedEmail');
        if (rememberedEmail) {
            emailInput.value = rememberedEmail;
            rememberCheckbox.checked = true;
        }
        
        // Save email when remember is checked
        rememberCheckbox.addEventListener('change', function() {
            if (this.checked && emailInput.value) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    });
</script>
@endsection