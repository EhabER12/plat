@extends('layouts.instructor')

@section('title', $exam->title . ' - Exam Details')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $exam->title }}</h1>
        <div>
            <a href="{{ route('instructor.exams.edit', $exam->exam_id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Exam
            </a>
            <a href="{{ route('instructor.exams.results', $exam->exam_id) }}" class="btn btn-success">
                <i class="fas fa-chart-bar"></i> View Results
            </a>
        </div>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Exam Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Course:</div>
                        <div class="col-md-9">{{ $exam->course->title }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Description:</div>
                        <div class="col-md-9">{{ $exam->description ?: 'No description provided' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Duration:</div>
                        <div class="col-md-9">{{ $exam->duration }} minutes</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Passing Score:</div>
                        <div class="col-md-9">{{ $exam->passing_score }}%</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Status:</div>
                        <div class="col-md-9">
                            @if($exam->is_published)
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Availability:</div>
                        <div class="col-md-9">
                            @if($exam->available_from && $exam->available_to)
                                From {{ $exam->available_from->format('M d, Y, h:i A') }} to {{ $exam->available_to->format('M d, Y, h:i A') }}
                            @elseif($exam->available_from)
                                From {{ $exam->available_from->format('M d, Y, h:i A') }}
                            @elseif($exam->available_to)
                                Until {{ $exam->available_to->format('M d, Y, h:i A') }}
                            @else
                                Always available
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Created:</div>
                        <div class="col-md-9">{{ $exam->created_at->format('M d, Y, h:i A') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Last Updated:</div>
                        <div class="col-md-9">{{ $exam->updated_at->format('M d, Y, h:i A') }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Questions ({{ $exam->questions->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($exam->questions->count() > 0)
                        <div class="accordion" id="questionsAccordion">
                            @foreach($exam->questions as $index => $question)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $question->question_id }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $question->question_id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $question->question_id }}">
                                            <div class="d-flex justify-content-between w-100 me-3">
                                                <span>Question {{ $index + 1 }}: {{ Str::limit($question->question_text, 50) }}</span>
                                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }} ({{ $question->points }} pts)</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $question->question_id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $question->question_id }}" data-bs-parent="#questionsAccordion">
                                        <div class="accordion-body">
                                            <p><strong>Question:</strong> {{ $question->question_text }}</p>

                                            @if($question->question_type === 'multiple_choice')
                                                <p><strong>Options:</strong></p>
                                                <ul class="list-group">
                                                    @foreach($question->options as $option)
                                                        <li class="list-group-item {{ $option->is_correct ? 'list-group-item-success' : '' }}">
                                                            {{ $option->option_text }}
                                                            @if($option->is_correct)
                                                                <span class="badge bg-success float-end">Correct</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @elseif($question->question_type === 'true_false')
                                                <p><strong>Correct Answer:</strong>
                                                    @foreach($question->options as $option)
                                                        @if($option->is_correct)
                                                            {{ $option->option_text }}
                                                        @endif
                                                    @endforeach
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No questions have been added to this exam yet.</p>
                            <a href="{{ route('instructor.exams.edit', $exam->exam_id) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Questions
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Recent Attempts</h5>
                </div>
                <div class="card-body">
                    @if($attempts->count() > 0)
                        <div class="list-group">
                            @foreach($attempts->take(5) as $attempt)
                                <a href="{{ route('instructor.exams.view-attempt', $attempt->attempt_id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $attempt->student->name }}</h6>
                                        <small>{{ $attempt->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">
                                        Score:
                                        @if($attempt->score !== null)
                                            <span class="badge {{ $attempt->passed ? 'bg-success' : 'bg-danger' }}">
                                                {{ number_format($attempt->score, 1) }}%
                                            </span>
                                        @else
                                            <span class="badge bg-warning">In Progress</span>
                                        @endif
                                    </p>
                                    <small>
                                        @if($attempt->completed_at)
                                            Completed: {{ $attempt->completed_at->format('M d, Y, h:i A') }}
                                        @else
                                            Started: {{ $attempt->started_at->format('M d, Y, h:i A') }}
                                        @endif
                                    </small>
                                </a>
                            @endforeach
                        </div>

                        @if($attempts->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('instructor.exams.results', $exam->exam_id) }}" class="btn btn-sm btn-outline-primary">
                                    View All Attempts
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">No attempts have been made on this exam yet.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('instructor.exams.edit', $exam->exam_id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Exam
                        </a>
                        <a href="{{ route('instructor.exams.results', $exam->exam_id) }}" class="btn btn-success">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
                        <button type="button" class="btn btn-danger"
                                onclick="if(confirm('Are you sure you want to delete this exam?')) {
                                    document.getElementById('delete-exam').submit();
                                }">
                            <i class="fas fa-trash"></i> Delete Exam
                        </button>
                        <form id="delete-exam" action="{{ route('instructor.exams.destroy', $exam->exam_id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
