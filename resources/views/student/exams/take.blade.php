@extends('layouts.app')

@section('title', 'Taking Exam: ' . $attempt->exam->title)

@section('styles')
<style>
    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
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
        margin-bottom: 10px;
    }
    
    .timer-container {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    
    .timer-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .timer {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .timer.warning {
        color: #fd7e14;
    }
    
    .timer.danger {
        color: #dc3545;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
    
    .question-navigation {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }
    
    .question-nav-btn {
        width: 100%;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .question-nav-btn.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    
    .question-nav-btn.answered {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ $attempt->exam->title }}</h1>
            </div>

            <form id="exam-form" action="{{ route('student.exams.submit', $attempt->attempt_id) }}" method="POST">
                @csrf
                
                @foreach($attempt->exam->questions as $index => $question)
                    <div class="question-card" id="question-{{ $index + 1 }}">
                        <div class="question-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Question {{ $index + 1 }} of {{ $attempt->exam->questions->count() }}</h5>
                            <span class="badge bg-info">{{ $question->points }} points</span>
                        </div>
                        <div class="question-body">
                            <p class="mb-4">{{ $question->question_text }}</p>
                            
                            @if($question->question_type === 'multiple_choice')
                                <div class="options">
                                    @foreach($question->options as $option)
                                        <div class="option-row">
                                            <div class="form-check">
                                                <input class="form-check-input answer-input" type="radio" name="answers[{{ $question->question_id }}]" id="option-{{ $option->option_id }}" value="{{ $option->option_id }}" data-question="{{ $index + 1 }}">
                                                <label class="form-check-label" for="option-{{ $option->option_id }}">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->question_type === 'true_false')
                                <div class="options">
                                    @foreach($question->options as $option)
                                        <div class="option-row">
                                            <div class="form-check">
                                                <input class="form-check-input answer-input" type="radio" name="answers[{{ $question->question_id }}]" id="option-{{ $option->option_id }}" value="{{ $option->option_id }}" data-question="{{ $index + 1 }}">
                                                <label class="form-check-label" for="option-{{ $option->option_id }}">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->question_type === 'short_answer')
                                <div class="mb-3">
                                    <textarea class="form-control answer-input" name="answers[{{ $question->question_id }}]" rows="3" placeholder="Type your answer here..." data-question="{{ $index + 1 }}"></textarea>
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between mt-4">
                                @if($index > 0)
                                    <button type="button" class="btn btn-outline-secondary prev-question" data-target="{{ $index }}">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                @else
                                    <div></div>
                                @endif
                                
                                @if($index < $attempt->exam->questions->count() - 1)
                                    <button type="button" class="btn btn-outline-primary next-question" data-target="{{ $index + 2 }}">
                                        Next <i class="fas fa-arrow-right"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitConfirmModal">
                                        <i class="fas fa-check-circle"></i> Submit Exam
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Submit Confirmation Modal -->
                <div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="submitConfirmModalLabel">Confirm Submission</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to submit your exam?</p>
                                <div id="unanswered-warning" class="alert alert-warning d-none">
                                    <i class="fas fa-exclamation-triangle"></i> You have unanswered questions. Are you sure you want to proceed?
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit Exam</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-md-3">
            <div class="timer-container">
                <div class="card timer-card mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title">Time Remaining</h5>
                        <div class="timer" id="timer">
                            <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                        </div>
                        <div class="progress mt-2">
                            <div class="progress-bar" id="timer-progress" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Question Navigation</h5>
                    </div>
                    <div class="card-body">
                        <div class="question-navigation" id="question-navigation">
                            @foreach($attempt->exam->questions as $index => $question)
                                <button type="button" class="question-nav-btn" data-target="{{ $index + 1 }}">{{ $index + 1 }}</button>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <div>
                                <span class="badge bg-secondary me-1"></span> Unanswered
                            </div>
                            <div>
                                <span class="badge bg-success me-1"></span> Answered
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitConfirmModal">
                                <i class="fas fa-check-circle"></i> Submit Exam
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Exam Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Course:</strong> {{ $attempt->exam->course->title }}</p>
                        <p><strong>Total Questions:</strong> {{ $attempt->exam->questions->count() }}</p>
                        <p><strong>Passing Score:</strong> {{ $attempt->exam->passing_score }}%</p>
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
        // Show only the first question initially
        const questions = document.querySelectorAll('.question-card');
        questions.forEach((question, index) => {
            if (index > 0) {
                question.style.display = 'none';
            }
        });
        
        // Navigation buttons
        const navButtons = document.querySelectorAll('.question-nav-btn');
        navButtons[0].classList.add('active');
        
        navButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetQuestion = this.getAttribute('data-target');
                showQuestion(targetQuestion);
            });
        });
        
        // Next/Previous buttons
        const nextButtons = document.querySelectorAll('.next-question');
        const prevButtons = document.querySelectorAll('.prev-question');
        
        nextButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetQuestion = this.getAttribute('data-target');
                showQuestion(targetQuestion);
            });
        });
        
        prevButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetQuestion = this.getAttribute('data-target');
                showQuestion(targetQuestion);
            });
        });
        
        // Function to show a specific question
        function showQuestion(questionNumber) {
            questions.forEach((question, index) => {
                question.style.display = (index + 1) == questionNumber ? 'block' : 'none';
            });
            
            navButtons.forEach(button => {
                button.classList.remove('active');
                if (button.getAttribute('data-target') == questionNumber) {
                    button.classList.add('active');
                }
            });
        }
        
        // Track answered questions
        const answerInputs = document.querySelectorAll('.answer-input');
        answerInputs.forEach(input => {
            input.addEventListener('change', function() {
                const questionNumber = this.getAttribute('data-question');
                const navButton = document.querySelector(`.question-nav-btn[data-target="${questionNumber}"]`);
                navButton.classList.add('answered');
            });
        });
        
        // Timer functionality
        const timerElement = document.getElementById('timer');
        const hoursElement = document.getElementById('hours');
        const minutesElement = document.getElementById('minutes');
        const secondsElement = document.getElementById('seconds');
        const timerProgressElement = document.getElementById('timer-progress');
        
        let remainingTime = {{ $remainingTime }};
        const totalTime = {{ $attempt->exam->duration * 60 }};
        const startTime = Math.min(remainingTime, totalTime);
        
        function updateTimer() {
            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                document.getElementById('exam-form').submit();
                return;
            }
            
            remainingTime--;
            
            const hours = Math.floor(remainingTime / 3600);
            const minutes = Math.floor((remainingTime % 3600) / 60);
            const seconds = remainingTime % 60;
            
            hoursElement.textContent = hours.toString().padStart(2, '0');
            minutesElement.textContent = minutes.toString().padStart(2, '0');
            secondsElement.textContent = seconds.toString().padStart(2, '0');
            
            // Update progress bar
            const percentage = (remainingTime / startTime) * 100;
            timerProgressElement.style.width = `${percentage}%`;
            
            // Change color based on remaining time
            if (remainingTime < 300) { // Less than 5 minutes
                timerElement.classList.add('danger');
                timerProgressElement.classList.remove('bg-warning');
                timerProgressElement.classList.add('bg-danger');
            } else if (remainingTime < 600) { // Less than 10 minutes
                timerElement.classList.add('warning');
                timerElement.classList.remove('danger');
                timerProgressElement.classList.add('bg-warning');
            }
        }
        
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Initial update
        
        // Submit confirmation
        document.getElementById('submitConfirmModal').addEventListener('show.bs.modal', function() {
            const answeredCount = document.querySelectorAll('.question-nav-btn.answered').length;
            const totalQuestions = {{ $attempt->exam->questions->count() }};
            
            if (answeredCount < totalQuestions) {
                document.getElementById('unanswered-warning').classList.remove('d-none');
            } else {
                document.getElementById('unanswered-warning').classList.add('d-none');
            }
        });
    });
</script>
@endsection
