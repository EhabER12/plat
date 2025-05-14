@extends('layouts.app')

@section('title', 'Login - Learning Platform')

@section('content')
<style>
    /* General Styles */
    body {
        background: #FAFAFA;
        padding-top: 0 !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .auth-card {
        background-color: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 51, 102, 0.1), 0 5px 15px rgba(0, 51, 102, 0.05);
        margin: 2rem auto;
        max-width: 1100px;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .auth-image {
        position: relative;
        height: 100%;
        min-height: 550px;
        background-color: #003366;
        background-size: cover;
        background-position: center;
        color: white;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
    }
    
    .auth-image img.bg-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .auth-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 51, 102, 0.8) 0%, rgba(0, 51, 102, 0.4) 50%, rgba(0, 51, 102, 0.2) 100%);
        z-index: 1;
    }

    .auth-image-content {
        position: relative;
        z-index: 2;
    }

    .auth-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        color: #FFD700;
    }

    .auth-subtitle {
        font-size: 1.3rem;
        opacity: 0.9;
        margin-bottom: 30px;
        line-height: 1.6;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .auth-forms-container {
        padding: 50px 40px;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
        background-color: #FAFAFA;
    }

    .auth-form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-welcome {
        font-size: 2rem;
        font-weight: 700;
        color: #003366;
        margin-bottom: 15px;
    }

    .auth-description {
        color: #1F1F1F;
        margin-bottom: 30px;
        font-size: 1.1rem;
    }

    /* Input Fields - Modern Style */
    .form-control {
        height: 56px;
        padding: 0 56px;
        border-radius: 12px;
        border: 2px solid rgba(0, 51, 102, 0.2);
        background-color: #FFFFFF;
        width: 100%;
        box-sizing: border-box;
        font-size: 16px;
        font-weight: 500;
        color: #1F1F1F;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .form-control:focus {
        border-color: #FFD700;
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.15);
        transform: translateY(-2px);
        outline: none;
        background-color: #fff;
    }

    .form-control:hover:not(:focus) {
        border-color: #003366;
        background-color: #fff;
    }

    .form-control::placeholder {
        color: #6C6F7F;
        opacity: 0.8;
    }

    /* Input Group & Icons - Professional Layout */
    .input-group {
        position: relative;
        margin-bottom: 24px;
    }

    .input-group-text {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #003366;
        transition: color 0.3s ease;
    }

    .input-group:focus-within .input-group-text {
        color: #FFD700;
    }

    /* Password Toggle Button - Enhanced Style */
    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        cursor: pointer;
        color: #003366;
        padding: 8px;
        transition: all 0.2s ease;
    }

    .password-toggle:hover {
        color: #FFD700;
    }

    .password-toggle:focus {
        outline: none;
        color: #FFD700;
    }
    
    /* Tabs - Enhanced Modern UI */
    .auth-tabs {
        position: relative;
        display: flex;
        margin-bottom: 35px;
        background-color: rgba(250, 250, 250, 0.8);
        border-radius: 16px;
        padding: 6px;
        box-shadow: 0 2px 10px rgba(0, 51, 102, 0.1);
        backdrop-filter: blur(5px);
    }

    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 14px 16px;
        color: #003366;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        z-index: 2;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }

    .auth-tab:hover:not(.active) {
        color: #003366;
        background-color: rgba(255, 215, 0, 0.1);
    }

    .auth-tab.active {
        color: #003366;
    }

    .auth-slider {
        position: absolute;
        height: calc(100% - 12px);
        width: 50%;
        background: #FFD700;
        top: 6px;
        left: 6px;
        border-radius: 12px;
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 1;
        box-shadow: 0 6px 15px rgba(255, 215, 0, 0.3);
    }

    .auth-slider.register {
        transform: translateX(100%);
    }

    .forms-slider {
        display: flex;
        width: 200%;
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-sizing: border-box;
    }

    .forms-slider.register {
        transform: translateX(-50%);
    }

    .form-container {
        width: 50%;
        box-sizing: border-box;
        opacity: 1;
        transition: opacity 0.3s ease-in-out;
    }
    
    .forms-slider.register .form-container:first-child,
    .forms-slider:not(.register) .form-container:last-child {
        opacity: 0.6;
    }

    .form-inner-container {
        padding: 0 15px;
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }

    .form-container:first-child .form-inner-container {
        padding-right: 3rem;
        padding-left: 0;
    }

    .form-container:last-child .form-inner-container {
        padding-left: 3rem;
        padding-right: 0;
    }

    /* Remember me and forgot password - Enhanced styling */
    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        margin-top: 5px;
    }

    .form-check {
        display: flex;
        align-items: center;
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        border-radius: 4px;
        border: 2px solid #003366;
        background-color: #fff;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
        appearance: none;
        -webkit-appearance: none;
    }

    .form-check-input:checked {
        background-color: #003366;
        border-color: #003366;
    }

    .form-check-input:checked::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #FFD700;
        font-size: 12px;
    }

    .form-check-input:hover:not(:checked) {
        border-color: #FFD700;
    }

    .form-check-label {
        color: #1F1F1F;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        user-select: none;
    }

    .forgot-password {
        color: #003366;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
        position: relative;
    }

    .forgot-password:hover {
        color: #FFD700;
    }

    .forgot-password::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -2px;
        left: 0;
        background: #FFD700;
        transition: width 0.3s ease;
    }

    .forgot-password:hover::after {
        width: 100%;
    }

    /* Buttons - Professional & Modern Styling */
    .auth-button {
        height: 56px;
        border-radius: 12px;
        background: #003366;
        border: none;
        font-weight: 600;
        font-size: 16px;
        letter-spacing: 0.5px;
        color: #FFD700;
        padding: 0 32px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0, 51, 102, 0.3);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }

    .auth-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 14px rgba(0, 51, 102, 0.4);
        background: linear-gradient(45deg, #003366, #002244);
    }

    .auth-button:active {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 51, 102, 0.3);
    }

    .auth-button::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 215, 0, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%, -50%);
        transform-origin: 50% 50%;
    }

    .auth-button:focus:not(:active)::after {
        animation: ripple 1s ease-out;
    }

    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        20% {
            transform: scale(25, 25);
            opacity: 0.3;
        }
        100% {
            opacity: 0;
            transform: scale(40, 40);
        }
    }

    /* Links styling */
    .auth-links {
        margin-top: 20px;
        text-align: center;
    }
    
    .auth-link {
        color: #003366;
        font-weight: 600;
        text-decoration: none;
        position: relative;
        transition: all 0.2s ease;
    }
    
    .auth-link:hover {
        color: #FFD700;
    }
    
    .auth-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -2px;
        left: 0;
        background: #FFD700;
        transition: width 0.3s ease;
    }
    
    .auth-link:hover::after {
        width: 100%;
    }

    /* Updated form label styles */
    .form-label {
        font-weight: 600;
        color: #003366;
        margin-bottom: 10px;
        font-size: 0.95rem;
        display: block;
        transition: all 0.2s ease;
    }

    .form-control:focus + .form-label,
    .form-control:not(:placeholder-shown) + .form-label {
        color: #003366;
    }
    
    /* RTL Adjustments for Input Groups */
    [dir="rtl"] .input-group-text {
        left: auto;
        right: 20px;
    }
    
    [dir="rtl"] .password-toggle {
        right: auto;
        left: 16px;
    }
    
    [dir="rtl"] .form-control {
        padding: 0 56px 0 56px;
        text-align: right;
    }

    /* Error messages */
    .invalid-feedback, .alert-danger {
        color: #E74C3C !important;
        font-size: 14px;
        margin-top: -15px;
        margin-bottom: 15px;
    }

    .alert-danger {
        background-color: rgba(231, 76, 60, 0.1);
        border-color: rgba(231, 76, 60, 0.2);
        border-radius: 12px;
        padding: 16px;
    }

    /* Success messages */
    .alert-success {
        color: #2ECC71 !important;
        background-color: rgba(46, 204, 113, 0.1);
        border-color: rgba(46, 204, 113, 0.2);
        border-radius: 12px;
        padding: 16px;
    }

    /* Role descriptions */
    .role-descriptions .role-desc {
        border: 1px solid #FAFAFA;
        border-radius: 12px;
        margin-bottom: 15px;
        background-color: #FFFFFF;
    }

    .student-desc .border-primary {
        border-color: #003366 !important;
    }

    .parent-desc .border-success {
        border-color: #2ECC71 !important;
    }

    .instructor-desc .border-warning {
        border-color: #FFD700 !important;
    }

    /* File upload styling */
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
    }

    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-label {
        display: block;
        padding: 12px 16px;
        background-color: #FAFAFA;
        border: 2px dashed #003366;
        border-radius: 12px;
        text-align: center;
        color: #1F1F1F;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-label:hover {
        background-color: rgba(0, 51, 102, 0.05);
        border-color: #FFD700;
    }

    .file-label i {
        color: #003366;
        margin-right: 8px;
    }

    /* Responsive styles */
    @media (max-width: 991px) {
        .auth-image {
            min-height: 300px;
        }

        .auth-forms-container {
            padding: 40px 25px;
        }

        .form-inner-container {
            padding: 0 5px;
        }

        .form-container:first-child .form-inner-container,
        .form-container:last-child .form-inner-container {
            padding-left: 5px;
            padding-right: 5px;
        }

        .remember-forgot {
            flex-direction: column;
            align-items: flex-start;
        }

        .forgot-password {
            margin-top: 15px;
        }

        .auth-welcome {
            font-size: 1.8rem;
        }

        .auth-description {
            font-size: 1rem;
        }
    }

    @media (max-width: 767px) {
        .auth-card {
            margin: 1rem;
        }

        .auth-title {
            font-size: 2.2rem;
        }

        .auth-subtitle {
            font-size: 1.1rem;
        }
    }

    /* RTL Support for Arabic */
    [dir="rtl"] .form-control,
    [dir="rtl"] .form-select,
    [dir="rtl"] .form-floating > label {
        text-align: right;
    }

    [dir="rtl"] .form-check-input {
        margin-left: 0.5rem;
        margin-right: 0;
    }

    [dir="rtl"] .file-input-wrapper .file-label i {
        margin-left: 10px;
        margin-right: 0;
    }
