@extends('layouts.instructor')

@section('title', 'تفاصيل محاولة الامتحان')

@section('styles')
<style>
    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #fff;
    }

    .question-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        border-bottom: 1px solid #ddd;
    }

    .question-body {
        padding: 15px;
    }

    .option-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 5px;
    }

    .correct-option {
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .incorrect-option {
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .user-answer {
        border-right: 4px solid #007bff;
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-danger {
        background-color: #dc3545;
    }

    .badge-warning {
        background-color: #ffc107;
    }

    .badge-info {
        background-color: #17a2b8;
    }

    .feedback-form {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="m-0">تفاصيل محاولة الامتحان</h4>
                    <a href="{{ route('instructor.quizzes.show', $attempt->quiz->quiz_id) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left"></i> العودة إلى الامتحان
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $attempt->quiz->title }}</h5>
                            
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <span class="fw-bold">الطالب: </span>
                                    {{ $attempt->user->name }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="fw-bold">الحالة: </span>
                                    @if($attempt->status == 'completed')
                                        <span class="badge bg-success">مكتمل</span>
                                    @elseif($attempt->status == 'in_progress')
                                        <span class="badge bg-info">قيد التقدم</span>
                                    @elseif($attempt->status == 'timed_out')
                                        <span class="badge bg-warning">انتهى الوقت</span>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="fw-bold">وقت البدء: </span>
                                    {{ $attempt->start_time->format('Y-m-d H:i:s') }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="fw-bold">وقت الانتهاء: </span>
                                    {{ $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : 'لم ينته بعد' }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="fw-bold">الوقت المستغرق: </span>
                                    @if($attempt->time_spent_seconds)
                                        {{ floor($attempt->time_spent_seconds / 60) }} دقيقة
                                        {{ $attempt->time_spent_seconds % 60 }} ثانية
                                    @else
                                        -
                                    @endif
                                </div>
                                @if($attempt->status == 'completed')
                                <div class="col-md-6 mb-3">
                                    <span class="fw-bold">النتيجة: </span>
                                    @if($attempt->is_passed)
                                        <span class="badge bg-success">ناجح</span>
                                    @else
                                        <span class="badge bg-danger">راسب</span>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-3">
                                    <span class="fw-bold">الدرجة: </span>
                                    {{ $attempt->score }} / {{ collect($attempt->quiz->questions_json)->sum('points') }}
                                    ({{ round(($attempt->score / collect($attempt->quiz->questions_json)->sum('points')) * 100) }}%)
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($attempt->status == 'completed')
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">ملاحظات المدرس</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($attempt->instructor_feedback)
                                            <p>{{ $attempt->instructor_feedback }}</p>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                                                <i class="fas fa-edit"></i> تعديل الملاحظات
                                            </button>
                                        @else
                                            <p class="text-muted">لم تتم إضافة ملاحظات بعد.</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                                                <i class="fas fa-plus"></i> إضافة ملاحظات
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">أسئلة الامتحان وإجابات الطالب</h5>
                </div>
                <div class="card-body">
                    @php 
                        $questions = $attempt->quiz->questions_json;
                        $answers = $attempt->answers_json ?? [];
                    @endphp

                    @foreach($questions as $index => $question)
                        <div class="question-card mb-4">
                            <div class="question-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">السؤال {{ $index + 1 }}</h5>
                                    <small class="text-muted">
                                        {{ $question['type'] == 'multiple_choice' ? 'اختيار من متعدد' : 
                                           ($question['type'] == 'true_false' ? 'صح/خطأ' : 
                                           'إجابة قصيرة') }} 
                                        ({{ $question['points'] }} نقطة)
                                    </small>
                                </div>
                                <div>
                                    @php
                                        $questionId = $question['id'] ?? $index;
                                        $isCorrect = isset($answers[$questionId]) && $attempt->status == 'completed';
                                        if ($isCorrect) {
                                            if ($question['type'] == 'multiple_choice') {
                                                $correctOptions = collect($question['options'])->where('is_correct', true)->pluck('text')->toArray();
                                                $userAnswers = $answers[$questionId] ?? [];
                                                $isCorrect = !empty(array_intersect($correctOptions, $userAnswers));
                                            } elseif ($question['type'] == 'true_false') {
                                                $isCorrect = isset($answers[$questionId]) && $answers[$questionId] == $question['correct_answer'];
                                            } elseif ($question['type'] == 'short_answer') {
                                                // يمكن إضافة منطق أكثر تعقيدًا للتحقق من الإجابات القصيرة
                                                $isCorrect = isset($answers[$questionId]) && strtolower(trim($answers[$questionId])) == strtolower(trim($question['correct_answer']));
                                            }
                                        }
                                    @endphp

                                    @if($attempt->status == 'completed')
                                        @if($isCorrect)
                                            <span class="badge bg-success"><i class="fas fa-check"></i> إجابة صحيحة</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> إجابة خاطئة</span>
                                        @endif
                                    @elseif(isset($answers[$questionId]))
                                        <span class="badge bg-info">تمت الإجابة</span>
                                    @else
                                        <span class="badge bg-secondary">لم تتم الإجابة</span>
                                    @endif
                                </div>
                            </div>
                            <div class="question-body">
                                <p class="fw-bold">{{ $question['text'] }}</p>
                                
                                @if($question['type'] == 'multiple_choice')
                                    @php
                                        $userAnswers = isset($answers[$questionId]) ? (is_array($answers[$questionId]) ? $answers[$questionId] : [$answers[$questionId]]) : [];
                                    @endphp
                                    @foreach($question['options'] as $option)
                                        @php
                                            $isUserAnswer = in_array($option['text'], $userAnswers);
                                            $isCorrectOption = isset($option['is_correct']) && $option['is_correct'];
                                            $classes = 'option-row';
                                            if ($isUserAnswer) $classes .= ' user-answer';
                                            if ($attempt->status == 'completed') {
                                                if ($isCorrectOption) $classes .= ' correct-option';
                                                if ($isUserAnswer && !$isCorrectOption) $classes .= ' incorrect-option';
                                            }
                                        @endphp
                                        <div class="{{ $classes }}">
                                            <div class="d-flex w-100 align-items-center">
                                                <div class="me-3">
                                                    @if($isUserAnswer)
                                                        <i class="fas fa-check-circle {{ $isCorrectOption ? 'text-success' : 'text-danger' }}"></i>
                                                    @elseif($isCorrectOption && $attempt->status == 'completed')
                                                        <i class="far fa-check-circle text-success"></i>
                                                    @else
                                                        <i class="far fa-circle text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div>{{ $option['text'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($question['type'] == 'true_false')
                                    @php
                                        $userAnswer = $answers[$questionId] ?? null;
                                    @endphp
                                    <div class="d-flex mt-2">
                                        <div class="me-4">
                                            <div class="form-check">
                                                @php
                                                    $isTrueCorrect = $question['correct_answer'] == 'true';
                                                    $userSelectedTrue = $userAnswer == 'true';
                                                @endphp
                                                <input class="form-check-input" type="radio" disabled 
                                                      {{ $userSelectedTrue ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $attempt->status == 'completed' && $userSelectedTrue ? ($isTrueCorrect ? 'text-success fw-bold' : 'text-danger') : '' }}">
                                                    صح
                                                    @if($attempt->status == 'completed' && $isTrueCorrect)
                                                        <i class="fas fa-check text-success ms-1"></i>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-check">
                                                @php
                                                    $isFalseCorrect = $question['correct_answer'] == 'false';
                                                    $userSelectedFalse = $userAnswer == 'false';
                                                @endphp
                                                <input class="form-check-input" type="radio" disabled 
                                                      {{ $userSelectedFalse ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $attempt->status == 'completed' && $userSelectedFalse ? ($isFalseCorrect ? 'text-success fw-bold' : 'text-danger') : '' }}">
                                                    خطأ
                                                    @if($attempt->status == 'completed' && $isFalseCorrect)
                                                        <i class="fas fa-check text-success ms-1"></i>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($question['type'] == 'short_answer')
                                    <div class="mt-2">
                                        @php
                                            $userAnswer = $answers[$questionId] ?? '';
                                        @endphp
                                        <div class="mb-3">
                                            <div class="fw-bold">إجابة الطالب:</div>
                                            <div class="mt-1 p-2 bg-light rounded {{ $attempt->status == 'completed' ? ($isCorrect ? 'border-success' : 'border-danger') : '' }}">
                                                {{ $userAnswer ?: 'لم يتم تقديم إجابة' }}
                                            </div>
                                        </div>
                                        
                                        @if($attempt->status == 'completed')
                                            <div class="mt-2">
                                                <div class="fw-bold">الإجابة النموذجية:</div>
                                                <div class="mt-1 p-2 bg-light rounded border-success">
                                                    {{ $question['correct_answer'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('instructor.quizzes.provide-feedback', $attempt->attempt_id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">إضافة ملاحظات للطالب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="instructor_feedback" class="form-label">الملاحظات</label>
                        <textarea class="form-control" id="instructor_feedback" name="instructor_feedback" rows="5">{{ $attempt->instructor_feedback }}</textarea>
                        <small class="text-muted">أضف ملاحظاتك حول أداء الطالب في هذا الامتحان.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ الملاحظات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 