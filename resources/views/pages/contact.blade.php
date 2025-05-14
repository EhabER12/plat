@extends('layouts.app')

@section('title', 'تواصل معنا')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="contact-hero text-center mb-5 animate__animated animate__fadeInDown">
                <h1 class="display-5 fw-bold mb-3 text-gradient">تواصل معنا</h1>
                <p class="lead text-secondary">نسعد باستفساراتك واقتراحاتك أو أي دعم تحتاجه. فريقنا جاهز لمساعدتك في أي وقت!</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success animate__animated animate__fadeInUp">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-lg border-0 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body p-4">
                    <form action="/contact" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">الموضوع</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">رسالتك</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-gradient btn-lg px-5">
                            <i class="fas fa-paper-plane me-2"></i> إرسال الرسالة
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow border-0 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="fw-bold mb-2"><i class="fas fa-info-circle text-primary me-2"></i> بيانات التواصل الرسمية</h5>
                        <p class="mb-1"><i class="fas fa-map-marker-alt text-danger me-2"></i> <strong>العنوان:</strong> القاهرة، مصر</p>
                        <p class="mb-1"><i class="fas fa-envelope text-info me-2"></i> <strong>البريد الإلكتروني:</strong> info@yourplatform.com</p>
                        <p class="mb-1"><i class="fas fa-phone-alt text-success me-2"></i> <strong>الهاتف:</strong> 0100-123-4567</p>
                    </div>
                    <div class="text-center">
                        <a href="mailto:info@yourplatform.com" class="btn btn-outline-primary btn-sm mx-1"><i class="fas fa-envelope"></i></a>
                        <a href="tel:01001234567" class="btn btn-outline-success btn-sm mx-1"><i class="fas fa-phone"></i></a>
                        <a href="https://wa.me/201001234567" target="_blank" class="btn btn-outline-success btn-sm mx-1"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-gradient {
            background: linear-gradient(90deg, #003366 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .btn-gradient {
            background: linear-gradient(90deg, #003366 0%, #FFD700 100%);
            color: #fff;
            border: none;
            transition: box-shadow 0.3s, transform 0.3s;
        }
        .btn-gradient:hover {
            box-shadow: 0 8px 24px rgba(0,51,102,0.15);
            transform: translateY(-2px) scale(1.03);
            color: #003366;
            background: linear-gradient(90deg, #FFD700 0%, #003366 100%);
        }
        .contact-hero {
            padding: 2.5rem 0 1.5rem 0;
        }
        .card {
            border-radius: 1rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection 