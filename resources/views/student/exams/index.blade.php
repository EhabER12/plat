@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">My Exams</h1>

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

    <!-- Debug Information -->
    @isset($debug)
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Debug Information</h5>
        </div>
        <div class="card-body">
            <p><strong>Student ID:</strong> {{ $debug['student_id'] }}</p>
            <p><strong>Enrolled Courses:</strong> {{ count($debug['enrolled_courses']) }} courses</p>
            <p><strong>Enrolled Course IDs:</strong> {{ implode(', ', $debug['enrolled_courses']) }}</p>
            <p><strong>Exams Count:</strong> {{ $debug['exam_count'] }}</p>
            
            @if($debug['exam_count'] > 0)
                <h6>Exams Details:</h6>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Course ID</th>
                            <th>Course Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($debug['exams'] as $exam)
                            <tr>
                                <td>{{ $exam['id'] }}</td>
                                <td>{{ $exam['title'] }}</td>
                                <td>{{ $exam['course_id'] }}</td>
                                <td>{{ $exam['course'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    @endisset

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Available Exams</h5>
                </div>
                <div class="card-body">
                    @if($exams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Course</th>
                                        <th>Duration</th>
                                        <th>Availability</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exams as $exam)
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $isAvailable = true;
                                            $availabilityMessage = '';
                                            
                                            if ($exam->available_from && $now->lt($exam->available_from)) {
                                                $isAvailable = false;
                                                $availabilityMessage = 'Available from ' . $exam->available_from->format('M d, Y, h:i A');
                                            } elseif ($exam->available_to && $now->gt($exam->available_to)) {
                                                $isAvailable = false;
                                                $availabilityMessage = 'Expired on ' . $exam->available_to->format('M d, Y, h:i A');
                                            } else {
                                                if ($exam->available_from && $exam->available_to) {
                                                    $availabilityMessage = 'Available until ' . $exam->available_to->format('M d, Y, h:i A');
                                                } elseif ($exam->available_to) {
                                                    $availabilityMessage = 'Available until ' . $exam->available_to->format('M d, Y, h:i A');
                                                } elseif ($exam->available_from) {
                                                    $availabilityMessage = 'Available since ' . $exam->available_from->format('M d, Y, h:i A');
                                                } else {
                                                    $availabilityMessage = 'Always available';
                                                }
                                            }
                                            
                                            $attempt = $attempts[$exam->exam_id] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $exam->title }}</td>
                                            <td>{{ $exam->course->title }}</td>
                                            <td>{{ $exam->duration }} minutes</td>
                                            <td>{{ $availabilityMessage }}</td>
                                            <td>
                                                @if($attempt)
                                                    @if($attempt->completed_at)
                                                        @if($attempt->passed)
                                                            <span class="badge bg-success">Passed ({{ number_format($attempt->score, 1) }}%)</span>
                                                        @else
                                                            <span class="badge bg-danger">Failed ({{ number_format($attempt->score, 1) }}%)</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-warning">In Progress</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Not Attempted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt && $attempt->completed_at)
                                                    <a href="{{ route('student.exams.result', $attempt->attempt_id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View Result
                                                    </a>
                                                @elseif($attempt && !$attempt->completed_at)
                                                    <a href="{{ route('student.exams.take', $attempt->attempt_id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-pencil-alt"></i> Continue Exam
                                                    </a>
                                                @elseif($isAvailable)
                                                    <a href="{{ route('student.exams.show', $exam->exam_id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-file-alt"></i> Start Exam
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="fas fa-lock"></i> Not Available
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <img src="{{ asset('images/empty-state.svg') }}" alt="No exams" class="img-fluid mb-3" style="max-height: 200px;">
                            <h3>No Exams Available</h3>
                            <p class="text-muted">There are no exams available for your enrolled courses at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
