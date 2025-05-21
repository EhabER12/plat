@extends('layouts.app')

@section('title', 'الامتحانات المتاحة')

@section('styles')
<style>
    .quiz-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        position: relative;
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease, box-shadow 0.3s ease;
    }

    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .quiz-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }

    .quiz-info {
        font-size: 0.875rem;
    }

    .quiz-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }

    .quiz-info-icon {
        margin-left: 0.5rem;
        color: #6c757d;
        min-width: 20px;
    }

    .quiz-attempt-badge {
        font-size: 0.75rem;
    }

    /* Mejoras responsivas */
    .card-body {
        padding: 1.25rem;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        word-break: break-word;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Ajustes para móviles */
    @media (max-width: 767px) {
        .quiz-badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }

        .card-title {
            font-size: 1.1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .quiz-info {
            font-size: 0.8rem;
        }

        .btn {
            width: 100%;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        h1 {
            font-size: 1.75rem;
        }

        .g-4 {
            --bs-gutter-y: 1rem;
        }
    }

    /* Ajustes para pantallas muy pequeñas */
    @media (max-width: 480px) {
        .quiz-info-item {
            margin-bottom: 0.7rem;
        }

        .quiz-attempt-badge {
            font-size: 0.7rem;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .container.py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }
    }

    /* RTL adjustments */
    [dir="rtl"] .quiz-badge {
        right: auto;
        left: 10px;
    }

    [dir="rtl"] .quiz-info-icon {
        margin-left: 0;
        margin-right: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <h1 class="mb-4">الامتحانات المتاحة</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @isset($debug)
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">معلومات التصحيح</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>معرف الطالب:</strong> {{ $debug['student_id'] }}</p>
                        <p><strong>عدد الكورسات المسجل فيها:</strong> {{ count($debug['enrolled_courses']) }}</p>
                        <p><strong>معرفات الكورسات:</strong> {{ implode(', ', $debug['enrolled_courses']) }}</p>
                        <p><strong>عدد الامتحانات:</strong> {{ $debug['quiz_count'] }}</p>

                        @if($debug['quiz_count'] > 0)
                            <h6>تفاصيل الامتحانات:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المعرف</th>
                                            <th>العنوان</th>
                                            <th>الكورس</th>
                                            <th>منشور</th>
                                            <th>نشط</th>
                                            <th>تاريخ البدء</th>
                                            <th>تاريخ الانتهاء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($debug['quizzes'] as $quiz)
                                            <tr>
                                                <td>{{ $quiz['id'] }}</td>
                                                <td>{{ $quiz['title'] }}</td>
                                                <td>{{ $quiz['course_id'] }}</td>
                                                <td>{{ $quiz['is_published'] ? 'نعم' : 'لا' }}</td>
                                                <td>{{ $quiz['is_active'] ? 'نعم' : 'لا' }}</td>
                                                <td>{{ $quiz['start_date'] }}</td>
                                                <td>{{ $quiz['end_date'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endisset

        @forelse($quizzes as $quiz)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card quiz-card">
                    @if($quiz->hasEnded())
                        <span class="quiz-badge badge bg-danger">انتهى</span>
                    @elseif($quiz->hasNotStarted())
                        <span class="quiz-badge badge bg-info">سيبدأ قريباً</span>
                    @elseif($quiz->isActive())
                        <span class="quiz-badge badge bg-success">متاح</span>
                    @else
                        <span class="quiz-badge badge bg-secondary">غير متاح</span>
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $quiz->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($quiz->description, 100) }}</p>

                        <div class="quiz-info mt-3">
                            <div class="quiz-info-item">
                                <i class="fas fa-book quiz-info-icon"></i>
                                <span>الدورة: <strong>{{ $quiz->course->title }}</strong></span>
                            </div>
                            <div class="quiz-info-item">
                                <i class="fas fa-clock quiz-info-icon"></i>
                                <span>المدة: <strong>{{ $quiz->duration_minutes }} دقيقة</strong></span>
                            </div>
                            <div class="quiz-info-item">
                                <i class="fas fa-question-circle quiz-info-icon"></i>
                                <span>عدد الأسئلة: <strong>{{ $quiz->questions_count }}</strong></span>
                            </div>

                            @if($quiz->max_attempts)
                                <div class="quiz-info-item">
                                    <i class="fas fa-redo quiz-info-icon"></i>
                                    <span>عدد المحاولات المسموحة: <strong>{{ $quiz->max_attempts }}</strong></span>
                                </div>
                            @endif

                            @if($quiz->start_date)
                                <div class="quiz-info-item">
                                    <i class="fas fa-calendar-alt quiz-info-icon"></i>
                                    <span>متاح من: <strong>{{ $quiz->start_date->format('Y-m-d H:i') }}</strong></span>
                                </div>
                            @endif

                            @if($quiz->end_date)
                                <div class="quiz-info-item">
                                    <i class="fas fa-calendar-times quiz-info-icon"></i>
                                    <span>متاح حتى: <strong>{{ $quiz->end_date->format('Y-m-d H:i') }}</strong></span>
                                </div>
                            @endif

                            <div class="quiz-info-item">
                                <i class="fas fa-hourglass-half quiz-info-icon"></i>
                                <span class="{{ $quiz->hasEnded() ? 'text-danger' : ($quiz->hasNotStarted() ? 'text-info' : 'text-success') }}">
                                    <strong>{{ $quiz->getTimeStatus() }}</strong>
                                </span>
                            </div>
                        </div>

                        <!-- Previous Attempts -->
                        @if(isset($attempts[$quiz->id]) && $attempts[$quiz->id]->count() > 0)
                            <div class="mt-4 mb-3">
                                <h6 class="mb-2">محاولاتك السابقة:</h6>
                                <div class="bg-light p-2 rounded">
                                    @foreach($attempts[$quiz->id] as $attempt)
                                        <div class="d-flex justify-content-between align-items-center mb-1 py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <span class="small">{{ $attempt->created_at->format('Y-m-d H:i') }}</span>
                                            <span class="quiz-attempt-badge badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                                                {{ $attempt->score }}/{{ $quiz->total_possible_score }}
                                                ({{ number_format($attempt->score_percentage, 1) }}%)
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-3 d-grid">
                            @if($quiz->hasEnded())
                                <button disabled class="btn btn-secondary">
                                    <i class="fas fa-times-circle me-2"></i> انتهى الامتحان
                                </button>
                            @elseif($quiz->hasNotStarted())
                                <button disabled class="btn btn-info">
                                    <i class="fas fa-clock me-2"></i> غير متاح حالياً
                                </button>
                            @else
                                <a href="{{ route('student.quizzes.show', $quiz->id) }}" class="btn btn-primary">
                                    <i class="fas {{ isset($attempts[$quiz->id]) && $attempts[$quiz->id]->count() > 0 ? 'fa-eye' : 'fa-play-circle' }} me-2"></i>
                                    @if(isset($attempts[$quiz->id]) && $attempts[$quiz->id]->count() > 0)
                                        عرض التفاصيل
                                    @else
                                        البدء
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info d-flex flex-column align-items-center py-4">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5 class="mb-2">u0644u0627 u062au0648u062cu062f u0627u0645u062au062du0627u0646u0627u062a u0645u062au0627u062du0629</h5>
                    <p class="text-center mb-3">u0644u0627 u062au0648u062cu062f u0627u0645u062au062du0627u0646u0627u062a u0645u062au0627u062du0629 u0641u064a u0627u0644u062fu0648u0631u0627u062a u0627u0644u0645u0633u062cu0644 u0628u0647u0627 u062du0627u0644u064au064bu0627.</p>
                    <a href="{{ route('student.my-courses') }}" class="btn btn-primary">
                        <i class="fas fa-book-open me-2"></i> u0627u0633u062au0639u0631u0627u0636 u062fu0648u0631u0627u062au064a
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation effect for quiz cards
        const quizCards = document.querySelectorAll('.quiz-card');
        quizCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    });
</script>
@endsection