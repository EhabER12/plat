@extends('layouts.student')

@section('title', 'لوحة التحفيز')

@section('styles')
<style>
    /* Page Container Styles */
    .motivation-container {
        background-color: #f8f9fa;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 30px;
        position: relative;
        overflow: hidden;
    }

    .motivation-container::before {
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

    /* Card Styles */
    .motivation-card {
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

    .motivation-card::before {
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

    .motivation-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .motivation-card:hover::before {
        opacity: 1;
    }

    .motivation-card .card-header {
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        color: white;
        position: relative;
        overflow: hidden;
        padding: 15px 20px;
        border: none;
    }

    .motivation-card .card-header::after {
        content: '';
        position: absolute;
        top: -10px;
        right: -10px;
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .motivation-card .card-header.bg-warning {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%) !important;
        color: #003366 !important;
    }

    .motivation-card .card-header.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    .motivation-card .card-header.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #0dcaf0 100%) !important;
    }

    .motivation-card .card-header.bg-primary {
        background: linear-gradient(135deg, #003366 0%, #004080 100%) !important;
    }

    .motivation-card .card-body {
        padding: 25px;
        position: relative;
        z-index: 1;
    }

    /* Message Card Styles */
    .message-card {
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 51, 102, 0.2);
        transition: all 0.4s ease;
        transform: translateY(0);
    }

    .message-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 51, 102, 0.3);
    }

    .message-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
    }

    .message-card::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -50px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
    }

    .message-card h3 {
        position: relative;
        display: inline-block;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .message-card h3::after {
        content: '';
        position: absolute;
        bottom: -5px;
        right: 0;
        width: 40px;
        height: 3px;
        background: #FFD700;
        border-radius: 2px;
    }

    /* Progress Circle Styles */
    .progress-circle {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

    .progress-circle svg {
        transform: rotate(-90deg);
        filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.1));
    }

    .progress-circle circle {
        fill: none;
        stroke-width: 10;
    }

    .progress-circle .bg {
        stroke: rgba(0, 0, 0, 0.1);
    }

    .progress-circle .progress {
        stroke: #4CAF50;
        stroke-linecap: round;
        transition: stroke-dashoffset 1.5s ease;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        font-weight: bold;
        color: #003366;
    }

    /* Badge Styles */
    .badge-item {
        text-align: center;
        padding: 20px;
        border-radius: 15px;
        background-color: #fff;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .badge-item::before {
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
    }

    .badge-item:hover {
        transform: translateY(-10px) scale(1.05);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .badge-item:hover::before {
        opacity: 1;
    }

    .badge-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        transition: transform 0.5s ease;
        display: inline-block;
    }

    .badge-item:hover .badge-icon {
        transform: rotate(15deg) scale(1.1);
    }

    .badge-icon.explore {
        color: #17a2b8;
    }

    .badge-icon.persistence {
        color: #fd7e14;
    }

    .badge-icon.streak {
        color: #dc3545;
    }

    .badge-icon.star {
        color: #ffc107;
    }

    .badge-icon.perfect {
        color: #6f42c1;
    }

    /* Stats Card Styles */
    .stats-card {
        border-radius: 15px;
        padding: 25px;
        background-color: #fff;
        height: 100%;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .stats-card::before {
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
    }

    .stats-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .stats-card:hover::before {
        opacity: 1;
    }

    .stats-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #003366;
        margin-bottom: 10px;
    }

    .stats-label {
        color: #6c757d;
        font-size: 1rem;
        font-weight: 500;
    }

    /* Suggestion Card Styles */
    .suggestion-card {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 165, 0, 0.1) 100%);
        border-left: 4px solid #FFD700;
        padding: 20px;
        border-radius: 10px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .suggestion-card:hover {
        box-shadow: 0 5px 15px rgba(255, 165, 0, 0.1);
    }

    .suggestion-card::before {
        content: '\f0eb';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 2rem;
        color: rgba(255, 215, 0, 0.1);
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

    /* Responsive Styles */
    @media (max-width: 991px) {
        .stats-card, .badge-item {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 767px) {
        .motivation-container {
            padding: 20px 15px;
        }

        .stats-value {
            font-size: 2rem;
        }

        .progress-circle {
            width: 120px;
            height: 120px;
        }

        .progress-text {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 575px) {
        .page-title {
            font-size: 1.5rem;
        }

        .message-card {
            padding: 15px;
        }

        .stats-card {
            padding: 15px;
        }

        .badge-icon {
            font-size: 2.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="motivation-container fade-in-up">
        <h1 class="page-title text-center mb-4">لوحة التحفيز والإنجازات</h1>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="message-card" data-aos="fade-up">
                    <h3 class="mb-3">رسالة تحفيزية</h3>
                    <p class="lead mb-0">{{ $motivationalContent['message'] }}</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stats-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-value">{{ $motivationalContent['stats']['average_score'] }}%</div>
                    <div class="stats-label">متوسط الدرجات</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-value">{{ $motivationalContent['stats']['passed_attempts'] }}/{{ $motivationalContent['stats']['total_attempts'] }}</div>
                    <div class="stats-label">الاختبارات المجتازة</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-value">{{ $motivationalContent['stats']['streak'] }}</div>
                    <div class="stats-label">سلسلة النجاح المتتالية</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card motivation-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">تقدمك الإجمالي</h5>
                    </div>
                    <div class="card-body text-center py-4">
                        <div class="progress-circle">
                            <svg width="150" height="150" viewBox="0 0 150 150">
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#003366" />
                                        <stop offset="100%" stop-color="#004080" />
                                    </linearGradient>
                                </defs>
                                <circle class="bg" cx="75" cy="75" r="65" />
                                <circle class="progress" cx="75" cy="75" r="65"
                                    stroke-dasharray="408.4"
                                    stroke-dashoffset="{{ 408.4 - (408.4 * $motivationalContent['progress'] / 100) }}" />
                            </svg>
                            <div class="progress-text">{{ $motivationalContent['progress'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card motivation-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">اقتراح للتحسين</h5>
                    </div>
                    <div class="card-body">
                        <div class="suggestion-card">
                            <p class="mb-0">{{ $motivationalContent['suggestion'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card motivation-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الشارات المكتسبة</h5>
                        <a href="{{ route('student.motivation.badges') }}" class="btn btn-sm btn-light">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        @if(count($motivationalContent['badges']) > 0)
                            <div class="row">
                                @foreach($motivationalContent['badges'] as $index => $badge)
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="badge-item" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                                            <div class="badge-icon {{ $badge['icon'] }}">
                                                <i class="fas fa-{{ $badge['icon'] == 'explore' ? 'compass' : ($badge['icon'] == 'persistence' ? 'tasks' : ($badge['icon'] == 'streak' ? 'fire' : ($badge['icon'] == 'star' ? 'star' : 'award'))) }}"></i>
                                            </div>
                                            <h6>{{ $badge['name'] }}</h6>
                                            <small>{{ $badge['description'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state" data-aos="fade-up">
                                <div class="empty-state-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <h3>لم تحصل على أي شارات بعد</h3>
                                <p>أكمل المزيد من الاختبارات للحصول على شارات تعكس تقدمك!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card motivation-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الإنجازات</h5>
                        <a href="{{ route('student.motivation.achievements') }}" class="btn btn-sm btn-light">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        @if(count($motivationalContent['achievements']) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الإنجاز</th>
                                            <th>الوصف</th>
                                            <th>تاريخ الحصول</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($motivationalContent['achievements'] as $achievement)
                                            <tr class="fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
                                                <td><strong>{{ $achievement['name'] }}</strong></td>
                                                <td>{{ $achievement['description'] }}</td>
                                                <td>{{ $achievement['date_earned'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state" data-aos="fade-up">
                                <div class="empty-state-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <h3>لم تحقق أي إنجازات بعد</h3>
                                <p>استمر في التعلم وإكمال الاختبارات للحصول على إنجازات رائعة!</p>
                                <a href="{{ route('student.quizzes.index') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-tasks ml-1"></i>
                                    استكشف الاختبارات المتاحة
                                </a>
                            </div>
                        @endif
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
        // Inicializar AOS con configuración personalizada
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });

        // Animar el círculo de progreso
        const progressCircle = document.querySelector('.progress-circle .progress');
        if (progressCircle) {
            setTimeout(() => {
                progressCircle.style.transition = 'stroke-dashoffset 1.5s ease-in-out';
            }, 300);
        }

        // Añadir efectos de hover a las tarjetas de estadísticas
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.querySelector('.stats-value').style.transform = 'scale(1.1)';
                this.querySelector('.stats-value').style.transition = 'transform 0.3s ease';
            });

            card.addEventListener('mouseleave', function() {
                this.querySelector('.stats-value').style.transform = 'scale(1)';
            });
        });

        // Añadir efectos de hover a los iconos de las insignias
        const badgeIcons = document.querySelectorAll('.badge-icon');
        badgeIcons.forEach(icon => {
            icon.addEventListener('mouseenter', function() {
                this.style.transform = 'rotate(15deg) scale(1.1)';
            });

            icon.addEventListener('mouseleave', function() {
                this.style.transform = 'rotate(0) scale(1)';
            });
        });

        // Añadir efecto de confeti si hay logros
        const achievements = document.querySelectorAll('.table-responsive tr');
        if (achievements.length > 0) {
            setTimeout(() => {
                createConfetti();
            }, 1000);
        }

        // Función para crear efecto de confeti
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

            for (let i = 0; i < 50; i++) {
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
