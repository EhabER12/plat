@extends('layouts.admin')

@section('title', 'Admin Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2 text-primary"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="position-relative mx-auto mb-3" style="width: 120px; height: 120px;">
                                    @if($user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 120px; height: 120px; font-size: 3rem;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <label for="profile_image" class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm" style="cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-camera text-primary"></i>
                                    </label>
                                    <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                                </div>
                                <small class="text-muted d-block">Click to change photo</small>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="Administrator" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock me-2 text-primary"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Account Created</label>
                        <p class="mb-0">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <p class="mb-0">{{ $user->updated_at->format('F d, Y') }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">Account Status</label>
                        <p class="mb-0"><span class="badge bg-success">Active</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Preview profile image before upload
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgElement = document.querySelector('.rounded-circle.img-thumbnail');
                if (imgElement) {
                    imgElement.src = e.target.result;
                } else {
                    const avatarDiv = document.querySelector('.rounded-circle.d-flex');
                    if (avatarDiv) {
                        const parent = avatarDiv.parentElement;
                        avatarDiv.remove();
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = "Profile Image";
                        img.className = "rounded-circle img-thumbnail";
                        img.style = "width: 120px; height: 120px; object-fit: cover;";
                        parent.prepend(img);
                    }
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
@endsection
