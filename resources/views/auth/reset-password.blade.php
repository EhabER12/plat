@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password')

@section('styles')
<!-- Custom Auth CSS -->
<link href="{{ asset('css/auth.css') }}" rel="stylesheet">
<style>
    .auth-container {
        max-width: 500px;
        margin: 60px auto;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }
    .auth-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .auth-header img {
        max-width: 80px;
        margin-bottom: 20px;
    }
    .form-control {
        height: 50px;
        padding-left: 45px;
        font-size: 16px;
    }
    .input-group-text {
        width: 45px;
        justify-content: center;
    }
    .auth-button {
        height: 50px;
        font-size: 16px;
        font-weight: 600;
        background: linear-gradient(135deg, #0056b3 0%, #007bff 100%);
        color: #fff;
        border: none;
        margin-top: 10px;
    }
    .auth-button:hover {
        background: linear-gradient(135deg, #004494 0%, #0069d9 100%);
        color: #fff;
    }
    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #6c757d;
        text-decoration: none;
    }
    .back-link:hover {
        color: #0056b3;
    }
    .password-requirements {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-header">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" onerror="this.src='https://via.placeholder.com/80x80?text=LOGO'">
            <h1>{{ app()->getLocale() == 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}</h1>
            <p class="text-muted">{{ app()->getLocale() == 'ar' ? 'الرجاء إدخال كلمة المرور الجديدة' : 'Please enter your new password' }}</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input id="password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                </div>
                <div class="password-requirements">
                    {{ app()->getLocale() == 'ar' ? 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل' : 'Password must be at least 8 characters long' }}
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input id="password_confirmation" type="password" class="form-control border-start-0" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn auth-button w-100">
                {{ app()->getLocale() == 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="fas fa-arrow-left me-2"></i> {{ app()->getLocale() == 'ar' ? 'العودة إلى تسجيل الدخول' : 'Back to Login' }}
        </a>
    </div>
</div>
@endsection 