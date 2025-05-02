@extends('layouts.app')

@section('title', 'حل الامتحان: ' . $quiz->title)

@section('styles')
<style>
    .quiz-timer {
        position: sticky;
        top: 20px;
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        z-index: 100;
    }
    
    .timer-display {
        font-size: 2rem;
        font-weight: bold;
        text-align: center;
        color: #dc3545;
    }
    
    .timer-warning {
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 30px;
        background-color: #fff;
    }
    
    .question-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        border-bottom: 1px solid #ddd;
    }
    
    .question-body {
        padding: 20px;
    }
    
    .question-number {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        background-color: #007bff;
        color: #fff;
        border-radius: 50%;
        margin-left: 10px;
    }
    
    .option-row {
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 5px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .option-row:hover {
        background-color: #f8f9fa;
    }
    
    .option-row.selected {
        background-color: #e3f2fd;
        border-color: #90caf9;
    }
    
    .nav-box {
        position: sticky;
        top: 100px;
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .question-nav {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }
    
    .question-nav-item {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        font-weight: bold;
        cursor: pointer;
    }
    
    .question-nav-item.active {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }
    
    .question-nav-item.answered {
        background-color: #28a745;
        color: #fff;
        border-color: #28a745;
    }
    
    .form-actions {
        position: sticky;
        bottom: 20px;
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <form id="quiz-form" action="{{ route('student.quiz-attempts.submit', $attempt->attempt_id) }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-9">
                <!-- Timer -->
                <div class="quiz-timer">
                    <h5 class="text-center mb-2">الوقت المتبقي</h5>
                    <div id="timer" class="timer-display" data-end-time="{{ $attempt->end_time->timestamp }}" data-current-time="{{ time() }}">
                        --:--:--
                    </div>
                </div>
                
                <!-- Quiz Header -->
                <div class="alert alert-info mb-4">
                    <h4>{{ $quiz->title }}</h4>
                    <p class="mb-0">عدد الأسئلة: {{ count($quiz->questions_json) }} | الدرجة الكلية: {{ $quiz->total_possible_score }} | مدة الامتحان: {{ $quiz->duration_minutes }} دقيقة</p>
                </div>
                
                <!-- Questions -->
                @foreach($quiz->questions_json as $index => $question)
                    <div id="question-container-{{ $index }}" class="question-card">
                        <div class="question-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <span class="question-number">{{ $index + 1 }}</span>
                                {{ $question['type'] == 'multiple_choice' ? 'اختيار من متعدد' : 
                                   ($question['type'] == 'true_false' ? 'صح/خطأ' : 'إجابة قصيرة') }} 
                                <span class="text-muted">({{ $question['points'] }} نقطة)</span>
                            </h5>
                        </div>
                        <div class="question-body">
                            <p class="fw-bold mb-4">{{ $question['text'] }}</p>
                            
                            @php
                                $questionId = $question['id'] ?? $index;
                                $userAnswer = isset($attempt->answers_json[$questionId]) ? $attempt->answers_json[$questionId] : null;
                            @endphp
                            
                            @if($question['type'] == 'multiple_choice')
                                @foreach($question['options'] as $optionIndex => $option)
                                    <div class="option-row {{ isset($userAnswer) && (is_array($userAnswer) ? in_array($option['text'], $userAnswer) : $userAnswer == $option['text']) ? 'selected' : '' }}"
                                        onclick="selectOption(this, '{{ $questionId }}', '{{ $option['text'] }}', 'multiple_choice')">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="answers[{{ $questionId }}][]" 
                                                   value="{{ $option['text'] }}"
                                                   id="option-{{ $questionId }}-{{ $optionIndex }}"
                                                   {{ isset($userAnswer) && (is_array($userAnswer) ? in_array($option['text'], $userAnswer) : $userAnswer == $option['text']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="option-{{ $questionId }}-{{ $optionIndex }}">
                                                {{ $option['text'] }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($question['type'] == 'true_false')
                                <div class="option-row {{ $userAnswer == 'true' ? 'selected' : '' }}"
                                     onclick="selectOption(this, '{{ $questionId }}', 'true', 'true_false')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[{{ $questionId }}]" 
                                               value="true"
                                               id="true-{{ $questionId }}"
                                               {{ $userAnswer == 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="true-{{ $questionId }}">
                                            صح
                                        </label>
                                    </div>
                                </div>
                                <div class="option-row {{ $userAnswer == 'false' ? 'selected' : '' }}"
                                     onclick="selectOption(this, '{{ $questionId }}', 'false', 'true_false')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[{{ $questionId }}]" 
                                               value="false"
                                               id="false-{{ $questionId }}"
                                               {{ $userAnswer == 'false' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="false-{{ $questionId }}">
                                            خطأ
                                        </label>
                                    </div>
                                </div>
                            @elseif($question['type'] == 'short_answer')
                                <div class="mb-3">
                                    <textarea class="form-control" 
                                              name="answers[{{ $questionId }}]" 
                                              id="answer-{{ $questionId }}" 
                                              rows="3"
                                              placeholder="اكتب إجابتك هنا"
                                              onkeyup="updateAnswerStatus('{{ $questionId }}', this.value)">{{ $userAnswer }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <input type="hidden" name="auto_submit" id="auto_submit" value="0">
                    <button type="button" class="btn btn-primary btn-lg" onclick="confirmSubmit()">
                        <i class="fas fa-check-circle"></i> إنهاء وتسليم الامتحان
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="saveProgress()">
                        <i class="fas fa-save"></i> حفظ الإجابات
                    </button>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="nav-box">
                    <h5 class="mb-3">تنقل بين الأسئلة</h5>
                    <div class="question-nav">
                        @foreach($quiz->questions_json as $index => $question)
                            @php
                                $questionId = $question['id'] ?? $index;
                                $isAnswered = isset($attempt->answers_json[$questionId]);
                            @endphp
                            <div id="nav-{{ $index }}" class="question-nav-item {{ $isAnswered ? 'answered' : '' }}" onclick="scrollToQuestion({{ $index }})">
                                {{ $index + 1 }}
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="question-nav-item" style="background-color: #f8f9fa;"></div>
                            <span class="ms-2">لم تتم الإجابة</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="question-nav-item answered"></div>
                            <span class="ms-2">تمت الإجابة</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>تنبيه:</strong> لا تنسى حفظ إجاباتك بشكل دوري.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitConfirmModalLabel">تأكيد تسليم الامتحان</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إنهاء الامتحان وتسليم إجاباتك؟</p>
                <div id="unanswered-warning" class="alert alert-danger d-none">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>لديك <span id="unanswered-count">0</span> أسئلة لم تجب عليها بعد.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="submitQuiz()">تسليم الامتحان</button>
            </div>
        </div>
    </div>
</div>

<!-- Saved Successfully Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="savedToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto"><i class="fas fa-check-circle"></i> تم الحفظ</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            تم حفظ إجاباتك بنجاح.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Timer Initialization
        updateTimer();
        setInterval(updateTimer, 1000);
        
        // Auto save answers every 60 seconds
        setInterval(saveProgress, 60000);
    });
    
    // Update Timer
    function updateTimer() {
        const timerElement = document.getElementById('timer');
        const endTime = parseInt(timerElement.dataset.endTime);
        const currentTime = Math.floor(Date.now() / 1000);
        const remainingSeconds = endTime - currentTime;
        
        if (remainingSeconds <= 0) {
            // Time's up, auto-submit the quiz
            document.getElementById('auto_submit').value = '1';
            document.getElementById('quiz-form').submit();
            return;
        }
        
        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;
        
        timerElement.textContent = `${padZero(hours)}:${padZero(minutes)}:${padZero(seconds)}`;
        
        // Add warning class if less than 5 minutes
        if (remainingSeconds < 300) {
            timerElement.classList.add('timer-warning');
        }
    }
    
    function padZero(num) {
        return num.toString().padStart(2, '0');
    }
    
    // Handle option selection
    function selectOption(element, questionId, value, type) {
        if (type === 'multiple_choice') {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            
            // Update nav item
            updateAnswerStatus(questionId);
        } else if (type === 'true_false') {
            const radio = element.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Remove selected class from other options
            document.querySelectorAll(`[name="answers[${questionId}]"]`).forEach(input => {
                input.closest('.option-row').classList.remove('selected');
            });
            
            // Add selected class to current option
            element.classList.add('selected');
            
            // Update nav item
            updateAnswerStatus(questionId, value);
        }
    }
    
    // Update answer status in navigation
    function updateAnswerStatus(questionId, value = null) {
        // Find the question index
        let index = -1;
        @foreach($quiz->questions_json as $i => $question)
            if ('{{ $question['id'] ?? $i }}' === questionId) {
                index = {{ $i }};
            }
        @endforeach
        
        if (index >= 0) {
            const navItem = document.getElementById(`nav-${index}`);
            
            // Check if answered
            let isAnswered = false;
            
            if (value !== null && value !== '') {
                isAnswered = true;
            } else {
                const inputs = document.querySelectorAll(`[name="answers[${questionId}]"], [name="answers[${questionId}][]"]`);
                inputs.forEach(input => {
                    if ((input.type === 'checkbox' || input.type === 'radio') && input.checked) {
                        isAnswered = true;
                    } else if (input.type === 'textarea' && input.value.trim() !== '') {
                        isAnswered = true;
                    }
                });
            }
            
            if (isAnswered) {
                navItem.classList.add('answered');
            } else {
                navItem.classList.remove('answered');
            }
        }
    }
    
    // Scroll to question
    function scrollToQuestion(index) {
        const questionContainer = document.getElementById(`question-container-${index}`);
        window.scrollTo({
            top: questionContainer.offsetTop - 100,
            behavior: 'smooth'
        });
    }
    
    // Save progress
    function saveProgress() {
        // Get form data
        const formData = new FormData(document.getElementById('quiz-form'));
        formData.append('_method', 'PUT');
        formData.append('save_progress', '1');
        
        // Send AJAX request
        fetch('{{ route('student.quiz-attempts.save-progress', $attempt->attempt_id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const savedToast = new bootstrap.Toast(document.getElementById('savedToast'));
                savedToast.show();
            }
        })
        .catch(error => {
            console.error('Error saving progress:', error);
        });
    }
    
    // Confirm submission
    function confirmSubmit() {
        // Check if all questions are answered
        const unansweredCount = document.querySelectorAll('.question-nav-item:not(.answered)').length;
        const unansweredWarning = document.getElementById('unanswered-warning');
        const unansweredCountElement = document.getElementById('unanswered-count');
        
        if (unansweredCount > 0) {
            unansweredWarning.classList.remove('d-none');
            unansweredCountElement.textContent = unansweredCount;
        } else {
            unansweredWarning.classList.add('d-none');
        }
        
        // Show confirmation modal
        const modal = new bootstrap.Modal(document.getElementById('submitConfirmModal'));
        modal.show();
    }
    
    // Submit quiz
    function submitQuiz() {
        document.getElementById('quiz-form').submit();
    }
</script>
@endsection 