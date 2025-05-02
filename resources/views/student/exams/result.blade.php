@extends('layouts.app')

@section('title', 'Exam Results')

@section('styles')
<style>
    .result-card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .result-header {
        padding: 20px;
        color: white;
    }
    
    .result-header.passed {
        background-color: #28a745;
    }
    
    .result-header.failed {
        background-color: #dc3545;
    }
    
    .result-body {
        padding: 20px;
    }
    
    .score-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2.5rem;
        font-weight: bold;
        color: white;
    }
    
    .score-circle.passed {
        background-color: #28a745;
    }
    
    .score-circle.failed {
        background-color: #dc3545;
    }
    
    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .question-header {
        padding: 15px;
        border-radius: 8px 8px 0 0;
        border-bottom: 1px solid #ddd;
    }
    
    .question-header.correct {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .question-header.incorrect {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .question-body {
        padding: 15px;
    }
    
    .option-row {
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    
    .option-row.selected {
        background-color: rgba(0, 123, 255, 0.1);
        border: 1px solid rgba(0, 123, 255, 0.3);
    }
    
    .option-row.correct {
        background-color: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.3);
    }
    
    .option-row.incorrect {
        background-color: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm result-card mb-4">
                <div class="result-header {{ $attempt->passed ? 'passed' : 'failed' }}">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-0">{{ $attempt->passed ? 'Congratulations!' : 'Better Luck Next Time!' }}</h1>
                            <p class="mb-0">{{ $attempt->passed ? 'You have passed the exam.' : 'You did not pass the exam.' }}</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="score-circle {{ $attempt->passed ? 'passed' : 'failed' }}">
                                {{ number_format($attempt->score, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="result-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Exam Details</h5>
                            <p><strong>Exam:</strong> {{ $attempt->exam->title }}</p>
                            <p><strong>Course:</strong> {{ $attempt->exam->course->title }}</p>
                            <p><strong>Date Taken:</strong> {{ $attempt->created_at->format('M d, Y, h:i A') }}</p>
                            <p><strong>Completion Time:</strong> {{ $attempt->completed_at->diffForHumans($attempt->started_at, true) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Score Summary</h5>
                            <p><strong>Your Score:</strong> {{ number_format($attempt->score, 1) }}%</p>
                            <p><strong>Passing Score:</strong> {{ $attempt->exam->passing_score }}%</p>
                            <p><strong>Status:</strong> 
                                @if($attempt->passed)
                                    <span class="badge bg-success">Passed</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('student.exams.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Exams
                        </a>
                        <a href="{{ route('student.course-content', $attempt->exam->course_id) }}" class="btn btn-primary">
                            <i class="fas fa-book"></i> Continue Learning
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detailed Results</h5>
                </div>
                <div class="card-body">
                    @php
                        $answers = $attempt->answers ?? [];
                        $totalPoints = 0;
                        $earnedPoints = 0;
                    @endphp
                    
                    @foreach($attempt->exam->questions as $index => $question)
                        @php
                            $isAnswered = isset($answers[$question->question_id]);
                            $selectedOptionId = $isAnswered ? $answers[$question->question_id] : null;
                            $correctOption = $question->options->where('is_correct', true)->first();
                            $isCorrect = false;
                            
                            if ($isAnswered && $question->question_type !== 'short_answer') {
                                $selectedOption = $question->options->where('option_id', $selectedOptionId)->first();
                                $isCorrect = $selectedOption && $selectedOption->is_correct;
                            }
                            
                            $totalPoints += $question->points;
                            if ($isCorrect) {
                                $earnedPoints += $question->points;
                            }
                        @endphp
                        
                        <div class="question-card">
                            <div class="question-header {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Question {{ $index + 1 }}</h5>
                                    <div>
                                        <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }}">
                                            {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                                        </span>
                                        <span class="badge bg-info ms-1">{{ $question->points }} points</span>
                                    </div>
                                </div>
                            </div>
                            <div class="question-body">
                                <p class="mb-4">{{ $question->question_text }}</p>
                                
                                @if($question->question_type === 'multiple_choice' || $question->question_type === 'true_false')
                                    <div class="options">
                                        @foreach($question->options as $option)
                                            <div class="option-row {{ $option->option_id == $selectedOptionId ? 'selected' : '' }} {{ $option->is_correct ? 'correct' : ($option->option_id == $selectedOptionId && !$option->is_correct ? 'incorrect' : '') }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($option->option_id == $selectedOptionId)
                                                            <i class="fas fa-check-circle {{ $option->is_correct ? 'text-success' : 'text-danger' }}"></i>
                                                        @elseif($option->is_correct)
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @else
                                                            <i class="far fa-circle"></i>
                                                        @endif
                                                    </div>
                                                    <div>{{ $option->option_text }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->question_type === 'short_answer')
                                    <div class="mb-3">
                                        <p><strong>Your Answer:</strong></p>
                                        <div class="p-3 bg-light border rounded">
                                            {{ $isAnswered ? $answers[$question->question_id] : 'No answer provided' }}
                                        </div>
                                        <p class="mt-3 text-muted">Short answer questions are graded manually by the instructor.</p>
                                    </div>
                                @endif
                                
                                @if(!$isCorrect)
                                    <div class="alert alert-info mt-3">
                                        <strong>Correct Answer:</strong> 
                                        @if($question->question_type === 'multiple_choice' || $question->question_type === 'true_false')
                                            {{ $correctOption ? $correctOption->option_text : 'Not specified' }}
                                        @else
                                            To be graded by instructor
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5>Score Summary</h5>
                            <p><strong>Total Points:</strong> {{ $totalPoints }}</p>
                            <p><strong>Points Earned:</strong> {{ $earnedPoints }}</p>
                            <p><strong>Percentage:</strong> {{ $totalPoints > 0 ? number_format(($earnedPoints / $totalPoints) * 100, 1) : 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
