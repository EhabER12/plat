@extends('admin.layout')

@section('title', 'New Message')
@section('page-title', 'رسالة جديدة')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>
                    إنشاء رسالة جديدة
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.messages.store') }}" method="POST" id="newMessageForm">
                    @csrf

                    <!-- User Selection -->
                    <div class="mb-4">
                        <label for="user_id" class="form-label fw-bold">
                            <i class="fas fa-user me-1"></i>
                            إلى
                        </label>
                        <select class="form-select @error('user_id') is-invalid @enderror"
                                name="user_id"
                                id="user_id"
                                required>
                            <option value="">اختر المستخدم...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}"
                                        {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                    @if($user->roles->count() > 0)
                                        -
                                        @foreach($user->roles as $role)
                                            @if($role->role == 'student') طالب
                                            @elseif($role->role == 'instructor') مدرس
                                            @elseif($role->role == 'parent') ولي أمر
                                            @else {{ $role->role }}
                                            @endif
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>



                    <!-- Message Content -->
                    <div class="mb-4">
                        <label for="content" class="form-label fw-bold">
                            <i class="fas fa-comment me-1"></i>
                            الرسالة
                        </label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content"
                                  name="content"
                                  rows="6"
                                  placeholder="اكتب رسالتك هنا..."
                                  maxlength="1000">{{ old('content') }}</textarea>
                        <div class="form-text">
                            <span id="char_count">0</span> / 1000 حرف
                        </div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            العودة
                        </a>
                        <button type="submit" class="btn btn-primary" id="send_btn">
                            <i class="fas fa-paper-plane me-1"></i>
                            إرسال الرسالة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection



@section('scripts')
<script>
$(document).ready(function() {
    // Character count
    $('#content').on('input', function() {
        const length = $(this).val().length;
        $('#char_count').text(length);

        if (length > 900) {
            $('#char_count').addClass('text-warning');
        } else {
            $('#char_count').removeClass('text-warning');
        }
    });

    // Form validation
    $('#newMessageForm').on('submit', function(e) {
        const userId = $('#user_id').val();
        const content = $('#content').val().trim();

        if (!userId) {
            e.preventDefault();
            alert('يرجى اختيار مستخدم لإرسال الرسالة إليه');
            return false;
        }

        if (!content) {
            e.preventDefault();
            alert('يرجى كتابة محتوى الرسالة');
            return false;
        }

        $('#send_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الإرسال...');
    });
});
</script>
@endsection
