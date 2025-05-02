@extends('layouts.app')

@section('title', $quiz->title)

@section('styles')
<style>
    .quiz-header {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .quiz-header h1 {
        margin-bottom: 15px;
    }
    
    .quiz-info {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .quiz-info-item {
        display: flex;
        align-items: center;
    }
    
    .quiz-info-icon {
        margin-left: 8px;
        color: #6c757d;
    }
    
    .attempt-card {
        margin-bottom: 20px;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .attempt-header {
        padding: 15px;
        color: #fff;
    }
    
    .attempt-passed {
        background-color: #198754;
    }
    
    .attempt-failed {
        background-color: #dc3545;
    }
    
    .attempt-inprogress {
        background-color: #0d6efd;
    }
    
    .attempt-body {
        padding: 15px;
    }
    
    .countdown-timer {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="quiz-header">
        <h1>{{ $quiz->title }}</h1>
        <p>{{ $quiz->description }}</p>
        
        <div class="quiz-info">
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
            <div class="quiz-info-item">
                <i class="fas fa-percent quiz-info-icon"></i>
                <span>درجة النجاح: {{ $quiz->passing_percentage }}%</span>
            </div>
            
            @if($quiz->max_attempts)
                <div class="quiz-info-item">
                    <i class="fas fa-redo quiz-info-icon"></i>
                    <span>عدد المحاولات المسموحة: {{ $quiz->max_attempts }}</span>
                </div>
            @endif
            
            <div class="quiz-info-item">
                <i class="fas fa-hourglass-half quiz-info-icon"></i>
                <span class="{{ $quiz->hasEnded() ? 'text-danger' : ($quiz->hasNotStarted() ? 'text-info' : 'text-success') }}">
                    <strong>{{ $quiz->getTimeStatus() }}</strong>
                </span>
            </div>
        </div>
        
        <!-- Alert messages based on quiz status -->
        @if($quiz->hasEnded())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>تنبيه:</strong> انتهت فترة هذا الامتحان ولم يعد متاحًا للمحاولة.
            </div>
        @elseif($quiz->hasNotStarted())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>تنبيه:</strong> لم يبدأ هذا الامتحان بعد، سيكون متاحًا بدءًا من {{ $quiz->start_date->format('Y-m-d H:i') }}.
            </div>
        @endif
    </div>
    
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
    
    <!-- Previous Attempts -->
    @if($attempts->count() > 0)
        <h3 class="mb-4">محاولاتك السابقة</h3>
        <div class="row">
            @foreach($attempts as $attempt)
                <div class="col-md-6">
                    <div class="card attempt-card">
                        <div class="attempt-header 
                            {{ $attempt->status == 'completed' ? ($attempt->is_passed ? 'attempt-passed' : 'attempt-failed') : 'attempt-inprogress' }}">
                            <h5 class="m-0">
                                المحاولة #{{ $loop->iteration }} - 
                                @if($attempt->status == 'completed')
                                    {{ $attempt->is_passed ? 'ناجح' : 'راسب' }}
                                @elseif($attempt->status == 'in_progress')
                                    قيد التقدم
                                @else
                                    انتهى الوقت
                                @endif
                            </h5>
                        </div>
                        <div class="attempt-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>تاريخ المحاولة:</span>
                                <span>{{ $attempt->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            
                            @if($attempt->status == 'completed')
                                <div class="d-flex justify-content-between mb-2">
                                    <span>الدرجة:</span>
                                    <span>{{ $attempt->score }}/{{ $quiz->total_possible_score }} ({{ number_format($attempt->score_percentage, 1) }}%)</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>الوقت المستغرق:</span>
                                    <span>{{ floor($attempt->time_spent_seconds / 60) }} دقيقة {{ $attempt->time_spent_seconds % 60 }} ثانية</span>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('student.quiz-attempts.show', $attempt->attempt_id) }}" class="btn btn-primary w-100">
                                        عرض النتائج
                                    </a>
                                </div>
                            @elseif($attempt->status == 'in_progress')
                                <div class="alert alert-info mb-2">
                                    لديك محاولة قيد التقدم حالياً
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('student.quiz-attempts.continue', $attempt->attempt_id) }}" class="btn btn-primary w-100">
                                        استكمال المحاولة
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    <!-- Start New Attempt -->
    <div class="mt-5">
        <h3 class="mb-4">بدء محاولة جديدة</h3>
        
        @if($quiz->hasEnded())
            <div class="alert alert-danger">
                لقد انتهت فترة هذا الامتحان ولا يمكن بدء محاولات جديدة.
            </div>
        @elseif($quiz->hasNotStarted())
            <div class="alert alert-info">
                لم يبدأ هذا الامتحان بعد. يرجى العودة في {{ $quiz->start_date->format('Y-m-d H:i') }}.
            </div>
        @elseif($attemptsLeft === 0)
            <div class="alert alert-warning">
                لقد وصلت إلى الحد الأقصى من المحاولات المسموحة لهذا الامتحان.
            </div>
        @elseif($hasInProgressAttempt)
            <div class="alert alert-info">
                لديك محاولة قيد التقدم بالفعل. يرجى إكمالها أولاً.
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">قواعد الامتحان</h5>
                    <ul class="mb-4">
                        <li>مدة الامتحان {{ $quiz->duration_minutes }} دقيقة.</li>
                        <li>يجب الحصول على {{ $quiz->passing_percentage }}% على الأقل للنجاح.</li>
                        <li>يجب الإجابة على جميع الأسئلة.</li>
                        <li>عند انتهاء الوقت سيتم تسليم الامتحان تلقائياً.</li>
                    </ul>
                    
                    <form action="{{ route('student.quiz-attempts.start', $quiz->quiz_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">
                            بدء الامتحان الآن
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 