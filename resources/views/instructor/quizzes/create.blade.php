@extends('layouts.instructor')

@section('title', 'إنشاء امتحان جديد')

@section('styles')
<style>
    .question-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        position: relative;
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
    
    .correct-answer-toggle .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="m-0">إنشاء امتحان جديد</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="create-quiz-form" action="{{ route('instructor.quizzes.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">الدورة <span class="text-danger">*</span></label>
                                <select class="form-select @error('course_id') is-invalid @enderror" name="course_id" required>
                                    <option value="">اختر الدورة</option>
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
                            
                            <div class="col-md-6">
                                <label class="form-label">عنوان الامتحان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وصف الامتحان</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">مدة الامتحان (بالدقائق) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1" required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">نسبة النجاح (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('passing_percentage') is-invalid @enderror" name="passing_percentage" value="{{ old('passing_percentage', 60) }}" min="1" max="100" required>
                                @error('passing_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">الحد الأقصى للمحاولات</label>
                                <input type="number" class="form-control @error('max_attempts') is-invalid @enderror" name="max_attempts" value="{{ old('max_attempts') }}" min="1">
                                @error('max_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">نشر الامتحان فوراً</label>
                        </div>

                        <hr class="my-4">

                        <h4>أسئلة الامتحان</h4>
                        <p class="text-muted mb-3">أضف سؤالاً واحداً على الأقل للامتحان.</p>

                        <div id="questions-container"></div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-primary me-2" id="add-mcq-btn">
                                <i class="fas fa-plus-circle"></i> إضافة سؤال اختيار من متعدد
                            </button>
                            <button type="button" class="btn btn-outline-primary me-2" id="add-tf-btn">
                                <i class="fas fa-plus-circle"></i> إضافة سؤال صح/خطأ
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="add-text-btn">
                                <i class="fas fa-plus-circle"></i> إضافة سؤال إجابة قصيرة
                            </button>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">إنشاء الامتحان</button>
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
        
        // إضافة سؤال اختيار من متعدد
        document.getElementById('add-mcq-btn').addEventListener('click', function() {
            addQuestion('multiple_choice');
        });
        
        // إضافة سؤال صح/خطأ
        document.getElementById('add-tf-btn').addEventListener('click', function() {
            addQuestion('true_false');
        });
        
        // إضافة سؤال إجابة قصيرة
        document.getElementById('add-text-btn').addEventListener('click', function() {
            addQuestion('short_answer');
        });
        
        // دالة إضافة سؤال جديد
        function addQuestion(type) {
            const questionId = questionCounter++;
            const questionCard = document.createElement('div');
            questionCard.className = 'question-card mb-4';
            questionCard.id = `question-${questionId}`;
            
            let questionHeader = `
                <div class="question-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">${type === 'multiple_choice' ? 'سؤال اختيار من متعدد' : 
                                      (type === 'true_false' ? 'سؤال صح/خطأ' : 'سؤال إجابة قصيرة')}</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-danger remove-question" 
                                onclick="removeQuestion(${questionId})">
                            <i class="fas fa-times"></i> حذف
                        </button>
                    </div>
                </div>
            `;
            
            let questionBody = `
                <div class="question-body">
                    <input type="hidden" name="questions[${questionId}][type]" value="${type}">
                    
                    <div class="mb-3">
                        <label class="form-label">نص السؤال <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="questions[${questionId}][text]" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">النقاط</label>
                            <input type="number" class="form-control" name="questions[${questionId}][points]" 
                                   value="1" min="1" required>
                        </div>
                    </div>
            `;
            
            // إضافة الخيارات حسب نوع السؤال
            if (type === 'multiple_choice') {
                questionBody += `
                    <div class="mb-3">
                        <label class="form-label">الخيارات</label>
                        <div id="options-container-${questionId}">
                            <!-- الخيارات ستضاف هنا ديناميكياً -->
                        </div>
                        <button type="button" class="btn btn-outline-secondary add-option-btn" 
                                onclick="addOption(${questionId})">
                            <i class="fas fa-plus"></i> إضافة خيار
                        </button>
                    </div>
                `;
            } else if (type === 'true_false') {
                questionBody += `
                    <div class="mb-3">
                        <label class="form-label">الإجابة الصحيحة</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="questions[${questionId}][correct_answer]" 
                                       id="true-${questionId}" value="true" checked>
                                <label class="form-check-label" for="true-${questionId}">صح</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="questions[${questionId}][correct_answer]" 
                                       id="false-${questionId}" value="false">
                                <label class="form-check-label" for="false-${questionId}">خطأ</label>
                            </div>
                        </div>
                    </div>
                `;
            } else if (type === 'short_answer') {
                questionBody += `
                    <div class="mb-3">
                        <label class="form-label">الإجابة النموذجية</label>
                        <textarea class="form-control" name="questions[${questionId}][correct_answer]" rows="2"></textarea>
                        <small class="text-muted">سيتم استخدام هذه الإجابة للتصحيح التلقائي.</small>
                    </div>
                `;
            }
            
            questionBody += `</div>`;
            
            questionCard.innerHTML = questionHeader + questionBody;
            document.getElementById('questions-container').appendChild(questionCard);
            
            // إضافة خيارين افتراضيين لسؤال الاختيار من متعدد
            if (type === 'multiple_choice') {
                addOption(questionId);
                addOption(questionId);
            }
        }
        
        // إضافة خيار جديد لسؤال الاختيار من متعدد
        window.addOption = function(questionId) {
            const optionsContainer = document.getElementById(`options-container-${questionId}`);
            const optionIndex = optionsContainer.children.length;
            
            const optionRow = document.createElement('div');
            optionRow.className = 'option-row';
            optionRow.id = `option-${questionId}-${optionIndex}`;
            
            optionRow.innerHTML = `
                <div class="d-flex w-100 align-items-center">
                    <div class="flex-grow-1 me-3">
                        <input type="text" class="form-control" 
                               name="questions[${questionId}][options][${optionIndex}][text]" 
                               placeholder="نص الخيار" required>
                    </div>
                    <div class="correct-answer-toggle me-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="questions[${questionId}][options][${optionIndex}][is_correct]" 
                                   id="correct-${questionId}-${optionIndex}" value="1">
                            <label class="form-check-label" for="correct-${questionId}-${optionIndex}">
                                إجابة صحيحة
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" 
                            onclick="removeOption(${questionId}, ${optionIndex})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            optionsContainer.appendChild(optionRow);
        };
        
        // حذف خيار
        window.removeOption = function(questionId, optionIndex) {
            const option = document.getElementById(`option-${questionId}-${optionIndex}`);
            if (option) {
                option.remove();
            }
        };
        
        // حذف سؤال
        window.removeQuestion = function(questionId) {
            const question = document.getElementById(`question-${questionId}`);
            if (question) {
                question.remove();
            }
        };
        
        // التحقق من صحة النموذج قبل الإرسال
        document.getElementById('create-quiz-form').addEventListener('submit', function(event) {
            const questionsContainer = document.getElementById('questions-container');
            if (questionsContainer.children.length === 0) {
                event.preventDefault();
                alert('يجب إضافة سؤال واحد على الأقل للامتحان');
            }
        });
    });
</script>
@endsection 