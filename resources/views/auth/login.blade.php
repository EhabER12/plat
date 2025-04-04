@extends('layouts.app')

@section('title', 'Login - Learning Platform')

@section('content')
<style>
    body {
        background-color: #f5f5f5;
        padding-top: 0 !important;
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .auth-card {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin: 2rem auto;
        max-width: 1100px;
    }

    .auth-image {
        position: relative;
        height: 100%;
        min-height: 500px;
        background-image: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=1422&auto=format&fit=crop&ixlib=rb-4.0.3');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .auth-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.1) 100%);
        z-index: 1;
    }

    .auth-image-content {
        position: relative;
        z-index: 2;
    }

    .auth-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .auth-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 30px;
    }

    .auth-forms-container {
        padding: 40px 30px;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .auth-form-header {
        text-align: left;
        margin-bottom: 25px;
    }

    .auth-welcome {
        font-size: 1.6rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .auth-description {
        color: #777;
        margin-bottom: 30px;
    }

    .form-control {
        height: 50px;
        padding: 10px 20px;
        border-radius: 25px;
        border: 1px solid #e1e1e1;
        margin-bottom: 20px;
        width: 100%;
        box-sizing: border-box;
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

    .auth-button {
        height: 50px;
        border-radius: 25px;
        background: #4ECDC4;
        border: none;
        font-weight: 500;
        letter-spacing: 1px;
        margin-top: 10px;
        transition: all 0.3s ease;
    }

    .auth-button:hover {
        background: #3dbdb5;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
    }

    .auth-tabs {
        position: relative;
        display: flex;
        margin-bottom: 30px;
        background-color: #f0f0f0;
        border-radius: 25px;
        padding: 5px;
    }

    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 10px;
        color: #555;
        font-weight: 500;
        cursor: pointer;
        z-index: 2;
        transition: color 0.3s ease;
    }

    .auth-tab.active {
        color: #fff;
    }

    .auth-slider {
        position: absolute;
        height: calc(100% - 10px);
        width: 50%;
        background: #4ECDC4;
        top: 5px;
        left: 5px;
        border-radius: 20px;
        transition: transform 0.3s ease;
        z-index: 1;
    }

    .auth-slider.register {
        transform: translateX(100%);
    }

    .forms-slider {
        display: flex;
        width: 200%;
        transition: transform 0.3s ease;
        box-sizing: border-box;
    }

    .forms-slider.register {
        transform: translateX(-50%);
    }

    .form-container {
        width: 50%;
        box-sizing: border-box;
    }

    .form-inner-container {
        padding: 0 15px;
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }

    .form-container:first-child .form-inner-container {
        padding-right: 20px;
        padding-left: 0;
    }

    .form-container:last-child .form-inner-container {
        padding-left: 20px;
        padding-right: 0;
    }

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .forgot-password {
        color: #777;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .forgot-password:hover {
        color: #4ECDC4;
    }

    @media (max-width: 991px) {
        .auth-image {
            min-height: 300px;
        }

        .auth-forms-container {
            padding: 30px 20px;
        }

        .form-inner-container {
            padding: 0 5px;
        }

        .form-container:first-child .form-inner-container,
        .form-container:last-child .form-inner-container {
            padding-left: 5px;
            padding-right: 5px;
        }

        .remember-forgot {
            flex-direction: column;
            align-items: flex-start;
        }

        .forgot-password {
            margin-top: 10px;
        }
    }
</style>

<div class="auth-container">
    <div class="container">
        <div class="auth-card">
            <div class="row g-0">
                <!-- Left Image Side -->
                <div class="col-lg-6">
                    <div class="auth-image">
                        <div class="auth-image-content">
                            <h1 class="auth-title">Learning Platform</h1>
                            <p class="auth-subtitle">Education is the passport to the future, for tomorrow belongs to those who prepare for it today.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Form Side -->
                <div class="col-lg-6">
                    <div class="auth-forms-container">
                        <div class="auth-form-header">
                            <h2 class="auth-welcome">Welcome to Learning Platform!</h2>
                            <p class="auth-description">Your gateway to quality online education and skill development.</p>
                        </div>

                        <!-- Login/Register Tabs with Slider -->
                        <div class="auth-tabs">
                            <div class="auth-slider"></div>
                            <div class="auth-tab active" id="login-tab">Login</div>
                            <div class="auth-tab" id="register-tab">Register</div>
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

                        <!-- Forms Container with Slider -->
                        <div class="forms-slider">
                            <!-- Login Form -->
                            <div class="form-container">
                                <div class="form-inner-container">
                                    <form method="POST" action="{{ url('/login') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autocomplete="email" autofocus>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter your password" required autocomplete="current-password">
                                        </div>

                                        <div class="remember-forgot">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="remember">Remember me</label>
                                            </div>
                                            <a href="#" class="forgot-password">Forgot Password?</a>
                                        </div>

                                        <button type="submit" class="btn auth-button w-100">Login</button>

                                        <div class="mt-4 text-center">
                                            <p>Don't have an account? <a href="#" class="text-decoration-none switch-to-register" style="color: #4ECDC4;">Register here</a></p>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Register Form -->
                            <div class="form-container">
                                <div class="form-inner-container">
                                    <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="register-name" class="form-label">Full Name</label>
                                            <input id="register-name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autocomplete="name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-email" class="form-label">Email Address</label>
                                            <input id="register-email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required autocomplete="email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-phone" class="form-label">Phone Number</label>
                                            <input id="register-phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-address" class="form-label">Address</label>
                                            <textarea id="register-address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Enter your address" rows="2">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-profile-image" class="form-label">Profile Picture</label>
                                            <input id="register-profile-image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image" accept="image/*">
                                            <small class="text-muted">Upload a profile picture (optional)</small>
                                            @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-password" class="form-label">Password</label>
                                            <input id="register-password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Create a strong password" required autocomplete="new-password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="register-password-confirm" class="form-label">Confirm Password</label>
                                            <input id="register-password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm your password" required autocomplete="new-password">
                                        </div>

                                        <div class="mb-3">
                                            <label for="role" class="form-label">Register as</label>
                                            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                                                <option value="">Select a role</option>
                                                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                                <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                                                <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> This determines your access level on the platform.
                                            </small>
                                        </div>

                                        <!-- Role descriptions -->
                                        <div class="mb-4 role-descriptions" style="display: none;">
                                            <div class="role-desc student-desc" style="display: none;">
                                                <div class="p-2 bg-light rounded border-start border-primary border-3">
                                                    <strong><i class="fas fa-user-graduate text-primary"></i> Student:</strong>
                                                    Access courses, track your progress, interact with instructors, and earn certificates.
                                                </div>
                                            </div>
                                            <div class="role-desc parent-desc" style="display: none;">
                                                <div class="p-2 bg-light rounded border-start border-success border-3">
                                                    <strong><i class="fas fa-user-friends text-success"></i> Parent:</strong>
                                                    Monitor your child's progress, manage payments, and communicate with instructors.
                                                </div>
                                            </div>
                                            <div class="role-desc instructor-desc" style="display: none;">
                                                <div class="p-2 bg-light rounded border-start border-warning border-3">
                                                    <strong><i class="fas fa-chalkboard-teacher text-warning"></i> Instructor:</strong>
                                                    Create and manage courses, interact with students, track analytics, and receive payments.
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn auth-button w-100">Register</button>

                                        <div class="mt-4 text-center">
                                            <p>Already have an account? <a href="#" class="text-decoration-none switch-to-login" style="color: #4ECDC4;">Login here</a></p>
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

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginTab = document.getElementById('login-tab');
        const registerTab = document.getElementById('register-tab');
        const slider = document.querySelector('.auth-slider');
        const formsSlider = document.querySelector('.forms-slider');
        const switchToRegister = document.querySelector('.switch-to-register');
        const switchToLogin = document.querySelector('.switch-to-login');

        // Switch to register
        function showRegister() {
            loginTab.classList.remove('active');
            registerTab.classList.add('active');
            slider.classList.add('register');
            formsSlider.classList.add('register');
        }

        // Switch to login
        function showLogin() {
            registerTab.classList.remove('active');
            loginTab.classList.add('active');
            slider.classList.remove('register');
            formsSlider.classList.remove('register');
        }

        // Add event listeners
        registerTab.addEventListener('click', showRegister);
        loginTab.addEventListener('click', showLogin);
        switchToRegister.addEventListener('click', function(e) {
            e.preventDefault();
            showRegister();
        });
        switchToLogin.addEventListener('click', function(e) {
            e.preventDefault();
            showLogin();
        });

        // Check if we should show register form based on URL or view parameter
        if (window.location.href.includes('show=register') || {{ isset($showRegister) && $showRegister ? 'true' : 'false' }}) {
            showRegister();
        }

        // Handle role selection and showing descriptions
        const roleSelect = document.getElementById('role');
        const roleDescriptions = document.querySelector('.role-descriptions');
        const roleDescs = document.querySelectorAll('.role-desc');

        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                // Hide all descriptions first
                roleDescs.forEach(desc => desc.style.display = 'none');

                // If a role is selected, show the description container
                if (this.value) {
                    roleDescriptions.style.display = 'block';
                    // Show the specific role description
                    document.querySelector(`.${this.value}-desc`).style.display = 'block';
                } else {
                    roleDescriptions.style.display = 'none';
                }
            });

            // Initial check if a role is already selected (e.g. on form validation error)
            if (roleSelect.value) {
                roleDescriptions.style.display = 'block';
                document.querySelector(`.${roleSelect.value}-desc`).style.display = 'block';
            }
        }
    });
</script>
@endsection