@extends('admin.layout')

@section('title', 'إضافة كورس جديد')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>إضافة كورس جديد</h1>
            <a href="{{ route('admin.courses') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> العودة إلى قائمة الكورسات
            </a>
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
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-plus-circle me-1"></i>
                تفاصيل الكورس
            </div>
            <div class="card-body">
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">معلومات أساسية</h5>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">عنوان الكورس <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">وصف الكورس <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="instructor_id" class="form-label">المدرس <span class="text-danger">*</span></label>
                                <select class="form-select" id="instructor_id" name="instructor_id" required>
                                    <option value="">اختر المدرس</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->user_id }}" {{ old('instructor_id') == $instructor->user_id ? 'selected' : '' }}>
                                            {{ $instructor->name }} ({{ $instructor->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">التصنيف <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">اختر التصنيف</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">معلومات إضافية</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">السعر <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" name="price" value="{{ old('price', 0) }}" min="0" step="0.01" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">المدة (بالساعات)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration') }}" min="1">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="level" class="form-label">المستوى <span class="text-danger">*</span></label>
                                <select class="form-select" id="level" name="level" required>
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="language" class="form-label">لغة الكورس <span class="text-danger">*</span></label>
                                <select class="form-select" id="language" name="language" required>
                                    <option value="العربية" {{ old('language') == 'العربية' ? 'selected' : '' }}>العربية</option>
                                    <option value="English" {{ old('language') == 'English' ? 'selected' : '' }}>English</option>
                                    <option value="Español" {{ old('language') == 'Español' ? 'selected' : '' }}>Español</option>
                                    <option value="Français" {{ old('language') == 'Français' ? 'selected' : '' }}>Français</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="approval_status" class="form-label">حالة الكورس <span class="text-danger">*</span></label>
                                <select class="form-select" id="approval_status" name="approval_status" required>
                                    <option value="pending" {{ old('approval_status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="approved" {{ old('approval_status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                                    <option value="rejected" {{ old('approval_status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">كورس مميز</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-secondary me-md-2">إعادة تعيين</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ الكورس
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // يمكن إضافة سكريبتات جافاسكريبت هنا إذا لزم الأمر
    });
</script>
@endsection 