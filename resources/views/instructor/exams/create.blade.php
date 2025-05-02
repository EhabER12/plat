@extends('layouts.instructor')

@section('title', 'Create New Exam')

@section('styles')
<style>
    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        position: relative;
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

    .option-row:last-child {
        margin-bottom: 0;
    }

    .remove-question {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        color: #dc3545;
    }

    .remove-option {
        cursor: pointer;
        color: #dc3545;
    }

    .add-option-btn {
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create New Exam</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="create-exam-form" action="{{ route('instructor.exams.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                <option value="">Select a course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->course_id }}" {{ (old('course_id') == $course->course_id || (isset($selectedCourse) && $selectedCourse->course_id == $course->course_id)) ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Exam Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', 60) }}" min="1" required>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="passing_score" class="form-label">Passing Score (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('passing_score') is-invalid @enderror" id="passing_score" name="passing_score" value="{{ old('passing_score', 60) }}" min="1" max="100" required>
                                    @error('passing_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="available_from" class="form-label">Available From</label>
                                    <input type="datetime-local" class="form-control @error('available_from') is-invalid @enderror" id="available_from" name="available_from" value="{{ old('available_from') }}">
                                    @error('available_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="available_to" class="form-label">Available To</label>
                                    <input type="datetime-local" class="form-control @error('available_to') is-invalid @enderror" id="available_to" name="available_to" value="{{ old('available_to') }}">
                                    @error('available_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Publish exam immediately</label>
                        </div>

                        <hr class="my-4">

                        <h4>Exam Questions</h4>
                        <p class="text-muted">Add at least one question to your exam.</p>

                        <div id="questions-container">
                            <!-- Questions will be added here dynamically -->
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" id="add-mcq-btn">
                                <i class="fas fa-plus"></i> Add Multiple Choice Question
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="add-tf-btn">
                                <i class="fas fa-plus"></i> Add True/False Question
                            </button>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('instructor.exams.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">Create Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let questionCounter = 0;

        // Add Multiple Choice Question
        document.getElementById('add-mcq-btn').addEventListener('click', function() {
            addQuestion('multiple_choice');
        });

        // Add True/False Question
        document.getElementById('add-tf-btn').addEventListener('click', function() {
            addQuestion('true_false');
        });

        // Function to add a new question
        function addQuestion(type) {
            const questionId = questionCounter++;
            const questionsContainer = document.getElementById('questions-container');

            let questionHtml = `
                <div class="question-card" id="question-${questionId}">
                    <div class="question-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Question ${questionId + 1}</h5>
                        <button type="button" class="btn btn-sm btn-danger remove-question" onclick="removeQuestion(${questionId})">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="question-body">
                        <div class="mb-3">
                            <label for="question-text-${questionId}" class="form-label">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="question-text-${questionId}" name="questions[${questionId}][question_text]" rows="2" required></textarea>
                        </div>

                        <input type="hidden" name="questions[${questionId}][question_type]" value="${type}">

                        <div class="mb-3">
                            <label for="question-points-${questionId}" class="form-label">Points <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="question-points-${questionId}" name="questions[${questionId}][points]" value="1" min="1" required>
                        </div>
            `;

            if (type === 'multiple_choice') {
                questionHtml += `
                    <div class="mb-3">
                        <label class="form-label">Options <span class="text-danger">*</span></label>
                        <div id="options-container-${questionId}">
                            <div class="option-row" id="option-${questionId}-0">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="questions[${questionId}][options][0][is_correct]" id="option-correct-${questionId}-0" value="1" checked>
                                    <label class="form-check-label" for="option-correct-${questionId}-0">
                                        Correct
                                    </label>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="questions[${questionId}][options][0][option_text]" placeholder="Option text" required>
                                </div>
                            </div>
                            <div class="option-row" id="option-${questionId}-1">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="questions[${questionId}][options][1][is_correct]" id="option-correct-${questionId}-1" value="1">
                                    <label class="form-check-label" for="option-correct-${questionId}-1">
                                        Correct
                                    </label>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="questions[${questionId}][options][1][option_text]" placeholder="Option text" required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary add-option-btn" onclick="addOption(${questionId})">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                `;
            } else if (type === 'true_false') {
                questionHtml += `
                    <div class="mb-3">
                        <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="questions[${questionId}][correct_answer]" id="true-${questionId}" value="true" checked>
                            <label class="form-check-label" for="true-${questionId}">
                                True
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="questions[${questionId}][correct_answer]" id="false-${questionId}" value="false">
                            <label class="form-check-label" for="false-${questionId}">
                                False
                            </label>
                        </div>
                    </div>
                `;
            }

            questionHtml += `
                    </div>
                </div>
            `;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = questionHtml;
            questionsContainer.appendChild(tempDiv.firstElementChild);

            // Update the submit button state
            updateSubmitButtonState();
        }

        // Function to add a new option to a multiple choice question
        window.addOption = function(questionId) {
            const optionsContainer = document.getElementById(`options-container-${questionId}`);
            const optionCount = optionsContainer.children.length;
            const optionId = optionCount;

            const optionHtml = `
                <div class="option-row" id="option-${questionId}-${optionId}">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="questions[${questionId}][options][${optionId}][is_correct]" id="option-correct-${questionId}-${optionId}" value="1">
                        <label class="form-check-label" for="option-correct-${questionId}-${optionId}">
                            Correct
                        </label>
                    </div>
                    <div class="flex-grow-1">
                        <input type="text" class="form-control" name="questions[${questionId}][options][${optionId}][option_text]" placeholder="Option text" required>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeOption(${questionId}, ${optionId})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = optionHtml;
            optionsContainer.appendChild(tempDiv.firstElementChild);
        };

        // Function to remove an option
        window.removeOption = function(questionId, optionId) {
            const option = document.getElementById(`option-${questionId}-${optionId}`);
            option.remove();
        };

        // Function to remove a question
        window.removeQuestion = function(questionId) {
            const question = document.getElementById(`question-${questionId}`);
            question.remove();

            // Update question numbers
            const questions = document.querySelectorAll('.question-card');
            questions.forEach((question, index) => {
                question.querySelector('h5').textContent = `Question ${index + 1}`;
            });

            // Update the submit button state
            updateSubmitButtonState();
        };

        // Function to update the submit button state
        function updateSubmitButtonState() {
            const questions = document.querySelectorAll('.question-card');
            const submitBtn = document.getElementById('submit-btn');

            if (questions.length === 0) {
                submitBtn.disabled = true;
                submitBtn.title = 'Add at least one question to create the exam';
            } else {
                submitBtn.disabled = false;
                submitBtn.title = '';
            }
        }

        // Initialize the submit button state
        updateSubmitButtonState();

        // Form validation before submission
        document.getElementById('create-exam-form').addEventListener('submit', function(event) {
            const questions = document.querySelectorAll('.question-card');

            if (questions.length === 0) {
                event.preventDefault();
                alert('Please add at least one question to the exam.');
            }
        });
    });
</script>
@endsection
