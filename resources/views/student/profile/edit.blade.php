@extends('layouts.app')

@section('title', 'Edit Profile')

@section('styles')
<style>
    .profile-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }

    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }

    .profile-image-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin-right: 30px;
    }

    .profile-image {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .profile-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s;
        cursor: pointer;
    }

    .profile-image-container:hover .profile-image-overlay {
        opacity: 1;
    }

    .profile-image-overlay i {
        color: white;
        font-size: 24px;
    }

    .profile-title h1 {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .profile-title p {
        color: #6c757d;
        margin-bottom: 0;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #ced4da;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    .required-field::after {
        content: "*";
        color: red;
        margin-left: 4px;
    }
</style>
@endsection

@section('content')
<style>
    .sidebar {
        background-color: #0056b3;
        color: white;
        min-height: calc(100vh - 180px);
        position: fixed;
        width: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 20px;
        padding-bottom: 20px;
        border-radius: 15px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        z-index: 100;
    }

    .sidebar-icon {
        color: white;
        font-size: 20px;
        margin-bottom: 25px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
        text-decoration: none;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .sidebar-icon:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
        color: white;
    }

    .sidebar-icon.active {
        background-color: rgba(255, 255, 255, 0.25);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .sidebar-tooltip {
        position: absolute;
        left: 50px;
        background-color: #004494;
        color: white;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 14px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        white-space: nowrap;
        z-index: 100;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        font-weight: 500;
    }

    .sidebar-icon:hover .sidebar-tooltip {
        opacity: 1;
        visibility: visible;
        left: 65px;
    }

    .sidebar-tooltip::before {
        content: '';
        position: absolute;
        top: 50%;
        left: -5px;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
        border-right: 6px solid #004494;
    }

    .profile-content {
        margin-left: 100px;
        margin-bottom: 100px; /* Add space for footer */
        margin-top: 20px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-auto p-0">
            <div class="sidebar">
                <a href="{{ route('student.profile') }}" class="sidebar-icon mb-5" style="background-color: rgba(255, 255, 255, 0.2); width: 50px; height: 50px;">
                    <h2 class="mb-0 fw-bold" style="font-family: 'Tajawal', sans-serif;">مت</h2>
                    <div class="sidebar-tooltip">منصة تعليمية</div>
                </a>
                <a href="{{ route('student.profile') }}" class="sidebar-icon {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Dashboard' }}</div>
                </a>
                <a href="{{ route('student.my-courses') }}" class="sidebar-icon {{ request()->routeIs('student.my-courses') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'دوراتي' : 'My Courses' }}</div>
                </a>
                <a href="{{ route('student.profile.index') }}" class="sidebar-icon {{ request()->routeIs('student.profile.index') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الملف الشخصي' : 'Profile' }}</div>
                </a>
                <a href="{{ route('student.messages.index') }}" class="sidebar-icon {{ request()->routeIs('student.messages.index') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    @php
                        $unreadMessages = App\Models\DirectMessage::where('receiver_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($unreadMessages > 0)
                        <span class="badge bg-danger rounded-pill position-absolute" style="font-size: 0.7rem; top: 5px; right: 5px;">{{ $unreadMessages }}</span>
                    @endif
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الرسائل' : 'Messages' }}</div>
                </a>
                <a href="{{ route('student.profile.edit') }}" class="sidebar-icon {{ request()->routeIs('student.profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الإعدادات' : 'Settings' }}</div>
                </a>
                <a href="{{ route('student.exams.index') }}" class="sidebar-icon mt-auto mb-4 {{ request()->routeIs('student.exams.index') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الاختبارات' : 'Exams' }}</div>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col profile-content">
            <div class="container py-5">
                <div class="profile-container">
        <div class="profile-header">
            <div class="profile-image-container">
                @if($user->profile_image)
                    <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="profile-image">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}" class="profile-image">
                @endif
                <label for="profile_image" class="profile-image-overlay">
                    <i class="fas fa-camera"></i>
                </label>
            </div>
            <div class="profile-title">
                <h1>Edit Profile</h1>
                <p>Update your personal information</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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

        <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">

            <div class="form-section">
                <h2 class="form-section-title">Personal Information</h2>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label required-field">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label required-field">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">About Me</h2>

                <div class="form-group">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                    <small class="text-muted">Tell us a little about yourself, your interests, and your learning goals.</small>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('student.profile.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
                        </form>
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
        // Preview profile image before upload
        const profileImageInput = document.getElementById('profile_image');
        const profileImage = document.querySelector('.profile-image');

        profileImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                }

                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection
