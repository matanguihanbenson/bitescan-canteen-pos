@extends('layouts.app')

@push('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="login-page min-vh-100 d-flex align-items-center">
    <div class="container">
        @php
            $maintenance = \App\Models\Maintenance::first();
        @endphp
        
        @if($maintenance && $maintenance->is_maintenance)
        <div class="row justify-content-center mb-4">
            <div class="col-md-4">
                <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="fas fa-tools me-2"></i>System Under Maintenance</h5>
                    <hr>
                    <p class="mb-0">{{ $maintenance->admin_notes }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm test-credentials-btn" data-bs-toggle="modal" data-bs-target="#testCredentialsModal">
                            <i class="bi bi-key-fill me-1"></i> Test Credentials
                        </button>
                    </div>
                    <h2 class="brand-text mb-1">BiteScan POS</h2>
                    <p class="text-muted">Welcome back! Please login to continue.</p>
                </div>
                
                <div class="card shadow border-0 rounded-4 animate-card">
                    <div class="card-body p-4">
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="floating-input mb-4">
                                <input id="login" type="text" 
                                    class="form-control @error('login') is-invalid @enderror" 
                                    name="login" value="{{ old('login') }}" placeholder=" " required autocomplete="login" autofocus>
                                <label for="login">{{ __('Email or Username') }}</label>
                                @error('login')
                                    @unless(strpos($message, 'deactivated') !== false)
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @endunless
                                @enderror
                            </div>

                            <div class="floating-input mb-4">
                                <input id="password" type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" placeholder=" " required autocomplete="current-password">
                                <label for="password">{{ __('Password') }}</label>
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" 
                                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3">
                                {{ __('Sign In') }}
                            </button>

                            <div class="text-center mt-3">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#resetRequestModal" class="text-muted">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Credentials Modal -->
<div class="modal fade" id="testCredentialsModal" tabindex="-1" aria-labelledby="testCredentialsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="testCredentialsModalLabel">
                    <i class="bi bi-key-fill text-primary me-2"></i>Test Credentials
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted small mb-3">Sample credentials available for testing BiteScan POS:</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Role</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Password</th>
                                <th class="text-center text-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-primary">Cashier</span></td>
                                <td><code class="text-dark">cashier@gmail.com</code></td>
                                <td><code>password</code></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-use-credential" data-bs-dismiss="modal" data-email="cashier@gmail.com" data-password="password">
                                        Use
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">Finance</span></td>
                                <td><code class="text-dark">finance@gmail.com</code></td>
                                <td><code>password</code></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-use-credential" data-bs-dismiss="modal" data-email="finance@gmail.com" data-password="password">
                                        Use
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td><code class="text-dark">admin@gmail.com</code></td>
                                <td><code>password</code></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-use-credential" data-bs-dismiss="modal" data-email="admin@gmail.com" data-password="password">
                                        Use
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning text-dark">Manager</span></td>
                                <td><code class="text-dark">manager@gmail.com</code></td>
                                <td><code>password</code></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-use-credential" data-bs-dismiss="modal" data-email="manager@gmail.com" data-password="password">
                                        Use
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-3 btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Password Reset Request Modal -->
<div class="modal fade" id="resetRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Password Reset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="passwordResetForm" action="{{ route('password.request.submit') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="resetModalMessages"></div>
                    <p>Enter your email address to request a password reset from the system administrator.</p>
                    <div class="floating-input">
                        <input type="email" name="email" class="form-control" required placeholder=" ">
                        <label>Email Address</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="maintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>System Under Maintenance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>The system is currently under maintenance. Only administrators can access the system at this time.</p>
                @if($maintenance && $maintenance->admin_notes)
                    <div class="alert alert-info">
                        {{ $maintenance->admin_notes }}
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@if(session('maintenance_mode'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var maintenanceModal = new bootstrap.Modal(document.getElementById('maintenanceModal'));
        maintenanceModal.show();
    });
</script>
@endif

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if($errors->has('login') && strpos($errors->first('login'), 'deactivated') !== false)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Account Deactivated',
            text: '{{ $errors->first('login') }}',
            confirmButtonText: 'Contact Administrator',
            confirmButtonColor: '#dc3545',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    });
</script>
@endif

<style>
.login-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.brand-text {
    font-weight: 700;
    background: linear-gradient(45deg, #2196F3, #1976D2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.test-credentials-btn {
    border-color: #2196F3;
    color: #1976D2;
    font-weight: 500;
    transition: all 0.2s ease;
}
.test-credentials-btn:hover {
    background: linear-gradient(45deg, #2196F3, #1976D2);
    border-color: transparent;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(33, 150, 243, 0.3);
}
.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}
.animate-card {
    animation: slideUp 0.5s ease-out;
}
.floating-input {
    position: relative;
}
.floating-input input {
    height: 56px;
    padding: 1rem 0.75rem;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.2s ease;
}
.floating-input label {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    transition: all 0.2s ease;
    pointer-events: none;
    font-size: 0.9rem;
    color: #757575;
    padding: 0 0.25rem;
}
.floating-input input:focus,
.floating-input input:not(:placeholder-shown) {
    border-color: #2196F3;
    background: #fff;
}
.floating-input input:focus + label,
.floating-input input:not(:placeholder-shown) + label {
    top: 0;
    font-size: 0.75rem;
    background: #fff;
}
.btn-primary {
    background: linear-gradient(45deg, #2196F3, #1976D2);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(33, 150, 243, 0.3);
}
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.form-check-input:checked {
    background-color: #2196F3;
    border-color: #2196F3;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testModalEl = document.getElementById('testCredentialsModal');
    
    function cleanupModalBackdrops() {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    if (testModalEl) {
        testModalEl.addEventListener('hidden.bs.modal', function () {
            cleanupModalBackdrops();
        });
    }

    document.querySelectorAll('.btn-use-credential').forEach(button => {
        button.addEventListener('click', function() {
            const email = this.getAttribute('data-email');
            const password = this.getAttribute('data-password');
            
            const loginInput = document.getElementById('login');
            const passwordInput = document.getElementById('password');
            
            if (loginInput && passwordInput) {
                loginInput.value = email;
                passwordInput.value = password;
                loginInput.dispatchEvent(new Event('input', { bubbles: true }));
                passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
                loginInput.dispatchEvent(new Event('change', { bubbles: true }));
                passwordInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            
            // Allow native data-bs-dismiss to finish animation, then ensure no lingering backdrops
            setTimeout(cleanupModalBackdrops, 300);
        });
    });
});

document.getElementById('passwordResetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const messagesDiv = document.getElementById('resetModalMessages');
    const submitButton = form.querySelector('button[type="submit"]');
    
    submitButton.disabled = true;
    submitButton.innerHTML = 'Submitting...';
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        messagesDiv.innerHTML = '';
        
        if (data.success) {
            messagesDiv.innerHTML = `
                <div class="alert alert-success">
                    ${data.message}
                </div>
            `;
            form.reset();
        } else if (data.warning) {
            messagesDiv.innerHTML = `
                <div class="alert alert-warning">
                    ${data.warning}
                </div>
            `;
        } else if (data.error) {
            messagesDiv.innerHTML = `
                <div class="alert alert-danger">
                    ${data.error}
                </div>
            `;
        }
    })
    .catch(error => {
        messagesDiv.innerHTML = `
            <div class="alert alert-danger">
                An unexpected error occurred. Please try again.
            </div>
        `;
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit Request';
    });
});
</script>

@endsection
