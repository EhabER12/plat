@extends('layouts.instructor')

@section('title', 'Create New Course')
@section('page-title', 'Create New Course')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Choose a clear, descriptive title that accurately represents your course.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Course Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Provide a comprehensive description of what students will learn in your course.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Set a price that reflects the value of your course content. Enter 0 for a free course.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="thumbnail" class="form-label">Course Thumbnail</label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail">
                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Recommended size: 1280x720 pixels. Max file size: 2MB. Supported formats: JPG, PNG.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-certificate me-2"></i> إعدادات الشهادة
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="certificate_available" name="certificate_available" {{ old('certificate_available') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="certificate_available">تفعيل الشهادة للطلاب بعد إكمال الدورة</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">عند تفعيل هذا الخيار، سيتمكن الطلاب من الحصول على شهادة بعد إكمال جميع محتويات الدورة</small>
                                        </div>

                                        <div id="certificate_options" class="{{ old('certificate_available') ? '' : 'd-none' }}">
                                            <div class="mb-3">
                                                <label class="form-label">نوع الشهادة</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input certificate-type" type="radio" name="certificate_type" id="certificate_type_default" value="default" {{ old('certificate_type', 'default') == 'default' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="certificate_type_default">
                                                                استخدام قالب الشهادة الافتراضي من النظام
                                                            </label>
                                                        </div>
                                                        <div class="ms-4 mt-2 mb-3">
                                                            <img src="{{ asset('images/default-certificate.png') }}" alt="Default Certificate" class="img-thumbnail" style="max-height: 150px;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input certificate-type" type="radio" name="certificate_type" id="certificate_type_custom" value="custom" {{ old('certificate_type') == 'custom' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="certificate_type_custom">
                                                                تحميل قالب شهادة مخصص
                                                            </label>
                                                        </div>
                                                        
                                                        <div id="custom_certificate_upload" class="ms-4 mt-2 mb-3 {{ old('certificate_type') == 'custom' ? '' : 'd-none' }}">
                                                            <input type="file" class="form-control @error('custom_certificate') is-invalid @enderror" id="custom_certificate" name="custom_certificate">
                                                            <small class="text-muted">قم بتحميل صورة قالب الشهادة. الحجم الموصى به: 1500x1000 بكسل، بتنسيق PNG أو JPEG.</small>
                                                            <small class="d-block text-muted mt-1">ملاحظة: سيتم إضافة اسم الطالب وتاريخ إكمال الدورة واسم الدورة تلقائياً.</small>
                                                            @error('custom_certificate')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="certificate_text" class="form-label">نص الشهادة (اختياري)</label>
                                                <textarea class="form-control @error('certificate_text') is-invalid @enderror" id="certificate_text" name="certificate_text" rows="2">{{ old('certificate_text') }}</textarea>
                                                <small class="text-muted">يمكنك إضافة نص مخصص سيظهر في الشهادة. استخدم {student_name} و {course_title} و {completion_date} كمتغيرات.</small>
                                                @error('certificate_text')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('instructor.courses') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Course Creation Guide</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i> Tips</h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Use a clear, specific title that includes keywords students might search for.</li>
                            <li class="mb-2">Write a detailed description that explains what students will learn and why it's valuable.</li>
                            <li class="mb-2">Select the most relevant category for better discovery.</li>
                            <li>Price your course based on the depth of content, your expertise, and market rates.</li>
                        </ul>
                    </div>

                    <div class="mb-0">
                        <h6 class="fw-bold"><i class="fas fa-info-circle text-primary me-2"></i> What's Next?</h6>
                        <p class="small mb-0">
                            After creating your course, you'll be able to add video lessons, downloadable materials, and set up quizzes.
                            Your course will be submitted for admin approval before being published.
                        </p>
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
        // تبديل ظهور خيارات الشهادة عند تفعيل/تعطيل الشهادة
        const certificateAvailable = document.getElementById('certificate_available');
        const certificateOptions = document.getElementById('certificate_options');
        
        certificateAvailable.addEventListener('change', function() {
            if (this.checked) {
                certificateOptions.classList.remove('d-none');
            } else {
                certificateOptions.classList.add('d-none');
            }
        });
        
        // تبديل ظهور خيارات تحميل الشهادة المخصصة
        const certificateTypeRadios = document.querySelectorAll('.certificate-type');
        const customCertificateUpload = document.getElementById('custom_certificate_upload');
        
        certificateTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customCertificateUpload.classList.remove('d-none');
                } else {
                    customCertificateUpload.classList.add('d-none');
                }
            });
        });
    });
</script>
@endsection