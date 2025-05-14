@extends('layouts.instructor')

@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Picture</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        @if($user->profile_image)
                            <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="mx-auto mb-3" style="width: 150px; height: 150px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-circle" style="font-size: 120px; color: #aaa;"></i>
                            </div>
                        @endif
                        
                        <form action="{{ route('instructor.profile.update.image') }}" method="POST" enctype="multipart/form-data" id="profileImageForm">
                            @csrf
                            <div class="mb-3">
                                <label for="profile_image" class="btn btn-outline-primary">
                                    <i class="fas fa-camera me-2"></i> Change Picture
                                </label>
                                <input type="file" name="profile_image" id="profile_image" class="d-none" onchange="document.getElementById('profileImageForm').submit()">
                            </div>
                        </form>
                    </div>
                    
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">Instructor</p>
                    
                    <div class="d-flex justify-content-center mb-2">
                        <a href="{{ route('instructors.show', $user->user_id) }}" class="btn btn-outline-primary ms-1" target="_blank">
                            <i class="fas fa-eye me-2"></i> View Public Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Banner</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="banner-preview mb-3" style="height: 100px; background-color: #f0f0f0; overflow: hidden; border-radius: 5px;">
                            @if($user->banner_image)
                                <img src="{{ asset($user->banner_image) }}" alt="Profile Banner" class="img-fluid w-100" style="object-fit: cover; height: 100px;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <i class="fas fa-image" style="font-size: 48px; color: #aaa;"></i>
                                </div>
                            @endif
                        </div>
                        
                        <form action="{{ route('instructor.profile.update.banner') }}" method="POST" enctype="multipart/form-data" id="bannerImageForm">
                            @csrf
                            <div class="mb-3">
                                <label for="banner_image" class="btn btn-outline-primary">
                                    <i class="fas fa-image me-2"></i> Change Banner
                                </label>
                                <input type="file" name="banner_image" id="banner_image" class="d-none" onchange="document.getElementById('bannerImageForm').submit()">
                            </div>
                            <small class="text-muted">Recommended size: 1200x300 pixels</small>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="fw-bold text-muted small">EMAIL</div>
                        <div>{{ $user->email }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-bold text-muted small">ACCOUNT CREATED</div>
                        <div>{{ $user->created_at->format('F d, Y') }}</div>
                    </div>
                    <div>
                        <div class="fw-bold text-muted small">ACCOUNT STATUS</div>
                        <div><span class="badge bg-success">Active</span></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('instructor.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $user->website) }}" placeholder="https://www.example.com">
                            <div class="form-text">Your personal or professional website</div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="linkedin_profile" class="form-label">LinkedIn Profile</label>
                                <input type="url" class="form-control" id="linkedin_profile" name="linkedin_profile" value="{{ old('linkedin_profile', $user->linkedin_profile) }}" placeholder="https://www.linkedin.com/in/your-profile">
                            </div>
                            <div class="col-md-6">
                                <label for="twitter_profile" class="form-label">Twitter Profile</label>
                                <input type="url" class="form-control" id="twitter_profile" name="twitter_profile" value="{{ old('twitter_profile', $user->twitter_profile) }}" placeholder="https://twitter.com/your-username">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Professional Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                            <div class="form-text">Write a short bio highlighting your expertise and teaching experience.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="detailed_description" class="form-label">Detailed Description</label>
                            <textarea class="form-control" id="detailed_description" name="detailed_description" rows="6">{{ old('detailed_description', $user->detailed_description) }}</textarea>
                            <div class="form-text">Write a detailed description about your background, expertise, teaching philosophy, accomplishments and what students can expect from your courses. This will be displayed on your public profile page.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Password</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">To change your password, please use the form below.</p>
                    
                    <form action="{{ route('instructor.profile.update.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-lock me-2"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 