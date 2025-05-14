@extends('layouts.app')

@section('title', 'تسجيل ولي الأمر - منصة التعلم')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        padding-top: 0 !important;
    }

    .register-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .register-card {
        background-color: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
        margin: 2rem auto;
        max-width: 1100px;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .register-image {
        position: relative;
        height: 100%;
        min-height: 500px;
        background-color: #4361ee;
        background-size: cover;
        background-position: center;
        color: white;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
    }
    
    .register-image img.bg-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .register-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.1) 100%);
        z-index: 1;
    }

    .register-image-content {
        position: relative;
        z-index: 2;
    }

    .register-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .register-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 30px;
        line-height: 1.6;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .register-form-container {
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .register-form-header {
        text-align: right;
        margin-bottom: 25px;
    }

    .register-welcome {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .register-description {
        color: #666;
        margin-bottom: 30px;
        font-size: 1.1rem;
    }
    
    /* Form control styles */
    .form-control {
        height: 55px;
        padding: 12px 16px 12px 50px;
        border-radius: 12px;
        border: 2px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        box-sizing: border-box;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) inset;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        transform: translateY(-2px);
        background-color: #fff;
        outline: none;
    }

    .form-control:hover {
        border-color: #bbc1e4;
    }

    textarea.form-control {
        height: auto;
        resize: none;
        min-height: 110px;
        padding-top: 15px;
        padding-left: 50px;
    }

    .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group-text {
        border-radius: 0 12px 12px 0;
        background-color: #f0f2f5;
        border: 2px solid #e1e1e1;
        border-left: none;
        padding: 0 15px;
        width: 50px;
        display: flex;
        justify-content: center;
    }
    
    .input-group {
        position: relative;
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        width: 100%;
    }
    
    /* Input Group with Icon */
    .input-group .input-group-text {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        z-index: 4;
        background: transparent;
        border: none;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .input-group .form-control {
        padding-left: 50px;
        border-radius: 12px;
        width: 100%;
    }
    
    .input-group .form-control.border-start-0 {
        border-left: 2px solid #e1e1e1;
    }

    /* Form label styles */
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 0.95rem;
        display: block;
        transition: all 0.2s ease;
    }

    .form-control:focus + .form-label,
    .form-control:not(:placeholder-shown) + .form-label {
        color: #4361ee;
    }

    .register-button {
        height: 55px;
        border-radius: 12px;
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

    .register-button:hover {
        background: #3a0ca3;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
    }

    .auth-tabs {
        display: flex;
        margin-bottom: 30px;
        background-color: #f0f0f0;
        border-radius: 30px;
        padding: 6px;
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 12px;
        margin: 0;
        border-radius: 25px;
        color: #555;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .auth-tab.active {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .auth-tab:not(.active):hover {
        background-color: rgba(0, 0, 0, 0.05);
        color: #4361ee;
    }
    
    /* Floating label effect */
    .form-floating {
        position: relative;
    }
    
    .form-floating > .form-control {
        padding-top: 24px;
        padding-bottom: 8px;
        padding-left: 16px;
    }
    
    .form-floating > label {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        padding: 16px 25px;
        pointer-events: none;
        border: 1px solid transparent;
        transform-origin: 0 0;
        transition: opacity .1s ease-in-out, transform .1s ease-in-out;
        color: #6c757d;
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        opacity: .75;
        transform: scale(.85) translateY(-14px) translateX(10px);
        background-color: white;
        padding: 0 5px;
        height: auto;
        color: #4361ee;
    }
    
    /* Invalid feedback styling */
    .invalid-feedback {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: -15px;
        margin-bottom: 15px;
        display: block;
    }
    
    /* File input styling */
    input[type="file"].form-control {
        padding: 12px 15px;
        height: auto;
        line-height: 1.5;
    }
    
    .file-input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }
    
    .file-input-wrapper .file-label {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fc;
        border: 2px dashed #e1e1e1;
        border-radius: 12px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .file-input-wrapper .form-control {
        opacity: 0;
        z-index: 2;
        position: relative;
    }
    
    .file-input-wrapper .file-label:hover {
        border-color: #4361ee;
        color: #4361ee;
    }
    
    .file-input-wrapper .file-label i {
        margin-right: 10px;
    }

    /* Password toggle button */
    .password-toggle {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
        background: none;
        border: none;
        padding: 0;
    }

    @media (max-width: 991px) {
        .register-image {
            min-height: 300px;
        }
    }

    /* RTL Support for Arabic */
    [dir="rtl"] .form-control,
    [dir="rtl"] .form-select,
    [dir="rtl"] .form-floating > label {
        text-align: right;
    }

    [dir="rtl"] .input-group .input-group-text {
        right: 0;
        left: auto;
    }

    [dir="rtl"] .input-group .form-control {
        padding-right: 50px;
        padding-left: 16px;
    }

    [dir="rtl"] .password-toggle {
        left: 15px;
        right: auto;
    }

    [dir="rtl"] .form-check-input {
        margin-left: 0.5rem;
        margin-right: 0;
    }

    [dir="rtl"] .file-input-wrapper .file-label i {
        margin-left: 10px;
        margin-right: 0;
    }

    /* Special override for input groups on RTL */
    [dir="rtl"] .input-group-text {
        border-radius: 12px 0 0 12px;
    }

    [dir="rtl"] .form-floating > .form-control:focus ~ label,
    [dir="rtl"] .form-floating > .form-control:not(:placeholder-shown) ~ label {
        transform: scale(.85) translateY(-14px) translateX(-10px);
    }
    
    /* Student information card */
    .student-info-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }
    
    .student-info-card .card-header {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 15px 20px;
        font-weight: 600;
        border: none;
    }
    
    .student-info-card .card-body {
        padding: 20px;
    }
    
    .alert-info {
        background-color: #e6f3ff;
        border-color: #bae0ff;
        color: #0072e5;
        border-radius: 10px;
    }
    
    .alert-warning {
        background-color: #fff9e6;
        border-color: #ffe7a0;
        color: #b37400;
        border-radius: 10px;
    }
</style>

<div class="register-container" dir="rtl">
    <div class="container">
        <div class="register-card">
            <div class="row g-0">
                <!-- Left Image Side -->
                <div class="col-lg-5">
                    <div class="register-image">
                        <img src="{{ asset('images/login-bg.jpg') }}" class="bg-image" alt="Register Background">
                        <div class="register-image-content">
                            <h1 class="register-title">تسجيل ولي الأمر</h1>
                            <p class="register-subtitle">انضم إلينا كولي أمر وتابع تقدم أطفالك في الدراسة عبر منصتنا التعليمية.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Form Side -->
                <div class="col-lg-7">
                    <div class="register-form-container">
                        <div class="register-form-header">
                            <h2 class="register-welcome">تسجيل جديد كولي أمر</h2>
                            <p class="register-description">سجل حسابك الآن لمتابعة نشاط أبنائك الطلاب عبر المنصة التعليمية.</p>
                        </div>

                        <!-- Login/Register Tabs -->
                        <div class="auth-tabs">
                            <a href="{{ url('/login') }}" class="auth-tab">تسجيل الدخول</a>
                            <a href="{{ url('/register') }}" class="auth-tab">تسجيل طالب</a>
                            <a href="{{ url('/register/parent') }}" class="auth-tab active">تسجيل ولي أمر</a>
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

                        <form method="POST" action="{{ url('/register/parent') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder=" " required autocomplete="name" autofocus>
                                        <label for="name">الاسم الكامل لولي الأمر</label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder=" " required autocomplete="email">
                                        <label for="email">البريد الإلكتروني</label>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder=" ">
                                        <label for="phone">رقم الهاتف</label>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder=" " rows="2">{{ old('address') }}</textarea>
                                        <label for="address">العنوان</label>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="profile_image" class="form-label">الصورة الشخصية</label>
                                <div class="file-input-wrapper">
                                    <input id="profile_image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image" accept="image/*">
                                    <div class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i> اختر صورة شخصية
                                    </div>
                                </div>
                                <small class="text-muted">رفع صورة شخصية (اختياري)</small>
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder=" " required autocomplete="new-password">
                                        <label for="password">كلمة المرور</label>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-4 form-floating">
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder=" " required autocomplete="new-password">
                                        <label for="password_confirmation">تأكيد كلمة المرور</label>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- معلومات الطالب -->
                            <div class="student-info-card">
                                <div class="card-header">
                                    <i class="fas fa-user-graduate me-2"></i> معلومات الطالب
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        يرجى ملء المعلومات التالية حتى يمكننا التحقق من علاقتك بالطالب. سيتم مراجعة هذه المعلومات من قبل إدارة المنصة.
                                    </div>
                                    
                                    <div class="mb-4 form-floating">
                                        <input id="student_name" type="text" class="form-control @error('student_name') is-invalid @enderror" name="student_name" value="{{ old('student_name') }}" placeholder=" " required>
                                        <label for="student_name">اسم الطالب الكامل</label>
                                        @error('student_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">أدخل اسم الطالب كما هو مسجل في المنصة</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="birth_certificate" class="form-label">شهادة ميلاد الطالب</label>
                                        <div class="file-input-wrapper">
                                            <input id="birth_certificate" type="file" class="form-control @error('birth_certificate') is-invalid @enderror" name="birth_certificate" accept="image/*,application/pdf" required>
                                            <div class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملف شهادة الميلاد
                                            </div>
                                        </div>
                                        @error('birth_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">صورة أو PDF لشهادة ميلاد الطالب (مطلوب)</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="parent_id_card" class="form-label">بطاقة هوية ولي الأمر</label>
                                        <div class="file-input-wrapper">
                                            <input id="parent_id_card" type="file" class="form-control @error('parent_id_card') is-invalid @enderror" name="parent_id_card" accept="image/*,application/pdf" required>
                                            <div class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملف بطاقة الهوية
                                            </div>
                                        </div>
                                        @error('parent_id_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">صورة أو PDF لبطاقة هويتك (مطلوب)</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="additional_document" class="form-label">مستند إضافي (اختياري)</label>
                                        <div class="file-input-wrapper">
                                            <input id="additional_document" type="file" class="form-control @error('additional_document') is-invalid @enderror" name="additional_document" accept="image/*,application/pdf">
                                            <div class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملف إضافي
                                            </div>
                                        </div>
                                        @error('additional_document')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">أي مستند إضافي يثبت علاقتك بالطالب</small>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        ملاحظة: لن تتمكن من الوصول إلى معلومات الطالب حتى يتم التحقق من علاقتك به من قبل إدارة المنصة.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn register-button">
                                    <i class="fas fa-user-plus me-2"></i> تسجيل حساب جديد
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">لديك حساب بالفعل؟ <a href="{{ url('/login') }}" class="text-primary fw-bold">تسجيل الدخول</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = event.currentTarget.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // File input preview
    document.addEventListener('DOMContentLoaded', function() {
        // Profile image preview
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.parentElement.querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
            }
        });
        
        // Birth certificate preview
        document.getElementById('birth_certificate').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.closest('.file-input-wrapper').querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file"></i> ${fileName}`;
            }
        });
        
        // Parent ID card preview
        document.getElementById('parent_id_card').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.closest('.file-input-wrapper').querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file"></i> ${fileName}`;
            }
        });
        
        // Additional document preview
        document.getElementById('additional_document').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.closest('.file-input-wrapper').querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file"></i> ${fileName}`;
            }
        });
    });
