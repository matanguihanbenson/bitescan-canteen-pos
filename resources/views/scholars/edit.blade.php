@extends('layouts.app')

@section('title', 'Edit Scholar')

@push('styles')
<style>
    .edit-form-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-badge {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">Edit Scholar</h4>
                    <p class="text-muted mb-0">Update scholar information and academic details</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('scholars.index') }}">Scholars</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

    <!-- Edit Scholar Form -->
    <div class="row">
        <div class="col-12">
            <div class="card edit-form-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-pencil-square me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">Edit Scholar Information</h5>
                                <small class="text-white opacity-90">Update personal and academic details</small>
                            </div>
                        </div>
                        <a href="{{ route('scholars.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('scholars.update', $scholar->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label fw-semibold">Student ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('student_id') is-invalid @enderror"
                                        id="student_id" name="student_id" value="{{ old('student_id', $scholar->student_id) }}" required>
                                    @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $scholar->email) }}">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        id="first_name" name="first_name" value="{{ old('first_name', $scholar->first_name) }}" required>
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        id="last_name" name="last_name" value="{{ old('last_name', $scholar->last_name) }}" required>
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_number" class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                                        id="contact_number" name="contact_number" value="{{ old('contact_number', $scholar->contact_number) }}">
                                    @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program" class="form-label fw-semibold">Program</label>
                                    <input type="text" class="form-control @error('program') is-invalid @enderror"
                                        id="program" name="program" value="{{ old('program', $scholar->program) }}">
                                    @error('program')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year_level" class="form-label fw-semibold">Year Level</label>
                                    <select class="form-select @error('year_level') is-invalid @enderror" id="year_level" name="year_level">
                                        <option value="">Select Year Level</option>
                                        <option value="1st Year" {{ old('year_level', $scholar->year_level) == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2nd Year" {{ old('year_level', $scholar->year_level) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3rd Year" {{ old('year_level', $scholar->year_level) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4th Year" {{ old('year_level', $scholar->year_level) == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                        <option value="Graduate" {{ old('year_level', $scholar->year_level) == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                                    </select>
                                    @error('year_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="daily_allowance" class="form-label fw-semibold">Daily Allowance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" step="0.01" class="form-control @error('daily_allowance') is-invalid @enderror"
                                            id="daily_allowance" name="daily_allowance" value="{{ old('daily_allowance', $scholar->daily_allowance) }}">
                                        @error('daily_allowance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scholar Info Display -->
                        <div class="row g-4 mt-3">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-header bg-transparent">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            <i class="bi bi-info-circle me-2"></i>Current Scholar Status
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold text-muted">Current Balance</label>
                                                    <div class="mt-1">
                                                        <span class="badge bg-{{ $scholar->current_balance > 50 ? 'success' : ($scholar->current_balance > 20 ? 'warning' : 'danger') }} fs-6 px-3 py-2">
                                                            ₱{{ number_format($scholar->current_balance, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold text-muted">Status</label>
                                                    <div class="mt-1">
                                                        <span class="badge {{ $scholar->is_active ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2">
                                                            <i class="bi bi-{{ $scholar->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                            {{ $scholar->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <a href="{{ route('scholars.index') }}" class="btn btn-outline-secondary me-3 px-4">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>Update Scholar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection