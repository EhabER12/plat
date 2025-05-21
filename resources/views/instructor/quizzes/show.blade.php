@extends('layouts.instructor')

@section('title', 'تفاصيل الامتحان')

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

    .attempts-table {
        font-size: 0.9rem;
    }

    .attempts-table th, .attempts-table td {
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="m-0">تفاصيل الامتحان</h4>
                    <div>
                        <a href="{{ route('instructor.quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-light me-2">
                            <i class="fas fa-edit"></i> تعديل الامتحان
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteQuizModal">
                            <i class="fas fa-trash"></i> حذف الامتحان
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $quiz->title }}</h5>
                            @if($quiz->description)
                                <p class="card-text">{{ $quiz->description }}</p>
                            @endif

                            <div class="row mt-4">
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">الدورة: </span>
                                    <a href="{{ route('instructor.courses.manage', $quiz->course->course_id) }}">{{ $quiz->course->title }}</a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">مدة الامتحان: </span>
                                    {{ $quiz->duration_minutes }} دقيقة
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">عدد الأسئلة: </span>
                                    {{ count($quiz->questions_json) }}
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">الدرجة الكاملة: </span>
                                    {{ collect($quiz->questions_json)->sum('points') }} نقطة
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">نسبة النجاح: </span>
                                    {{ $quiz->passing_percentage }}%
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">الحد الأقصى للمحاولات: </span>
                                    {{ $quiz->max_attempts ?? 'غير محدود' }}
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">حالة الامتحان: </span>
                                    {!! $quiz->getStatusBadgeHtml() !!}
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">الوقت المتبقي: </span>
                                    <span class="{{ $quiz->hasEnded() ? 'text-danger' : ($quiz->hasNotStarted() ? 'text-info' : 'text-success') }}">
                                        {{ $quiz->getTimeStatus() }}
                                    </span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">تاريخ البدء: </span>
                                    {{ $quiz->start_date ? $quiz->start_date->format('Y-m-d H:i') : 'غير محدد' }}
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">تاريخ الانتهاء: </span>
                                    {{ $quiz->end_date ? $quiz->end_date->format('Y-m-d H:i') : 'غير محدد' }}
                                </div>
                                <div class="col-md-4 mb-3">
                                    <span class="fw-bold">الحالة: </span>
                                    @if($quiz->is_published)
                                        <span class="badge bg-success">منشور</span>
                                    @else
                                        <span class="badge bg-warning">مسودة</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">إحصائيات الامتحان</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>عدد المحاولات:</span>
                                        <span>{{ $quiz->attempts->count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>متوسط الدرجات:</span>
                                        <span>
                                            @if($quiz->attempts->where('status', 'completed')->count() > 0)
                                                {{ round($quiz->attempts->where('status', 'completed')->avg('score'), 1) }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>نسبة النجاح:</span>
                                        <span>
                                            @if($quiz->attempts->where('status', 'completed')->count() > 0)
                                                {{ round(($quiz->attempts->where('is_passed', true)->count() / $quiz->attempts->where('status', 'completed')->count()) * 100) }}%
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أسئلة الامتحان</h5>
                    <a href="{{ route('instructor.quizzes.edit', $quiz->id) }}#questions" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> تعديل الأسئلة
                    </a>
                </div>
                <div class="card-body">
                    @if(count($quiz->questions_json) > 0)
                        @if($quiz->hasEnded())
                            <div class="alert alert-danger mb-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>تنبيه:</strong> لقد انتهت مدة هذا الامتحان ولم يعد متاحًا للطلاب.
                            </div>
                        @elseif($quiz->hasNotStarted())
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>تنبيه:</strong> لم يبدأ هذا الامتحان بعد، سيكون متاحًا للطلاب بدءًا من {{ $quiz->start_date->format('Y-m-d H:i') }}.
                            </div>
                        @elseif(!$quiz->is_published)
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>تنبيه:</strong> هذا الامتحان في وضع المسودة وغير منشور للطلاب.
                            </div>
                        @endif

                        @foreach($quiz->questions_json as $index => $question)
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
                                </div>
                                <div class="question-body">
                                    <p class="fw-bold">{{ $question['text'] }}</p>

                                    @if($question['type'] == 'multiple_choice')
                                        @foreach($question['options'] as $option)
                                            <div class="option-row {{ isset($option['is_correct']) && $option['is_correct'] ? 'correct-option' : '' }}">
                                                <div class="d-flex w-100 align-items-center">
                                                    <div class="me-3">
                                                        @if(isset($option['is_correct']) && $option['is_correct'])
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @else
                                                            <i class="fas fa-circle text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <div>{{ $option['text'] }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif($question['type'] == 'true_false')
                                        <div class="d-flex mt-2">
                                            <div class="me-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" disabled {{ $question['correct_answer'] == 'true' ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        صح
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" disabled {{ $question['correct_answer'] == 'false' ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        خطأ
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($question['type'] == 'short_answer')
                                        <div class="mt-2">
                                            <div class="fw-bold">الإجابة النموذجية:</div>
                                            <div class="mt-1 p-2 bg-light rounded">{{ $question['correct_answer'] }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            لا توجد أسئلة في هذا الامتحان بعد. قم بتعديل الامتحان لإضافة الأسئلة.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">محاولات الطلاب</h5>
                </div>
                <div class="card-body">
                    @if(count($quiz->attempts) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped attempts-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الطالب</th>
                                        <th>تاريخ المحاولة</th>
                                        <th>الوقت المستغرق</th>
                                        <th>الدرجة</th>
                                        <th>النتيجة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quiz->attempts as $index => $attempt)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $attempt->user->name }}</td>
                                            <td>{{ $attempt->start_time->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if($attempt->time_spent_seconds)
                                                    {{ floor($attempt->time_spent_seconds / 60) }} دقيقة
                                                    {{ $attempt->time_spent_seconds % 60 }} ثانية
                                                @elseif($attempt->status == 'in_progress')
                                                    جارية
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->status == 'completed')
                                                    {{ $attempt->score }} / {{ collect($quiz->questions_json)->sum('points') }}
                                                @elseif($attempt->status == 'in_progress')
                                                    جارية
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->status == 'completed')
                                                    @if($attempt->is_passed)
                                                        <span class="badge bg-success">ناجح</span>
                                                    @else
                                                        <span class="badge bg-danger">راسب</span>
                                                    @endif
                                                @elseif($attempt->status == 'in_progress')
                                                    <span class="badge bg-info">قيد التقدم</span>
                                                @elseif($attempt->status == 'timed_out')
                                                    <span class="badge bg-warning">انتهى الوقت</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('instructor.quizzes.attempt', $attempt->attempt_id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            لم يقم أي طالب بمحاولة هذا الامتحان بعد.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Quiz Modal -->
<div class="modal fade" id="deleteQuizModal" tabindex="-1" aria-labelledby="deleteQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQuizModalLabel">تأكيد حذف الامتحان</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف هذا الامتحان وجميع بياناته؟</p>
                <p class="text-danger"><strong>تحذير:</strong> لا يمكن التراجع عن هذا الإجراء.</p>

                @if($quiz->attempts->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> هذا الامتحان لديه {{ $quiz->attempts->count() }} محاولة. حذف الامتحان سيؤدي إلى حذف جميع المحاولات.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('instructor.quizzes.destroy', $quiz->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف الامتحان</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection