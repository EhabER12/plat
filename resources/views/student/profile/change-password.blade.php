@extends('layouts.app')

@section('title', 'Change Password')

@section('styles')
<style>
    .password-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }
    
    .password-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .password-icon {
        font-size: 48px;
        color: #007bff;
        margin-bottom: 20px;
    }
    
    .password-title h1 {
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .password-title p {
        color: #6c757d;
        margin-bottom: 0;
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
    
    .password-strength {
        height: 5px;
        margin-top: 10px;
        border-radius: 5px;
        background-color: #e9ecef;
    }
    
    .password-strength-bar {
        height: 100%;
        border-radius: 5px;
        width: 0%;
        transition: width 0.3s, background-color 0.3s;
    }
    
    .password-strength-text {
        font-size: 12px;
        margin-top: 5px;
    }
    
    .password-requirements {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .password-requirements h5 {
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .password-requirements ul {
        padding-left: 20px;
        margin-bottom: 0;
    }
    
    .password-requirements li {
        font-size: 13px;
        margin-bottom: 5px;
        color: #6c757d;
    }
    
    .password-requirements li.valid {
        color: #28a745;
    }
    
    .password-requirements li i {
        margin-right: 5px;
    }
    
    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="password-container">
        <div class="password-header">
            <div class="password-icon">
                <i class="fas fa-lock"></i>
            </div>
            <div class="password-title">
                <h1>Change Password</h1>
                <p>Ensure your account is using a secure password</p>
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
        
        <form action="{{ route('student.profile.change-password') }}" method="POST" id="password-form">
            @csrf
            
            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                    <span class="password-toggle" data-target="current_password">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="password-toggle" data-target="password">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
                <div class="password-strength">
                    <div class="password-strength-bar"></div>
                </div>
                <div class="password-strength-text"></div>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    <span class="password-toggle" data-target="password_confirmation">
                        <i class="far fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="password-requirements">
                <h5>Password Requirements:</h5>
                <ul>
                    <li id="length-check"><i class="far fa-circle"></i> At least 8 characters long</li>
                    <li id="uppercase-check"><i class="far fa-circle"></i> Contains at least one uppercase letter</li>
                    <li id="lowercase-check"><i class="far fa-circle"></i> Contains at least one lowercase letter</li>
                    <li id="number-check"><i class="far fa-circle"></i> Contains at least one number</li>
                    <li id="special-check"><i class="far fa-circle"></i> Contains at least one special character</li>
                    <li id="match-check"><i class="far fa-circle"></i> Passwords match</li>
                </ul>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('student.profile.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Change Password</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const strengthBar = document.querySelector('.password-strength-bar');
        const strengthText = document.querySelector('.password-strength-text');
        const submitBtn = document.getElementById('submit-btn');
        
        // Password toggle
        const toggleButtons = document.querySelectorAll('.password-toggle');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            
            // Length check
            const lengthCheck = document.getElementById('length-check');
            if (password.length >= 8) {
                strength += 20;
                lengthCheck.classList.add('valid');
                lengthCheck.innerHTML = '<i class="fas fa-check-circle"></i> At least 8 characters long';
            } else {
                lengthCheck.classList.remove('valid');
                lengthCheck.innerHTML = '<i class="far fa-circle"></i> At least 8 characters long';
            }
            
            // Uppercase check
            const uppercaseCheck = document.getElementById('uppercase-check');
            if (/[A-Z]/.test(password)) {
                strength += 20;
                uppercaseCheck.classList.add('valid');
                uppercaseCheck.innerHTML = '<i class="fas fa-check-circle"></i> Contains at least one uppercase letter';
            } else {
                uppercaseCheck.classList.remove('valid');
                uppercaseCheck.innerHTML = '<i class="far fa-circle"></i> Contains at least one uppercase letter';
            }
            
            // Lowercase check
            const lowercaseCheck = document.getElementById('lowercase-check');
            if (/[a-z]/.test(password)) {
                strength += 20;
                lowercaseCheck.classList.add('valid');
                lowercaseCheck.innerHTML = '<i class="fas fa-check-circle"></i> Contains at least one lowercase letter';
            } else {
                lowercaseCheck.classList.remove('valid');
                lowercaseCheck.innerHTML = '<i class="far fa-circle"></i> Contains at least one lowercase letter';
            }
            
            // Number check
            const numberCheck = document.getElementById('number-check');
            if (/[0-9]/.test(password)) {
                strength += 20;
                numberCheck.classList.add('valid');
                numberCheck.innerHTML = '<i class="fas fa-check-circle"></i> Contains at least one number';
            } else {
                numberCheck.classList.remove('valid');
                numberCheck.innerHTML = '<i class="far fa-circle"></i> Contains at least one number';
            }
            
            // Special character check
            const specialCheck = document.getElementById('special-check');
            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 20;
                specialCheck.classList.add('valid');
                specialCheck.innerHTML = '<i class="fas fa-check-circle"></i> Contains at least one special character';
            } else {
                specialCheck.classList.remove('valid');
                specialCheck.innerHTML = '<i class="far fa-circle"></i> Contains at least one special character';
            }
            
            return strength;
        }
        
        // Check if passwords match
        function checkPasswordsMatch() {
            const matchCheck = document.getElementById('match-check');
            if (passwordInput.value && confirmPasswordInput.value && passwordInput.value === confirmPasswordInput.value) {
                matchCheck.classList.add('valid');
                matchCheck.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                return true;
            } else {
                matchCheck.classList.remove('valid');
                matchCheck.innerHTML = '<i class="far fa-circle"></i> Passwords match';
                return false;
            }
        }
        
        // Update submit button state
        function updateSubmitButton() {
            const currentPassword = document.getElementById('current_password').value;
            const passwordStrength = checkPasswordStrength(passwordInput.value);
            const passwordsMatch = checkPasswordsMatch();
            
            if (currentPassword && passwordStrength >= 60 && passwordsMatch) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }
        
        // Password input event
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Update strength text and color
            if (strength < 20) {
                strengthBar.style.backgroundColor = '#dc3545';
                strengthText.textContent = 'Very Weak';
                strengthText.style.color = '#dc3545';
            } else if (strength < 40) {
                strengthBar.style.backgroundColor = '#ffc107';
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#ffc107';
            } else if (strength < 60) {
                strengthBar.style.backgroundColor = '#fd7e14';
                strengthText.textContent = 'Medium';
                strengthText.style.color = '#fd7e14';
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#20c997';
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#20c997';
            } else {
                strengthBar.style.backgroundColor = '#28a745';
                strengthText.textContent = 'Very Strong';
                strengthText.style.color = '#28a745';
            }
            
            checkPasswordsMatch();
            updateSubmitButton();
        });
        
        // Confirm password input event
        confirmPasswordInput.addEventListener('input', function() {
            checkPasswordsMatch();
            updateSubmitButton();
        });
        
        // Current password input event
        document.getElementById('current_password').addEventListener('input', updateSubmitButton);
    });
</script>
@endsection