</style>

<div class="auth-container">
    <div class="container">
        <div class="auth-card">
            <div class="row g-0">
                <!-- Left Image Side -->
                <div class="col-lg-6">
                    <div class="auth-image">
                        <img src="{{ asset('images/login-bg.jpg') }}" class="bg-image" alt="Login Background">
                        <div class="auth-image-content">
                            <h1 class="auth-title">منصة tamayozedu</h1>
                            <p class="auth-subtitle">{{ app()->getLocale() == 'ar' ? 'منصة تعليمية متكاملة تجمع أفضل المدربين والطلاب في مكان واحد' : 'A comprehensive learning platform that brings together the best instructors and students in one place' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Form Side -->
                <div class="col-lg-6">
                    <div class="auth-forms-container">
                        <div class="auth-form-header">
                            <h2 class="auth-welcome">{{ app()->getLocale() == 'ar' ? 'مرحباً بك في منصة TOTO!' : 'Welcome to TOTO Platform!' }}</h2>
                            <p class="auth-description">{{ app()->getLocale() == 'ar' ? 'بوابتك للتعليم عبر الإنترنت وتطوير المهارات بجودة عالية' : 'Your gateway to quality online education and skill development' }}</p>
                        </div>

                        <!-- Login/Register Tabs with Slider -->
                        <div class="auth-tabs">
                            <div class="auth-slider"></div>
                            <div class="auth-tab active" id="login-tab">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}</div>
                            <div class="auth-tab" id="register-tab">{{ app()->getLocale() == 'ar' ? 'إنشاء حساب' : 'Register' }}</div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Forms Container with Slider -->
                        <div class="forms-slider">
                            <!-- Login Form -->
                            <div class="form-container">
                                <div class="form-inner-container">
                                    <form method="POST" action="{{ url('/login') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="form-label" style="font-weight: 600; color: #2d3748; margin-bottom: 10px; font-size: 15px;">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                                            <div class="input-group" style="position: relative; margin-bottom: 24px;">
                                                <span class="input-group-text" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); z-index: 10; background: transparent; color: #718096;"><i class="fas fa-envelope"></i></span>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email' }}" required autocomplete="email" autofocus style="height: 56px; padding: 0 56px; border-radius: 12px; border: 2px solid rgba(226, 232, 240, 0.8); background-color: rgba(255, 255, 255, 0.9); width: 100%; font-size: 16px; font-weight: 500; color: #2d3748; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);">
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block" style="font-size: 14px; color: #e53e3e; margin-top: -15px; margin-bottom: 15px;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label" style="font-weight: 600; color: #2d3748; margin-bottom: 10px; font-size: 15px;">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                                            <div class="input-group" style="position: relative; margin-bottom: 24px;">
                                                <span class="input-group-text" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); z-index: 10; background: transparent; color: #718096;"><i class="fas fa-lock"></i></span>
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل كلمة المرور' : 'Enter your password' }}" required autocomplete="current-password" style="height: 56px; padding: 0 56px; border-radius: 12px; border: 2px solid rgba(226, 232, 240, 0.8); background-color: rgba(255, 255, 255, 0.9); width: 100%; font-size: 16px; font-weight: 500; color: #2d3748; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);">
                                                <button type="button" class="password-toggle" onclick="togglePassword('password')" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: #718096; padding: 8px;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block" style="font-size: 14px; color: #e53e3e; margin-top: -15px; margin-bottom: 15px;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="remember-forgot" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                                            <div class="form-check" style="display: flex; align-items: center;">
                                                <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="width: 20px; height: 20px; margin-right: 10px; border-radius: 4px; border: 2px solid #d1d3e0; background-color: #fff; cursor: pointer;">
                                                <label class="form-check-label" for="remember" style="color: #4a5568; font-weight: 500; font-size: 14px; cursor: pointer;">{{ app()->getLocale() == 'ar' ? 'تذكرني' : 'Remember me' }}</label>
                                            </div>
                                            <a href="{{ route('password.request') }}" class="forgot-password" style="color: #4361ee; font-weight: 500; font-size: 14px; text-decoration: none; transition: all 0.2s ease; position: relative;">{{ app()->getLocale() == 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot Password?' }}</a>
                                        </div>

                                        <button type="submit" class="btn auth-button w-100" style="height: 56px; border-radius: 12px; background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); border: none; font-weight: 600; font-size: 16px; letter-spacing: 0.5px; color: white; box-shadow: 0 4px 14px rgba(67, 97, 238, 0.3); transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}</button>

                                        <div class="mt-4 text-center">
                                            <p>{{ app()->getLocale() == 'ar' ? 'ليس لديك حساب؟' : 'Don\'t have an account?' }} 
                                               <a href="#" class="switch-to-register" style="color: #4361ee; font-weight: 600; text-decoration: none; position: relative;">{{ app()->getLocale() == 'ar' ? 'سجل هنا' : 'Register here' }}</a> | 
                                               <a href="{{ url('/register/parent') }}" style="color: #4361ee; font-weight: 600; text-decoration: none; position: relative;">تسجيل ولي أمر</a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Register Form -->
                            <div class="form-container">
                                <div class="form-inner-container">
                                    <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="register-name" class="form-label">{{ app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Full Name' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                                                <input id="register-name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name' }}" required autocomplete="name">
                                            </div>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-email" class="form-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                                <input id="register-email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email address' }}" required autocomplete="email">
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-phone" class="form-label">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone-alt text-muted"></i></span>
                                                <input id="register-phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل رقم هاتفك' : 'Enter your phone number' }}">
                                            </div>
                                            @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-address" class="form-label">{{ app()->getLocale() == 'ar' ? 'العنوان' : 'Address' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                <textarea id="register-address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل عنوانك' : 'Enter your address' }}" rows="2">{{ old('address') }}</textarea>
                                            </div>
                                            @error('address')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-profile-image" class="form-label">{{ app()->getLocale() == 'ar' ? 'الصورة الشخصية' : 'Profile Picture' }}</label>
                                            <div class="file-input-wrapper">
                                                <input id="register-profile-image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image" accept="image/*">
                                                <div class="file-label">
                                                    <i class="fas fa-cloud-upload-alt"></i> {{ app()->getLocale() == 'ar' ? 'اختر صورة شخصية' : 'Choose a profile picture' }}
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'قم بتحميل صورة شخصية (اختياري)' : 'Upload a profile picture (optional)' }}</small>
                                            @error('profile_image')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-password" class="form-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                                <input id="register-password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ app()->getLocale() == 'ar' ? 'أنشئ كلمة مرور قوية' : 'Create a strong password' }}" required autocomplete="new-password">
                                                <button type="button" class="password-toggle" onclick="togglePassword('register-password')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-password-confirm" class="form-label">{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                                <input id="register-password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ app()->getLocale() == 'ar' ? 'أكد كلمة المرور' : 'Confirm your password' }}" required autocomplete="new-password">
                                                <button type="button" class="password-toggle" onclick="togglePassword('register-password-confirm')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="role" class="form-label">{{ app()->getLocale() == 'ar' ? 'التسجيل كـ' : 'Register as' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tag text-muted"></i></span>
                                                <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                                                    <option value="">{{ app()->getLocale() == 'ar' ? 'اختر دورك' : 'Select a role' }}</option>
                                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'طالب' : 'Student' }}</option>
                                                    <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ولي أمر' : 'Parent' }}</option>
                                                    <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مدرب' : 'Instructor' }}</option>
                                                </select>
                                            </div>
                                            @error('role')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> {{ app()->getLocale() == 'ar' ? 'هذا يحدد مستوى وصولك على المنصة' : 'This determines your access level on the platform' }}
                                            </small>
                                        </div>

                                        <!-- Role descriptions -->
                                        <div class="mb-4 role-descriptions" style="display: none;">
                                            <div class="role-desc student-desc" style="display: none;">
                                                <div class="p-3 bg-light rounded border-start border-primary border-3">
                                                    <strong><i class="fas fa-user-graduate text-primary"></i> {{ app()->getLocale() == 'ar' ? 'طالب:' : 'Student:' }}</strong>
                                                    {{ app()->getLocale() == 'ar' ? 'الوصول إلى الدورات، وتتبع تقدمك، والتفاعل مع المدربين، والحصول على الشهادات.' : 'Access courses, track your progress, interact with instructors, and earn certificates.' }}
                                                </div>
                                            </div>
                                            <div class="role-desc parent-desc" style="display: none;">
                                                <div class="p-3 bg-light rounded border-start border-success border-3">
                                                    <strong><i class="fas fa-user-friends text-success"></i> {{ app()->getLocale() == 'ar' ? 'ولي أمر:' : 'Parent:' }}</strong>
                                                    {{ app()->getLocale() == 'ar' ? 'مراقبة تقدم طفلك، وإدارة المدفوعات، والتواصل مع المدربين.' : 'Monitor your child\'s progress, manage payments, and communicate with instructors.' }}
                                                </div>
                                            </div>
                                            <div class="role-desc instructor-desc" style="display: none;">
                                                <div class="p-3 bg-light rounded border-start border-warning border-3">
                                                    <strong><i class="fas fa-chalkboard-teacher text-warning"></i> {{ app()->getLocale() == 'ar' ? 'مدرب:' : 'Instructor:' }}</strong>
                                                    {{ app()->getLocale() == 'ar' ? 'إنشاء وإدارة الدورات، والتفاعل مع الطلاب، وتتبع التحليلات، واستلام المدفوعات.' : 'Create and manage courses, interact with students, track analytics, and receive payments.' }}
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn auth-button w-100">{{ app()->getLocale() == 'ar' ? 'إنشاء حساب' : 'Register' }}</button>

                                        <div class="mt-4 text-center">
                                            <p>{{ app()->getLocale() == 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }} <a href="#" class="text-decoration-none switch-to-login" style="color: #4361ee;">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول هنا' : 'Login here' }}</a></p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginTab = document.getElementById('login-tab');
        const registerTab = document.getElementById('register-tab');
        const slider = document.querySelector('.auth-slider');
        const formsSlider = document.querySelector('.forms-slider');
        const switchToRegister = document.querySelector('.switch-to-register');
        const switchToLogin = document.querySelector('.switch-to-login');
        
        // File input preview
        const fileInput = document.getElementById('register-profile-image');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                if (fileName) {
                    const fileLabel = e.target.closest('.file-input-wrapper').querySelector('.file-label');
                    fileLabel.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
                }
            });
        }

        // Enhanced password toggle with animation
        window.togglePassword = function(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const icon = event.currentTarget.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                icon.style.color = '#4361ee';
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                icon.style.color = '#718096';
            }
            
            // Add ripple effect
            const button = event.currentTarget;
            const ripple = document.createElement('span');
            ripple.classList.add('password-toggle-ripple');
            ripple.style.width = '100%';
            ripple.style.height = '100%';
            ripple.style.position = 'absolute';
            ripple.style.left = '0';
            ripple.style.top = '0';
            ripple.style.backgroundColor = 'rgba(67, 97, 238, 0.1)';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple-effect 0.5s linear';
            
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 500);
        };

        // Improved switch to register with enhanced animation
        function showRegister() {
            loginTab.classList.remove('active');
            registerTab.classList.add('active');
            
            // Add smooth movement
            slider.style.transform = 'translateX(100%)';
            formsSlider.style.transform = 'translateX(-50%)';
            
            // Fade effect for form containers
            const forms = document.querySelectorAll('.form-container');
            forms[0].style.opacity = '0.6';
            forms[1].style.opacity = '1';
        }

        // Improved switch to login with enhanced animation
        function showLogin() {
            registerTab.classList.remove('active');
            loginTab.classList.add('active');
            
            // Add smooth movement
            slider.style.transform = 'translateX(0)';
            formsSlider.style.transform = 'translateX(0)';
            
            // Fade effect for form containers
            const forms = document.querySelectorAll('.form-container');
            forms[0].style.opacity = '1';
            forms[1].style.opacity = '0.6';
        }

        // Add event listeners with enhanced effects
        registerTab.addEventListener('click', function(e) {
            e.preventDefault();
            showRegister();
        });

        loginTab.addEventListener('click', function(e) {
            e.preventDefault();
            showLogin();
        });

        if (switchToRegister) {
            switchToRegister.addEventListener('click', function(e) {
                e.preventDefault();
                showRegister();
            });
        }

        if (switchToLogin) {
            switchToLogin.addEventListener('click', function(e) {
                e.preventDefault();
                showLogin();
            });
        }

        // Check if we should show register form based on URL or view parameter
        if (window.location.href.includes('show=register') || {{ isset($showRegister) && $showRegister ? 'true' : 'false' }}) {
            showRegister();
        }

        // Enhanced input focus effects with subtle animation
        const formControls = document.querySelectorAll('.form-control');
        formControls.forEach(input => {
            // Add focus effect to input 
            input.addEventListener('focus', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px rgba(67, 97, 238, 0.15)';
                this.style.borderColor = '#4361ee';
                
                // Also highlight the icon
                const inputGroup = this.closest('.input-group');
                if (inputGroup) {
                    const icon = inputGroup.querySelector('.input-group-text i');
                    if (icon) {
                        icon.style.color = '#4361ee';
                        icon.style.transform = 'translateY(-2px)';
                        icon.style.transition = 'all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1)';
                    }
                }
            });

            // Remove focus effect when blurred
            input.addEventListener('blur', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
                if (!this.value) {
                    this.style.borderColor = 'rgba(226, 232, 240, 0.8)';
                }
                
                // Reset icon style
                const inputGroup = this.closest('.input-group');
                if (inputGroup) {
                    const icon = inputGroup.querySelector('.input-group-text i');
                    if (icon) {
                        icon.style.color = '#718096';
                        icon.style.transform = '';
                    }
                }
            });
        });
        
        // Add ripple effect to buttons
        const buttons = document.querySelectorAll('.auth-button');
        buttons.forEach(button => {
            button.addEventListener('mousedown', function(e) {
                const rect = button.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.width = '5px';
                ripple.style.height = '5px';
                ripple.style.borderRadius = '50%';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 1s ease-out';
                
                button.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 1000);
            });
        });
    });
    
    // Add the ripple animation if not already defined in CSS
    if (!document.querySelector('style#ripple-animation')) {
        const style = document.createElement('style');
        style.id = 'ripple-animation';
        style.textContent = `
            @keyframes ripple {
                0% {
                    transform: scale(0);
                    opacity: 0.5;
                }
                20% {
                    transform: scale(25);
                    opacity: 0.3;
                }
                100% {
                    opacity: 0;
                    transform: scale(50);
                }
            }
            
            @keyframes ripple-effect {
                0% {
                    transform: scale(0);
                    opacity: 1;
                }
                100% {
                    transform: scale(10);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
</script>
@endsection