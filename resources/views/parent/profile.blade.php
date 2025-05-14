@extends('layouts.app')

@section('title', 'الملف الشخصي للوالد')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">الملف الشخصي</h1>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center mb-4">
                                <img src="{{ $parent->profile_image ? asset('storage/profile_images/' . $parent->profile_image) : asset('images/default_profile.jpg') }}" 
                                     class="img-fluid rounded-circle profile-image" 
                                     alt="{{ $parent->name }}" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            </div>

                            <div class="list-group mb-4">
                                <a href="#profile-info" class="list-group-item list-group-item-action active" data-toggle="tab">
                                    <i class="fas fa-user-circle mr-2"></i> معلومات الملف الشخصي
                                </a>
                                <a href="#change-password" class="list-group-item list-group-item-action" data-toggle="tab">
                                    <i class="fas fa-key mr-2"></i> تغيير كلمة المرور
                                </a>
                                <a href="{{ route('parent.dashboard') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-tachometer-alt mr-2"></i> لوحة التحكم
                                </a>
                                <a href="{{ route('parent.link-request') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-link mr-2"></i> ربط الطلاب
                                </a>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">إحصائيات</h5>
                                </div>
                                <div class="card-body">
                                    <p><i class="fas fa-user-graduate mr-2"></i> الطلاب المرتبطين: <strong>{{ $studentsCount }}</strong></p>
                                    <p><i class="fas fa-calendar-alt mr-2"></i> تاريخ التسجيل: <strong>{{ $parent->created_at->format('Y-m-d') }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="tab-content">
                                <!-- Profile Information Tab -->
                                <div class="tab-pane active" id="profile-info">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">تحديث معلومات الملف الشخصي</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('parent.profile') }}" enctype="multipart/form-data">
                                                @csrf

                                                <div class="form-group row">
                                                    <label for="name" class="col-md-3 col-form-label text-md-right">الاسم الكامل</label>
                                                    <div class="col-md-9">
                                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                                               name="name" value="{{ old('name', $parent->name) }}" required autocomplete="name">
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="email" class="col-md-3 col-form-label text-md-right">البريد الإلكتروني</label>
                                                    <div class="col-md-9">
                                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                                               name="email" value="{{ old('email', $parent->email) }}" required autocomplete="email">
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="phone" class="col-md-3 col-form-label text-md-right">رقم الهاتف</label>
                                                    <div class="col-md-9">
                                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                               name="phone" value="{{ old('phone', $parent->phone) }}" autocomplete="phone">
                                                        @error('phone')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="profile_image" class="col-md-3 col-form-label text-md-right">صورة الملف الشخصي</label>
                                                    <div class="col-md-9">
                                                        <input id="profile_image" type="file" class="form-control-file @error('profile_image') is-invalid @enderror" 
                                                               name="profile_image" accept="image/jpeg,image/png,image/jpg">
                                                        <small class="form-text text-muted">الصور المسموح بها: JPG, JPEG, PNG. بحد أقصى 2MB.</small>
                                                        @error('profile_image')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <div class="col-md-9 offset-md-3">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-save mr-1"></i> حفظ التغييرات
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Password Tab -->
                                <div class="tab-pane" id="change-password">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">تغيير كلمة المرور</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('parent.profile.update-password') }}">
                                                @csrf

                                                <div class="form-group row">
                                                    <label for="current_password" class="col-md-4 col-form-label text-md-right">كلمة المرور الحالية</label>
                                                    <div class="col-md-8">
                                                        <input id="current_password" type="password" 
                                                               class="form-control @error('current_password') is-invalid @enderror" 
                                                               name="current_password" required>
                                                        @error('current_password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="new_password" class="col-md-4 col-form-label text-md-right">كلمة المرور الجديدة</label>
                                                    <div class="col-md-8">
                                                        <input id="new_password" type="password" 
                                                               class="form-control @error('new_password') is-invalid @enderror" 
                                                               name="new_password" required>
                                                        <small class="form-text text-muted">يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.</small>
                                                        @error('new_password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="new_password_confirmation" class="col-md-4 col-form-label text-md-right">تأكيد كلمة المرور الجديدة</label>
                                                    <div class="col-md-8">
                                                        <input id="new_password_confirmation" type="password" class="form-control" 
                                                               name="new_password_confirmation" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-key mr-1"></i> تحديث كلمة المرور
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Make tabs work
    $(document).ready(function() {
        $('.list-group-item').on('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all items
            $('.list-group-item').removeClass('active');
            
            // Add active class to clicked item
            $(this).addClass('active');
            
            // Show the corresponding tab
            var tabId = $(this).attr('href');
            $('.tab-pane').removeClass('active');
            $(tabId).addClass('active');
        });
    });
</script>
@endsection 