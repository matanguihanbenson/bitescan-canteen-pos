@extends('layouts.app')

@section('title', 'System Settings')

@push('styles')
<style>
    .settings-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .settings-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .avatar-lg {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .settings-grid-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%) !important;
    }

    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div
                class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">System Settings</h4>
                    <p class="text-muted mb-0">Configure and manage application settings and preferences</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Settings Grid -->
    <div class="row">
        <div class="col-12">
            <div class="card settings-grid-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-gear-fill me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Configuration Options</h5>
                            <small class="text-white opacity-90">Access different system configuration areas</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- General Settings -->
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card settings-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="avatar-lg bg-primary bg-gradient rounded-3 mx-auto mb-3">
                                        <i class="bi bi-gear text-white fs-1"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">General Settings</h5>
                                    <p class="text-muted mb-3">Configure basic application settings, company information, and system preferences
                                    </p>
                                    <a href="{{ route('settings.general') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-arrow-right me-1"></i> Configure
                                    </a>
                                </div>
                            </div>
                        </div>


                        <!-- Scholar Settings -->
                        @php
                            $user = Auth::user();
                            $roleName = $user->role->name ?? null;
                            $displayName = $user->role->display_name ?? null;
                            $hasRoleAccess = in_array($roleName, ['admin', 'manager', 'canteen_manager'], true)
                                || in_array($displayName, ['System Admin', 'Manager'], true);
                            $hasPermAccess = method_exists($user, 'hasPermission')
                                ? ($user->hasPermission('manage_scholars') || $user->hasPermission('view_scholar_reports'))
                                : false;
                            $canAccessScholarSettings = $hasRoleAccess || $hasPermAccess;
                        @endphp
                        @if($canAccessScholarSettings)
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card settings-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="avatar-lg bg-info bg-gradient rounded-3 mx-auto mb-3">
                                        <i class="bi bi-mortarboard text-white fs-1"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Scholar Settings</h5>
                                    <p class="text-muted mb-3">Configure scholar allowances, daily limits, and scholarship management settings
                                    </p>
                                    <a href="{{ route('settings.scholars') }}" class="btn btn-info btn-sm">
                                        <i class="bi bi-arrow-right me-1"></i> Configure
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- POS Settings -->
                        @php
                            $hasPOSAccess = method_exists($user, 'hasPermission') && $user->hasPermission('access_pos');
                        @endphp
                        @if($hasPOSAccess)
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card settings-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="avatar-lg bg-warning bg-gradient rounded-3 mx-auto mb-3">
                                        <i class="bi bi-shop text-white fs-1"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">POS Settings</h5>
                                    <p class="text-muted mb-3">Configure departments, discounts, and credit user types for POS
                                    </p>
                                    <a href="{{ route('settings.pos') }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-arrow-right me-1"></i> Configure
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Role Management -->
                        @php
                            $hasRoleManagement = method_exists($user, 'hasPermission') && $user->hasPermission('role_management');
                            // Fallback to admin check for backward compatibility
                            if (!$hasRoleManagement) {
                                $hasRoleManagement = in_array($roleName, ['admin'], true) || in_array($displayName, ['System Admin'], true);
                            }
                        @endphp
                        @if($hasRoleManagement)
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card settings-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="avatar-lg bg-danger bg-gradient rounded-3 mx-auto mb-3">
                                        <i class="bi bi-shield-lock text-white fs-1"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Role Management</h5>
                                    <p class="text-muted mb-3">Manage user roles and assign default permissions to each role
                                    </p>
                                    <a href="{{ route('settings.roles') }}" class="btn btn-danger btn-sm">
                                        <i class="bi bi-arrow-right me-1"></i> Configure
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>System Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h6 class="text-primary">Application Version</h6>
                                <p class="mb-0 fw-semibold">v1.0.0</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h6 class="text-success">Laravel Version</h6>
                                <p class="mb-0 fw-semibold">{{ app()->version() }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h6 class="text-info">PHP Version</h6>
                                <p class="mb-0 fw-semibold">{{ PHP_VERSION }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h6 class="text-warning">Last Updated</h6>
                                <p class="mb-0 fw-semibold">{{ date('M d, Y') }}</p>
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
    $(document).ready(function() {
        // Add hover effects for better interactivity
        $('.settings-card').hover(
            function() {
                $(this).addClass('shadow-lg');
            },
            function() {
                $(this).removeClass('shadow-lg');
            }
        );

        });
</script>
@endsection