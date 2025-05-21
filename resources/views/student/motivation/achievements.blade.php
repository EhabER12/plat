@extends('layouts.student')

@section('title', 'الإنجازات')

@section('styles')
<style>
    /* Page Container Styles */
    .achievements-container {
        background-color: #f8f9fa;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 30px;
        position: relative;
        overflow: hidden;
    }

    .achievements-container::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, rgba(0, 51, 102, 0.05) 0%, rgba(0, 51, 102, 0) 70%);
        border-radius: 50%;
        z-index: 0;
    }

    .page-title {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
        font-weight: 700;
        color: #003366;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        right: 0;
        width: 50px;
        height: 4px;
        background: linear-gradient(90deg, #FFD700, #FFA500);
        border-radius: 2px;
    }

    /* Achievement Cards Styles */
    .achievement-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        margin-bottom: 25px;
        border: none;
        background: #fff;
        position: relative;
        z-index: 1;
    }

    .achievement-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 51, 102, 0.05) 0%, rgba(0, 51, 102, 0) 70%);
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .achievement-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .achievement-card:hover::before {
        opacity: 1;
    }

    .achievement-header {
        padding: 20px;
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .achievement-header::after {
        content: '';
        position: absolute;
        top: -10px;
        right: -10px;
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .achievement-body {
        padding: 25px;
        position: relative;
        z-index: 1;
    }

    .achievement-date {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        display: inline-block;
        padding: 5px 10px;
        background-color: #f8f9fa;
        border-radius: 20px;
        margin-top: 10px;
    }

    .achievement-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        color: #FFD700;
        background: -webkit-linear-gradient(#FFD700, #FFA500);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
        transition: transform 0.5s ease;
    }

    .achievement-card:hover .achievement-icon {
        transform: rotate(15deg) scale(1.1);
    }

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding: 30px 0;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, #003366, #004080, #002244);
        left: 50%;
        margin-left: -2px;
        border-radius: 2px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 50px;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s ease;
    }

    .timeline-item.animate {
        opacity: 1;
        transform: translateY(0);
    }

    .timeline-item::after {
        content: '';
        display: table;
        clear: both;
    }

    .timeline-item .timeline-content {
        width: 45%;
        padding: 25px;
        background: #fff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        transition: all 0.4s ease;
        position: relative;
        z-index: 1;
        border-top: 4px solid #003366;
    }

    .timeline-item .timeline-content::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 51, 102, 0.03) 0%, rgba(0, 51, 102, 0) 70%);
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s ease;
        border-radius: 10px;
    }

    .timeline-item .timeline-content:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }

    .timeline-item .timeline-content:hover::after {
        opacity: 1;
    }

    .timeline-item:nth-child(odd) .timeline-content {
        float: left;
    }

    .timeline-item:nth-child(even) .timeline-content {
        float: right;
    }

    .timeline-item .timeline-content::before {
        content: '';
        position: absolute;
        top: 20px;
        width: 20px;
        height: 20px;
        background: #fff;
        transform: rotate(45deg);
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        z-index: -1;
    }

    .timeline-item:nth-child(odd) .timeline-content::before {
        right: -10px;
    }

    .timeline-item:nth-child(even) .timeline-content::before {
        left: -10px;
    }

    .timeline-item .timeline-date {
        position: absolute;
        top: 15px;
        width: 120px;
        text-align: center;
        padding: 7px 10px;
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #003366;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(255, 165, 0, 0.2);
        z-index: 2;
        transition: all 0.3s ease;
    }

    .timeline-item:hover .timeline-date {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 165, 0, 0.3);
    }

    .timeline-item:nth-child(odd) .timeline-date {
        right: -140px;
    }

    .timeline-item:nth-child(even) .timeline-date {
        left: -140px;
    }

    .timeline-item .timeline-icon {
        position: absolute;
        top: 10px;
        left: 50%;
        width: 50px;
        height: 50px;
        margin-left: -25px;
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        border-radius: 50%;
        text-align: center;
        line-height: 50px;
        color: #FFD700;
        font-size: 1.3rem;
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
        z-index: 2;
        transition: all 0.4s ease;
    }

    .timeline-item:hover .timeline-icon {
        transform: rotate(360deg) scale(1.1);
        box-shadow: 0 8px 20px rgba(0, 51, 102, 0.4);
    }

    .timeline-content h4 {
        color: #003366;
        margin-bottom: 15px;
        position: relative;
        padding-bottom: 10px;
    }

    .timeline-content h4::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, #FFD700, #FFA500);
        border-radius: 2px;
    }

    .timeline-content p {
        color: #555;
        line-height: 1.6;
    }

    /* Empty State Styles */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .empty-state::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 100px;
        height: 100px;
        background: rgba(0, 51, 102, 0.03);
        border-radius: 50%;
    }

    .empty-state::after {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 100px;
        height: 100px;
        background: rgba(0, 51, 102, 0.03);
        border-radius: 50%;
    }

    .empty-state-icon {
        font-size: 5rem;
        margin-bottom: 20px;
        color: #e0e0e0;
        animation: pulse 2s infinite;
    }

    .empty-state h3 {
        color: #003366;
        margin-bottom: 15px;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 25px;
    }

    .empty-state .btn {
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        transition: all 0.3s ease;
    }

    .empty-state .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 51, 102, 0.3);
    }

    /* Upcoming Achievements Section */
    .upcoming-achievements {
        margin-top: 50px;
    }

    .upcoming-achievements .card-header {
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 15px 20px;
        position: relative;
        overflow: hidden;
    }

    .upcoming-achievements .card-header::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    /* Animations */
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }
        50% {
            transform: scale(1.05);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 0.8;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.5s ease forwards;
    }

    .alert-achievement {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 165, 0, 0.1) 100%);
        border-left: 4px solid #FFD700;
        border-radius: 10px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.5s ease forwards;
    }

    .alert-achievement i {
        color: #FFD700;
        font-size: 1.5rem;
        margin-left: 10px;
    }

    /* Responsive Styles */
    @media (max-width: 991px) {
        .achievement-card {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 767px) {
        .timeline::before {
            left: 40px;
        }

        .timeline-item .timeline-content {
            width: calc(100% - 80px);
            float: right;
        }

        .timeline-item:nth-child(odd) .timeline-content::before,
        .timeline-item:nth-child(even) .timeline-content::before {
            left: -10px;
        }

        .timeline-item .timeline-date {
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            margin-bottom: 15px;
            margin-top: 5px;
        }

        .timeline-item:nth-child(odd) .timeline-date,
        .timeline-item:nth-child(even) .timeline-date {
            right: auto;
            left: auto;
        }

        .timeline-item .timeline-icon {
            left: 40px;
            margin-left: 0;
        }

        .achievements-container {
            padding: 20px 15px;
        }
    }

    @media (max-width: 575px) {
        .page-title {
            font-size: 1.5rem;
        }

        .timeline-item .timeline-content {
            padding: 15px;
        }

        .achievement-header {
            padding: 15px;
        }

        .achievement-body {
            padding: 15px;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="achievements-container fade-in-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">لوحة الإنجازات</h1>
            <a href="{{ route('student.motivation.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right ml-1"></i> العودة إلى لوحة التحفيز
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert-achievement">
                    <i class="fas fa-trophy"></i>
                    الإنجازات هي علامات مهمة في رحلة التعلم الخاصة بك. كل إنجاز يمثل تقدمًا كبيرًا في مسيرتك التعليمية!
                </div>
            </div>
        </div>

        @if(count($motivationalContent['achievements']) > 0)
            <div class="timeline" id="achievements-timeline">
                @foreach($motivationalContent['achievements'] as $index => $achievement)
                    <div class="timeline-item" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="timeline-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="timeline-content">
                            <h4>{{ $achievement['name'] }}</h4>
                            <p>{{ $achievement['description'] }}</p>
                            <span class="achievement-date">
                                <i class="far fa-calendar-alt ml-1"></i>
                                {{ $achievement['date_earned'] }}
                            </span>
                        </div>
                        <div class="timeline-date">{{ $achievement['date_earned'] }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="empty-state" data-aos="fade-up">
                        <div class="empty-state-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3>لم تحقق أي إنجازات بعد</h3>
                        <p>استمر في التعلم وإكمال الاختبارات للحصول على إنجازات رائعة تعكس تقدمك!</p>
                        <a href="{{ route('student.quizzes.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-tasks ml-1"></i>
                            استكشف الاختبارات المتاحة
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="upcoming-achievements" data-aos="fade-up" data-aos-delay="200">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الإنجازات القادمة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="achievement-card" data-aos="fade-up" data-aos-delay="300">
                                <div class="achievement-header">
                                    <h5 class="mb-0">المتعلم المثالي</h5>
                                </div>
                                <div class="achievement-body text-center">
                                    <div class="achievement-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <p>أكمل 20 اختبارًا بنجاح</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="achievement-card" data-aos="fade-up" data-aos-delay="400">
                                <div class="achievement-header">
                                    <h5 class="mb-0">المتفوق الذهبي</h5>
                                </div>
                                <div class="achievement-body text-center">
                                    <div class="achievement-icon">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <p>حافظ على متوسط درجات 90% أو أعلى في 10 اختبارات متتالية</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="achievement-card" data-aos="fade-up" data-aos-delay="500">
                                <div class="achievement-header">
                                    <h5 class="mb-0">المستكشف الشامل</h5>
                                </div>
                                <div class="achievement-body text-center">
                                    <div class="achievement-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <p>أكمل اختبارًا واحدًا على الأقل في كل فئة من فئات الدورات</p>
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
        // Initialize AOS with custom settings for this page
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });

        // Animate timeline items with a staggered effect
        const timelineItems = document.querySelectorAll('.timeline-item');
        if (timelineItems.length > 0) {
            setTimeout(() => {
                timelineItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.classList.add('animate');
                    }, index * 200);
                });
            }, 300);
        }

        // Add hover effects to achievement cards
        const achievementCards = document.querySelectorAll('.achievement-card');
        achievementCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.querySelector('.achievement-icon').style.transform = 'rotate(15deg) scale(1.1)';
            });

            card.addEventListener('mouseleave', function() {
                this.querySelector('.achievement-icon').style.transform = 'rotate(0) scale(1)';
            });
        });

        // Add confetti effect when page loads (if there are achievements)
        if (timelineItems.length > 0) {
            createConfetti();
        }

        // Simple confetti effect function
        function createConfetti() {
            const confettiContainer = document.createElement('div');
            confettiContainer.style.position = 'fixed';
            confettiContainer.style.top = '0';
            confettiContainer.style.left = '0';
            confettiContainer.style.width = '100%';
            confettiContainer.style.height = '100%';
            confettiContainer.style.pointerEvents = 'none';
            confettiContainer.style.zIndex = '9999';
            document.body.appendChild(confettiContainer);

            const colors = ['#FFD700', '#FFA500', '#003366', '#004080', '#FFFFFF'];

            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.style.position = 'absolute';
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.borderRadius = '50%';
                confetti.style.opacity = Math.random() * 0.5 + 0.5;
                confetti.style.top = '-10px';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                confetti.style.transition = 'all ' + (Math.random() * 3 + 2) + 's ease-out';

                confettiContainer.appendChild(confetti);

                setTimeout(() => {
                    confetti.style.top = '100vh';
                    confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                }, 10);

                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }

            setTimeout(() => {
                confettiContainer.remove();
            }, 5000);
        }
    });
</script>
@endsection
