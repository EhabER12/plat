@extends('layouts.app')

@section('title', 'تواصل معنا')

@section('content')
<!-- Hero Section with Background -->
<div class="contact-hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="contact-hero text-center animate__animated animate__fadeIn">
                    <h1 class="display-4 fw-bold mb-3 text-gradient">تواصل معنا</h1>
                    <p class="lead mb-0">نسعد باستفساراتك واقتراحاتك أو أي دعم تحتاجه. فريقنا جاهز لمساعدتك في أي وقت!</p>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</div>

<!-- Main Content -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if(session('success'))
                <div class="alert alert-success animate__animated animate__fadeInUp">
                    <div class="d-flex align-items-center">
                        <div class="alert-icon me-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">تم إرسال رسالتك بنجاح!</h5>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="card contact-card shadow-lg border-0 h-100 animate__animated animate__fadeInUp">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h3 class="fw-bold mb-0"><i class="fas fa-envelope-open-text me-2 text-primary"></i> أرسل لنا رسالة</h3>
                            <p class="text-muted">يرجى ملء النموذج أدناه وسنرد عليك في أقرب وقت ممكن</p>
                        </div>
                        <div class="card-body p-4">
                            <form action="/contact" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم الكامل</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل اسمك الكامل" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-primary"></i></span>
                                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="أدخل بريدك الإلكتروني" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">الموضوع</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-tag text-primary"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="موضوع رسالتك" required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="form-label">رسالتك</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-comment-alt text-primary"></i></span>
                                        <textarea class="form-control form-control-lg @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="اكتب رسالتك هنا..." required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-gradient btn-lg px-5 py-3 w-100">
                                        <i class="fas fa-paper-plane me-2"></i> إرسال الرسالة
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-5">
                    <div class="card contact-info-card shadow border-0 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h3 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i> بيانات التواصل</h3>
                            <p class="text-muted">يمكنك التواصل معنا مباشرة من خلال</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="contact-info-item d-flex align-items-center mb-4">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">العنوان</h5>
                                    <p class="mb-0">القاهرة، مصر</p>
                                </div>
                            </div>
                            <div class="contact-info-item d-flex align-items-center mb-4">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">البريد الإلكتروني</h5>
                                    <p class="mb-0">info@yourplatform.com</p>
                                </div>
                            </div>
                            <div class="contact-info-item d-flex align-items-center mb-4">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">الهاتف</h5>
                                    <p class="mb-0">0100-123-4567</p>
                                </div>
                            </div>
                            <div class="contact-info-item d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">ساعات العمل</h5>
                                    <p class="mb-0">من الأحد إلى الخميس: 9 صباحًا - 5 مساءً</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-4 px-4">
                            <h5 class="mb-3">تواصل معنا مباشرة</h5>
                            <div class="social-links">
                                <a href="mailto:info@yourplatform.com" class="social-link" title="البريد الإلكتروني">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                <a href="tel:01001234567" class="social-link" title="الهاتف">
                                    <i class="fas fa-phone"></i>
                                </a>
                                <a href="https://wa.me/201001234567" target="_blank" class="social-link" title="واتساب">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="#" target="_blank" class="social-link" title="فيسبوك">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" target="_blank" class="social-link" title="تويتر">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Map Card -->
                    <div class="card map-card shadow border-0 animate__animated animate__fadeInUp animate__delay-2s">
                        <div class="card-body p-0">
                            <div class="map-container">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d110502.76983794065!2d31.18401455!3d30.059482450000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14583fa60b21beeb%3A0x79dfb296e8423bba!2z2KfZhNmC2KfZh9ix2KnYjCDZhdit2KfZgdi42Kkg2KfZhNmC2KfZh9ix2KnigKw!5e0!3m2!1sar!2seg!4v1656612141020!5m2!1sar!2seg" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="faq-section mt-5 animate__animated animate__fadeInUp animate__delay-3s">
                <h3 class="text-center fw-bold mb-4"><i class="fas fa-question-circle me-2 text-primary"></i> الأسئلة الشائعة</h3>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                                كيف يمكنني التسجيل في المنصة؟
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                يمكنك التسجيل في المنصة بسهولة من خلال النقر على زر "تسجيل" في الصفحة الرئيسية، ثم اتباع الخطوات البسيطة لإنشاء حساب جديد.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                كيف يمكنني الاشتراك في دورة تعليمية؟
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                بعد تسجيل الدخول، يمكنك تصفح الدورات المتاحة واختيار الدورة التي ترغب بها، ثم النقر على زر "اشترك الآن" واتباع خطوات الدفع لإتمام عملية الاشتراك.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                ما هي طرق الدفع المتاحة؟
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نوفر العديد من طرق الدفع المختلفة بما في ذلك بطاقات الائتمان، والدفع الإلكتروني، والتحويل البنكي. يمكنك اختيار الطريقة المناسبة لك عند إتمام عملية الشراء.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hero Section Styles */
    .contact-hero-section {
        background: linear-gradient(135deg, rgba(0, 51, 102, 0.95) 0%, rgba(0, 34, 68, 0.9) 100%), url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80') center/cover no-repeat;
        padding: 5rem 0 6rem;
        color: white;
        position: relative;
        margin-bottom: 2rem;
    }

    .hero-wave {
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        line-height: 0;
    }

    .text-gradient {
        background: linear-gradient(90deg, #FFD700 0%, #FFFFFF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Card Styles */
    .card {
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .contact-card:hover, .contact-info-card:hover, .map-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    /* Form Styles */
    .form-control {
        border: 1px solid #e0e0e0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.15);
    }

    .input-group-text {
        border: 1px solid #e0e0e0;
    }

    /* Button Styles */
    .btn-gradient {
        background: linear-gradient(90deg, #003366 0%, #FFD700 100%);
        color: #fff;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        box-shadow: 0 8px 24px rgba(0,51,102,0.2);
        transform: translateY(-2px);
        background: linear-gradient(90deg, #FFD700 0%, #003366 100%);
    }

    /* Contact Info Styles */
    .contact-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 5px 15px rgba(0,51,102,0.2);
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: 10px;
    }

    .social-link {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .social-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,51,102,0.3);
        color: #FFD700;
    }

    /* Map Styles */
    .map-container {
        border-radius: 1rem;
        overflow: hidden;
    }

    /* Alert Styles */
    .alert-success {
        background-color: #d1e7dd;
        border-color: #badbcc;
        color: #0f5132;
        border-radius: 0.5rem;
        padding: 1.25rem;
    }

    .alert-icon {
        color: #198754;
    }

    /* FAQ Section */
    .faq-section {
        margin-top: 4rem;
    }

    .accordion-item {
        border: 1px solid rgba(0,51,102,0.1);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .accordion-button {
        background-color: #f8f9fa;
        color: #003366;
        font-weight: 600;
        padding: 1.25rem;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e6f0ff;
        color: #003366;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,51,102,0.1);
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .contact-hero-section {
            padding: 4rem 0 5rem;
        }
    }

    @media (max-width: 768px) {
        .contact-hero-section {
            padding: 3rem 0 4rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection