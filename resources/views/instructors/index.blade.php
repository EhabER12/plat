@extends('layouts.app')

@section('title', 'المدربون - منصة تعليمية')

@section('content')
    <!-- قسم رأس صفحة المدربين -->
    <section class="instructors-header-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="section-title animate__animated animate__fadeInDown">تعرف على <span class="text-highlight">مدربينا المتميزين</span></h1>
                    <p class="section-description animate__animated animate__fadeInUp animate__delay-1s">
                        تعلم على أيدي محترفين متخصصين ذوي خبرة طويلة في مجالاتهم.
                        مدربونا شغوفون بالتعليم وملتزمون بنجاحك.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم شبكة المدربين -->
    <section class="instructors-grid-section">
        <div class="container">
            <div class="row" id="instructorsGrid">
                @if(count($instructors) > 0)
                    @foreach($instructors as $key => $instructor)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 instructor-col" data-aos="fade-up" data-aos-delay="{{ $key * 100 }}">
                            <a href="{{ route('instructors.show', $instructor->user_id) }}" class="instructor-card-link" style="text-decoration:none;color:inherit;">
                                <div class="instructor-card">
                                    <div class="instructor-image">
                                        @if($instructor->profile_image && !Str::contains($instructor->profile_image, 'imgur.com'))
                                            <img src="{{ asset($instructor->profile_image) }}" alt="{{ $instructor->name }}" class="img-fluid">
                                        @else
                                            <div class="default-profile-icon">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                        @endif
                                        <div class="instructor-overlay">
                                            <span class="view-profile-btn">عرض الملف الشخصي</span>
                                        </div>
                                    </div>
                                    <div class="instructor-details">
                                        <h4 class="instructor-name">{{ $instructor->name }}</h4>
                                        <div class="instructor-meta">
                                            <span class="instructor-courses"><i class="fas fa-book-open"></i> {{ $instructor->courses_count }} دورة</span>
                                            <span class="instructor-students"><i class="fas fa-user-graduate"></i> {{ $instructor->enrollments_count }} طالب</span>
                                        </div>
                                        <div class="instructor-rating">
                                            <div class="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $instructor->average_rating)
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i - 0.5 <= $instructor->average_rating)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="rating-value">{{ number_format($instructor->average_rating, 1) }} ({{ $instructor->total_reviews }})</span>
                                        </div>
                                        <p class="instructor-bio">{{ Str::limit($instructor->bio ?? 'مدرب محترف متخصص في مساعدة الطلاب على تحقيق أهدافهم التعليمية.', 100) }}</p>
                                        <div class="instructor-social">
                                            @if(isset($instructor->linkedin_profile))
                                                <a href="{{ $instructor->linkedin_profile }}" target="_blank" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                                            @endif
                                            @if(isset($instructor->twitter_profile))
                                                <a href="{{ $instructor->twitter_profile }}" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
                                            @endif
                                            @if(isset($instructor->website))
                                                <a href="{{ $instructor->website }}" target="_blank" class="social-icon"><i class="fas fa-globe"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> لم يتم العثور على مدربين. يرجى التحقق مرة أخرى لاحقًا.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- قسم كن مدربًا -->
    <section class="become-instructor-section" data-aos="fade-up">
        <div class="container">
            <div class="become-instructor-wrapper">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="become-instructor-content">
                            <h2 class="animate__animated animate__fadeInRight">هل ترغب في مشاركة معرفتك؟</h2>
                            <p class="animate__animated animate__fadeInRight animate__delay-1s">انضم إلى مجتمع المدربين لدينا وساعد الطلاب حول العالم على تعلم مهارات جديدة. قم بإنشاء دورات تعليمية مميزة واكسب المال مع إحداث فرق في حياة الآخرين.</p>
                            <a href="{{ route('instructor.verification.form') }}" class="btn btn-primary btn-lg animate__animated animate__fadeInUp animate__delay-2s">كن مدربًا الآن</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="become-instructor-image animate__animated animate__fadeInLeft">
                            <img src="https://img.freepik.com/free-photo/teacher-explaining-lesson-her-students_23-2148668633.jpg" alt="كن مدربًا" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<style>
    :root {
        --primary: #003366;
        --secondary: #002244;
        --accent: #FFD700;
        --light: #f8f9fa;
        --dark: #212529;
        --text: #333;
        --text-light: #666;
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --hover-shadow: 0 20px 40px rgba(0,0,0,0.15);
        --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    /* تطبيق الخط العربي على جميع العناصر */
    body, h1, h2, h3, h4, h5, h6, p, span, a, button {
        font-family: 'Tajawal', 'Cairo', sans-serif !important;
    }

    .instructors-header-section {
        padding: 100px 0 60px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        margin-bottom: 60px;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .instructors-header-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"%3E%3Cpath fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"%3E%3C/path%3E%3C/svg%3E');
        background-size: cover;
        background-position: center bottom;
        opacity: 0.2;
        z-index: 0;
    }

    .instructors-header-section .section-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .text-highlight {
        position: relative;
        display: inline-block;
        z-index: 1;
    }

    .text-highlight::after {
        content: '';
        position: absolute;
        bottom: 5px;
        right: 0; /* تغيير من left إلى right للدعم العربي */
        width: 100%;
        height: 12px;
        background-color: var(--accent);
        opacity: 0.3;
        z-index: -1;
    }

    .section-description {
        font-size: 1.2rem;
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
        line-height: 1.8;
    }

    .instructors-grid-section {
        padding: 60px 0 80px;
    }

    .instructor-col {
        transition: var(--transition);
        padding: 15px;
    }

    .instructor-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        background-color: white !important;
        position: relative;
        border: 1px solid rgba(0,0,0,0.05);
        background-image: none !important;
    }

    .instructor-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-shadow);
    }

    .instructor-image {
        position: relative;
        overflow: hidden;
        height: 220px;
        background-color: #f8f9fa;
        background-image: none !important;
    }

    .instructor-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s ease;
    }

    .default-profile-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #e6e9f0 0%, #eef1f5 100%);
        color: #aaa;
        font-size: 8rem;
        transition: var(--transition);
    }

    .instructor-card:hover .instructor-image img {
        transform: scale(1.1);
    }

    .instructor-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 51, 102, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: var(--transition);
    }

    .instructor-card:hover .instructor-overlay {
        opacity: 1;
    }

    .view-profile-btn {
        color: white;
        background-color: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        display: inline-block;
        transform: translateY(20px);
        transition: all 0.4s ease;
        border: 2px solid white;
    }

    .instructor-card:hover .view-profile-btn {
        transform: translateY(0);
    }

    .view-profile-btn:hover {
        background-color: white;
        color: var(--primary);
    }

    .instructor-details {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
        background-color: white !important;
        text-align: right; /* محاذاة النص إلى اليمين للغة العربية */
    }

    .instructor-details::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0; /* تغيير من left إلى right للدعم العربي */
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent), var(--primary));
        transform: scaleX(0);
        transform-origin: right; /* تغيير من left إلى right للدعم العربي */
        transition: transform 0.4s ease;
        z-index: -1;
    }

    .instructor-card:hover .instructor-details::before {
        transform: scaleX(1);
    }

    .instructor-name {
        font-size: 1.4rem;
        margin-bottom: 15px;
        color: var(--text);
        font-weight: 700;
    }

    .instructor-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.9rem;
        color: var(--text-light);
    }

    .instructor-meta i {
        color: var(--primary);
        margin-left: 5px; /* تغيير من margin-right إلى margin-left للدعم العربي */
    }

    .instructor-rating {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .rating-stars {
        color: #ffc107;
        margin-left: 8px; /* تغيير من margin-right إلى margin-left للدعم العربي */
        font-size: 1rem;
    }

    .rating-value {
        font-size: 0.9rem;
        color: var(--text-light);
    }

    .instructor-bio {
        font-size: 0.95rem;
        color: var(--text-light);
        margin-bottom: 20px;
        line-height: 1.8;
        flex-grow: 1;
        min-height: 60px;
    }

    .instructor-social {
        display: flex;
        gap: 10px;
        justify-content: flex-end; /* محاذاة الأيقونات إلى اليمين للغة العربية */
    }

    .social-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #f0f2f5;
        color: var(--text-light);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    .social-icon:hover {
        background-color: var(--primary);
        color: white;
        transform: translateY(-3px);
    }

    .become-instructor-section {
        padding: 100px 0;
        background-color: var(--light);
        position: relative;
        overflow: hidden;
    }

    .become-instructor-wrapper {
        background: white;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        padding: 50px;
        position: relative;
        overflow: hidden;
    }

    .become-instructor-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0; /* تغيير من left إلى right للدعم العربي */
        width: 100%;
        height: 10px;
        background: linear-gradient(90deg, var(--accent), var(--primary));
    }

    .become-instructor-content {
        padding-left: 30px; /* تغيير من padding-right إلى padding-left للدعم العربي */
        text-align: right; /* محاذاة النص إلى اليمين للغة العربية */
    }

    .become-instructor-content h2 {
        margin-bottom: 20px;
        color: var(--text);
        font-size: 2.5rem;
        font-weight: 700;
    }

    .become-instructor-content p {
        margin-bottom: 30px;
        color: var(--text-light);
        font-size: 1.1rem;
        line-height: 1.8;
    }

    .become-instructor-image {
        position: relative;
    }

    .become-instructor-image::before {
        content: '';
        position: absolute;
        top: 20px;
        right: 20px; /* تغيير من left إلى right للدعم العربي */
        left: 20px;
        bottom: 20px;
        border: 5px solid var(--primary);
        border-radius: 10px;
        opacity: 0.3;
        z-index: 0;
    }

    .become-instructor-image img {
        border-radius: 10px;
        position: relative;
        z-index: 1;
        box-shadow: var(--card-shadow);
    }

    @media (max-width: 991px) {
        .instructors-header-section .section-title {
            font-size: 2.5rem;
        }

        .become-instructor-content {
            padding-left: 0; /* تغيير من padding-right إلى padding-left للدعم العربي */
            margin-bottom: 50px;
            text-align: center;
        }

        .become-instructor-wrapper {
            padding: 30px;
        }
    }

    @media (max-width: 768px) {
        .instructors-header-section {
            padding: 80px 0 40px;
        }

        .instructors-header-section .section-title {
            font-size: 2rem;
        }

        .section-description {
            font-size: 1rem;
        }

        .become-instructor-content h2 {
            font-size: 1.8rem;
        }

        .instructor-meta {
            flex-direction: column;
            align-items: flex-end; /* محاذاة العناصر إلى اليمين للغة العربية */
            gap: 5px;
        }
    }

    /* Animation for cards on scroll */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animated-card {
        animation: scaleIn 0.6s forwards;
    }

    /* تأكيد على الخلفية البيضاء النقية */
    .instructor-col *, .instructor-card *, .instructor-image *, .instructor-details * {
        background-image: none !important;
    }

    /* تحسينات إضافية للغة العربية */
    .btn {
        font-weight: 600;
        letter-spacing: 0;
    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background-color: var(--secondary);
        border-color: var(--secondary);
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة مكتبة الرسوم المتحركة AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true,
            mirror: true // تمكين التأثيرات المرآة للدعم العربي
        });

        // إضافة تأثيرات متدرجة لبطاقات المدربين
        const instructorCards = document.querySelectorAll('.instructor-col');

        // إزالة الخلفية بطريقة برمجية للتأكد
        document.querySelectorAll('.instructor-card, .instructor-image, .instructor-details, .instructor-col').forEach(el => {
            el.style.backgroundImage = 'none';
            el.style.backgroundColor = el.classList.contains('instructor-image') ? '#f8f9fa' : '#ffffff';
        });

        // تأثير بديل في حالة عدم عمل AOS بشكل جيد
        function animateCards() {
            instructorCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animated-card');
                }, 100 * index);
            });
        }

        // التحقق من دعم IntersectionObserver
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            instructorCards.forEach(card => {
                observer.observe(card);
            });
        } else {
            // بديل للمتصفحات التي لا تدعم IntersectionObserver
            animateCards();
        }

        // تحسين تأثير التحويم على البطاقات
        instructorCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                instructorCards.forEach(otherCard => {
                    if (otherCard !== card) {
                        otherCard.style.opacity = '0.7';
                        otherCard.style.transform = 'scale(0.95)';
                    }
                });
            });

            card.addEventListener('mouseleave', function() {
                instructorCards.forEach(otherCard => {
                    otherCard.style.opacity = '1';
                    otherCard.style.transform = 'scale(1)';
                });
            });
        });

        // تحسين عرض النص العربي
        document.querySelectorAll('.instructor-name, .instructor-bio, .instructor-meta, .section-title, .section-description').forEach(el => {
            el.style.fontFamily = "'Tajawal', 'Cairo', sans-serif";
            el.style.lineHeight = "1.8";
        });

        // تأكد من أن الأيقونات في الاتجاه الصحيح
        document.querySelectorAll('.instructor-meta i').forEach(icon => {
            icon.style.marginLeft = "5px";
            icon.style.marginRight = "0";
        });
    });
</script>
@endsection
