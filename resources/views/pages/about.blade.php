@extends('layouts.app')

@section('title', 'عن المنصة')

@section('content')
    <!-- Hero Section with Animated Background -->
    <div class="about-hero-wrapper">
        <div class="about-hero-bg">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="about-hero text-center mb-5">
                        <h1 class="display-4 fw-bold mb-4 text-gradient animate__animated animate__fadeInDown">منصتك للنجاح والتعلم العصري</h1>
                        <p class="lead text-secondary animate__animated animate__fadeIn animate__delay-1s">منصة تعليمية متكاملة تجمع بين الدورات، التقييمات، الشهادات، والتواصل الفعّال بين المعلم والطالب في بيئة رقمية آمنة وملهمة.</p>
                    </div>

                    <!-- Mission, Vision, Values Cards with Hover Effects -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="about-card mission-card">
                                <div class="card-icon">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <h5 class="card-title">رسالتنا</h5>
                                <p class="card-text">تمكين كل متعلم ومدرس من تحقيق أقصى إمكاناته عبر أدوات تعليمية ذكية، محتوى عالي الجودة، ودعم متواصل في كل خطوة.</p>
                            </div>
                        </div>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="about-card vision-card">
                                <div class="card-icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <h5 class="card-title">رؤيتنا</h5>
                                <p class="card-text">أن نصبح الخيار الأول للتعليم الرقمي في العالم العربي، ونبني مجتمعًا معرفيًا متعاونًا يواكب تطورات العصر.</p>
                            </div>
                        </div>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="about-card values-card">
                                <div class="card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5 class="card-title">قيمنا</h5>
                                <p class="card-text">الجودة، الشفافية، الابتكار، دعم المواهب، وتكافؤ الفرص للجميع في بيئة تعليمية محفزة وآمنة.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Why Choose Us Section with Animated Icons -->
                    <div class="why-choose-us" data-aos="fade-up">
                        <h3 class="section-title"><i class="fas fa-graduation-cap me-2"></i> لماذا منصتنا؟</h3>
                        <div class="features-grid">
                            <div class="feature-item" data-aos="zoom-in" data-aos-delay="100">
                                <div class="feature-icon">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="feature-content">
                                    <h5>دورات متجددة</h5>
                                    <p>في جميع المجالات وبإشراف خبراء متخصصين</p>
                                </div>
                            </div>
                            <div class="feature-item" data-aos="zoom-in" data-aos-delay="200">
                                <div class="feature-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div class="feature-content">
                                    <h5>نظام شهادات احترافي</h5>
                                    <p>مع إمكانية تخصيص الشهادة حسب احتياجاتك</p>
                                </div>
                            </div>
                            <div class="feature-item" data-aos="zoom-in" data-aos-delay="300">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="feature-content">
                                    <h5>تحليلات وتقارير ذكية</h5>
                                    <p>لمتابعة تقدمك ونجاحك بشكل مستمر</p>
                                </div>
                            </div>
                            <div class="feature-item" data-aos="zoom-in" data-aos-delay="400">
                                <div class="feature-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div class="feature-content">
                                    <h5>دعم فني سريع</h5>
                                    <p>ومجتمع تفاعلي لمساعدتك في أي وقت</p>
                                </div>
                            </div>
                            <div class="feature-item" data-aos="zoom-in" data-aos-delay="500">
                                <div class="feature-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="feature-content">
                                    <h5>واجهة استخدام عصرية</h5>
                                    <p>سهلة الاستخدام ومتوافقة مع جميع الأجهزة</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Counter Section -->
                    <div class="stats-section" data-aos="fade-up">
                        <div class="row text-center">
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-number counter" data-count="15000">0</div>
                                    <div class="stat-label">طالب</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-number counter" data-count="200">0</div>
                                    <div class="stat-label">دورة تعليمية</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-number counter" data-count="50">0</div>
                                    <div class="stat-label">مدرس محترف</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <div class="stat-number counter" data-count="95">0</div>
                                    <div class="stat-label">نسبة الرضا %</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="text-center mt-5" data-aos="fade-up">
                        <a href="/contact" class="btn-cta">
                            <span class="btn-text"><i class="fas fa-envelope-open-text me-2"></i> تواصل معنا</span>
                            <span class="btn-icon"><i class="fas fa-arrow-left"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hero Section Styles */
        .about-hero-wrapper {
            position: relative;
            padding: 80px 0;
            overflow: hidden;
        }

        .about-hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.2;
        }

        .shape-1 {
            top: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--primary-color), #004080);
            animation: float 8s ease-in-out infinite;
        }

        .shape-2 {
            top: 30%;
            right: -80px;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--secondary-color), #FFC000);
            animation: float 10s ease-in-out infinite;
        }

        .shape-3 {
            bottom: -80px;
            left: 20%;
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            animation: float 12s ease-in-out infinite;
        }

        .shape-4 {
            top: 20%;
            left: 30%;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #E91E63, #C2185B);
            animation: float 9s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }

        .text-gradient {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card Styles */
        .about-card {
            background: white;
            border-radius: 20px;
            padding: 30px 25px;
            height: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .about-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 0;
            background: linear-gradient(to bottom, rgba(0, 51, 102, 0.05), transparent);
            z-index: -1;
            transition: height 0.5s ease;
        }

        .about-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .about-card:hover:before {
            height: 100%;
        }

        .card-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: white;
            transition: all 0.3s ease;
        }

        .mission-card .card-icon {
            background: linear-gradient(135deg, var(--primary-color), #004080);
            box-shadow: 0 10px 20px rgba(0, 51, 102, 0.2);
        }

        .vision-card .card-icon {
            background: linear-gradient(135deg, var(--secondary-color), #FFC000);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.2);
        }

        .values-card .card-icon {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.2);
        }

        .about-card:hover .card-icon {
            transform: scale(1.1) rotate(10deg);
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .card-text {
            color: var(--text-light);
            line-height: 1.7;
        }

        /* Why Choose Us Section */
        .why-choose-us {
            background: linear-gradient(to right, rgba(230, 240, 255, 0.5), rgba(255, 250, 230, 0.5));
            border-radius: 20px;
            padding: 40px;
            margin: 60px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--secondary-color);
            border-radius: 2px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            background: white;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .feature-item:hover .feature-icon {
            background: var(--secondary-color);
            color: var(--primary-color);
            transform: rotate(10deg);
        }

        .feature-content h5 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-color);
        }

        .feature-content p {
            color: var(--text-light);
            margin: 0;
            font-size: 14px;
        }

        /* Stats Section */
        .stats-section {
            margin: 60px 0;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .stat-item {
            padding: 20px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 42px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 16px;
            color: var(--text-light);
            font-weight: 500;
        }

        /* CTA Button */
        .btn-cta {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(0, 51, 102, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-cta:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transition: width 0.5s ease;
            z-index: -1;
        }

        .btn-cta:hover {
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 51, 102, 0.3);
        }

        .btn-cta:hover:before {
            width: 100%;
        }

        .btn-icon {
            margin-right: 10px;
            transition: transform 0.3s ease;
        }

        .btn-cta:hover .btn-icon {
            transform: translateX(-5px);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .about-hero-wrapper {
                padding: 60px 0;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .why-choose-us {
                padding: 30px 20px;
            }

            .stats-section {
                padding: 30px 15px;
            }

            .stat-number {
                font-size: 32px;
            }

            .stat-label {
                font-size: 14px;
            }
        }
    </style>

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS animation library
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });

            // Counter animation for statistics
            const counters = document.querySelectorAll('.counter');
            const speed = 200; // The lower the slower

            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-count');
                    const count = +counter.innerText;

                    // Lower inc to slow and higher to speed up
                    const inc = target / speed;

                    if (count < target) {
                        // Add inc to count and output in counter
                        counter.innerText = Math.ceil(count + inc);
                        // Call function every ms
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target;
                    }
                };

                // Run the function when element is in viewport
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCount();
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });

                observer.observe(counter);
            });
        });
    </script>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection