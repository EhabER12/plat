@extends('layouts.instructor')

@section('title', 'Create Coupon')
@section('page-title', 'Create New Coupon')

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

    .btn-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
    }

    .btn-outline-primary {
        border: 2px solid #4361ee;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: #4361ee;
    }

    .btn-outline-primary:hover {
        background-color: #4361ee;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.1);
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
    <div class="mb-4">
        <a href="{{ route('instructor.coupons.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Coupons
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Coupon Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.coupons.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required placeholder="Enter coupon code">
                        <small class="form-text text-muted">Leave empty to generate a random code.</small>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" step="0.01" min="0" required placeholder="Enter discount value">
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="max_uses" class="form-label">Maximum Uses</label>
                        <input type="number" class="form-control @error('max_uses') is-invalid @enderror" id="max_uses" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="Maximum number of uses">
                        <small class="form-text text-muted">Leave empty for unlimited uses.</small>
                        @error('max_uses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="valid_from" class="form-label">Valid From</label>
                        <input type="date" class="form-control @error('valid_from') is-invalid @enderror" id="valid_from" name="valid_from" value="{{ old('valid_from') }}">
                        @error('valid_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="valid_to" class="form-label">Valid Until</label>
                        <input type="date" class="form-control @error('valid_to') is-invalid @enderror" id="valid_to" name="valid_to" value="{{ old('valid_to') }}">
                        @error('valid_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="minimum_order_amount" class="form-label">Minimum Order Amount</label>
                        <input type="number" class="form-control @error('minimum_order_amount') is-invalid @enderror" id="minimum_order_amount" name="minimum_order_amount" value="{{ old('minimum_order_amount', 0) }}" step="0.01" min="0" placeholder="Minimum order value for coupon">
                        @error('minimum_order_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : 'checked' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mb-4">
                        <label class="form-label">Applicable Courses <span class="text-danger">*</span></label>
                        <p class="text-muted small">Select which of your courses this coupon applies to.</p>
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
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input course-checkbox" type="checkbox" value="{{ $course->course_id }}" id="course_{{ $course->course_id }}" name="courses_applicable[]" {{ is_array(old('courses_applicable')) && in_array($course->course_id, old('courses_applicable')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="course_{{ $course->course_id }}">
                                                    {{ $course->title }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('courses_applicable')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Coupon
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
 

@section('title', 'Create Coupon')
@section('page-title', 'Create New Coupon')

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

    .btn-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
    }

    .btn-outline-primary {
        border: 2px solid #4361ee;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: #4361ee;
    }

    .btn-outline-primary:hover {
        background-color: #4361ee;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.1);
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
    <div class="mb-4">
        <a href="{{ route('instructor.coupons.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Coupons
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Coupon Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.coupons.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required placeholder="Enter coupon code">
                        <small class="form-text text-muted">Leave empty to generate a random code.</small>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" step="0.01" min="0" required placeholder="Enter discount value">
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="max_uses" class="form-label">Maximum Uses</label>
                        <input type="number" class="form-control @error('max_uses') is-invalid @enderror" id="max_uses" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="Maximum number of uses">
                        <small class="form-text text-muted">Leave empty for unlimited uses.</small>
                        @error('max_uses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="valid_from" class="form-label">Valid From</label>
                        <input type="date" class="form-control @error('valid_from') is-invalid @enderror" id="valid_from" name="valid_from" value="{{ old('valid_from') }}">
                        @error('valid_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="valid_to" class="form-label">Valid Until</label>
                        <input type="date" class="form-control @error('valid_to') is-invalid @enderror" id="valid_to" name="valid_to" value="{{ old('valid_to') }}">
                        @error('valid_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="minimum_order_amount" class="form-label">Minimum Order Amount</label>
                        <input type="number" class="form-control @error('minimum_order_amount') is-invalid @enderror" id="minimum_order_amount" name="minimum_order_amount" value="{{ old('minimum_order_amount', 0) }}" step="0.01" min="0" placeholder="Minimum order value for coupon">
                        @error('minimum_order_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : 'checked' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mb-4">
                        <label class="form-label">Applicable Courses <span class="text-danger">*</span></label>
                        <p class="text-muted small">Select which of your courses this coupon applies to.</p>
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
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input course-checkbox" type="checkbox" value="{{ $course->course_id }}" id="course_{{ $course->course_id }}" name="courses_applicable[]" {{ is_array(old('courses_applicable')) && in_array($course->course_id, old('courses_applicable')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="course_{{ $course->course_id }}">
                                                    {{ $course->title }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('courses_applicable')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Coupon
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