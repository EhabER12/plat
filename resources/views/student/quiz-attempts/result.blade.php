@extends('layouts.app')

@section('title', 'نتيجة الامتحان: ' . $quiz->title)

@section('styles')
<style>
    .result-header {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .result-header h1 {
        margin-bottom: 15px;
    }

    .score-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 30px;
        text-align: center;
    }

    .score-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 0 auto 20px;
        font-weight: bold;
        color: #fff;
    }

    .score-pass {
        background-color: #28a745;
    }

    .score-fail {
        background-color: #dc3545;
    }

    .score-percent {
        font-size: 2.5rem;
        line-height: 1;
    }

    .score-label {
        font-size: 1rem;
        opacity: 0.8;
    }

    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 30px;
        background-color: #fff;
        overflow: hidden;
    }

    .question-header {
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .question-body {
        padding: 20px;
    }

    .question-result {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        color: #fff;
        margin-right: 10px;
    }

    .question-correct {
        background-color: #28a745;
    }

    .question-incorrect {
        background-color: #dc3545;
    }

    .option-row {
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .option-correct {
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .option-incorrect {
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .option-selected {
        border-right: 4px solid #007bff;
    }

    .result-summary {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .summary-item:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="result-header">
        <h1>نتيجة الامتحان</h1>
        <p>{{ $quiz->title }}</p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Score Card -->
            <div class="score-card">
                <div class="score-circle {{ $attempt->is_passed ? 'score-pass' : 'score-fail' }}">
                    <div class="score-percent">{{ round($attempt->score_percentage) }}%</div>
                    <div class="score-label">{{ $attempt->is_passed ? 'ناجح' : 'راسب' }}</div>
                </div>

                <div class="row mt-4">
                    <div class="col-6">
                        <h5>مجموع الدرجات</h5>
                        <p class="fs-4">{{ $attempt->score }} / {{ $quiz->total_possible_score }}</p>
                    </div>
                    <div class="col-6">
                        <h5>درجة النجاح</h5>
                        <p class="fs-4">{{ $quiz->passing_percentage }}%</p>
                    </div>
                </div>
            </div>

            <!-- Questions and Answers -->
            <h3 class="mb-4">الأسئلة والإجابات</h3>

            @foreach($quiz->questions_json as $index => $question)
                @php
                    $questionId = $question['id'] ?? $index;
                    $userAnswer = $attempt->answers_json[$questionId] ?? null;
                    $isCorrect = false;

                    if ($question['type'] == 'multiple_choice') {
                        $correctOptions = collect($question['options'])->where('is_correct', true)->pluck('text')->toArray();
                        $isCorrect = !empty($userAnswer) && (is_array($userAnswer) ? count(array_diff($correctOptions, $userAnswer)) === 0 && count(array_diff($userAnswer, $correctOptions)) === 0 : in_array($userAnswer, $correctOptions));
                    } elseif ($question['type'] == 'true_false') {
                        $isCorrect = $userAnswer == $question['correct_answer'];
                    } elseif ($question['type'] == 'short_answer') {
                        $isCorrect = strtolower(trim($userAnswer)) == strtolower(trim($question['correct_answer']));
                    }
                @endphp

                <div class="question-card">
                    <div class="question-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            السؤال {{ $index + 1 }}
                            <span class="question-result {{ $isCorrect ? 'question-correct' : 'question-incorrect' }}">
                                {{ $isCorrect ? 'إجابة صحيحة' : 'إجابة خاطئة' }}
                            </span>
                        </h5>
                        <span class="text-muted">{{ $question['points'] }} نقطة</span>
                    </div>
                    <div class="question-body">
                        <p class="fw-bold mb-4">{{ $question['text'] }}</p>

                        @if($question['type'] == 'multiple_choice')
                            @foreach($question['options'] as $option)
                                @php
                                    $isUserAnswer = is_array($userAnswer) ? in_array($option['text'], $userAnswer) : $userAnswer == $option['text'];
                                    $isCorrectOption = isset($option['is_correct']) && $option['is_correct'];

                                    $optionClasses = 'option-row';
                                    if ($isUserAnswer) $optionClasses .= ' option-selected';
                                    if ($isCorrectOption) $optionClasses .= ' option-correct';
                                    if ($isUserAnswer && !$isCorrectOption) $optionClasses .= ' option-incorrect';
                                @endphp

                                <div class="{{ $optionClasses }}">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($isUserAnswer)
                                                <i class="fas fa-check-circle {{ $isCorrectOption ? 'text-success' : 'text-danger' }}"></i>
                                            @elseif($isCorrectOption)
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="far fa-circle text-secondary"></i>
                                            @endif
                                        </div>
                                        <div>{{ $option['text'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($question['type'] == 'true_false')
                            <div class="option-row {{ $userAnswer == 'true' ? 'option-selected' : '' }} {{ $question['correct_answer'] == 'true' ? 'option-correct' : ($userAnswer == 'true' ? 'option-incorrect' : '') }}">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($userAnswer == 'true')
                                            <i class="fas fa-check-circle {{ $question['correct_answer'] == 'true' ? 'text-success' : 'text-danger' }}"></i>
                                        @elseif($question['correct_answer'] == 'true')
                                            <i class="fas fa-check-circle text-success"></i>
                                        @else
                                            <i class="far fa-circle text-secondary"></i>
                                        @endif
                                    </div>
                                    <div>صح</div>
                                </div>
                            </div>
                            <div class="option-row {{ $userAnswer == 'false' ? 'option-selected' : '' }} {{ $question['correct_answer'] == 'false' ? 'option-correct' : ($userAnswer == 'false' ? 'option-incorrect' : '') }}">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($userAnswer == 'false')
                                            <i class="fas fa-check-circle {{ $question['correct_answer'] == 'false' ? 'text-success' : 'text-danger' }}"></i>
                                        @elseif($question['correct_answer'] == 'false')
                                            <i class="fas fa-check-circle text-success"></i>
                                        @else
                                            <i class="far fa-circle text-secondary"></i>
                                        @endif
                                    </div>
                                    <div>خطأ</div>
                                </div>
                            </div>
                        @elseif($question['type'] == 'short_answer')
                            <div class="mb-3">
                                <label class="form-label">إجابتك:</label>
                                <div class="p-3 bg-light rounded {{ $isCorrect ? 'border-success' : 'border-danger' }}">
                                    {{ $userAnswer ?: 'لم تقدم إجابة' }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الإجابة الصحيحة:</label>
                                <div class="p-3 bg-light rounded border-success">
                                    {{ $question['correct_answer'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-md-4">
            <!-- Summary -->
            <div class="result-summary">
                <h4 class="mb-4">ملخص المحاولة</h4>

                <div class="summary-item">
                    <span>تاريخ المحاولة:</span>
                    <span>{{ $attempt->created_at->format('Y-m-d H:i') }}</span>
                </div>

                <div class="summary-item">
                    <span>وقت البدء:</span>
                    <span>{{ $attempt->start_time->format('H:i:s') }}</span>
                </div>

                <div class="summary-item">
                    <span>وقت الانتهاء:</span>
                    <span>{{ $attempt->end_time->format('H:i:s') }}</span>
                </div>

                <div class="summary-item">
                    <span>الوقت المستغرق:</span>
                    <span>
                        {{ floor($attempt->time_spent_seconds / 60) }} دقيقة
                        {{ $attempt->time_spent_seconds % 60 }} ثانية
                    </span>
                </div>

                <div class="summary-item">
                    <span>عدد الأسئلة:</span>
                    <span>{{ count($quiz->questions_json) }}</span>
                </div>

                <div class="summary-item">
                    <span>الإجابات الصحيحة:</span>
                    <span>{{ $attempt->correct_answers_count }}</span>
                </div>

                <div class="summary-item">
                    <span>الإجابات الخاطئة:</span>
                    <span>{{ count($quiz->questions_json) - $attempt->correct_answers_count }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2">
                <a href="{{ route('student.quizzes.show', $quiz->id) }}" class="btn btn-primary">
                    <i class="fas fa-chevron-right"></i> العودة إلى تفاصيل الامتحان
                </a>

                @if($canRetake)
                    <form action="{{ route('student.quiz-attempts.start', $quiz->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-redo"></i> إعادة المحاولة
                        </button>
                    </form>
                @endif

                <a href="{{ route('student.quizzes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> جميع الامتحانات
                </a>
            </div>

            @if($attempt->instructor_feedback)
                <div class="mt-4 p-3 bg-light rounded">
                    <h5>ملاحظات المدرس:</h5>
                    <p class="mb-0">{{ $attempt->instructor_feedback }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection