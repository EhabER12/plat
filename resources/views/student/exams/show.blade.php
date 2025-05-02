@extends('layouts.app')

@section('title', $exam->title . ' - Exam Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $exam->title }}</h4>
                </div>
                <div class="card-body">
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

                    @if(!$isAvailable)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> {{ $availabilityMessage }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>Exam Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Course:</strong> {{ $exam->course->title }}</p>
                                <p><strong>Duration:</strong> {{ $exam->duration }} minutes</p>
                                <p><strong>Passing Score:</strong> {{ $exam->passing_score }}%</p>
                            </div>
                            <div class="col-md-6">
                                @if($exam->available_from)
                                    <p><strong>Available From:</strong> {{ $exam->available_from->format('M d, Y, h:i A') }}</p>
                                @endif
                                @if($exam->available_to)
                                    <p><strong>Available Until:</strong> {{ $exam->available_to->format('M d, Y, h:i A') }}</p>
                                @endif
                                <p><strong>Total Questions:</strong> {{ $exam->questions->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Exam Description</h5>
                        <p>{{ $exam->description ?: 'No description provided.' }}</p>
                    </div>

                    <div class="mb-4">
                        <h5>Instructions</h5>
                        <ul>
                            <li>This exam contains {{ $exam->questions->count() }} questions and must be completed within {{ $exam->duration }} minutes.</li>
                            <li>Once you start the exam, the timer will begin and cannot be paused.</li>
                            <li>You must achieve a score of {{ $exam->passing_score }}% or higher to pass this exam.</li>
                            <li>Make sure you have a stable internet connection before starting.</li>
                            <li>Do not refresh the page or navigate away during the exam.</li>
                        </ul>
                    </div>

                    @if($attempts->count() > 0)
                        <div class="mb-4">
                            <h5>Your Previous Attempts</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Attempt</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attempts as $index => $attempt)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $attempt->created_at->format('M d, Y, h:i A') }}</td>
                                                <td>
                                                    @if($attempt->completed_at)
                                                        <span class="badge bg-success">Completed</span>
                                                    @else
                                                        <span class="badge bg-warning">In Progress</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attempt->completed_at)
                                                        {{ number_format($attempt->score, 1) }}%
                                                        @if($attempt->passed)
                                                            <span class="badge bg-success">Passed</span>
                                                        @else
                                                            <span class="badge bg-danger">Failed</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attempt->completed_at)
                                                        <a href="{{ route('student.exams.result', $attempt->attempt_id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View Result
                                                        </a>
                                                    @else
                                                        <a href="{{ route('student.exams.take', $attempt->attempt_id) }}" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-pencil-alt"></i> Continue
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($isAvailable)
                            @if($attempts->where('completed_at', null)->count() > 0)
                                <a href="{{ route('student.exams.take', $attempts->where('completed_at', null)->first()->attempt_id) }}" class="btn btn-warning btn-lg">
                                    <i class="fas fa-pencil-alt"></i> Continue Exam
                                </a>
                            @else
                                <form action="{{ route('student.exams.start', $exam->exam_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-play-circle"></i> Start Exam
                                    </button>
                                </form>
                            @endif
                        @else
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-lock"></i> Exam Not Available
                            </button>
                        @endif
                        <a href="{{ route('student.exams.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Exams
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
