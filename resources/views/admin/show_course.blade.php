@extends('admin.layout')

@section('title', 'تفاصيل الدورة: ' . $course->title)
@section('page-title', 'تفاصيل الدورة')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="card-title">{{ $course->title }}</h3>
                        <div>
                            <a href="{{ route('admin.courses.edit', $course->course_id) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> تعديل الدورة
                            </a>
                            <a href="{{ route('course.detail', $course->course_id) }}" target="_blank" class="btn btn-outline-primary ms-2">
                                <i class="fas fa-eye me-1"></i> معاينة الدورة
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5 class="text-muted mb-3">وصف الدورة</h5>
                                <p>{{ $course->description }}</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">معلومات الدورة</h5>
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th style="width: 150px;">المستوى</th>
                                                <td>
                                                    @if($course->level == 'beginner')
                                                        <span class="badge bg-success">مبتدئ</span>
                                                    @elseif($course->level == 'intermediate')
                                                        <span class="badge bg-primary">متوسط</span>
                                                    @else
                                                        <span class="badge bg-danger">متقدم</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>المدة</th>
                                                <td>{{ $course->duration ?? 'غير محدد' }} ساعة</td>
                                            </tr>
                                            <tr>
                                                <th>اللغة</th>
                                                <td>{{ $course->language }}</td>
                                            </tr>
                                            <tr>
                                                <th>التصنيف</th>
                                                <td>{{ $course->category->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>السعر</th>
                                                <td>{{ $course->price }} $</td>
                                            </tr>
                                            <tr>
                                                <th>تاريخ الإنشاء</th>
                                                <td>{{ $course->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                            <tr>
                                                <th>آخر تحديث</th>
                                                <td>{{ $course->updated_at->format('Y-m-d') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-3">إحصائيات الدورة</h5>
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th style="width: 150px;">الطلاب المسجلين</th>
                                                <td>{{ $enrollmentsCount }}</td>
                                            </tr>
                                            <tr>
                                                <th>عدد الفيديوهات</th>
                                                <td>{{ $videoCount }}</td>
                                            </tr>
                                            <tr>
                                                <th>عدد المواد التعليمية</th>
                                                <td>{{ $materialCount }}</td>
                                            </tr>
                                            <tr>
                                                <th>التقييم</th>
                                                <td>
                                                    @if(isset($ratings->average_rating))
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                {{ number_format($ratings->average_rating, 1) }}
                                                            </div>
                                                            <div class="text-warning">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= round($ratings->average_rating))
                                                                        <i class="fas fa-star"></i>
                                                                    @elseif($i - 0.5 <= $ratings->average_rating)
                                                                        <i class="fas fa-star-half-alt"></i>
                                                                    @else
                                                                        <i class="far fa-star"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            <div class="ms-2 text-muted small">
                                                                ({{ $ratings->total_reviews }} تقييم)
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">لا توجد تقييمات بعد</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>الحالة</th>
                                                <td>
                                                    @if($course->approval_status == 'approved')
                                                        <span class="badge bg-success">معتمدة</span>
                                                    @elseif($course->approval_status == 'pending')
                                                        <span class="badge bg-warning">قيد المراجعة</span>
                                                    @else
                                                        <span class="badge bg-danger">مرفوضة</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>مميزة</th>
                                                <td>
                                                    @if($course->featured)
                                                        <span class="badge bg-primary">نعم</span>
                                                    @else
                                                        <span class="badge bg-secondary">لا</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">معلومات المدرس</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            @if($course->instructor->profile_image)
                                                <img src="{{ asset($course->instructor->profile_image) }}" alt="{{ $course->instructor->name }}" class="rounded-circle" width="60" height="60">
                                            @else
                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 60px; height: 60px;">
                                                    {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $course->instructor->name }}</h5>
                                            <p class="text-muted mb-0">{{ $course->instructor->email }}</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <a href="{{ route('admin.users.edit', $course->instructor->user_id) }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-user-edit me-1"></i> عرض ملف المدرس
                                        </a>
                                    </div>
                                    <div class="mb-0">
                                        <h6 class="text-muted mb-2">دورات أخرى للمدرس</h6>
                                        <div class="list-group list-group-flush">
                                            @php
                                                $otherCourses = \App\Models\Course::where('instructor_id', $course->instructor_id)
                                                    ->where('course_id', '!=', $course->course_id)
                                                    ->take(3)
                                                    ->get();
                                            @endphp

                                            @forelse($otherCourses as $otherCourse)
                                                <a href="{{ route('admin.courses.show', $otherCourse->course_id) }}" class="list-group-item list-group-item-action">
                                                    {{ $otherCourse->title }}
                                                </a>
                                            @empty
                                                <div class="text-muted small">لا توجد دورات أخرى</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($course->image_path)
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">صورة الدورة</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <img src="{{ asset($course->image_path) }}" alt="{{ $course->title }}" class="img-fluid">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قسم فيديوهات الدورة -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">فيديوهات الدورة</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                        <i class="fas fa-plus-circle me-2"></i> إضافة فيديو
                    </button>
                </div>
                <div class="card-body">
                    @php
                        $videos = \App\Models\CourseVideo::where('course_id', $course->course_id)->orderBy('sequence_order')->get();
                    @endphp

                    @if(count($videos) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>العنوان</th>
                                        <th>المدة</th>
                                        <th>النوع</th>
                                        <th>تاريخ الإضافة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($videos as $index => $video)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $video->title }}</td>
                                            <td>
                                                @if(isset($video->duration_seconds))
                                                    {{ gmdate("i:s", $video->duration_seconds) }}
                                                @else
                                                    غير محدد
                                                @endif
                                            </td>
                                            <td>
                                                @if($video->video_path)
                                                    <span class="badge bg-primary">ملف مرفوع</span>
                                                @else
                                                    <span class="badge bg-info">رابط خارجي</span>
                                                @endif
                                            </td>
                                            <td>{{ $video->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-info preview-video" data-id="{{ $video->video_id }}" data-path="{{ $video->video_path }}" data-url="{{ $video->video_url }}">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-warning edit-video" data-id="{{ $video->video_id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-video" data-id="{{ $video->video_id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-video fa-3x text-muted"></i>
                            </div>
                            <h5>لا توجد فيديوهات مضافة بعد</h5>
                            <p class="text-muted">قم بإضافة فيديوهات للدورة باستخدام زر "إضافة فيديو"</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة فيديو -->
<div class="modal fade" id="addVideoModal" tabindex="-1" aria-labelledby="addVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVideoModalLabel">إضافة فيديو جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVideoForm" action="{{ route('admin.courses.videos.store', $course->course_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="upload-progress-container" class="mb-3 d-none">
                        <label class="form-label">جاري التحميل... <span id="upload-percentage">0%</span></label>
                        <div class="progress">
                            <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div id="upload-success" class="alert alert-success d-none">
                        تم تحميل الفيديو بنجاح!
                    </div>
                    <div id="upload-error" class="alert alert-danger d-none">
                        حدث خطأ أثناء تحميل الفيديو. يرجى المحاولة مرة أخرى.
                    </div>
                    <div class="mb-3">
                        <label for="videoTitle" class="form-label">عنوان الفيديو <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="videoTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="videoDescription" class="form-label">وصف الفيديو</label>
                        <textarea class="form-control" id="videoDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">نوع الفيديو <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="videoTypeUpload" value="upload" checked>
                            <label class="form-check-label" for="videoTypeUpload">
                                رفع ملف فيديو
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="videoTypeExternal" value="external">
                            <label class="form-check-label" for="videoTypeExternal">
                                رابط فيديو خارجي (YouTube, Vimeo, إلخ)
                            </label>
                        </div>
                    </div>

                    <div id="uploadVideoSection" class="mb-3">
                        <label for="videoFile" class="form-label">ملف الفيديو <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="videoFile" name="video_file" accept="video/mp4,video/webm,video/mov">
                        <small class="text-muted">الحد الأقصى لحجم الملف: 200 ميجابايت. الصيغ المدعومة: MP4, WebM, MOV.</small>
                    </div>

                    <div id="externalVideoSection" class="mb-3 d-none">
                        <label for="videoUrl" class="form-label">رابط الفيديو <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="videoUrl" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                        <small class="text-muted">أدخل رابط صالح من YouTube أو Vimeo أو أي منصة فيديو أخرى.</small>
                    </div>

                    <div class="mb-3">
                        <label for="videoDuration" class="form-label">مدة الفيديو (بالثواني) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="videoDuration" name="duration_seconds" min="1" required>
                        <small class="text-muted">أدخل مدة الفيديو بالثواني. مثال: 300 ثانية = 5 دقائق.</small>
                    </div>

                    <div class="mb-3">
                        <label for="videoOrder" class="form-label">ترتيب الفيديو</label>
                        <input type="number" class="form-control" id="videoOrder" name="sequence_order" min="0" value="0">
                        <small class="text-muted">ترتيب عرض الفيديو في قائمة الفيديوهات. 0 = الأول.</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="isFreePreview" name="is_free_preview" value="1">
                        <label class="form-check-label" for="isFreePreview">عرض مجاني (يمكن للطلاب مشاهدته بدون تسجيل)</label>
                    </div>

                    <div class="mb-3">
                        <label for="videoThumbnail" class="form-label">صورة مصغرة للفيديو</label>
                        <input type="file" class="form-control" id="videoThumbnail" name="thumbnail" accept="image/*">
                        <small class="text-muted">الحد الأقصى لحجم الملف: 5 ميجابايت. الأبعاد المقترحة: 1280×720 بكسل.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelUploadBtn">إلغاء</button>
                <button type="button" id="uploadVideoBtn" class="btn btn-primary">إضافة الفيديو</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal معاينة الفيديو -->
<div class="modal fade" id="previewVideoModal" tabindex="-1" aria-labelledby="previewVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewVideoModalLabel">معاينة الفيديو</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="videoPreviewContainer" class="ratio ratio-16x9">
                    <!-- سيتم إضافة الفيديو هنا بواسطة JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تحرير الفيديو -->
<div class="modal fade" id="editVideoModal" tabindex="-1" aria-labelledby="editVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVideoModalLabel">تحرير الفيديو</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editVideoForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editVideoId" name="video_id">

                    <div class="mb-3">
                        <label for="editVideoTitle" class="form-label">عنوان الفيديو <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editVideoTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="editVideoDescription" class="form-label">وصف الفيديو</label>
                        <textarea class="form-control" id="editVideoDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">نوع الفيديو <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="editVideoTypeUnchanged" value="unchanged" checked>
                            <label class="form-check-label" for="editVideoTypeUnchanged">
                                الاحتفاظ بالفيديو الحالي
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="editVideoTypeUpload" value="upload">
                            <label class="form-check-label" for="editVideoTypeUpload">
                                رفع ملف فيديو جديد
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="editVideoTypeExternal" value="external">
                            <label class="form-check-label" for="editVideoTypeExternal">
                                رابط فيديو خارجي (YouTube, Vimeo, إلخ)
                            </label>
                        </div>
                    </div>

                    <div id="editUploadVideoSection" class="mb-3 d-none">
                        <label for="editVideoFile" class="form-label">ملف الفيديو <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="editVideoFile" name="video_file" accept="video/mp4,video/webm,video/mov">
                        <small class="text-muted">الحد الأقصى لحجم الملف: 200 ميجابايت. الصيغ المدعومة: MP4, WebM, MOV.</small>
                    </div>

                    <div id="editExternalVideoSection" class="mb-3 d-none">
                        <label for="editVideoUrl" class="form-label">رابط الفيديو <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="editVideoUrl" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                        <small class="text-muted">أدخل رابط صالح من YouTube أو Vimeo أو أي منصة فيديو أخرى.</small>
                    </div>

                    <div class="mb-3">
                        <label for="editVideoDuration" class="form-label">مدة الفيديو (بالثواني) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="editVideoDuration" name="duration_seconds" min="1" required>
                        <small class="text-muted">أدخل مدة الفيديو بالثواني. مثال: 300 ثانية = 5 دقائق.</small>
                    </div>

                    <div class="mb-3">
                        <label for="editVideoOrder" class="form-label">ترتيب الفيديو</label>
                        <input type="number" class="form-control" id="editVideoOrder" name="sequence_order" min="0" value="0">
                        <small class="text-muted">ترتيب عرض الفيديو في قائمة الفيديوهات. 0 = الأول.</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="editIsFreePreview" name="is_free_preview" value="1">
                        <label class="form-check-label" for="editIsFreePreview">عرض مجاني (يمكن للطلاب مشاهدته بدون تسجيل)</label>
                    </div>

                    <div class="mb-3">
                        <label for="editVideoThumbnail" class="form-label">صورة مصغرة للفيديو</label>
                        <input type="file" class="form-control" id="editVideoThumbnail" name="thumbnail" accept="image/*">
                        <small class="text-muted">الحد الأقصى لحجم الملف: 5 ميجابايت. الأبعاد المقترحة: 1280×720 بكسل.</small>
                        <div id="currentThumbnail" class="mt-2 d-none">
                            <p class="mb-1">الصورة المصغرة الحالية:</p>
                            <img src="" alt="الصورة المصغرة الحالية" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" form="editVideoForm" class="btn btn-primary">حفظ التغييرات</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // متغيرات للتحكم في تحميل الفيديو
        const addVideoForm = document.getElementById('addVideoForm');
        const uploadVideoBtn = document.getElementById('uploadVideoBtn');
        const cancelUploadBtn = document.getElementById('cancelUploadBtn');
        const uploadProgressContainer = document.getElementById('upload-progress-container');
        const uploadProgressBar = document.getElementById('upload-progress-bar');
        const uploadPercentage = document.getElementById('upload-percentage');
        const uploadSuccess = document.getElementById('upload-success');
        const uploadError = document.getElementById('upload-error');
        let xhr; // لتخزين طلب XMLHttpRequest

        // معالجة نقر زر التحميل
        if (uploadVideoBtn) {
            uploadVideoBtn.addEventListener('click', function() {
                // التحقق من صحة النموذج
                if (!addVideoForm.checkValidity()) {
                    addVideoForm.reportValidity();
                    return;
                }

                // إعادة تعيين شريط التقدم
                uploadProgressBar.style.width = '0%';
                uploadProgressBar.setAttribute('aria-valuenow', 0);
                uploadPercentage.textContent = '0%';

                // إخفاء رسائل النجاح والخطأ
                uploadSuccess.classList.add('d-none');
                uploadError.classList.add('d-none');

                // إظهار شريط التقدم
                uploadProgressContainer.classList.remove('d-none');

                // إنشاء كائن FormData لإرسال البيانات
                const formData = new FormData(addVideoForm);

                // إنشاء طلب AJAX
                xhr = new XMLHttpRequest();

                // مراقبة تقدم التحميل
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        uploadProgressBar.style.width = percentComplete + '%';
                        uploadProgressBar.setAttribute('aria-valuenow', percentComplete);
                        uploadPercentage.textContent = percentComplete + '%';
                    }
                });

                // معالجة الاستجابة
                xhr.onload = function() {
                    if (xhr.status === 200 || xhr.status === 201) {
                        // نجاح التحميل
                        uploadSuccess.classList.remove('d-none');

                        // إعادة تحميل الصفحة بعد ثانيتين
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else {
                        // فشل التحميل
                        uploadError.classList.remove('d-none');
                        uploadProgressContainer.classList.add('d-none');
                    }
                };

                // معالجة الأخطاء
                xhr.onerror = function() {
                    uploadError.classList.remove('d-none');
                    uploadProgressContainer.classList.add('d-none');
                };

                // إرسال الطلب
                xhr.open('POST', addVideoForm.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            });
        }

        // إلغاء التحميل
        if (cancelUploadBtn) {
            cancelUploadBtn.addEventListener('click', function() {
                if (xhr && xhr.readyState !== 4) {
                    xhr.abort();
                }
            });
        }

        // التبديل بين أقسام إدخال الفيديو بناءً على النوع المحدد
        const videoTypeUpload = document.getElementById('videoTypeUpload');
        const videoTypeExternal = document.getElementById('videoTypeExternal');
        const uploadSection = document.getElementById('uploadVideoSection');
        const externalSection = document.getElementById('externalVideoSection');
        const videoFileInput = document.getElementById('videoFile');
        const videoUrlInput = document.getElementById('videoUrl');

        if (videoTypeUpload && videoTypeExternal) {
            videoTypeUpload.addEventListener('change', function() {
                if (this.checked) {
                    uploadSection.classList.remove('d-none');
                    externalSection.classList.add('d-none');
                    videoFileInput.setAttribute('required', 'required');
                    videoUrlInput.removeAttribute('required');
                }
            });

            videoTypeExternal.addEventListener('change', function() {
                if (this.checked) {
                    uploadSection.classList.add('d-none');
                    externalSection.classList.remove('d-none');
                    videoFileInput.removeAttribute('required');
                    videoUrlInput.setAttribute('required', 'required');
                }
            });
        }

        // معاينة الفيديو
        const previewButtons = document.querySelectorAll('.preview-video');
        const previewModal = document.getElementById('previewVideoModal');
        const previewContainer = document.getElementById('videoPreviewContainer');

        if (previewButtons.length > 0 && previewModal && previewContainer) {
            previewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoPath = this.getAttribute('data-path');
                    const videoUrl = this.getAttribute('data-url');

                    // تفريغ الحاوية
                    previewContainer.innerHTML = '';

                    if (videoPath) {
                        // إنشاء عنصر فيديو للملفات المرفوعة
                        const video = document.createElement('video');
                        video.controls = true;
                        video.style.width = '100%';
                        video.style.height = '100%';

                        const source = document.createElement('source');
                        source.src = '/' + videoPath;
                        source.type = 'video/mp4';

                        video.appendChild(source);
                        previewContainer.appendChild(video);
                    } else if (videoUrl) {
                        // التعامل مع روابط YouTube
                        if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                            let youtubeId = '';

                            if (videoUrl.includes('youtube.com/watch?v=')) {
                                youtubeId = videoUrl.split('v=')[1].split('&')[0];
                            } else if (videoUrl.includes('youtu.be/')) {
                                youtubeId = videoUrl.split('youtu.be/')[1].split('?')[0];
                            }

                            if (youtubeId) {
                                const iframe = document.createElement('iframe');
                                iframe.src = `https://www.youtube.com/embed/${youtubeId}`;
                                iframe.width = '100%';
                                iframe.height = '100%';
                                iframe.frameBorder = '0';
                                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                                iframe.allowFullscreen = true;

                                previewContainer.appendChild(iframe);
                            }
                        }
                        // التعامل مع روابط Vimeo
                        else if (videoUrl.includes('vimeo.com')) {
                            let vimeoId = videoUrl.split('vimeo.com/')[1].split('?')[0];

                            if (vimeoId) {
                                const iframe = document.createElement('iframe');
                                iframe.src = `https://player.vimeo.com/video/${vimeoId}`;
                                iframe.width = '100%';
                                iframe.height = '100%';
                                iframe.frameBorder = '0';
                                iframe.allow = 'autoplay; fullscreen';
                                iframe.allowFullscreen = true;

                                previewContainer.appendChild(iframe);
                            }
                        }
                        // روابط أخرى
                        else {
                            const message = document.createElement('div');
                            message.className = 'alert alert-info';
                            message.textContent = 'لا يمكن معاينة هذا النوع من الفيديو. الرابط: ' + videoUrl;

                            previewContainer.appendChild(message);
                        }
                    } else {
                        const message = document.createElement('div');
                        message.className = 'alert alert-warning';
                        message.textContent = 'لا يوجد فيديو متاح للمعاينة.';

                        previewContainer.appendChild(message);
                    }

                    // عرض النافذة المنبثقة
                    const modal = new bootstrap.Modal(previewModal);
                    modal.show();
                });
            });
        }

        // تحرير الفيديو
        const editButtons = document.querySelectorAll('.edit-video');
        const editModal = document.getElementById('editVideoModal');
        const editForm = document.getElementById('editVideoForm');
        const editVideoId = document.getElementById('editVideoId');
        const editVideoTitle = document.getElementById('editVideoTitle');
        const editVideoDescription = document.getElementById('editVideoDescription');
        const editVideoDuration = document.getElementById('editVideoDuration');
        const editVideoOrder = document.getElementById('editVideoOrder');
        const editIsFreePreview = document.getElementById('editIsFreePreview');
        const editVideoTypeUnchanged = document.getElementById('editVideoTypeUnchanged');
        const editVideoTypeUpload = document.getElementById('editVideoTypeUpload');
        const editVideoTypeExternal = document.getElementById('editVideoTypeExternal');
        const editUploadSection = document.getElementById('editUploadVideoSection');
        const editExternalSection = document.getElementById('editExternalVideoSection');
        const editVideoFile = document.getElementById('editVideoFile');
        const editVideoUrl = document.getElementById('editVideoUrl');
        const currentThumbnail = document.getElementById('currentThumbnail');

        if (editButtons.length > 0 && editModal) {
            // التبديل بين أقسام إدخال الفيديو في نموذج التحرير
            if (editVideoTypeUnchanged && editVideoTypeUpload && editVideoTypeExternal) {
                editVideoTypeUnchanged.addEventListener('change', function() {
                    if (this.checked) {
                        editUploadSection.classList.add('d-none');
                        editExternalSection.classList.add('d-none');
                        editVideoFile.removeAttribute('required');
                        editVideoUrl.removeAttribute('required');
                    }
                });

                editVideoTypeUpload.addEventListener('change', function() {
                    if (this.checked) {
                        editUploadSection.classList.remove('d-none');
                        editExternalSection.classList.add('d-none');
                        editVideoFile.setAttribute('required', 'required');
                        editVideoUrl.removeAttribute('required');
                    }
                });

                editVideoTypeExternal.addEventListener('change', function() {
                    if (this.checked) {
                        editUploadSection.classList.add('d-none');
                        editExternalSection.classList.remove('d-none');
                        editVideoFile.removeAttribute('required');
                        editVideoUrl.setAttribute('required', 'required');
                    }
                });
            }

            // معالجة نقر زر التحرير
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-id');

                    // الحصول على بيانات الفيديو باستخدام AJAX
                    fetch(`{{ url('admin/courses/' . $course->course_id . '/videos') }}/${videoId}/edit`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.video) {
                            const video = data.video;

                            // تعيين عنوان URL للنموذج
                            editForm.action = `{{ url('admin/courses/' . $course->course_id . '/videos') }}/${videoId}`;

                            // ملء النموذج ببيانات الفيديو
                            editVideoId.value = video.video_id;
                            editVideoTitle.value = video.title;
                            editVideoDescription.value = video.description || '';
                            editVideoDuration.value = video.duration_seconds;
                            editVideoOrder.value = video.sequence_order;
                            editIsFreePreview.checked = video.is_free_preview;

                            // إذا كان هناك صورة مصغرة
                            if (video.thumbnail_url) {
                                currentThumbnail.classList.remove('d-none');
                                currentThumbnail.querySelector('img').src = '/' + video.thumbnail_url;
                            } else {
                                currentThumbnail.classList.add('d-none');
                            }

                            // عرض النافذة المنبثقة
                            const modal = new bootstrap.Modal(editModal);
                            modal.show();
                        } else {
                            alert('حدث خطأ أثناء تحميل بيانات الفيديو.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء تحميل بيانات الفيديو.');
                    });
                });
            });
        }

        // حذف الفيديو
        const deleteButtons = document.querySelectorAll('.delete-video');

        if (deleteButtons.length > 0) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-id');

                    if (confirm('هل أنت متأكد من رغبتك في حذف هذا الفيديو؟ لا يمكن التراجع عن هذا الإجراء.')) {
                        // إنشاء نموذج حذف وإرساله
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ url('admin/courses/' . $course->course_id . '/videos') }}/${videoId}`;
                        form.style.display = 'none';

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(method);
                        document.body.appendChild(form);

                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endsection
