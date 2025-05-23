@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title">{{ $course->title }}</h1>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <span class="badge bg-primary">{{ $course->category->name }}</span>
                        </div>
                        <div class="me-3">
                            <i class="bi bi-clock"></i> {{ $course->duration }} ساعة
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="ratings">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($course->average_rating))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="ms-1">({{ number_format($course->average_rating, 1) }})</span>
                            <span class="ms-2">{{ $course->reviews->count() }} تقييم</span>
                        </div>
                    </div>
                    
                    <p class="card-text">{{ $course->description }}</p>
                    
                    <!-- Instructor Information -->
                    <div class="mt-4">
                        <h4>المدرس</h4>
                        <div class="d-flex align-items-center">
                            <img src="{{ $course->instructor->profile_image ? asset('storage/' . $course->instructor->profile_image) : asset('images/default-profile.jpg') }}" 
                                 alt="{{ $course->instructor->name }}" class="rounded-circle me-2" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h5 class="mb-0">{{ $course->instructor->name }}</h5>
                                <p class="text-muted mb-0">{{ $course->instructor->email }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Course Content Overview -->
                    <div class="mt-4">
                        <h4>محتوى الكورس</h4>
                        <ul class="list-group">
                            @foreach($course->sections as $section)
                                <li class="list-group-item">
                                    <h5>{{ $section->title }}</h5>
                                    <ul class="list-unstyled ms-3">
                                        @foreach($section->videos as $video)
                                            <li class="mb-1">
                                                <i class="bi bi-play-circle"></i> {{ $video->title }}
                                                <span class="text-muted small">{{ $video->formatted_duration }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Section -->
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">التقييمات والمراجعات</h3>
                    
                    <!-- Reviews Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <h1 class="display-4">{{ number_format($course->average_rating, 1) }}</h1>
                            <div class="ratings mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($course->average_rating))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <p>{{ $course->reviews->count() }} تقييم</p>
                        </div>
                        <div class="col-md-8">
                            <!-- Rating Bars -->
                            @php
                                $ratings = [
                                    5 => $course->reviews->where('rating', 5)->count(),
                                    4 => $course->reviews->where('rating', 4)->count(),
                                    3 => $course->reviews->where('rating', 3)->count(),
                                    2 => $course->reviews->where('rating', 2)->count(),
                                    1 => $course->reviews->where('rating', 1)->count(),
                                ];
                                $totalCount = $course->reviews->count() ?: 1;
                            @endphp
                            
                            @for($i = 5; $i >= 1; $i--)
                                <div class="d-flex align-items-center mb-1">
                                    <div class="me-2">{{ $i }} <i class="bi bi-star-fill text-warning"></i></div>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ ($ratings[$i] / $totalCount) * 100 }}%" 
                                             aria-valuenow="{{ ($ratings[$i] / $totalCount) * 100 }}" 
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="ms-2">{{ $ratings[$i] }}</div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    
                    <!-- Write Review Form (only for enrolled students) -->
                    @if($isEnrolled)
                        <div class="mb-4">
                            <h4>أضف تقييمك</h4>
                            <form action="{{ route('student.review', $course->course_id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">تقييمك</label>
                                    <div class="rating-stars">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="rating-input">
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" 
                                                            {{ old('rating', $userReview ? $userReview->rating : 0) == $i ? 'checked' : '' }}>
                                                        <label for="star{{ $i }}">
                                                            <i class="bi bi-star-fill"></i>
                                                        </label>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">تعليقك</label>
                                    <textarea name="comment" id="comment" rows="3" class="form-control @error('comment') is-invalid @enderror">{{ old('comment', $userReview ? $userReview->review : '') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $errors->first('comment') }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">إرسال التقييم</button>
                            </form>
                        </div>
                    @endif
                    
                    <!-- List of Reviews -->
                    <div>
                        <h4>تقييمات الطلاب</h4>
                        @if($course->reviews->count() > 0)
                            @foreach($course->reviews->sortByDesc('created_at') as $review)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $review->user->profile_image ? asset('storage/' . $review->user->profile_image) : asset('images/default-profile.jpg') }}" 
                                                     alt="{{ $review->user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">{{ $review->user->name }}</h6>
                                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                                </div>
                                            </div>
                                            <div class="ratings">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    @else
                                                        <i class="bi bi-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="mb-0">{{ $review->review }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                لا توجد تقييمات حتى الآن، كن أول من يقيم هذا الكورس!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4 sticky-top" style="top: 20px;">
                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('images/default-course-thumbnail.jpg') }}" 
                     class="card-img-top" alt="{{ $course->title }}">
                <div class="card-body">
                    <div class="mb-3">
                        @if($course->getDiscountedPrice() < $course->price)
                            <h3 class="card-title mb-0">{{ number_format($course->getDiscountedPrice(), 2) }} جنيه</h3>
                            <p class="text-muted"><del>{{ number_format($course->price, 2) }} جنيه</del></p>
                        @else
                            <h3 class="card-title mb-0">{{ number_format($course->price, 2) }} جنيه</h3>
                        @endif
                    </div>
                    
                    @if($isEnrolled)
                        <a href="{{ route('student.course-content', $course->course_id) }}" class="btn btn-success w-100 mb-3">
                            الذهاب إلى محتوى الكورس
                        </a>
                    @else
                        <form action="{{ route('student.enroll', $course->course_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 mb-3">الاشتراك في الكورس</button>
                        </form>
                        
                        <a href="{{ route('payment.checkout', $course->course_id) }}" class="btn btn-outline-primary w-100">
                            شراء الآن
                        </a>
                    @endif
                    
                    <hr>
                    
                    <div class="course-features">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-film"></i> عدد الفيديوهات</span>
                            <span>{{ $course->videos->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-clock"></i> المدة الإجمالية</span>
                            <span>{{ $course->duration }} ساعة</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-people"></i> عدد الطلاب</span>
                            <span>{{ $course->enrollments->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-bar-chart"></i> المستوى</span>
                            <span>{{ $course->level }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-translate"></i> اللغة</span>
                            <span>{{ $course->language }}</span>
                        </div>
                        @if($course->certificate_available)
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="bi bi-award"></i> شهادة</span>
                                <span>متاح</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    
    .rating-input input {
        display: none;
    }
    
    .rating-input label {
        font-size: 24px;
        color: #ddd;
        cursor: pointer;
        margin-right: 5px;
    }
    
    .rating-input label:hover,
    .rating-input label:hover ~ label,
    .rating-input input:checked ~ label {
        color: #ffc107;
    }
</style>
@endsection 