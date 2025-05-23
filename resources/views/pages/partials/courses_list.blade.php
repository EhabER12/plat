@if(count($courses) > 0)
    <div class="row g-4" id="coursesList">
        @foreach($courses as $course)
            <div class="col-lg-6 col-md-12">
                <div class="course-card">
                    <div class="course-image">
                        @if(isset($course->thumbnail) && !empty($course->thumbnail))
                            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}">
                        @else
                            <img src="https://img.freepik.com/free-photo/students-using-laptop-together_23-2149038413.jpg" alt="{{ $course->title }}">
                        @endif
                        <div class="course-category">{{ $course->category->name ?? 'Uncategorized' }}</div>
                        <div class="course-price">${{ $course->price }}</div>
                    </div>
                    <div class="course-content">
                        <h3 class="course-title">{{ $course->title }}</h3>
                        <p class="course-instructor">
                            <i class="fas fa-user-tie"></i> {{ $course->instructor->name ?? 'Unknown' }}
                        </p>
                        
                        @if(isset($course->ratings) || isset($course->reviews))
                            @php
                                $ratings = $course->ratings ?? $course->reviews ?? collect([]);
                                $avgRating = $ratings->avg('rating') ?? $ratings->avg('rating_value') ?? 0;
                                $avgRating = number_format($avgRating, 1);
                                $ratingsCount = $ratings->count();
                            @endphp
                            <div class="course-rating mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="stars-container me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($avgRating))
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($i - 0.5 <= $avgRating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div>
                                        <span class="fw-bold">{{ $avgRating }}</span>
                                        <span class="text-muted">({{ $ratingsCount }})</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="course-rating mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="stars-container me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="far fa-star text-warning"></i>
                                        @endfor
                                    </div>
                                    <div>
                                        <span class="fw-bold">0.0</span>
                                        <span class="text-muted">(0)</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="course-stats">
                            <div class="course-stat">
                                <i class="fas fa-users"></i>
                                <span>
                                    {{ $course->students_count ?? $course->enrollments_count ?? '0' }}
                                    {{ app()->getLocale() == 'ar' ? 'طالب' : 'students' }}
                                </span>
                            </div>
                            <div class="course-stat">
                                <i class="fas fa-clock"></i>
                                <span>
                                    @php
                                        $duration = $course->duration ?? 0;
                                        if (!$duration && isset($course->videos) && $course->videos->count() > 0) {
                                            $duration = ceil($course->videos->sum('duration') / 60);
                                        }
                                    @endphp
                                    {{ $duration }}
                                    {{ app()->getLocale() == 'ar' ? 'ساعة' : 'hours' }}
                                </span>
                            </div>
                        </div>
                        <p class="course-description">{{ \Illuminate\Support\Str::limit($course->description, 150) }}</p>
                    </div>
                    <div class="course-footer">
                        <span class="text-muted">{{ app()->getLocale() == 'ar' ? 'تم التحديث' : 'Updated' }} {{ \Carbon\Carbon::parse($course->updated_at ?? now())->diffForHumans() }}</span>
                        <a href="{{ url('/courses/' . ($course->course_id ?? $course->id ?? '')) }}" class="btn btn-outline-primary course-btn">
                            {{ app()->getLocale() == 'ar' ? 'عرض الدورة' : 'View Course' }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($courses instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center pagination" id="pagination">
            {{ $courses->appends(request()->except('page'))->links() }}
        </div>
    @endif
@else
    <div class="no-courses">
        <i class="fas fa-search"></i>
        <h3>{{ app()->getLocale() == 'ar' ? 'لم يتم العثور على دورات' : 'No Courses Found' }}</h3>
        <p>{{ app()->getLocale() == 'ar' ? 'لا توجد دورات تطابق معايير البحث الخاصة بك. حاول تعديل عوامل التصفية الخاصة بك.' : 'No courses match your criteria. Try adjusting your filters.' }}</p>
        <button type="button" class="btn btn-primary" id="resetAllFilters">
            <i class="fas fa-sync-alt me-2"></i>{{ app()->getLocale() == 'ar' ? 'إعادة ضبط المرشحات' : 'Reset Filters' }}
        </button>
    </div>
@endif 