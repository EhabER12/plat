@extends('layouts.app')

@section('title', 'عن المنصة')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="about-hero text-center mb-5 animate__animated animate__fadeInDown">
                <h1 class="display-4 fw-bold mb-3 text-gradient">منصتك للنجاح والتعلم العصري</h1>
                <p class="lead text-secondary">منصة تعليمية متكاملة تجمع بين الدورات، التقييمات، الشهادات، والتواصل الفعّال بين المعلم والطالب في بيئة رقمية آمنة وملهمة.</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-bullseye fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title fw-bold mb-2">رسالتنا</h5>
                            <p class="card-text">تمكين كل متعلم ومدرس من تحقيق أقصى إمكاناته عبر أدوات تعليمية ذكية، محتوى عالي الجودة، ودعم متواصل في كل خطوة.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-lightbulb fa-3x mb-3 text-warning"></i>
                            <h5 class="card-title fw-bold mb-2">رؤيتنا</h5>
                            <p class="card-text">أن نصبح الخيار الأول للتعليم الرقمي في العالم العربي، ونبني مجتمعًا معرفيًا متعاونًا يواكب تطورات العصر.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-3s">
                    <div class="card shadow border-0 h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x mb-3 text-success"></i>
                            <h5 class="card-title fw-bold mb-2">قيمنا</h5>
                            <p class="card-text">الجودة، الشفافية، الابتكار، دعم المواهب، وتكافؤ الفرص للجميع في بيئة تعليمية محفزة وآمنة.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-story card shadow-lg border-0 mb-5 animate__animated animate__fadeIn animate__delay-2s">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3 text-primary"><i class="fas fa-graduation-cap me-2"></i> لماذا منصتنا؟</h3>
                    <ul class="list-unstyled fs-5 mb-0">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> دورات متجددة في جميع المجالات وبإشراف خبراء.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> نظام شهادات احترافي مع إمكانية تخصيص الشهادة.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> تحليلات وتقارير ذكية لمتابعة تقدمك ونجاحك.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> دعم فني سريع ومجتمع تفاعلي.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> واجهة استخدام عصرية وسهلة للجميع.</li>
                    </ul>
                </div>
            </div>

            <div class="text-center animate__animated animate__fadeInUp animate__delay-4s">
                <a href="/contact" class="btn btn-lg btn-gradient shadow px-5 py-2">
                    <i class="fas fa-envelope-open-text me-2"></i> تواصل معنا
                </a>
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
        .about-hero {
            padding: 2.5rem 0 1.5rem 0;
        }
        .about-story {
            background: #f8fafc;
        }
        .card {
            border-radius: 1rem;
        }
        .card .fa-3x {
            margin-bottom: 0.5rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection 