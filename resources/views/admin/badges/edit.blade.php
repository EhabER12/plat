@extends('layouts.admin')

@section('title', 'تعديل الشارة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تعديل الشارة: {{ $badge->name }}</h3>
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

                    <form action="{{ route('admin.badges.update', $badge->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">اسم الشارة</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $badge->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">وصف الشارة</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $badge->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="icon">الأيقونة</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i id="icon-preview" class="fas fa-award"></i></span>
                                </div>
                                <select class="form-control" id="icon" name="icon">
                                    <option value="award" {{ old('icon', $badge->icon) == 'award' ? 'selected' : '' }}>جائزة (award)</option>
                                    <option value="star" {{ old('icon', $badge->icon) == 'star' ? 'selected' : '' }}>نجمة (star)</option>
                                    <option value="trophy" {{ old('icon', $badge->icon) == 'trophy' ? 'selected' : '' }}>كأس (trophy)</option>
                                    <option value="medal" {{ old('icon', $badge->icon) == 'medal' ? 'selected' : '' }}>ميدالية (medal)</option>
                                    <option value="certificate" {{ old('icon', $badge->icon) == 'certificate' ? 'selected' : '' }}>شهادة (certificate)</option>
                                    <option value="crown" {{ old('icon', $badge->icon) == 'crown' ? 'selected' : '' }}>تاج (crown)</option>
                                    <option value="fire" {{ old('icon', $badge->icon) == 'fire' ? 'selected' : '' }}>نار (fire)</option>
                                    <option value="bolt" {{ old('icon', $badge->icon) == 'bolt' ? 'selected' : '' }}>صاعقة (bolt)</option>
                                    <option value="compass" {{ old('icon', $badge->icon) == 'compass' ? 'selected' : '' }}>بوصلة (compass)</option>
                                    <option value="tasks" {{ old('icon', $badge->icon) == 'tasks' ? 'selected' : '' }}>مهام (tasks)</option>
                                    <option value="brain" {{ old('icon', $badge->icon) == 'brain' ? 'selected' : '' }}>دماغ (brain)</option>
                                    <option value="graduation-cap" {{ old('icon', $badge->icon) == 'graduation-cap' ? 'selected' : '' }}>قبعة تخرج (graduation-cap)</option>
                                    <option value="book" {{ old('icon', $badge->icon) == 'book' ? 'selected' : '' }}>كتاب (book)</option>
                                    <option value="lightbulb" {{ old('icon', $badge->icon) == 'lightbulb' ? 'selected' : '' }}>مصباح (lightbulb)</option>
                                    <option value="explore" {{ old('icon', $badge->icon) == 'explore' ? 'selected' : '' }}>استكشاف (explore)</option>
                                    <option value="persistence" {{ old('icon', $badge->icon) == 'persistence' ? 'selected' : '' }}>مثابرة (persistence)</option>
                                    <option value="streak" {{ old('icon', $badge->icon) == 'streak' ? 'selected' : '' }}>سلسلة (streak)</option>
                                    <option value="perfect" {{ old('icon', $badge->icon) == 'perfect' ? 'selected' : '' }}>كمال (perfect)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="level">المستوى</label>
                            <input type="number" class="form-control" id="level" name="level" value="{{ old('level', $badge->level) }}" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="criteria">معايير الحصول على الشارة (JSON)</label>
                            <textarea class="form-control" id="criteria" name="criteria" rows="5">{{ old('criteria', json_encode($badge->criteria, JSON_PRETTY_PRINT)) }}</textarea>
                            <small class="form-text text-muted">
                                أمثلة:<br>
                                - إكمال عدد معين من الاختبارات: <code>{"type": "quiz_attempts", "count": 5}</code><br>
                                - الحصول على درجة معينة: <code>{"type": "quiz_score", "min_score": 90}</code><br>
                                - سلسلة نجاحات متتالية: <code>{"type": "quiz_streak", "count": 3}</code>
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $badge->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">تفعيل الشارة</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
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
            var iconValue = $(this).val();
            var iconClass = 'fas fa-';
            
            // Map custom icons to FontAwesome equivalents
            if (iconValue === 'explore') {
                iconClass += 'compass';
            } else if (iconValue === 'persistence') {
                iconClass += 'tasks';
            } else if (iconValue === 'streak') {
                iconClass += 'fire';
            } else if (iconValue === 'perfect') {
                iconClass += 'award';
            } else {
                iconClass += iconValue;
            }
            
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
