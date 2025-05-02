@extends('layouts.instructor')

@section('title', 'تعديل الامتحان')

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

    .nav-tabs .nav-link {
        color: #6c757d;
    }

    .nav-tabs .nav-link.active {
        color: #495057;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="m-0">تعديل الامتحان</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <ul class="nav nav-tabs mb-4" id="quizTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-content" type="button" role="tab" aria-controls="info-content" aria-selected="true">معلومات الامتحان</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions-content" type="button" role="tab" aria-controls="questions-content" aria-selected="false">الأسئلة</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="quizTabsContent">
                        <!-- معلومات الامتحان -->
                        <div class="tab-pane fade show active" id="info-content" role="tabpanel" aria-labelledby="info-tab">
                            <form id="update-quiz-form" action="{{ route('instructor.quizzes.update', $quiz->quiz_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الدورة <span class="text-danger">*</span></label>
                                        <select class="form-select @error('course_id') is-invalid @enderror" name="course_id" required>
                                            <option value="">اختر الدورة</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->course_id }}" {{ (old('course_id', $quiz->course_id) == $course->course_id) ? 'selected' : '' }}>
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
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $quiz->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">وصف الامتحان</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $quiz->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">مدة الامتحان (بالدقائق) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1" required>
                                        @error('duration_minutes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">نسبة النجاح (%) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('passing_percentage') is-invalid @enderror" name="passing_percentage" value="{{ old('passing_percentage', $quiz->passing_percentage) }}" min="1" max="100" required>
                                        @error('passing_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">الحد الأقصى للمحاولات</label>
                                        <input type="number" class="form-control @error('max_attempts') is-invalid @enderror" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1">
                                        @error('max_attempts')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">تاريخ البدء</label>
                                        <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date', $quiz->start_date ? $quiz->start_date->format('Y-m-d\TH:i') : '') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">تاريخ الانتهاء</label>
                                        <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date', $quiz->end_date ? $quiz->end_date->format('Y-m-d\TH:i') : '') }}">
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">نشر الامتحان</label>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('instructor.quizzes.show', $quiz->quiz_id) }}" class="btn btn-secondary">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                </div>
                            </form>
                        </div>

                        <!-- الأسئلة -->
                        <div class="tab-pane fade" id="questions-content" role="tabpanel" aria-labelledby="questions-tab">
                            <form id="update-questions-form" action="{{ route('instructor.quizzes.update-questions', $quiz->quiz_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div id="questions-container">
                                    @if(count($quiz->questions_json) > 0)
                                        @foreach($quiz->questions_json as $index => $question)
                                            <div class="question-card mb-4" id="question-{{ $index }}">
                                                <div class="question-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">
                                                        {{ $question['type'] == 'multiple_choice' ? 'سؤال اختيار من متعدد' : 
                                                            ($question['type'] == 'true_false' ? 'سؤال صح/خطأ' : 'سؤال إجابة قصيرة') }}
                                                    </h5>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-danger remove-question" 
                                                                onclick="removeQuestion({{ $index }})">
                                                            <i class="fas fa-times"></i> حذف
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="question-body">
                                                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question['id'] ?? '' }}">
                                                    <input type="hidden" name="questions[{{ $index }}][type]" value="{{ $question['type'] }}">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">نص السؤال <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" name="questions[{{ $index }}][text]" rows="2" required>{{ $question['text'] }}</textarea>
                                                    </div>
                                                    
                                                    <div class="mb-3 row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">النقاط</label>
                                                            <input type="number" class="form-control" name="questions[{{ $index }}][points]" 
                                                                    value="{{ $question['points'] }}" min="1" required>
                                                        </div>
                                                    </div>

                                                    @if($question['type'] == 'multiple_choice')
                                                        <div class="mb-3">
                                                            <label class="form-label">الخيارات</label>
                                                            <div id="options-container-{{ $index }}">
                                                                @foreach($question['options'] as $optionIndex => $option)
                                                                    <div class="option-row" id="option-{{ $index }}-{{ $optionIndex }}">
                                                                        <div class="d-flex w-100 align-items-center">
                                                                            <div class="flex-grow-1 me-3">
                                                                                <input type="text" class="form-control" 
                                                                                    name="questions[{{ $index }}][options][{{ $optionIndex }}][text]" 
                                                                                    placeholder="نص الخيار" value="{{ $option['text'] }}" required>
                                                                            </div>
                                                                            <div class="correct-answer-toggle me-3">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" 
                                                                                        name="questions[{{ $index }}][options][{{ $optionIndex }}][is_correct]" 
                                                                                        id="correct-{{ $index }}-{{ $optionIndex }}" value="1"
                                                                                        {{ isset($option['is_correct']) && $option['is_correct'] ? 'checked' : '' }}>
                                                                                    <label class="form-check-label" for="correct-{{ $index }}-{{ $optionIndex }}">
                                                                                        إجابة صحيحة
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <button type="button" class="btn btn-sm btn-danger" 
                                                                                    onclick="removeOption({{ $index }}, {{ $optionIndex }})">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="button" class="btn btn-outline-secondary add-option-btn" 
                                                                    onclick="addOption({{ $index }})">
                                                                <i class="fas fa-plus"></i> إضافة خيار
                                                            </button>
                                                        </div>
                                                    @elseif($question['type'] == 'true_false')
                                                        <div class="mb-3">
                                                            <label class="form-label">الإجابة الصحيحة</label>
                                                            <div class="d-flex">
                                                                <div class="form-check me-4">
                                                                    <input class="form-check-input" type="radio" name="questions[{{ $index }}][correct_answer]" 
                                                                        id="true-{{ $index }}" value="true" {{ $question['correct_answer'] == 'true' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="true-{{ $index }}">صح</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="questions[{{ $index }}][correct_answer]" 
                                                                        id="false-{{ $index }}" value="false" {{ $question['correct_answer'] == 'false' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="false-{{ $index }}">خطأ</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif($question['type'] == 'short_answer')
                                                        <div class="mb-3">
                                                            <label class="form-label">الإجابة النموذجية</label>
                                                            <textarea class="form-control" name="questions[{{ $index }}][correct_answer]" rows="2">{{ $question['correct_answer'] }}</textarea>
                                                            <small class="text-muted">سيتم استخدام هذه الإجابة للتصحيح التلقائي.</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

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
                                    <a href="{{ route('instructor.quizzes.show', $quiz->quiz_id) }}" class="btn btn-secondary">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">حفظ الأسئلة</button>
                                </div>
                            </form>
                        </div>
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
        // انتقال إلى تبويب الأسئلة إذا كان هناك هاش في الرابط يشير إلى ذلك
        if (window.location.hash === '#questions') {
            const questionsTab = document.getElementById('questions-tab');
            if (questionsTab) {
                const tabInstance = new bootstrap.Tab(questionsTab);
                tabInstance.show();
            }
        }

        // تحديد أعلى قيمة للأسئلة الموجودة
        let questionCounter = {{ count($quiz->questions_json) }};
        
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
        window.addQuestion = function(type) {
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
        };
        
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
        
        // التحقق من صحة نموذج الأسئلة قبل الإرسال
        document.getElementById('update-questions-form').addEventListener('submit', function(event) {
            const questionsContainer = document.getElementById('questions-container');
            if (questionsContainer.children.length === 0) {
                event.preventDefault();
                alert('يجب إضافة سؤال واحد على الأقل للامتحان');
            }
        });
    });
</script>
@endsection 