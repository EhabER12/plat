@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'استعادة كلمة المرور' : 'Forgot Password')

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
</style>
@endsection

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-header">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" onerror="this.src='https://via.placeholder.com/80x80?text=LOGO'">
            <h1>{{ app()->getLocale() == 'ar' ? 'استعادة كلمة المرور' : 'Forgot Password' }}</h1>
            <p class="text-muted">{{ app()->getLocale() == 'ar' ? 'أدخل عنوان بريدك الإلكتروني وسنرسل لك رابطًا لإعادة تعيين كلمة المرور' : 'Enter your email address and we\'ll send you a link to reset your password' }}</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (session('direct_reset_link'))
            <div class="alert alert-info">
                <p class="mb-2"><strong>{{ app()->getLocale() == 'ar' ? 'تم إنشاء رابط إعادة تعيين كلمة المرور:' : 'Reset link generated:' }}</strong></p>
                <div class="mb-2">
                    <a href="{{ session('direct_reset_link') }}" class="btn btn-primary btn-sm">{{ app()->getLocale() == 'ar' ? 'انقر هنا لإعادة تعيين كلمة المرور' : 'Click here to reset password' }}</a>
                </div>
                <div class="d-flex">
                    <input type="text" id="reset-link" class="form-control form-control-sm" value="{{ session('direct_reset_link') }}" readonly>
                    <button class="btn btn-sm btn-secondary ms-2" onclick="copyResetLink()">{{ app()->getLocale() == 'ar' ? 'نسخ' : 'Copy' }}</button>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email' }}">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <button type="submit" class="btn auth-button w-100">
                        {{ app()->getLocale() == 'ar' ? 'إرسال رابط إعادة التعيين' : 'Send Reset Link' }}
                    </button>
                </div>
                <div class="col-md-6 mt-2 mt-md-0">
                    <button type="submit" class="btn btn-secondary w-100" formaction="{{ route('password.direct-reset') }}">
                        {{ app()->getLocale() == 'ar' ? 'إعادة تعيين مباشرة' : 'Direct Reset' }}
                    </button>
                </div>
            </div>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="fas fa-arrow-left me-2"></i> {{ app()->getLocale() == 'ar' ? 'العودة إلى تسجيل الدخول' : 'Back to Login' }}
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
function copyResetLink() {
    var copyText = document.getElementById("reset-link");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("{{ app()->getLocale() == 'ar' ? 'تم نسخ الرابط!' : 'Link copied!' }}");
}
</script>
@endsection 