</script>
@endsection 
 

@section('title', 'تسجيل ولي الأمر - منصة التعلم')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        padding-top: 0 !important;
    }

    .register-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .register-card {
        background-color: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
        margin: 2rem auto;
        max-width: 1100px;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .register-image {
        position: relative;
        height: 100%;
        min-height: 500px;
        background-color: #4361ee;
        background-size: cover;
        background-position: center;
        color: white;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
    }
    
    .register-image img.bg-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .register-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.1) 100%);
        z-index: 1;
    }

    .register-image-content {
        position: relative;
        z-index: 2;
    }

    .register-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .register-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 30px;
        line-height: 1.6;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .register-form-container {
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .register-form-header {
        text-align: right;
        margin-bottom: 25px;
    }

    .register-welcome {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .register-description {
        color: #666;
        margin-bottom: 30px;
        font-size: 1.1rem;
    }
    
    /* Form control styles */
    .form-control {
        height: 55px;
        padding: 12px 16px 12px 50px;
        border-radius: 12px;
        border: 2px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        box-sizing: border-box;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) inset;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        transform: translateY(-2px);
        background-color: #fff;
        outline: none;
    }

    .form-control:hover {
        border-color: #bbc1e4;
    }

    textarea.form-control {
        height: auto;
        resize: none;
        min-height: 110px;
        padding-top: 15px;
        padding-left: 50px;
    }

    .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group-text {
        border-radius: 0 12px 12px 0;
        background-color: #f0f2f5;
        border: 2px solid #e1e1e1;
        border-left: none;
        padding: 0 15px;
        width: 50px;
        display: flex;
        justify-content: center;
    }
    
    .input-group {
        position: relative;
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        width: 100%;
    }
    
    /* Input Group with Icon */
    .input-group .input-group-text {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        z-index: 4;
        background: transparent;
        border: none;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .input-group .form-control {
        padding-left: 50px;
        border-radius: 12px;
        width: 100%;
    }
    
    .input-group .form-control.border-start-0 {
        border-left: 2px solid #e1e1e1;
    }

    /* Form label styles */
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 0.95rem;
        display: block;
        transition: all 0.2s ease;
    }

    .form-control:focus + .form-label,
    .form-control:not(:placeholder-shown) + .form-label {
        color: #4361ee;
    }

    .register-button {
        height: 55px;
        border-radius: 12px;
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

    .register-button:hover {
        background: #3a0ca3;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
    }

    .auth-tabs {
        display: flex;
        margin-bottom: 30px;
        background-color: #f0f0f0;
        border-radius: 30px;
        padding: 6px;
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 12px;
        margin: 0;
        border-radius: 25px;
        color: #555;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .auth-tab.active {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .auth-tab:not(.active):hover {
        background-color: rgba(0, 0, 0, 0.05);
        color: #4361ee;
    }
    
    /* Floating label effect */
    .form-floating {
        position: relative;
    }
    
    .form-floating > .form-control {
        padding-top: 24px;
        padding-bottom: 8px;
        padding-left: 16px;
    }
    
    .form-floating > label {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        padding: 16px 25px;
        pointer-events: none;
        border: 1px solid transparent;
        transform-origin: 0 0;
        transition: opacity .1s ease-in-out, transform .1s ease-in-out;
        color: #6c757d;
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        opacity: .75;
        transform: scale(.85) translateY(-14px) translateX(10px);
        background-color: white;
        padding: 0 5px;
        height: auto;
        color: #4361ee;
    }
    
    /* Invalid feedback styling */
    .invalid-feedback {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: -15px;
        margin-bottom: 15px;
        display: block;
    }
    
    /* File input styling */
    input[type="file"].form-control {
        padding: 12px 15px;
        height: auto;
        line-height: 1.5;
    }
    
    .file-input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }
    
    .file-input-wrapper .file-label {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fc;
        border: 2px dashed #e1e1e1;
        border-radius: 12px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .file-input-wrapper .form-control {
        opacity: 0;
        z-index: 2;
        position: relative;
    }
    
    .file-input-wrapper .file-label:hover {
        border-color: #4361ee;
        color: #4361ee;
    }
    
    .file-input-wrapper .file-label i {
        margin-right: 10px;
    }

    /* Password toggle button */
    .password-toggle {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
        background: none;
        border: none;
        padding: 0;
    }

    @media (max-width: 991px) {
        .register-image {
            min-height: 300px;
        }
    }

    /* RTL Support for Arabic */
    [dir="rtl"] .form-control,
    [dir="rtl"] .form-select,
    [dir="rtl"] .form-floating > label {
        text-align: right;
    }

    [dir="rtl"] .input-group .input-group-text {
        right: 0;
        left: auto;
    }

    [dir="rtl"] .input-group .form-control {
        padding-right: 50px;
        padding-left: 16px;
    }

    [dir="rtl"] .password-toggle {
        left: 15px;
        right: auto;
    }

    [dir="rtl"] .form-check-input {
        margin-left: 0.5rem;
        margin-right: 0;
    }

    [dir="rtl"] .file-input-wrapper .file-label i {
        margin-left: 10px;
        margin-right: 0;
    }

    /* Special override for input groups on RTL */
    [dir="rtl"] .input-group-text {
        border-radius: 12px 0 0 12px;
    }

    [dir="rtl"] .form-floating > .form-control:focus ~ label,
    [dir="rtl"] .form-floating > .form-control:not(:placeholder-shown) ~ label {
        transform: scale(.85) translateY(-14px) translateX(-10px);
    }
    
    /* Student information card */
    .student-info-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }
    
    .student-info-card .card-header {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 15px 20px;
        font-weight: 600;
        border: none;
    }
    
    .student-info-card .card-body {
        padding: 20px;
    }
    
    .alert-info {
        background-color: #e6f3ff;
        border-color: #bae0ff;
        color: #0072e5;
        border-radius: 10px;
    }
    
    .alert-warning {
        background-color: #fff9e6;
        border-color: #ffe7a0;
        color: #b37400;
        border-radius: 10px;
    }
</style>

<div class="register-container" dir="rtl">
    <div class="container">
        <div class="register-card">
            <div class="row g-0">
                <!-- Left Image Side -->
                <div class="col-lg-5">
                    <div class="register-image">
                        <img src="{{ asset('images/login-bg.jpg') }}" class="bg-image" alt="Register Background">
                        <div class="register-image-content">
                            <h1 class="register-title">تسجيل ولي الأمر</h1>
                            <p class="register-subtitle">انضم إلينا كولي أمر وتابع تقدم أطفالك في الدراسة عبر منصتنا التعليمية.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Form Side -->
                <div class="col-lg-7">
                    <div class="register-form-container">
                        <div class="register-form-header">
                            <h2 class="register-welcome">تسجيل جديد كولي أمر</h2>
                            <p class="register-description">سجل حسابك الآن لمتابعة نشاط أبنائك الطلاب عبر المنصة التعليمية.</p>
                        </div>

                        <!-- Login/Register Tabs -->
                        <div class="auth-tabs">
                            <a href="{{ url('/login') }}" class="auth-tab">تسجيل الدخول</a>
                            <a href="{{ url('/register') }}" class="auth-tab">تسجيل طالب</a>
                            <a href="{{ url('/register/parent') }}" class="auth-tab active">تسجيل ولي أمر</a>
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

                        <form method="POST" action="{{ url('/register/parent') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder=" " required autocomplete="name" autofocus>
                                        <label for="name">الاسم الكامل لولي الأمر</label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder=" " required autocomplete="email">
                                        <label for="email">البريد الإلكتروني</label>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder=" ">
                                        <label for="phone">رقم الهاتف</label>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder=" " rows="2">{{ old('address') }}</textarea>
                                        <label for="address">العنوان</label>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="profile_image" class="form-label">الصورة الشخصية</label>
                                <div class="file-input-wrapper">
                                    <input id="profile_image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image" accept="image/*">
                                    <div class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i> اختر صورة شخصية
                                    </div>
                                </div>
                                <small class="text-muted">رفع صورة شخصية (اختياري)</small>
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 form-floating">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder=" " required autocomplete="new-password">
                                        <label for="password">كلمة المرور</label>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-4 form-floating">
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder=" " required autocomplete="new-password">
                                        <label for="password_confirmation">تأكيد كلمة المرور</label>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- معلومات الطالب -->
                            <div class="student-info-card">
                                <div class="card-header">
                                    <i class="fas fa-user-graduate me-2"></i> معلومات الطالب
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        يرجى ملء المعلومات التالية حتى يمكننا التحقق من علاقتك بالطالب. سيتم مراجعة هذه المعلومات من قبل إدارة المنصة.
                                    </div>
                                    
                                    <div class="mb-4 form-floating">
                                        <input id="student_name" type="text" class="form-control @error('student_name') is-invalid @enderror" name="student_name" value="{{ old('student_name') }}" placeholder=" " required>
                                        <label for="student_name">اسم الطالب الكامل</label>
                                        @error('student_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">أدخل اسم الطالب كما هو مسجل في المنصة</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="birth_certificate" class="form-label">شهادة ميلاد الطالب</label>
                                        <div class="file-input-wrapper">
                                            <input id="birth_certificate" type="file" class="form-control @error('birth_certificate') is-invalid @enderror" name="birth_certificate" accept="image/*,application/pdf" required>
                                            <div class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملف شهادة الميلاد
                                            </div>
                                        </div>
                                        @error('birth_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">صورة أو PDF لشهادة ميلاد الطالب (مطلوب)</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="parent_id_card" class="form-label">بطاقة هوية ولي الأمر</label>
                                        <div class="file-input-wrapper">
                                            <input id="parent_id_card" type="file" class="form-control @error('parent_id_card') is-invalid @enderror" name="parent_id_card" accept="image/*,application/pdf" required>
                                            <div class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملف بطاقة الهوية
                                            </div>
                                        </div>
                                        @error('parent_id_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">صورة أو PDF لبطاقة هويتك (مطلوب)</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="additional_document" class="form-label">مستند إضافي (اختياري)</label>
                                        <div class="file-input-wrapper">
                                            <input id="additional_document" type="file" class="form-control @error('additional_document') is-invalid @enderror" name="additional_document" accept="image/*,application/pdf">
                                            <div class="file-label">
                                                <i class="fas fa-upload"></i> اختر ملف إضافي
                                            </div>
                                        </div>
                                        @error('additional_document')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">أي مستند إضافي يثبت علاقتك بالطالب</small>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        ملاحظة: لن تتمكن من الوصول إلى معلومات الطالب حتى يتم التحقق من علاقتك به من قبل إدارة المنصة.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn register-button">
                                    <i class="fas fa-user-plus me-2"></i> تسجيل حساب جديد
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">لديك حساب بالفعل؟ <a href="{{ url('/login') }}" class="text-primary fw-bold">تسجيل الدخول</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = event.currentTarget.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // File input preview
    document.addEventListener('DOMContentLoaded', function() {
        // Profile image preview
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.parentElement.querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
            }
        });
        
        // Birth certificate preview
        document.getElementById('birth_certificate').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.closest('.file-input-wrapper').querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file"></i> ${fileName}`;
            }
        });
        
        // Parent ID card preview
        document.getElementById('parent_id_card').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.closest('.file-input-wrapper').querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file"></i> ${fileName}`;
            }
        });
        
        // Additional document preview
        document.getElementById('additional_document').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const fileLabel = e.target.closest('.file-input-wrapper').querySelector('.file-label');
                fileLabel.innerHTML = `<i class="fas fa-file"></i> ${fileName}`;
            }
        });
    });
</script>
@endsection 