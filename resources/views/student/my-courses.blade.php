@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">My Courses</h1>

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

            @if(isset($enrolledCourses) && is_countable($enrolledCourses) && count($enrolledCourses) > 0)
                <div class="row">
                    @foreach($enrolledCourses as $enrollment)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="bg-light text-center py-4">
                                    <i class="fas fa-book fa-3x text-primary"></i>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $enrollment->title }}</h5>
                                    <p class="card-text">{{ Str::limit($enrollment->description, 100) }}</p>

                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="badge bg-primary">{{ $enrollment->category->name ?? 'Uncategorized' }}</span>
                                        <span class="text-muted small">Enrolled: {{ \Carbon\Carbon::parse($enrollment->enrolled_at)->format('M d, Y') }}</span>
                                    </div>

                                    @php
                                        // Get average rating
                                        $reviews = $enrollment->reviews ?? collect([]);
                                        $avgRating = $reviews->avg('rating') ?? 0;
                                        $avgRating = number_format($avgRating, 1);
                                    @endphp
                                    
                                    <div class="course-rating mb-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($avgRating))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($i - 0.5 <= $avgRating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2">{{ $avgRating }} ({{ $reviews->count() }})</span>
                                    </div>

                                    @php
                                        $progress = 0; // This should be calculated based on completed lessons
                                    @endphp

                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <span class="text-muted">By {{ $enrollment->instructor->name }}</span>
                                    <a href="{{ route('student.course-content', $enrollment->course_id) }}" class="btn btn-sm btn-primary">Continue Learning</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $enrolledCourses->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <p>You haven't enrolled in any courses yet.</p>
                    <a href="{{ url('/courses') }}" class="btn btn-primary mt-3">Browse Courses</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection