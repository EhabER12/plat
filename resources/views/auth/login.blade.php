@extends('layouts.app')

@section('title', 'Login - Learning Platform')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        padding-top: 0 !important;
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
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
        margin: 2rem auto;
        max-width: 1100px;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .auth-image {
        position: relative;
        height: 100%;
        min-height: 550px;
        background-color: #4361ee;
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
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.2) 100%);
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
    }

    .auth-form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-welcome {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .auth-description {
        color: #666;
        margin-bottom: 30px;
        font-size: 1.1rem;
    }

    .form-control {
        height: 55px;
        padding: 12px 25px;
        border-radius: 30px;
        border: 1px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        box-sizing: border-box;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        transform: translateY(-2px);
    }

    textarea.form-control {
        height: auto;
        resize: none;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .auth-button {
        height: 55px;
        border-radius: 30px;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border: none;
        font-weight: 600;
        letter-spacing: 1px;
        margin-top: 15px;
        transition: all 0.3s ease;
        color: white;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .auth-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
    }

    .auth-tabs {
        position: relative;
        display: flex;
        margin-bottom: 35px;
        background-color: #f0f0f0;
        border-radius: 30px;
        padding: 6px;
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 12px;
        color: #555;
        font-weight: 600;
        cursor: pointer;
        z-index: 2;
        transition: all 0.3s ease;
        border-radius: 24px;
    }

    .auth-tab.active {
        color: #fff;
    }

    .auth-slider {
        position: absolute;
        height: calc(100% - 12px);
        width: 50%;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        top: 6px;
        left: 6px;
        border-radius: 24px;
        transition: transform 0.3s ease;
        z-index: 1;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .auth-slider.register {
        transform: translateX(100%);
    }

    .forms-slider {
        display: flex;
        width: 200%;
        transition: transform 0.3s ease;
        box-sizing: border-box;
    }

    .forms-slider.register {
        transform: translateX(-50%);
    }

    .form-container {
        width: 50%;
        box-sizing: border-box;
    }

    .form-inner-container {
        padding: 0 15px;
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }

    .form-container:first-child .form-inner-container {
        padding-right: 20px;
        padding-left: 0;
    }

    .form-container:last-child .form-inner-container {
        padding-left: 20px;
        padding-right: 0;
    }

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0.2rem;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .form-check-label {
        font-size: 0.95rem;
        color: #555;
        cursor: pointer;
    }

    .forgot-password {
        color: #4361ee;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .forgot-password:hover {
        color: #3a0ca3;
        text-decoration: underline;
    }

    .role-descriptions {
        margin-bottom: 25px;
        transition: all 0.3s ease;
        opacity: 1;
    }

    .role-desc {
        margin-top: 10px;
        transition: all 0.3s ease;
        opacity: 1;
    }

    .role-desc div {
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        font-size: 0.95rem;
        line-height: 1.5;
        transform: translateY(0);
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .role-desc div:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .input-group {
        transition: all 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        border-radius: 30px;
    }

    .input-group-text {
        border-top-left-radius: 30px;
        border-bottom-left-radius: 30px;
        border-right: none;
    }

    .switch-to-register, .switch-to-login {
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .switch-to-register:hover, .switch-to-login:hover {
        text-decoration: underline !important;
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
                            <h1 class="auth-title">منصة تعليمية</h1>
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
                                            <label for="email" class="form-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                                <input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email' }}" required autocomplete="email" autofocus>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                                <input id="password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" name="password" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل كلمة المرور' : 'Enter your password' }}" required autocomplete="current-password">
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="remember-forgot">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="remember">{{ app()->getLocale() == 'ar' ? 'تذكرني' : 'Remember me' }}</label>
                                            </div>
                                            <a href="{{ route('password.request') }}" class="forgot-password">{{ app()->getLocale() == 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot Password?' }}</a>
                                        </div>

                                        <button type="submit" class="btn auth-button w-100">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}</button>

                                        <div class="mt-4 text-center">
                                            <p>{{ app()->getLocale() == 'ar' ? 'ليس لديك حساب؟' : 'Don\'t have an account?' }} <a href="#" class="text-decoration-none switch-to-register" style="color: #4361ee;">{{ app()->getLocale() == 'ar' ? 'سجل هنا' : 'Register here' }}</a></p>
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
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-user text-muted"></i></span>
                                                <input id="register-name" type="text" class="form-control border-start-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name' }}" required autocomplete="name">
                                            </div>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-email" class="form-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                                <input id="register-email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email address' }}" required autocomplete="email">
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-phone" class="form-label">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                                <input id="register-phone" type="text" class="form-control border-start-0 @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل رقم هاتفك' : 'Enter your phone number' }}">
                                            </div>
                                            @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-address" class="form-label">{{ app()->getLocale() == 'ar' ? 'العنوان' : 'Address' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                <textarea id="register-address" class="form-control border-start-0 @error('address') is-invalid @enderror" name="address" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل عنوانك' : 'Enter your address' }}" rows="2">{{ old('address') }}</textarea>
                                            </div>
                                            @error('address')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-profile-image" class="form-label">{{ app()->getLocale() == 'ar' ? 'الصورة الشخصية' : 'Profile Picture' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-image text-muted"></i></span>
                                                <input id="register-profile-image" type="file" class="form-control border-start-0 @error('profile_image') is-invalid @enderror" name="profile_image" accept="image/*">
                                            </div>
                                            <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'قم بتحميل صورة شخصية (اختياري)' : 'Upload a profile picture (optional)' }}</small>
                                            @error('profile_image')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-password" class="form-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                                <input id="register-password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" name="password" placeholder="{{ app()->getLocale() == 'ar' ? 'أنشئ كلمة مرور قوية' : 'Create a strong password' }}" required autocomplete="new-password">
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-password-confirm" class="form-label">{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                                <input id="register-password-confirm" type="password" class="form-control border-start-0" name="password_confirmation" placeholder="{{ app()->getLocale() == 'ar' ? 'أكد كلمة المرور' : 'Confirm your password' }}" required autocomplete="new-password">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="role" class="form-label">{{ app()->getLocale() == 'ar' ? 'التسجيل كـ' : 'Register as' }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-user-tag text-muted"></i></span>
                                                <select id="role" name="role" class="form-control border-start-0 @error('role') is-invalid @enderror" required>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginTab = document.getElementById('login-tab');
        const registerTab = document.getElementById('register-tab');
        const slider = document.querySelector('.auth-slider');
        const formsSlider = document.querySelector('.forms-slider');
        const switchToRegister = document.querySelector('.switch-to-register');
        const switchToLogin = document.querySelector('.switch-to-login');

        // Switch to register with animation
        function showRegister() {
            // Add a small delay for better animation
            setTimeout(() => {
                loginTab.classList.remove('active');
                registerTab.classList.add('active');
                slider.classList.add('register');
                formsSlider.classList.add('register');
            }, 50);
        }

        // Switch to login with animation
        function showLogin() {
            // Add a small delay for better animation
            setTimeout(() => {
                registerTab.classList.remove('active');
                loginTab.classList.add('active');
                slider.classList.remove('register');
                formsSlider.classList.remove('register');
            }, 50);
        }

        // Add event listeners with smooth transitions
        registerTab.addEventListener('click', function(e) {
            e.preventDefault();
            showRegister();
        });

        loginTab.addEventListener('click', function(e) {
            e.preventDefault();
            showLogin();
        });

        switchToRegister.addEventListener('click', function(e) {
            e.preventDefault();
            showRegister();
        });

        switchToLogin.addEventListener('click', function(e) {
            e.preventDefault();
            showLogin();
        });

        // Check if we should show register form based on URL or view parameter
        if (window.location.href.includes('show=register') || {{ isset($showRegister) && $showRegister ? 'true' : 'false' }}) {
            showRegister();
        }

        // Handle role selection and showing descriptions with animations
        const roleSelect = document.getElementById('role');
        const roleDescriptions = document.querySelector('.role-descriptions');
        const roleDescs = document.querySelectorAll('.role-desc');

        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                // If a role is selected, show the description container with animation
                if (this.value) {
                    // Hide all descriptions first with fade out
                    roleDescs.forEach(desc => {
                        if (desc.style.display !== 'none') {
                            desc.style.opacity = '0';
                            setTimeout(() => {
                                desc.style.display = 'none';
                            }, 200);
                        }
                    });

                    // Show the container if it's hidden
                    if (roleDescriptions.style.display === 'none') {
                        roleDescriptions.style.display = 'block';
                        roleDescriptions.style.opacity = '0';
                        setTimeout(() => {
                            roleDescriptions.style.opacity = '1';
                        }, 10);
                    }

                    // Show the specific role description with fade in
                    const selectedDesc = document.querySelector(`.${this.value}-desc`);
                    setTimeout(() => {
                        selectedDesc.style.display = 'block';
                        selectedDesc.style.opacity = '0';
                        setTimeout(() => {
                            selectedDesc.style.opacity = '1';
                        }, 10);
                    }, 200);
                } else {
                    // Hide the container with fade out
                    roleDescriptions.style.opacity = '0';
                    setTimeout(() => {
                        roleDescriptions.style.display = 'none';
                    }, 200);
                }
            });

            // Initial check if a role is already selected (e.g. on form validation error)
            if (roleSelect.value) {
                roleDescriptions.style.display = 'block';
                document.querySelector(`.${roleSelect.value}-desc`).style.display = 'block';
            }
        }

        // Add input focus effects
        const formControls = document.querySelectorAll('.form-control');
        formControls.forEach(input => {
            // Add focus effect to input group
            input.addEventListener('focus', function() {
                const inputGroup = this.closest('.input-group');
                if (inputGroup) {
                    inputGroup.style.transform = 'translateY(-2px)';
                    inputGroup.style.boxShadow = '0 5px 15px rgba(67, 97, 238, 0.1)';
                }
            });

            // Remove focus effect when blurred
            input.addEventListener('blur', function() {
                const inputGroup = this.closest('.input-group');
                if (inputGroup) {
                    inputGroup.style.transform = '';
                    inputGroup.style.boxShadow = '';
                }
            });
        });
    });
</script>
@endsection