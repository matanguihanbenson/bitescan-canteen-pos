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

    .inventory-analytics-card {
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
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">Inventory Analytics Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive overview of inventory performance and analytics</p>
                </div>
                <div class="page-title-right">
                    <div class="d-flex align-items-center">
                        <ol class="breadcrumb m-0 me-3">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Inventory Analytics</li>
                        </ol>
                        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#reportOptionsModal">
                            <i class="bi bi-download me-1"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.inventory-report.summary-cards')

    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            @include('components.inventory-report.stock-trend-chart')
        </div>
        <div class="col-xl-4">
            @include('components.inventory-report.category-distribution-chart')
        </div>
    </div>

    @include('components.inventory-report.inventory-table')
</div>

<!-- Report Options Modal -->
<div class="modal fade" id="reportOptionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Report Options
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportOptionsForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date Range</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="start_date" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                            <span class="input-group-text">to</span>
                            <input type="date" class="form-control" name="end_date" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="form-text">Select the date range for your analytics report</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Include Sections</label>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="sections[]" value="summary" checked id="section_summary">
                            <label class="form-check-label" for="section_summary">Summary</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="sections[]" value="trends" checked id="section_trends">
                            <label class="form-check-label" for="section_trends">Trends</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" name="sections[]" value="categories" checked id="section_categories">
                            <label class="form-check-label" for="section_categories">Categories</label>
                        </div>
                        <div class="form-text">Choose which sections to include in your report</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-download me-1"></i>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('components.inventory-report.scripts')

<script>
    document.getElementById('reportOptionsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const queryString = new URLSearchParams(formData).toString();

        window.open(`{{ route('inventory.analytics.export') }}?${queryString}`, 'AnalyticsReport', 'width=900,height=1100,menubar=no,toolbar=no,location=no,status=no');

        bootstrap.Modal.getInstance(document.getElementById('reportOptionsModal')).hide();
    });
</script>
@endsection