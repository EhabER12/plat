@extends('layouts.app')

@section('title', 'Register - Learning Platform')

@section('content')
<style>
    body {
        background-color: #f5f5f5;
        padding-top: 0 !important;
    }

    .register-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .register-card {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin: 2rem auto;
        max-width: 1100px;
    }

    .register-image {
        position: relative;
        height: 100%;
        min-height: 500px;
        background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1471&auto=format&fit=crop&ixlib=rb-4.0.3');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .register-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.1) 100%);
        z-index: 1;
    }

    .register-image-content {
        position: relative;
        z-index: 2;
    }

    .register-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .register-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 30px;
    }

    .register-form-container {
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .register-form-header {
        text-align: left;
        margin-bottom: 25px;
    }

    .register-welcome {
        font-size: 1.6rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .register-description {
        color: #777;
        margin-bottom: 30px;
    }

    .form-control {
        height: 50px;
        padding: 10px 20px;
        border-radius: 25px;
        border: 1px solid #e1e1e1;
        margin-bottom: 20px;
    }

    .form-control:focus {
        border-color: #4ECDC4;
        box-shadow: 0 0 0 0.2rem rgba(78, 205, 196, 0.25);
    }

    .form-label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }

    .register-button {
        height: 50px;
        border-radius: 25px;
        background: #4ECDC4;
        border: none;
        font-weight: 500;
        letter-spacing: 1px;
        margin-top: 10px;
        transition: all 0.3s ease;
    }

    .register-button:hover {
        background: #3dbdb5;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
    }

    .auth-tabs {
        display: flex;
        margin-bottom: 30px;
    }

    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 10px;
        margin: 0 5px;
        border-radius: 25px;
        color: #555;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .auth-tab.active {
        background-color: #4ECDC4;
        color: white;
    }

    .auth-tab:not(.active):hover {
        background-color: #f0f0f0;
    }

    @media (max-width: 991px) {
        .register-image {
            min-height: 300px;
        }
    }
</style>

<div class="register-container">
    <div class="container">
        <div class="register-card">
            <div class="row g-0">
                <!-- Left Image Side -->
                <div class="col-lg-6">
                    <div class="register-image">
                        <div class="register-image-content">
                            <h1 class="register-title">Join Our Learning Community</h1>
                            <p class="register-subtitle">Start your educational journey today and unlock a world of knowledge and opportunities.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Form Side -->
                <div class="col-lg-6">
                    <div class="register-form-container">
                        <div class="register-form-header">
                            <h2 class="register-welcome">Create an Account</h2>
                            <p class="register-description">Join thousands of students already learning on our platform.</p>
                        </div>

                        <!-- Login/Register Tabs -->
                        <div class="auth-tabs">
                            <a href="{{ url('/login') }}" class="auth-tab">Login</a>
                            <a href="{{ url('/register') }}" class="auth-tab active">Register</a>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autocomplete="name" autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required autocomplete="email">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Enter your address" rows="2">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Profile Picture</label>
                                <input id="profile_image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image" accept="image/*">
                                <small class="text-muted">Upload a profile picture (optional)</small>
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Account Type</label>
                                <select id="role" class="form-select @error('role') is-invalid @enderror" name="role" required>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                    <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Create a strong password" required autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">Confirm Password</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm your password" required autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn register-button w-100">Register</button>

                            <div class="mt-4 text-center">
                                <p>Already have an account? <a href="{{ url('/login') }}" class="text-decoration-none" style="color: #4ECDC4;">Login here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection