@extends('layouts.app')

@push('styles')
<style>
.stats-card {
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.avatar-sm {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.menu-load-card {
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Menu Management</h4>
                    <p class="text-muted mb-0">Organize and manage your menu groups</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Menu Load</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Menu Items</h6>
                            <h3 class="mb-1 text-dark fw-semibold">248</h3>
                            <p class="text-muted mb-0 small">Total items</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-menu-button-wide text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Categories</h6>
                            <h3 class="mb-1 text-dark fw-semibold">12</h3>
                            <p class="text-muted mb-0 small">Active categories</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-grid text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Menu Groups</h6>
                            <h3 class="mb-1 text-dark fw-semibold">15</h3>
                            <p class="text-muted mb-0 small">Active groups</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-info bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-collection text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Menu Sets</h6>
                            <h3 class="mb-1 text-dark fw-semibold">8</h3>
                            <p class="text-muted mb-0 small">Active combo sets</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-secondary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-layers text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card menu-load-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-grid-3x3-gap-fill me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">Menu Group Management</h5>
                                <small class="text-white opacity-90">Organize and manage your menu groups</small>
                            </div>
                        </div>
                        <button class="btn btn-light" onclick="addMenuGroup()">
                            <i class="bi bi-plus-circle me-2"></i> New Menu Group
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.menu.load-menu.search-filter')

    @include('components.menu.load-menu.tabs')

    @include('components.menu.load-menu.menu-group-details')

    @include('components.menu.load-menu.edit-menu-group')

    @include('components.menu.load-menu.new-menu-group', ['categories' => $categories])

    @include('components.menu.load-menu.confirm')

    @include('components.menu.load-menu.scripts')

    <style>
    @include('components.menu.styles')
    </style>
    @endsection