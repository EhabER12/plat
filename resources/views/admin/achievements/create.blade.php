@extends('layouts.admin')

@section('title', 'إضافة إنجاز جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إضافة إنجاز جديد</h3>
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

                    <form action="{{ route('admin.achievements.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">اسم الإنجاز</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">وصف الإنجاز</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="icon">الأيقونة</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i id="icon-preview" class="fas fa-trophy"></i></span>
                                </div>
                                <select class="form-control" id="icon" name="icon">
                                    <option value="trophy" {{ old('icon') == 'trophy' ? 'selected' : '' }}>كأس (trophy)</option>
                                    <option value="medal" {{ old('icon') == 'medal' ? 'selected' : '' }}>ميدالية (medal)</option>
                                    <option value="award" {{ old('icon') == 'award' ? 'selected' : '' }}>جائزة (award)</option>
                                    <option value="star" {{ old('icon') == 'star' ? 'selected' : '' }}>نجمة (star)</option>
                                    <option value="certificate" {{ old('icon') == 'certificate' ? 'selected' : '' }}>شهادة (certificate)</option>
                                    <option value="crown" {{ old('icon') == 'crown' ? 'selected' : '' }}>تاج (crown)</option>
                                    <option value="fire" {{ old('icon') == 'fire' ? 'selected' : '' }}>نار (fire)</option>
                                    <option value="bolt" {{ old('icon') == 'bolt' ? 'selected' : '' }}>صاعقة (bolt)</option>
                                    <option value="graduation-cap" {{ old('icon') == 'graduation-cap' ? 'selected' : '' }}>قبعة تخرج (graduation-cap)</option>
                                    <option value="book" {{ old('icon') == 'book' ? 'selected' : '' }}>كتاب (book)</option>
                                    <option value="lightbulb" {{ old('icon') == 'lightbulb' ? 'selected' : '' }}>مصباح (lightbulb)</option>
                                    <option value="globe" {{ old('icon') == 'globe' ? 'selected' : '' }}>كرة أرضية (globe)</option>
                                    <option value="flag-checkered" {{ old('icon') == 'flag-checkered' ? 'selected' : '' }}>علم (flag-checkered)</option>
                                    <option value="gem" {{ old('icon') == 'gem' ? 'selected' : '' }}>جوهرة (gem)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="points">النقاط</label>
                            <input type="number" class="form-control" id="points" name="points" value="{{ old('points', 100) }}" min="0" required>
                            <small class="form-text text-muted">عدد النقاط التي يحصل عليها الطالب عند تحقيق هذا الإنجاز</small>
                        </div>

                        <div class="form-group">
                            <label for="criteria">معايير تحقيق الإنجاز (JSON)</label>
                            <textarea class="form-control" id="criteria" name="criteria" rows="5">{{ old('criteria', '{"type": "quiz_attempts", "count": 10}') }}</textarea>
                            <small class="form-text text-muted">
                                أمثلة:<br>
                                - إكمال عدد معين من الاختبارات: <code>{"type": "quiz_attempts", "count": 10}</code><br>
                                - معدل نجاح عالي: <code>{"type": "quiz_pass_rate", "min_rate": 80, "min_attempts": 5}</code><br>
                                - سلسلة نجاحات متتالية: <code>{"type": "quiz_streak", "count": 5}</code><br>
                                - إكمال اختبارات في جميع الفئات: <code>{"type": "quiz_categories", "all_categories": true}</code>
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">تفعيل الإنجاز</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary">إلغاء</a>
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
                    alert('معايير تحقيق الإنجاز يجب أن تكون بتنسيق JSON صحيح');
                }
            }
        });
    });
</script>
@endsection
