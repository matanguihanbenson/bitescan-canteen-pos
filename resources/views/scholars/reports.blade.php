@extends('layouts.app')

@section('title', 'Scholar Reports')

@push('styles')
<style>
    .filter-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .summary-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .avatar-sm {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .report-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
    }

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
                    <h4 class="mb-1 font-size-18 fw-semibold">Scholar Reports</h4>
                    <p class="text-muted mb-0">Generate and export detailed reports on scholar allowances and usage</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Scholar Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card filter-card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-funnel-fill me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Report Filters</h5>
                            <small class="text-white opacity-90">Configure your report parameters and date range</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label for="dateFrom" class="form-label fw-semibold">Date From</label>
                            <input type="date" class="form-control" id="dateFrom" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="dateTo" class="form-label fw-semibold">Date To</label>
                            <input type="date" class="form-control" id="dateTo" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="reportType" class="form-label fw-semibold">Report Type</label>
                            <select class="form-select" id="reportType">
                                <option value="allowances">Allowance Claims</option>
                                <option value="usage">Scholar Usage</option>
                                <option value="both">Both Reports</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="button" class="btn btn-primary flex-fill" id="generateReport">
                                    <i class="bi bi-search me-1"></i> Generate Report
                                </button>
                                <button type="button" class="btn btn-success" id="exportReport" disabled>
                                    <i class="bi bi-download me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4" id="summaryCards" style="display: none;">
        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Claims/Transactions</h6>
                            <h3 class="mb-0 fw-semibold" id="totalCount">0</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-primary bg-gradient rounded-3">
                                <i class="bi bi-hash text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Amount</h6>
                            <h3 class="mb-0 fw-semibold" id="totalAmount">₱0.00</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-success bg-gradient rounded-3">
                                <i class="bi bi-cash-coin text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Unique Scholars</h6>
                            <h3 class="mb-0 fw-semibold" id="uniqueScholars">0</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-info bg-gradient rounded-3">
                                <i class="bi bi-people text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Date Range</h6>
                            <h6 class="mb-0 text-muted fw-medium" id="dateRange">-</h6>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-warning bg-gradient rounded-3">
                                <i class="bi bi-calendar text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Allowance Claims Report -->
    <div class="row mt-4" id="allowanceReport" style="display: none;">
        <div class="col-12">
            <div class="card report-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-gift-fill me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Allowance Claims Report</h5>
                            <small class="text-white opacity-90">Detailed breakdown of allowance claims by scholars</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="allowanceTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold text-dark px-4 py-3">Date</th>
                                    <th class="border-0 fw-bold text-dark py-3">Student ID</th>
                                    <th class="border-0 fw-bold text-dark py-3">Scholar Name</th>
                                    <th class="border-0 fw-bold text-dark py-3">Amount</th>
                                    <th class="border-0 fw-bold text-dark py-3">Description</th>
                                    <th class="border-0 fw-bold text-dark py-3">Processed By</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Report -->
    <div class="row mt-4" id="usageReport" style="display: none;">
        <div class="col-12">
            <div class="card report-card border-0 shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-cart-fill me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Scholar Usage Report</h5>
                            <small class="text-white opacity-90">Transaction history and spending patterns</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usageTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold text-dark px-4 py-3">Date</th>
                                    <th class="border-0 fw-bold text-dark py-3">Student ID</th>
                                    <th class="border-0 fw-bold text-dark py-3">Scholar Name</th>
                                    <th class="border-0 fw-bold text-dark py-3">Amount</th>
                                    <th class="border-0 fw-bold text-dark py-3">Description</th>
                                    <th class="border-0 fw-bold text-dark py-3">Transaction #</th>
                                    <th class="border-0 fw-bold text-dark py-3">Processed By</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let allowanceData = [];
    let usageData = [];

    $(document).ready(function() {
        $('#generateReport').on('click', function() {
            generateReport();
        });

        $('#exportReport').on('click', function() {
            exportReport();
        });
    });

    function generateReport() {
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        const reportType = $('#reportType').val();

        if (!dateFrom || !dateTo) {
            toastr.error('Please select both from and to dates');
            return;
        }

        if (new Date(dateFrom) > new Date(dateTo)) {
            toastr.error('From date cannot be later than to date');
            return;
        }

        // Show loading
        $('#generateReport').html('<i class="bi bi-hourglass-split me-1"></i> Loading...');
        $('#generateReport').prop('disabled', true);

        // Hide all reports first
        $('#summaryCards, #allowanceReport, #usageReport').hide();

        const requests = [];

        if (reportType === 'allowances' || reportType === 'both') {
            requests.push(
                $.get('{{ route("scholars.reports.data") }}', {
                    date_from: dateFrom,
                    date_to: dateTo,
                    transaction_type: 'credit'
                })
            );
        } else {
            requests.push(null);
        }

        if (reportType === 'usage' || reportType === 'both') {
            requests.push(
                $.get('{{ route("scholars.reports.data") }}', {
                    date_from: dateFrom,
                    date_to: dateTo,
                    transaction_type: 'debit'
                })
            );
        } else {
            requests.push(null);
        }

        Promise.all(requests)
            .then(responses => {
                let allowanceResponse = responses[0];
                let usageResponse = responses[1];

                // Process allowance data
                if (allowanceResponse && allowanceResponse.success) {
                    allowanceData = allowanceResponse.data.filter(item => item.type === 'Credit');
                    populateAllowanceTable(allowanceData);
                    updateSummary(allowanceResponse.summary, 'allowance');
                    $('#allowanceReport').show();
                }

                // Process usage data
                if (usageResponse && usageResponse.success) {
                    usageData = usageResponse.data.filter(item => item.type === 'Debit');
                    populateUsageTable(usageData);
                    updateSummary(usageResponse.summary, 'usage');
                    $('#usageReport').show();
                }

                if (allowanceResponse || usageResponse) {
                    $('#summaryCards').show();
                    $('#exportReport').prop('disabled', false);
                    toastr.success('Report generated successfully');
                } else {
                    toastr.warning('No data found for the selected criteria');
                }
            })
            .catch(error => {
                toastr.error('Error generating report');
            })
            .finally(() => {
                $('#generateReport').html('<i class="bi bi-search me-1"></i> Generate Report');
                $('#generateReport').prop('disabled', false);
            });
    }

    function populateAllowanceTable(data) {
        const tbody = $('#allowanceTable tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="6" class="text-center">No allowance claims found for the selected period</td></tr>');
            return;
        }

        data.forEach(record => {
            const row = `
                <tr>
                    <td>${record.date}</td>
                    <td>${record.student_id}</td>
                    <td>${record.scholar_name}</td>
                    <td><span class="badge bg-success">₱${record.amount}</span></td>
                    <td>${record.description}</td>
                    <td>${record.processed_by}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function populateUsageTable(data) {
        const tbody = $('#usageTable tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center">No usage transactions found for the selected period</td></tr>');
            return;
        }

        data.forEach(record => {
            const row = `
                <tr>
                    <td>${record.date}</td>
                    <td>${record.student_id}</td>
                    <td>${record.scholar_name}</td>
                    <td><span class="badge bg-danger">₱${record.amount}</span></td>
                    <td>${record.description}</td>
                    <td>${record.transaction_number || '-'}</td>
                    <td>${record.processed_by}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updateSummary(summary, type) {
        if (type === 'allowance') {
            $('#totalCount').text(summary.total_transactions);
            $('#totalAmount').text('₱' + Number(summary.total_credits).toFixed(2));
        } else if (type === 'usage') {
            $('#totalCount').text(summary.total_transactions);
            $('#totalAmount').text('₱' + Number(summary.total_debits).toFixed(2));
        }

        $('#uniqueScholars').text(summary.unique_scholars);
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        $('#dateRange').text(`${dateFrom} to ${dateTo}`);
    }

    function exportReport() {
        // Use server-side export endpoint
        const reportType = $('#reportType').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        
        if (!dateFrom || !dateTo) {
            toastr.error('Please select date range first');
            return;
        }
        
        let params = new URLSearchParams({
            date_from: dateFrom,
            date_to: dateTo,
            format: 'excel'
        });
        
        if (reportType === 'allowances') {
            params.append('transaction_type', 'credit');
        } else if (reportType === 'usage') {
            params.append('transaction_type', 'debit');
        }
        
        const exportUrl = '{{ route("scholars.reports.export") }}?' + params.toString();
        
        // Create a hidden link and trigger download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        toastr.success('Export started. Download will begin shortly.');
    }
</script>
@endsection