@extends('layouts.instructor')

@section('title', 'Edit Discount')

@section('content')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 0.95rem;
        border: 1px solid #e2e8f0;
        transition: border 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }
    
    .form-check-input:checked {
        background-color: #4361ee;
        border-color: #4361ee;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: #4361ee;
        border: none;
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
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
        <a href="{{ route('instructor.discounts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Discounts
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Discount Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.discounts.update', $discount->discount_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Discount Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $discount->code) }}" required placeholder="Enter discount code">
                        @error('code')
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
                        <label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="percentage" {{ old('type', $discount->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type', $discount->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $discount->value) }}" step="0.01" min="0" required placeholder="Enter discount value">
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="min_order_value" class="form-label">Minimum Order Value</label>
                        <input type="number" class="form-control @error('min_order_value') is-invalid @enderror" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', $discount->min_order_value) }}" step="0.01" min="0" placeholder="Enter minimum order value">
                        @error('min_order_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="max_discount_value" class="form-label">Maximum Discount Value</label>
                        <input type="number" class="form-control @error('max_discount_value') is-invalid @enderror" id="max_discount_value" name="max_discount_value" value="{{ old('max_discount_value', $discount->max_discount_value) }}" step="0.01" min="0" placeholder="Enter maximum discount value">
                        @error('max_discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="usage_limit" class="form-label">Usage Limit</label>
                        <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $discount->usage_limit) }}" min="1" placeholder="Enter usage limit (leave empty for unlimited)">
                        @error('usage_limit')
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

                    <div class="col-12 mb-4">
                        <label class="form-label">Applied Courses</label>
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
                                                   (isset($selectedCourses) && in_array($course->course_id, $selectedCourses)) ? 'checked' : '' }}>
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

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('instructor.discounts.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const courseCheckboxes = document.querySelectorAll('.course-checkbox');
        
        // Select/deselect all courses
        selectAllCheckbox.addEventListener('change', function() {
            courseCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Update "Select All" checkbox state based on individual selections
        courseCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(courseCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(courseCheckboxes).some(cb => cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });
        
        // Initial check - if all courses are checked, check the "Select All" checkbox
        const allChecked = Array.from(courseCheckboxes).every(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = !allChecked && Array.from(courseCheckboxes).some(cb => cb.checked);
    });
</script>
@endsection 