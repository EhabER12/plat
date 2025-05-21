@extends('layouts.admin')

@section('title', 'إضافة شارة جديدة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إضافة شارة جديدة</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.badges.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">اسم الشارة</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">وصف الشارة</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="icon">الأيقونة</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i id="icon-preview" class="fas fa-award"></i></span>
                                </div>
                                <select class="form-control" id="icon" name="icon">
                                    <option value="award" {{ old('icon') == 'award' ? 'selected' : '' }}>جائزة (award)</option>
                                    <option value="star" {{ old('icon') == 'star' ? 'selected' : '' }}>نجمة (star)</option>
                                    <option value="trophy" {{ old('icon') == 'trophy' ? 'selected' : '' }}>كأس (trophy)</option>
                                    <option value="medal" {{ old('icon') == 'medal' ? 'selected' : '' }}>ميدالية (medal)</option>
                                    <option value="certificate" {{ old('icon') == 'certificate' ? 'selected' : '' }}>شهادة (certificate)</option>
                                    <option value="crown" {{ old('icon') == 'crown' ? 'selected' : '' }}>تاج (crown)</option>
                                    <option value="fire" {{ old('icon') == 'fire' ? 'selected' : '' }}>نار (fire)</option>
                                    <option value="bolt" {{ old('icon') == 'bolt' ? 'selected' : '' }}>صاعقة (bolt)</option>
                                    <option value="compass" {{ old('icon') == 'compass' ? 'selected' : '' }}>بوصلة (compass)</option>
                                    <option value="tasks" {{ old('icon') == 'tasks' ? 'selected' : '' }}>مهام (tasks)</option>
                                    <option value="brain" {{ old('icon') == 'brain' ? 'selected' : '' }}>دماغ (brain)</option>
                                    <option value="graduation-cap" {{ old('icon') == 'graduation-cap' ? 'selected' : '' }}>قبعة تخرج (graduation-cap)</option>
                                    <option value="book" {{ old('icon') == 'book' ? 'selected' : '' }}>كتاب (book)</option>
                                    <option value="lightbulb" {{ old('icon') == 'lightbulb' ? 'selected' : '' }}>مصباح (lightbulb)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="level">المستوى</label>
                            <input type="number" class="form-control" id="level" name="level" value="{{ old('level', 1) }}" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="criteria">معايير الحصول على الشارة (JSON)</label>
                            <textarea class="form-control" id="criteria" name="criteria" rows="5">{{ old('criteria', '{"type": "quiz_attempts", "count": 1}') }}</textarea>
                            <small class="form-text text-muted">
                                أمثلة:<br>
                                - إكمال عدد معين من الاختبارات: <code>{"type": "quiz_attempts", "count": 5}</code><br>
                                - الحصول على درجة معينة: <code>{"type": "quiz_score", "min_score": 90}</code><br>
                                - سلسلة نجاحات متتالية: <code>{"type": "quiz_streak", "count": 3}</code>
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">تفعيل الشارة</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Update icon preview when selection changes
        $('#icon').change(function() {
            var iconClass = 'fas fa-' + $(this).val();
            $('#icon-preview').attr('class', iconClass);
        });
        
        // Trigger change on page load to set initial preview
        $('#icon').trigger('change');
        
        // Validate JSON in criteria field
        $('form').submit(function(e) {
            var criteriaValue = $('#criteria').val();
            if (criteriaValue) {
                try {
                    JSON.parse(criteriaValue);
                } catch (error) {
                    e.preventDefault();
                    alert('معايير الحصول على الشارة يجب أن تكون بتنسيق JSON صحيح');
                }
            }
        });
    });
</script>
@endsection
