@extends('admin.layout')

@section('title', 'Edit Discount')

@section('content')
<style>
    /* Modern form styles */
    .form-control {
        height: 50px;
        padding: 12px 20px;
        border-radius: 12px;
        border: 2px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) inset;
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        transform: translateY(-2px);
        background-color: #fff;
        outline: none;
    }

    .form-control:hover {
        border-color: #bbc1e4;
    }

    textarea.form-control {
        height: auto;
        resize: none;
        min-height: 110px;
        padding-top: 15px;
    }

    .form-select {
        height: 50px;
        padding: 12px 20px;
        border-radius: 12px;
        border: 2px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) inset;
    }

    .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        transform: translateY(-2px);
        background-color: #fff;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 0.95rem;
        display: block;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0.2rem;
        margin-right: 0.5rem;
        border-radius: 4px;
        border: 2px solid #d1d3e0;
        background-color: #fff;
    }

    .form-check-input:checked {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 15px 20px;
    }

    .btn-primary, .btn-secondary {
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
    }
    
    .btn-secondary {
        background: #6c757d;
        box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);
    }

    .btn-primary:hover, .btn-secondary:hover {
        transform: translateY(-2px);
    }
    
    .btn-primary:hover {
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
    }
    
    .btn-secondary:hover {
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
    }

    .invalid-feedback {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: -15px;
        margin-bottom: 15px;
        display: block;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Discount</h1>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Discounts
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Discount Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.discounts.update', $discount->discount_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Discount Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $discount->name) }}" required placeholder="Enter discount name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Enter discount description">{{ old('description', $discount->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="discount_type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" required>
                            <option value="percentage" {{ old('discount_type', $discount->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('discount_type', $discount->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                        @error('discount_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="discount_value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('discount_value') is-invalid @enderror" id="discount_value" name="discount_value" value="{{ old('discount_value', $discount->discount_value) }}" step="0.01" min="0" required placeholder="Enter discount value">
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d') : '') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d') : '') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="applies_to_all_courses" name="applies_to_all_courses" {{ old('applies_to_all_courses', $discount->applies_to_all_courses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="applies_to_all_courses">
                                Apply to all courses
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mb-4" id="coursesSection">
                        <label class="form-label">Select Courses</label>
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label fw-bold" for="selectAll">
                                            Select All Courses
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach($courses as $course)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input course-checkbox" type="checkbox" value="{{ $course->course_id }}" id="course_{{ $course->course_id }}" name="courses[]" 
                                                {{ (is_array(old('courses')) && in_array($course->course_id, old('courses'))) || 
                                                   (is_array($discount->courses) && in_array($course->course_id, $discount->courses)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="course_{{ $course->course_id }}">
                                                    {{ $course->title }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('courses')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Discount
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
        // Toggle courses section based on "applies to all courses" checkbox
        const appliesToAllCoursesCheckbox = document.getElementById('applies_to_all_courses');
        const coursesSection = document.getElementById('coursesSection');
        
        function toggleCoursesSection() {
            if(appliesToAllCoursesCheckbox.checked) {
                coursesSection.style.display = 'none';
            } else {
                coursesSection.style.display = 'block';
            }
        }
        
        appliesToAllCoursesCheckbox.addEventListener('change', toggleCoursesSection);
        
        // Initial toggle
        toggleCoursesSection();
        
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const courseCheckboxes = document.querySelectorAll('.course-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            courseCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // If all course checkboxes are checked, check the "Select All" checkbox
        function updateSelectAllCheckbox() {
            const allChecked = Array.from(courseCheckboxes).every(checkbox => checkbox.checked);
            selectAllCheckbox.checked = allChecked;
        }
        
        courseCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllCheckbox);
        });
        
        // Initial check
        updateSelectAllCheckbox();
    });
</script>
@endsection 
 

@section('title', 'Edit Discount')

@section('content')
<style>
    /* Modern form styles */
    .form-control {
        height: 50px;
        padding: 12px 20px;
        border-radius: 12px;
        border: 2px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) inset;
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        transform: translateY(-2px);
        background-color: #fff;
        outline: none;
    }

    .form-control:hover {
        border-color: #bbc1e4;
    }

    textarea.form-control {
        height: auto;
        resize: none;
        min-height: 110px;
        padding-top: 15px;
    }

    .form-select {
        height: 50px;
        padding: 12px 20px;
        border-radius: 12px;
        border: 2px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) inset;
    }

    .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        transform: translateY(-2px);
        background-color: #fff;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 0.95rem;
        display: block;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0.2rem;
        margin-right: 0.5rem;
        border-radius: 4px;
        border: 2px solid #d1d3e0;
        background-color: #fff;
    }

    .form-check-input:checked {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 15px 20px;
    }

    .btn-primary, .btn-secondary {
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
    }
    
    .btn-secondary {
        background: #6c757d;
        box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);
    }

    .btn-primary:hover, .btn-secondary:hover {
        transform: translateY(-2px);
    }
    
    .btn-primary:hover {
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
    }
    
    .btn-secondary:hover {
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
    }

    .invalid-feedback {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: -15px;
        margin-bottom: 15px;
        display: block;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Discount</h1>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Discounts
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Discount Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.discounts.update', $discount->discount_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Discount Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $discount->name) }}" required placeholder="Enter discount name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Enter discount description">{{ old('description', $discount->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="discount_type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" required>
                            <option value="percentage" {{ old('discount_type', $discount->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('discount_type', $discount->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                        @error('discount_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="discount_value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('discount_value') is-invalid @enderror" id="discount_value" name="discount_value" value="{{ old('discount_value', $discount->discount_value) }}" step="0.01" min="0" required placeholder="Enter discount value">
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d') : '') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d') : '') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="applies_to_all_courses" name="applies_to_all_courses" {{ old('applies_to_all_courses', $discount->applies_to_all_courses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="applies_to_all_courses">
                                Apply to all courses
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mb-4" id="coursesSection">
                        <label class="form-label">Select Courses</label>
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label fw-bold" for="selectAll">
                                            Select All Courses
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach($courses as $course)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input course-checkbox" type="checkbox" value="{{ $course->course_id }}" id="course_{{ $course->course_id }}" name="courses[]" 
                                                {{ (is_array(old('courses')) && in_array($course->course_id, old('courses'))) || 
                                                   (is_array($discount->courses) && in_array($course->course_id, $discount->courses)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="course_{{ $course->course_id }}">
                                                    {{ $course->title }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('courses')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Discount
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
        // Toggle courses section based on "applies to all courses" checkbox
        const appliesToAllCoursesCheckbox = document.getElementById('applies_to_all_courses');
        const coursesSection = document.getElementById('coursesSection');
        
        function toggleCoursesSection() {
            if(appliesToAllCoursesCheckbox.checked) {
                coursesSection.style.display = 'none';
            } else {
                coursesSection.style.display = 'block';
            }
        }
        
        appliesToAllCoursesCheckbox.addEventListener('change', toggleCoursesSection);
        
        // Initial toggle
        toggleCoursesSection();
        
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const courseCheckboxes = document.querySelectorAll('.course-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            courseCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // If all course checkboxes are checked, check the "Select All" checkbox
        function updateSelectAllCheckbox() {
            const allChecked = Array.from(courseCheckboxes).every(checkbox => checkbox.checked);
            selectAllCheckbox.checked = allChecked;
        }
        
        courseCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllCheckbox);
        });
        
        // Initial check
        updateSelectAllCheckbox();
    });
</script>
@endsection 