@extends('layouts.app')

@section('content')
<div class="login-page min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="text-center mb-4">
                    <h2 class="brand-text mb-1">Reset Password</h2>
                    <p class="text-muted">Enter your email to receive reset instructions.</p>
                </div>

                <div class="card shadow border-0 rounded-4 animate-card">
                    <div class="card-body p-4">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="floating-input mb-4">
                                <input id="email" type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" 
                                    required autocomplete="email" autofocus>
                                <label for="email">{{ __('Email Address') }}</label>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3">
                                {{ __('Send Password Reset Link') }}
                            </button>

                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="text-muted">
                                    {{ __('Back to Login') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
