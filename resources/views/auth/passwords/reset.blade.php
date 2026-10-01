@extends('layouts.auth')

@push('styles')
<style>
    .auth-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }

    .auth-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
        transform: translateY(-1px);
        box-shadow: 0 .5rem 1rem rgba(13, 110, 253, .25);
    }
</style>
@endpush

@section('content')
<div class="auth-container d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card auth-card border-0 shadow-lg">
                    <div class="card-header bg-gradient-primary text-white text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-shield-lock display-4 text-white"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-1">Reset Password</h4>
                        <p class="text-white opacity-90 mb-0">Enter your new password below</p>
                    </div>

                    <div class="card-body p-4">
                        @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('status') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input id="email" type="email"
                                        class="form-control border-start-0 @error('email') is-invalid @enderror"
                                        name="email" value="{{ $email ?? old('email') }}"
                                        required autocomplete="email" autofocus>
                                </div>
                                @error('email')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">{{ __('New Password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input id="password" type="password"
                                        class="form-control border-start-0 @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Password must be at least 8 characters long
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password-confirm" class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock-fill text-muted"></i>
                                    </span>
                                    <input id="password-confirm" type="password"
                                        class="form-control border-start-0"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2 fw-semibold">
                                    <i class="bi bi-check-circle me-2"></i>{{ __('Reset Password') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3">
                        <div class="text-muted small">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'password') {
                    toggleIcon.className = 'bi bi-eye';
                } else {
                    toggleIcon.className = 'bi bi-eye-slash';
                }
            });
        }

        // Password confirmation validation
        const passwordConfirm = document.getElementById('password-confirm');
        if (passwordConfirm) {
            passwordConfirm.addEventListener('input', function() {
                if (passwordInput.value !== passwordConfirm.value) {
                    passwordConfirm.setCustomValidity('Passwords do not match');
                } else {
                    passwordConfirm.setCustomValidity('');
                }
            });
        }
    });
</script>
@endsection