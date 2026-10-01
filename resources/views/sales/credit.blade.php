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

    .credit-management-card {
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
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
    }
    
    /* Enhanced tab styling */
    .nav-tabs-custom .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
    }
    
    .nav-tabs-custom .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
    }
    
    .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    
    /* Enhanced button group styling */
    .btn-group .btn {
        border-width: 1px;
    }
    
    /* Action buttons spacing */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .btn-group > .btn:not(:first-child) {
        margin-left: 0.25rem;
    }
    
    /* Removed hover effects as requested */
    
    /* Filter section styling */
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    /* Table styling improvements */
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table-responsive {
        border-radius: 0.375rem;
    }
    
    /* Modal enhancements */
    .modal-header.bg-success,
    .modal-header.bg-danger {
        border-bottom: none;
    }
    
    .modal-body .alert {
        border-radius: 0.375rem;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .nav-tabs-custom .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Credit Transactions</h4>
                    <p class="text-muted mb-0">Manage and approve credit-based transactions and payments</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Credit Transactions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Credit Transactions</h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="total-transactions">0</h3>
                            <p class="text-muted mb-0 small">All time transactions</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-credit-card text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Pending Approvals</h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="pending-transactions">0</h3>
                            <p class="text-muted mb-0 small">Awaiting response</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Approved Transactions</h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="approved-transactions">0</h3>
                            <p class="text-muted mb-0 small">Successfully processed</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Declined Transactions</h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="declined-transactions">0</h3>
                            <p class="text-muted mb-0 small">Not approved</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-danger bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-x-circle text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $roleName = strtolower(auth()->user()->role->name ?? '');
        // Finance users: role name contains 'finance' (e.g., 'Finance Office'), or has explicit permission if configured
        $isFinance = str_contains($roleName, 'staff') || (auth()->user()->hasPermission('approve_credit') ?? false);
    @endphp

    @if($isFinance)
    <div class="card credit-management-card border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex align-items-center">
                <i class="bi bi-credit-card-2-front me-2 fs-5 text-white"></i>
                <div>
                    <h4 class="card-title mb-0 fw-semibold text-white">Credit Management System</h4>
                    <small class="text-white opacity-90">Comprehensive credit transaction management for finance office</small>
                </div>
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="transactions-tab" data-bs-toggle="tab" href="#transactions" role="tab">
                    <i class="bi bi-list-check me-2"></i>Credit Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history" role="tab">
                    <i class="bi bi-clock-history me-2"></i>Credit History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reports-tab" data-bs-toggle="tab" href="#reports" role="tab">
                    <i class="bi bi-graph-up me-2"></i>Reports & Analytics
                </a>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Credit Transactions Tab -->
            <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                <div class="card-body p-0">
                    <!-- Filter Controls -->
                    <div class="p-4 bg-light border-bottom">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Charge Type</label>
                                <select class="form-select" id="chargeTypeFilter">
                                    <option value="">All Types</option>
                                    <option value="department">Department</option>
                                    <option value="faculty">Faculty</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Date Range</label>
                                <input type="date" class="form-control" id="dateFromFilter">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-white">.</label>
                                <input type="date" class="form-control" id="dateToFilter">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Full Width Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="creditTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold text-dark px-4 py-3">Transaction Details</th>
                                    <th class="border-0 fw-bold text-dark py-3">Charge To</th>
                                    <th class="border-0 fw-bold text-dark py-3">Amount & Status</th>
                                    <th class="border-0 fw-bold text-dark text-center py-3" style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Credit History Tab -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="card-body p-0">
                    <!-- History Filters -->
                    <div class="p-4 bg-light border-bottom">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Status</label>
                                <select class="form-select" id="historyStatusFilter">
                                    <option value="">All</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Approved</option>
                                    <option value="declined">Declined</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Charge Type</label>
                                <select class="form-select" id="historyChargeTypeFilter">
                                    <option value="">All Types</option>
                                    <option value="department">Department</option>
                                    <option value="faculty">Faculty</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Charge To</label>
                                <input type="text" class="form-control" id="historyChargeToFilter" placeholder="Department/Name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Processed By</label>
                                <input type="text" class="form-control" id="historyProcessedByFilter" placeholder="Cashier name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Date From</label>
                                <input type="date" class="form-control" id="historyDateFromFilter">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Date To</label>
                                <input type="date" class="form-control" id="historyDateToFilter">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Amount Min</label>
                                <input type="number" step="0.01" class="form-control" id="historyAmountMinFilter" placeholder="0.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Amount Max</label>
                                <input type="number" step="0.01" class="form-control" id="historyAmountMaxFilter" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- History Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="creditHistoryTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold text-dark px-4 py-3">Transaction ID</th>
                                    <th class="border-0 fw-bold text-dark py-3">Date</th>
                                    <th class="border-0 fw-bold text-dark py-3">Processed By</th>
                                    <th class="border-0 fw-bold text-dark py-3">Charge Type</th>
                                    <th class="border-0 fw-bold text-dark py-3">Charge To</th>
                                    <th class="border-0 fw-bold text-dark py-3 text-end">Amount</th>
                                    <th class="border-0 fw-bold text-dark py-3">Status</th>
                                    <th class="border-0 fw-bold text-dark text-center py-3" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="reports" role="tabpanel">
                <div class="card-body">
                    <!-- Report Filters -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="bg-light p-4 rounded">
                                <h6 class="fw-bold mb-3">Report Filters</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <select class="form-select" id="reportPeriod">
                                            <option value="today">Today</option>
                                            <option value="week">This Week</option>
                                            <option value="month" selected>This Month</option>
                                            <option value="quarter">This Quarter</option>
                                            <option value="year">This Year</option>
                                            <option value="custom">Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-none" id="customDateRange">
                                        <input type="date" class="form-control" id="reportDateFrom">
                                    </div>
                                    <div class="col-md-3 d-none" id="customDateRangeTo">
                                        <input type="date" class="form-control" id="reportDateTo">
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-primary" onclick="loadReports()">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh Reports
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success" onclick="exportReports()">
                                            <i class="bi bi-download me-2"></i>Export Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Analytics Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-info bg-gradient rounded-circle mx-auto mb-3">
                                        <i class="bi bi-currency-dollar text-white fs-4"></i>
                                    </div>
                                    <h4 class="mb-1" id="reportTotalAmount">₱0.00</h4>
                                    <p class="text-muted mb-0 small">Total Credit Amount</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-warning bg-gradient rounded-circle mx-auto mb-3">
                                        <i class="bi bi-hourglass-split text-white fs-4"></i>
                                    </div>
                                    <h4 class="mb-1" id="reportPendingAmount">₱0.00</h4>
                                    <p class="text-muted mb-0 small">Pending Approvals</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-success bg-gradient rounded-circle mx-auto mb-3">
                                        <i class="bi bi-check-circle text-white fs-4"></i>
                                    </div>
                                    <h4 class="mb-1" id="reportApprovedAmount">₱0.00</h4>
                                    <p class="text-muted mb-0 small">Approved Amount</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-primary bg-gradient rounded-circle mx-auto mb-3">
                                        <i class="bi bi-building text-white fs-4"></i>
                                    </div>
                                    <h4 class="mb-1" id="reportDepartmentCount">0</h4>
                                    <p class="text-muted mb-0 small">Active Departments</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Reports -->
                    <div class="row g-4">
                        <!-- Department Breakdown -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-gradient-info text-white">
                                    <h6 class="mb-0 text-white">
                                        <i class="bi bi-building me-2"></i>Department Credit Summary
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-3 border-bottom bg-light">
                                        <div class="small text-muted">Legend: <span class="badge bg-primary">Total</span> <span class="badge bg-warning">Pending</span> <span class="badge bg-secondary">Count</span></div>
                                    </div>
                                    <div style="max-height: 400px; overflow-y: auto;">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead class="table-light sticky-top">
                                                    <tr>
                                                        <th class="fw-bold">Department</th>
                                                        <th class="fw-bold text-end">Total</th>
                                                        <th class="fw-bold text-end">Pending</th>
                                                        <th class="fw-bold text-center">Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="departmentReportTable">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-gradient-secondary text-white">
                                    <h6 class="mb-0 text-white">
                                        <i class="bi bi-people me-2"></i>Faculty & Staff Summary
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-3 border-bottom bg-light">
                                        <div class="small text-muted">Legend: <span class="badge bg-secondary">Faculty</span> <span class="badge bg-info">Staff</span> <span class="badge bg-primary">Count</span></div>
                                    </div>
                                    <div style="max-height: 400px; overflow-y: auto;">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <thead class="table-light sticky-top">
                                                    <tr>
                                                        <th class="fw-bold">Name</th>
                                                        <th class="fw-bold">Type</th>
                                                        <th class="fw-bold text-end">Total</th>
                                                        <th class="fw-bold text-center">Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="facultyReportTable">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card credit-management-card border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-table me-2 fs-5 text-white"></i>
                    <div>
                        <h5 class="card-title mb-0 fw-semibold text-white">Credit Sales</h5>
                        <small class="text-white opacity-90">View your credit transaction history</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="regularUserCreditTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-bold text-dark px-4 py-3">Transaction ID</th>
                            <th class="border-0 fw-bold text-dark py-3">Date</th>
                            <th class="border-0 fw-bold text-dark py-3">Charge To</th>
                            <th class="border-0 fw-bold text-dark py-3">Amount</th>
                            <th class="border-0 fw-bold text-dark py-3">Status</th>
                            <th class="border-0 fw-bold text-dark text-center py-3">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Transaction Information</h6>
                        <div class="mb-2">
                            <strong>Transaction ID:</strong>
                            <span id="modal-transaction-number"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Date:</strong>
                            <span id="modal-transaction-date"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Processed By:</strong>
                            <span id="modal-processed-by"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Credit Details</h6>
                        <div class="mb-2">
                            <strong>Charge To:</strong>
                            <span id="modal-charge-to"></span>
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span id="modal-status"></span>
                        </div>
                        <div id="approval-details" class="d-none">
                            <div class="mb-2">
                                <strong>Approved By:</strong>
                                <span id="modal-approved-by"></span>
                            </div>
                            <div class="mb-2">
                                <strong>Approved At:</strong>
                                <span id="modal-approved-at"></span>
                            </div>
                        </div>
                        <div id="decline-details" class="d-none">
                            <div class="mb-2">
                                <strong>Declined By:</strong>
                                <span id="modal-declined-by"></span>
                            </div>
                            <div class="mb-2">
                                <strong>Declined At:</strong>
                                <span id="modal-declined-at"></span>
                            </div>
                            <div class="mb-2">
                                <strong>Reason:</strong>
                                <span id="modal-decline-reason"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="text-muted">Ordered Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items-list"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Amount:</th>
                                <th class="text-end" id="modal-total-amount"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Approval Confirmation Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Confirm Credit Transaction Approval
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Transaction Details:</strong>
                    <div class="mt-2" id="approval-transaction-details"></div>
                </div>
                <p>Are you sure you want to approve this credit transaction?</p>
                <p class="text-muted mb-0">This action will:</p>
                <ul class="text-muted">
                    <li>Set the transaction status to "Completed"</li>
                    <li>Allow the credited amount to be processed</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmApproval">
                    <i class="bi bi-check-circle me-2"></i>Approve Transaction
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Decline Confirmation Modal -->
<div class="modal fade" id="declineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle me-2"></i>Decline Credit Transaction
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Transaction Details:</strong>
                    <div class="mt-2" id="decline-transaction-details"></div>
                </div>
                <div class="mb-3">
                    <label for="declineReason" class="form-label fw-bold">Reason for Decline <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="declineReason" rows="3" placeholder="Please provide a reason for declining this transaction..."></textarea>
                    <div class="form-text">This reason will be recorded and shared with the transaction initiator.</div>
                </div>
                <p class="text-muted mb-0">Declining this transaction will:</p>
                <ul class="text-muted">
                    <li>Set the transaction status to "Declined"</li>
                    <li>Prevent any further processing</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDecline">
                    <i class="bi bi-x-circle me-2"></i>Decline Transaction
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#creditTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: '{{ route("transactions.credit.list") }}',
                data: function(d) {
                    d.status = 'pending';
                    d.charge_type = $('#chargeTypeFilter').val();
                    d.date_from = $('#dateFromFilter').val();
                    d.date_to = $('#dateToFilter').val();
                }
            },
            columns: [{
                    data: null,
                    name: 'transaction_number',
                    render: function(data, type, row) {
                        return `<div>
                        <div class="fw-bold">${row.transaction_number}</div>
                        <small class="text-muted">${moment(row.created_at).format('MMM DD, YYYY h:mm A')}</small>
                        <hr class="my-2">
                        <div class="text-muted small">Processed by:</div>
                        <div class="fw-semibold">${row.processed_by}</div>
                    </div>`;
                    }
                },
                {
                    data: null,
                    name: 'charge_to',
                    render: function(data, type, row) {
                        let chargeTypeBadge = {
                            'department': '<span class="badge bg-info mb-2">Department</span>',
                            'faculty': '<span class="badge bg-secondary mb-2">Faculty</span>',
                            'staff': '<span class="badge bg-primary mb-2">Staff</span>'
                        } [row.charge_type?.toLowerCase()] || '<span class="badge bg-secondary mb-2">Unknown</span>';

                        return `<div>
                        ${chargeTypeBadge}
                        <div class="fw-semibold">${row.charge_to}</div>
                    </div>`;
                    }
                },
                {
                    data: null,
                    name: 'total_amount',
                    render: function(data, type, row) {
let badgeClass = {
                            'pending': 'bg-warning text-dark',
                            'approved': 'bg-success text-white',
                            'completed': 'bg-success text-white',
                            'declined': 'bg-danger text-white'
                        } [row.status.toLowerCase()] || 'bg-secondary';

                        let amount = 0;
                        if (row.total_amount) {
                            amount = typeof row.total_amount === 'string' ?
                                parseFloat(row.total_amount.replace(/[^\d.-]/g, '')) :
                                parseFloat(row.total_amount);
                        }

                        return `<div>
                        <div class="h5 mb-1">₱${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                        <span class="badge ${badgeClass}">${row.status}</span>
                    </div>`;
                    }
                },
                {
                    data: null,
                    width: '180px',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        let buttons = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary view-transaction" data-id="${row.id}" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>`;
                        
                        if (row.status.toLowerCase() === 'pending') {
                            buttons += `
                            <button class="btn btn-sm btn-success approve-transaction ms-1" data-id="${row.id}" title="Approve Transaction">
                                <i class="bi bi-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger decline-transaction ms-1" data-id="${row.id}" title="Decline Transaction">
                                <i class="bi bi-x"></i>
                            </button>`;
                        } else {
                            }
                        
                        buttons += '</div>';
                        return buttons;
                    }
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
            }
        });

        // Enhanced filter handling
        $('#chargeTypeFilter, #dateFromFilter, #dateToFilter').change(function() {
            table.ajax.reload();
        });
        
        // Report period change handler
        $('#reportPeriod').change(function() {
            if ($(this).val() === 'custom') {
                $('#customDateRange, #customDateRangeTo').removeClass('d-none');
            } else {
                $('#customDateRange, #customDateRangeTo').addClass('d-none');
            }
        });

        function loadSummaryData() {
            $.ajax({
                url: '{{ route("transactions.credit.summary") }}',
                method: 'GET',
                success: function(response) {
                    $('#total-transactions').text(response.summary.total);
                    $('#pending-transactions').text(response.summary.pending);
                    $('#approved-transactions').text(response.summary.approved);
                    $('#declined-transactions').text(response.summary.declined);

                    $('#department-list').empty();
                    response.departments.forEach(function(dept) {
                        $('#department-list').append(`
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            ${dept.charge_to}
                            <span class="badge bg-primary rounded-pill">₱${parseFloat(dept.total_amount).toFixed(2)}</span>
                        </div>
                    `);
                    });

                    $('#faculty-list').empty();
                    response.faculty.forEach(function(faculty) {
                        $('#faculty-list').append(`
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            ${faculty.charge_to}
                            <span class="badge bg-primary rounded-pill">₱${parseFloat(faculty.total_amount).toFixed(2)}</span>
                        </div>
                    `);
                    });
                }
            });
        }

        loadSummaryData();

        setInterval(loadSummaryData, 300000);

        let transactionIdToApprove = null;
        let transactionIdToDecline = null;
        let approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
        let declineModal = new bootstrap.Modal(document.getElementById('declineModal'));

        // Approve transaction handler
        $(document).on('click', '.approve-transaction', function() {
            transactionIdToApprove = $(this).data('id');
            
            // Get transaction details for the approval modal
            const row = table.row($(this).closest('tr')).data();
            const details = `
                <strong>ID:</strong> ${row.transaction_number}<br>
                <strong>Amount:</strong> ₱${parseFloat(row.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}<br>
                <strong>Charge to:</strong> ${row.charge_to} (${row.charge_type})
            `;
            $('#approval-transaction-details').html(details);
            
            approvalModal.show();
        });
        
        // Decline transaction handler
        $(document).on('click', '.decline-transaction', function() {
            transactionIdToDecline = $(this).data('id');
            
            // Get transaction details for the decline modal
            const row = table.row($(this).closest('tr')).data();
            const details = `
                <strong>ID:</strong> ${row.transaction_number}<br>
                <strong>Amount:</strong> ₱${parseFloat(row.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}<br>
                <strong>Charge to:</strong> ${row.charge_to} (${row.charge_type})
            `;
            $('#decline-transaction-details').html(details);
            $('#declineReason').val('');
            
            declineModal.show();
        });

        // Confirm approval
        $('#confirmApproval').click(function() {
            if (!transactionIdToApprove) return;

            $.ajax({
                url: `/transactions/credit/${transactionIdToApprove}/approve`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        approvalModal.hide();
                        table.ajax.reload();
                        loadSummaryData();
                        loadReports();
                        toastr.success('Transaction approved successfully', 'Success!');
                    } else {
                        toastr.error(response.message || 'Error approving transaction');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error approving transaction');
                }
            });
        });
        
        // Confirm decline
        $('#confirmDecline').click(function() {
            if (!transactionIdToDecline) return;
            
            const reason = $('#declineReason').val().trim();
            if (!reason) {
                toastr.warning('Please provide a reason for declining this transaction.');
                return;
            }

            $.ajax({
                url: `/transactions/credit/${transactionIdToDecline}/decline`,
                method: 'POST',
                data: {
                    reason: reason
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        declineModal.hide();
                        table.ajax.reload();
                        loadSummaryData();
                        loadReports();
                        toastr.success('Transaction declined successfully', 'Success!');
                    } else {
                        toastr.error(response.message || 'Error declining transaction');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error declining transaction');
                }
            });
        });

        let transactionDetailsModal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));

        $(document).on('click', '.view-transaction', function() {
            const transactionId = $(this).data('id');

            $.ajax({
                url: `/transactions/credit/${transactionId}/details`,
                method: 'GET',
                success: function(response) {
                    const totalAmount = parseFloat(response.total_amount).toLocaleString('en-US', {
                        style: 'decimal',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    $('#modal-transaction-number').text(response.transaction_number || 'N/A');
                    $('#modal-transaction-date').text(moment(response.created_at).format('MMM DD, YYYY h:mm A'));
                    $('#modal-processed-by').text(response.processed_by || 'N/A');
                    $('#modal-charge-to').text(response.charge_to || 'N/A');

const statusBadgeClass = {
                        'pending': 'bg-warning',
                        'approved': 'bg-success',
                        'completed': 'bg-success',
                        'declined': 'bg-danger'
                    } [response.status.toLowerCase()] || 'bg-secondary';

                    const textClass = statusBadgeClass === 'bg-warning' ? 'text-dark' : 'text-white';
                    $('#modal-status').html(`<span class=\"badge ${statusBadgeClass} ${textClass}\">${response.status}</span>`);

                    if (response.approved_by) {
                        $('#approval-details').removeClass('d-none');
                        $('#modal-approved-by').text(response.approved_by);
                        $('#modal-approved-at').text(moment(response.approved_at).format('MMM DD, YYYY h:mm A'));
                    } else {
                        $('#approval-details').addClass('d-none');
                    }

                    if (response.declined_by) {
                        $('#decline-details').removeClass('d-none');
                        $('#modal-declined-by').text(response.declined_by);
                        $('#modal-declined-at').text(moment(response.declined_at).format('MMM DD, YYYY h:mm A'));
                        $('#modal-decline-reason').text(response.decline_reason || '—');
                    } else {
                        $('#decline-details').addClass('d-none');
                        $('#modal-decline-reason').text('');
                    }

                    $('#modal-items-list').empty();
                    if (response.items && response.items.length > 0) {
                        response.items.forEach(item => {
                            const unitPrice = parseFloat(item.unit_price).toLocaleString('en-US', {
                                style: 'decimal',
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            const totalPrice = parseFloat(item.total_price).toLocaleString('en-US', {
                                style: 'decimal',
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });

                            $('#modal-items-list').append(`
                            <tr>
                                <td>${item.product_name}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-end">₱${unitPrice}</td>
                                <td class="text-end">₱${totalPrice}</td>
                            </tr>
                        `);
                        });
                    }

                    $('#modal-total-amount').text(`₱${totalAmount}`);

                    transactionDetailsModal.show();
                },
                error: function(xhr) {
                    toastr.error('Failed to load transaction details');
                }
            });
        });

        if ($('#creditHistoryTable').length) {
            var historyTable = $('#creditHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route("transactions.credit.list") }}',
                    data: function(d) {
                        d.status = $('#historyStatusFilter').val();
                        d.charge_type = $('#historyChargeTypeFilter').val();
                        d.charge_to = $('#historyChargeToFilter').val();
                        d.processed_by = $('#historyProcessedByFilter').val();
                        d.date_from = $('#historyDateFromFilter').val();
                        d.date_to = $('#historyDateToFilter').val();
                        d.amount_min = $('#historyAmountMinFilter').val();
                        d.amount_max = $('#historyAmountMaxFilter').val();
                    }
                },
                columns: [
                    { data: 'transaction_number', name: 'transaction_number' },
                    { data: 'created_at', name: 'created_at', render: function(data) { return moment(data).format('MMM DD, YYYY h:mm A'); } },
                    { data: 'processed_by', name: 'processed_by' },
                    { data: 'charge_type', name: 'charge_type', render: function(data) {
                        const type = (data || '').toLowerCase();
                        const map = { department: 'info', faculty: 'secondary', staff: 'primary' };
                        const cls = map[type] || 'secondary';
                        return `<span class="badge bg-${cls}">${data || '—'}</span>`;
                    }},
                    { data: 'charge_to', name: 'charge_to' },
                    { data: 'total_amount', name: 'total_amount', className: 'text-end' },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                    { data: null, orderable: false, searchable: false, className: 'text-center', render: function(data, type, row) {
                        return `<button class="btn btn-sm btn-primary view-transaction" data-id="${row.id}" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>`;
                    }}
                ],
                order: [[1, 'desc']]
            });

            $('#historyStatusFilter, #historyChargeTypeFilter, #historyChargeToFilter, #historyProcessedByFilter, #historyDateFromFilter, #historyDateToFilter, #historyAmountMinFilter, #historyAmountMaxFilter').on('change keyup', function() {
                historyTable.ajax.reload();
            });
        }

        if (!$('#creditTable').length) {
            var regularTable = $('#regularUserCreditTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route("transactions.credit.list") }}',
                    data: function(d) {
                        d.status = $('#statusFilter').val();
                    }
                },
                columns: [{
                        data: 'transaction_number',
                        name: 'transaction_number'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return moment(data).format('MMM DD, YYYY h:mm A');
                        }
                    },
                    {
                        data: 'charge_to',
                        name: 'charge_to'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: 'text-end',
                        render: function(data) {
                            let amount = 0;
                            if (data) {
                                amount = typeof data === 'string' ?
                                    parseFloat(data.replace(/[^\d.-]/g, '')) :
                                    parseFloat(data);
                            }

                            return '₱' + amount.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            const badgeClass = {
                                'pending': 'bg-warning',
                                'approved': 'bg-success',
                                'completed': 'bg-success',
                                'declined': 'bg-danger'
                            } [data.toLowerCase()] || 'bg-secondary';
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            const viewBtn = `<button class=\"btn btn-sm btn-primary view-transaction\" data-id=\"${data}\" title=\"View\">\r
                                <i class=\"bi bi-eye-fill\"></i> View\r
                            </button>`;
                            let approveBtn = '';
                            if ((row.status || '').toLowerCase() === 'pending') {
                                approveBtn = ` <button class=\"btn btn-sm btn-success approve-transaction ms-1\" data-id=\"${data}\" title=\"Approve\">\r
                                    <i class=\"bi bi-check-circle-fill\"></i>
                                </button>`;
                            }
                            return viewBtn + approveBtn;
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });

            $('#statusFilter').change(function() {
                regularTable.ajax.reload();
            });
        }
        
        // Enhanced Reports Functionality
        window.loadReports = function() {
            const period = $('#reportPeriod').val();
            const dateFrom = $('#reportDateFrom').val();
            const dateTo = $('#reportDateTo').val();
            
            $.ajax({
                url: '{{ route("transactions.credit.reports") }}',
                method: 'GET',
                data: {
                    period: period,
                    date_from: dateFrom,
                    date_to: dateTo
                },
                success: function(response) {
                    // Update analytics cards
                    $('#reportTotalAmount').text(`₱${parseFloat(response.analytics.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                    $('#reportPendingAmount').text(`₱${parseFloat(response.analytics.pending_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                    $('#reportApprovedAmount').text(`₱${parseFloat(response.analytics.approved_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}`);
                    $('#reportDepartmentCount').text(response.analytics.department_count || 0);
                    
                    // Update department report table
                    $('#departmentReportTable').empty();
                    if (response.departments && response.departments.length > 0) {
                        response.departments.forEach(function(dept) {
                            $('#departmentReportTable').append(`
                                <tr>
                                    <td class="fw-semibold">${dept.charge_to}</td>
                                    <td class="text-end">₱${parseFloat(dept.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="text-end">₱${parseFloat(dept.pending_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="text-center"><span class="badge bg-primary rounded-pill">${dept.transaction_count}</span></td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#departmentReportTable').append(`
                            <tr><td colspan="4" class="text-center text-muted">No department data found for selected period</td></tr>
                        `);
                    }
                    
                    // Update faculty report table
                    $('#facultyReportTable').empty();
                    if (response.faculty && response.faculty.length > 0) {
                        response.faculty.forEach(function(faculty) {
                            const typeClass = faculty.charge_type === 'faculty' ? 'bg-secondary' : 'bg-info';
                            $('#facultyReportTable').append(`
                                <tr>
                                    <td class="fw-semibold">${faculty.charge_to}</td>
                                    <td><span class="badge ${typeClass}">${faculty.charge_type}</span></td>
                                    <td class="text-end">₱${parseFloat(faculty.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="text-center"><span class="badge bg-primary rounded-pill">${faculty.transaction_count}</span></td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#facultyReportTable').append(`
                            <tr><td colspan="4" class="text-center text-muted">No faculty/staff data found for selected period</td></tr>
                        `);
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to load reports data');
                }
            });
        };
        
        // Export Reports
        window.exportReports = function() {
            const period = $('#reportPeriod').val();
            const dateFrom = $('#reportDateFrom').val();
            const dateTo = $('#reportDateTo').val();
            
            const params = new URLSearchParams({
                period: period,
                date_from: dateFrom || '',
                date_to: dateTo || '',
                format: 'excel'
            });
            
            const url = `{{ route("transactions.credit.export") }}?${params.toString()}`;
            window.open(url, '_blank');
            
            toastr.info('Report export initiated. Download will start shortly.');
        };
        
        // Load initial reports
        setTimeout(function() {
            if ($('#reports-tab').length) {
                loadReports();
            }
        }, 1000);
        
        // Reload reports when tab is activated
        $('#reports-tab').on('shown.bs.tab', function() {
            loadReports();
        });
    });
</script>
@endpush

@endsection