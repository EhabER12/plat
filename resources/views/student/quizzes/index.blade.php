@extends('layouts.app')

@section('title', 'الامتحانات المتاحة')

@section('styles')
<style>
    .quiz-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .quiz-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .quiz-info {
        font-size: 0.875rem;
    }
    
    .quiz-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .quiz-info-icon {
        margin-left: 0.5rem;
        color: #6c757d;
    }
    
    .quiz-attempt-badge {
        font-size: 0.75rem;
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
                            <table class="table table-bordered">
                                <thead>
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
                        @endif
                    </div>
                </div>
            </div>
        @endisset

        @forelse($quizzes as $quiz)
            <div class="col-lg-4 col-md-6">
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
                                <span>الدورة: {{ $quiz->course->title }}</span>
                            </div>
                            <div class="quiz-info-item">
                                <i class="fas fa-clock quiz-info-icon"></i>
                                <span>المدة: {{ $quiz->duration_minutes }} دقيقة</span>
                            </div>
                            <div class="quiz-info-item">
                                <i class="fas fa-question-circle quiz-info-icon"></i>
                                <span>عدد الأسئلة: {{ $quiz->questions_count }}</span>
                            </div>
                            
                            @if($quiz->max_attempts)
                                <div class="quiz-info-item">
                                    <i class="fas fa-redo quiz-info-icon"></i>
                                    <span>عدد المحاولات المسموحة: {{ $quiz->max_attempts }}</span>
                                </div>
                            @endif
                            
                            @if($quiz->start_date)
                                <div class="quiz-info-item">
                                    <i class="fas fa-calendar-alt quiz-info-icon"></i>
                                    <span>متاح من: {{ $quiz->start_date->format('Y-m-d H:i') }}</span>
                                </div>
                            @endif
                            
                            @if($quiz->end_date)
                                <div class="quiz-info-item">
                                    <i class="fas fa-calendar-times quiz-info-icon"></i>
                                    <span>متاح حتى: {{ $quiz->end_date->format('Y-m-d H:i') }}</span>
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
                        @if(isset($attempts[$quiz->quiz_id]) && $attempts[$quiz->quiz_id]->count() > 0)
                            <div class="mt-3">
                                <h6>محاولاتك السابقة:</h6>
                                @foreach($attempts[$quiz->quiz_id] as $attempt)
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span>{{ $attempt->created_at->format('Y-m-d H:i') }}</span>
                                        <span class="quiz-attempt-badge badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                                            {{ $attempt->score }}/{{ $quiz->total_possible_score }}
                                            ({{ number_format($attempt->score_percentage, 1) }}%)
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            @if($quiz->hasEnded())
                                <button disabled class="btn btn-secondary">
                                    انتهى الامتحان
                                </button>
                            @elseif($quiz->hasNotStarted())
                                <button disabled class="btn btn-info">
                                    غير متاح حالياً
                                </button>
                            @else
                                <a href="{{ route('student.quizzes.show', $quiz->quiz_id) }}" class="btn btn-primary">
                                    @if(isset($attempts[$quiz->quiz_id]) && $attempts[$quiz->quiz_id]->count() > 0)
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
                <div class="alert alert-info">
                    لا توجد امتحانات متاحة في الدورات المسجل بها حاليًا.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection 