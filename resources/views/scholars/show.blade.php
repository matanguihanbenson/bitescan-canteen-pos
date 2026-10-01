@extends('layouts.app')

@section('title', 'Scholar Details')

@push('styles')
<style>
    .detail-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-weight: 500;
        color: #495057;
        font-size: 1rem;
    }

    .avatar-lg {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    /* Compact helpers */
    .compact-card .card-body { padding: 1rem !important; }
    .compact-card .card-header { padding: 0.75rem 1rem !important; }
    .compact-list .mb-3 { margin-bottom: .5rem !important; }
    .table-compact thead th { padding: .5rem .75rem !important; }
    .table-compact tbody td { padding: .5rem .75rem !important; vertical-align: middle; }

    /* Summary stats alignment */
    .summary-stat .card-body { padding: 1rem !important; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 130px; }
    .summary-stat .avatar-sm { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-bottom: .5rem; }
    .summary-stat .label { text-transform: uppercase; letter-spacing: .05em; font-size: .75rem; color: #6c757d; font-weight: 600; margin-bottom: .25rem; }
    .summary-stat .value { font-size: 1.25rem; font-weight: 700; color: #212529; }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Scholar Details</h4>
                    <p class="text-muted mb-0">View comprehensive scholar information and transaction history</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('scholars.index') }}">Scholars</a></li>
                        <li class="breadcrumb-item active">{{ $scholar->student_id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Scholar Profile Card -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card detail-card compact-card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0 fw-semibold text-white">
                        <i class="bi bi-person-circle me-2"></i>Scholar Profile
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle mx-auto mb-2 text-primary">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $scholar->first_name }} {{ $scholar->last_name }}</h6>
                    <small class="text-muted d-block mb-2">{{ $scholar->student_id }}</small>
                    
                    <div class="d-flex justify-content-center mb-2">
                        <span class="badge {{ $scholar->is_active ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                            <i class="bi bi-{{ $scholar->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                            {{ $scholar->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('scholars.edit', $scholar->id) }}" class="btn btn-primary btn-sm me-2">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('scholars.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card detail-card compact-card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0 fw-semibold text-white">
                        <i class="bi bi-info-circle me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body p-4 compact-list">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="info-label">Email Address</label>
                                <p class="info-value mb-0">{{ $scholar->email ?: 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="info-label">Contact Number</label>
                                <p class="info-value mb-0">{{ $scholar->contact_number ?: 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="info-label">Program</label>
                                <p class="info-value mb-0">{{ $scholar->program ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="info-label">Year Level</label>
                                <p class="info-value mb-0">{{ $scholar->year_level ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="info-label">Daily Allowance</label>
                                <p class="info-value mb-0">₱{{ number_format($scholar->daily_allowance ?? 0, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="info-label">Member Since</label>
                                <p class="info-value mb-0">{{ $scholar->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Information -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card detail-card compact-card border-0 shadow-sm summary-stat h-100">
                <div class="card-body text-center">
                    <div class="avatar-sm bg-success bg-opacity-10 rounded-circle mx-auto mb-3">
                        <i class="bi bi-wallet2 text-success fs-4"></i>
                    </div>
                    <div class="label">Current Balance</div>
                    @php
                        $balance = $scholar->current_balance ?? 0;
                        $color = $balance > 50 ? 'success' : ($balance > 20 ? 'warning' : 'danger');
                    @endphp
                    <div class="value text-{{ $color }}">₱{{ number_format($balance, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card detail-card compact-card border-0 shadow-sm summary-stat h-100">
                <div class="card-body text-center">
                    <div class="avatar-sm bg-info bg-opacity-10 rounded-circle mx-auto mb-3">
                        <i class="bi bi-calendar-check text-info fs-4"></i>
                    </div>
                    <div class="label">Last Allowance</div>
                    <div class="value text-dark">
                        @if($scholar->last_allowance_date)
                            {{ $scholar->last_allowance_date->format('M d, Y') }}
                        @else
                            Never
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card detail-card compact-card border-0 shadow-sm summary-stat h-100">
                <div class="card-body text-center">
                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle mx-auto mb-3">
                        <i class="bi bi-clock-history text-primary fs-4"></i>
                    </div>
                    <div class="label">Total Transactions</div>
                    <div class="value text-dark">{{ $scholar->balanceHistory->count() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card detail-card compact-card border-0 shadow-sm summary-stat h-100">
                <div class="card-body text-center">
                    <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle mx-auto mb-3">
                        <i class="bi bi-graph-up text-warning fs-4"></i>
                    </div>
                    <div class="label">Total Credits</div>
                    @php
                        $totalCredits = $scholar->balanceHistory->where('transaction_type', 'credit')->sum('amount') ?? 0;
                    @endphp
                    <div class="value text-dark">₱{{ number_format($totalCredits, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="row">
        <div class="col-12">
            <div class="card detail-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">Transaction History</h5>
                                <small class="text-white opacity-90">Complete record of all balance transactions</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-light btn-sm" onclick="location.reload();">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-compact">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold text-dark px-4 py-3">Date</th>
                                    <th class="border-0 fw-bold text-dark py-3">Type</th>
                                    <th class="border-0 fw-bold text-dark py-3">Amount</th>
                                    <th class="border-0 fw-bold text-dark py-3">Description</th>
                                    <th class="border-0 fw-bold text-dark py-3">Balance After</th>
                                    <th class="border-0 fw-bold text-dark py-3">Processed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scholar->balanceHistory->sortByDesc('created_at') as $history)
                                <tr>
                                    <td class="align-middle px-4">
                                        <span class="fw-medium">{{ $history->created_at->format('M d, Y') }}</span>
                                        <br><small class="text-muted">{{ $history->created_at->format('g:i A') }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge {{ $history->transaction_type === 'credit' ? 'bg-success' : 'bg-danger' }}">
                                            <i class="bi bi-{{ $history->transaction_type === 'credit' ? 'plus-circle' : 'dash-circle' }} me-1"></i>
                                            {{ ucfirst($history->transaction_type) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-bold text-{{ $history->transaction_type === 'credit' ? 'success' : 'danger' }}">
                                            {{ $history->transaction_type === 'credit' ? '+' : '-' }}₱{{ number_format($history->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-dark">{{ $history->description }}</span>
                                        @if($history->transaction_number)
                                        <br><small class="text-muted">Ref: {{ $history->transaction_number }}</small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-medium">₱{{ number_format($history->new_balance, 2) }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @if($history->user)
                                            <span class="text-dark">{{ $history->user->firstname }} {{ $history->user->lastname }}</span>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted opacity-50"></i>
                                            <h6 class="fw-semibold">No transactions found</h6>
                                            <p class="mb-0 text-muted">This scholar has no transaction history yet</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
