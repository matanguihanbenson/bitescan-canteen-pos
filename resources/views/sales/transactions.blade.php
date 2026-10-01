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

.transactions-card {
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

.card {
    transition: transform .2s;
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 600;
}

#transactionsTable {
    border-collapse: separate;
    border-spacing: 0;
}

#transactionsTable thead th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

/* Modal Styles */
.modal-content {
    border-radius: 0.75rem;
}

.modal-header {
    border-radius: 0.75rem 0.75rem 0 0;
}

.table> :not(caption)>*>* {
    padding: 0.75rem 0;
}

.badge {
    font-weight: 500;
}

#modalItemsList tr:last-child td {
    border-bottom: none;
}

.text-muted {
    color: #6c757d !important;
}

.fw-medium {
    font-weight: 500;
}

/* Additional Modal Styles */
.modal .table {
    margin-bottom: 0;
}

.modal .table> :not(caption)>*>* {
    padding: 1rem 0.75rem;
}

.modal .list-group-item {
    background: transparent;
}

.modal .badge {
    padding: 0.5em 1em;
}

#modalItemsList tr td {
    border-bottom-width: 1px;
}

#modalItemsList tr:last-child td {
    border-bottom: none;
}

.modal .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Additional Modal Styles */
.modal .table> :not(caption)>*>* {
    padding: 0.5rem 0.5rem;
}

.modal .fs-7 {
    font-size: 0.875rem;
}

.modal .badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.modal .table {
    font-size: 0.875rem;
}

.modal-body {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

#modalItemsList tr:last-child td {
    border-bottom: none;
}

/* DataTable Styles */
.table> :not(caption)>*>* {
    padding: 0.75rem 0.75rem;
    vertical-align: middle;
}

#transactionsTable {
    width: 100% !important;
}

#transactionsTable thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

#transactionsTable tbody td {
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}

.dataTables_wrapper .dataTables_info {
    font-size: 0.875rem;
    padding-top: 1rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white !important;
}

.dataTables_wrapper .dataTables_processing {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* Enhanced DataTable Styles */
#transactionsTable thead th {
    text-align: center !important;
    vertical-align: middle !important;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1rem;
}

#transactionsTable tbody td {
    text-align: center !important;
    vertical-align: middle !important;
    padding: 0.75rem;
}

#transactionsTable tbody td:nth-child(2) {
    /* Processed By column */
    text-align: left !important;
}

#transactionsTable .badge {
    min-width: 100px;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    padding: 1rem;
}

/* Keep amount right-aligned */
.text-end {
    text-align: right !important;
}

/* Filter Styles */
.input-group {
    transition: all 0.2s;
}

.input-group:hover {
    box-shadow: 0 0 0 0.1rem rgba(13, 110, 253, 0.25);
}

.input-group:focus-within {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-select:focus,
.form-control:focus {
    box-shadow: none;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Transaction History</h4>
                    <p class="text-muted mb-0">Monitor and manage all sales transactions and payment records</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transactions</li>
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
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Today's Transactions</h6>
                            <h3 id="todayTotal" class="mb-1 text-dark fw-semibold">0</h3>
                            <p class="text-muted mb-0 small">transactions today</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-calendar-day text-white fs-3"></i>
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
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Today's Revenue</h6>
                            <h3 id="todayAmount" class="mb-1 text-dark fw-semibold">₱0.00</h3>
                            <p class="text-muted mb-0 small">total sales today</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-currency-peso text-white fs-3"></i>
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
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Cash on Hand</h6>
                            <h3 id="cashOnHand" class="mb-1 text-dark fw-semibold">₱0.00</h3>
                            <p class="text-muted mb-0 small">today's cash payments</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-info bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-cash text-white fs-3"></i>
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
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Pending Transactions</h6>
                            <h3 id="pendingCount" class="mb-1 text-dark fw-semibold">0</h3>
                            <p class="text-muted mb-0 small">require attention</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card transactions-card border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-receipt-cutoff me-2 fs-5 text-white"></i>
                    <div>
                        <h5 class="card-title mb-0 fw-semibold text-white">Transaction History</h5>
                        <small class="text-white opacity-90">Complete record of all sales transactions</small>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="input-group input-group-sm border rounded" style="width: 200px;">
                        <span class="input-group-text border-0">
                            <i class="bi bi-funnel"></i>
                        </span>
                        <select class="form-select border-0" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm border rounded" style="width: 200px;">
                        <span class="input-group-text border-0">
                            <i class="bi bi-credit-card"></i>
                        </span>
                        <select class="form-select border-0" id="paymentFilter">
                            <option value="">All Payments</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm border rounded" style="width: 200px;">
                        <span class="input-group-text border-0">
                            <i class="bi bi-calendar-range"></i>
                        </span>
                        <select class="form-select border-0" id="periodFilter">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2" id="customDateRange" style="display: none;">
                        <div class="input-group input-group-sm border rounded">
                            <span class="input-group-text border-0">
                                <i class="bi bi-calendar-range"></i>
                            </span>
                            <input type="date" class="form-control border-0" id="startDate" placeholder="Start Date">
                        </div>
                        <div class="input-group input-group-sm border rounded">
                            <span class="input-group-text border-0">
                                <i class="bi bi-calendar-range"></i>
                            </span>
                            <input type="date" class="form-control border-0" id="endDate" placeholder="End Date">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <table id="transactionsTable" class="table table-hover align-middle w-100">
                <thead class="bg-light">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Processed By</th>
                        <th class="text-end">Total Amount</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th class="text-center" style="width: 80px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2 border-0 bg-primary bg-opacity-10">
                <h5 class="modal-title">
                    <i class="fas fa-receipt text-primary me-2"></i>
                    <span class="fw-bold">Transaction Details</span>
                    <span class="text-muted fw-normal fs-6 ms-2" id="modalTransactionNumber"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Transaction Summary Card -->
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body py-2">
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <div class="d-flex flex-column border-start border-primary border-3 ps-2">
                                    <span class="text-muted small text-uppercase">Date & Time</span>
                                    <span class="fw-medium small" id="modalDateTime"></span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex flex-column border-start border-success border-3 ps-2">
                                    <span class="text-muted small text-uppercase">Status</span>
                                    <span id="modalStatus"></span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex flex-column border-start border-info border-3 ps-2">
                                    <span class="text-muted small text-uppercase">Payment Type</span>
                                    <span class="fw-medium small" id="modalPaymentType"></span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex flex-column border-start border-warning border-3 ps-2">
                                    <span class="text-muted small text-uppercase">Processed By</span>
                                    <span class="fw-medium small" id="modalProcessedBy"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-2 d-flex align-items-center fs-7">
                            <i class="fas fa-shopping-basket text-primary me-2"></i>
                            Order Items
                        </h6>
                        <div class="table-responsive shadow-sm rounded">
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-nowrap ps-2 py-2">Product</th>
                                        <th class="text-nowrap py-2">Unit</th>
                                        <th class="text-end py-2">Qty</th>
                                        <th class="text-end py-2">Price</th>
                                        <th class="text-end pe-2 py-2">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="modalItemsList"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <h6 class="fw-bold mb-2 d-flex align-items-center fs-7">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                            Payment Summary
                        </h6>
                        <div class="bg-light rounded p-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Subtotal</span>
                                <span id="modalSubtotal" class="fw-medium"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Discount</span>
                                <span id="modalDiscount" class="fw-medium text-danger"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                                <span class="fw-bold">Total</span>
                                <span id="modalTotal" class="fw-bold text-primary"></span>
                            </div>

                            <!-- Credit Payment Details -->
                            <div id="creditPaymentDetails" class="mt-3 pt-2 border-top" style="display: none;">
                                <h6 class="fs-7 mb-2 fw-bold text-primary">
                                    <i class="fas fa-credit-card"></i>
                                    Credit Payment Details
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="text-muted small ps-0" style="width: 120px;">Charge Type:</td>
                                            <td id="modalChargeType" class="fw-medium"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted small ps-0">Charge To:</td>
                                            <td id="modalChargeTo" class="fw-medium"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 border-0 bg-light bg-opacity-75">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="printReceipt()">
                    <i class="fas fa-print me-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(document).ready(function() {
    const transactionModal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));

    // Function to get date range based on period
    function getDateRange(period) {
        const today = new Date();
        const startOfDay = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const endOfDay = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59);

        switch (period) {
            case 'today':
                return {
                    start: startOfDay.toISOString().split('T')[0],
                        end: endOfDay.toISOString().split('T')[0]
                };

            case 'week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                return {
                    start: startOfWeek.toISOString().split('T')[0],
                        end: endOfWeek.toISOString().split('T')[0]
                };

            case 'month':
                const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                return {
                    start: startOfMonth.toISOString().split('T')[0],
                        end: endOfMonth.toISOString().split('T')[0]
                };

            case 'year':
                const startOfYear = new Date(today.getFullYear(), 0, 1);
                const endOfYear = new Date(today.getFullYear(), 11, 31);
                return {
                    start: startOfYear.toISOString().split('T')[0],
                        end: endOfYear.toISOString().split('T')[0]
                };

            case 'custom':
                return {
                    start: $('#startDate').val(),
                        end: $('#endDate').val()
                };

            default:
                return {
                    start: startOfDay.toISOString().split('T')[0],
                        end: endOfDay.toISOString().split('T')[0]
                };
        }
    }

    let table = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('transactions.data') }}",
            data: function(d) {
                d.status = $('#statusFilter').val();
                d.payment_type = $('#paymentFilter').val();

                const period = $('#periodFilter').val();
                const dateRange = getDateRange(period);
                d.start_date = dateRange.start;
                d.end_date = dateRange.end;
            }
        },
        columns: [{
                data: 'transaction_number',
                name: 'transaction_number',
                className: 'text-center'
            },
            {
                data: 'processed_by',
                name: 'processed_by',
                className: 'text-start'
            },
            {
                data: 'total_amount',
                name: 'total_amount',
                className: 'text-end',
                render: function(data) {
                    return '₱' + parseFloat(data).toFixed(2);
                }
            },
            {
                data: 'payment_type',
                name: 'payment_type',
                className: 'text-center text-capitalize'
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center'
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center',
                render: function(data) {
                    return moment(data).format('MMM D, YYYY h:mm A');
                }
            },
            {
                data: 'actions',
                name: 'actions',
                className: 'text-center',
                orderable: false,
                searchable: false
            }
        ],
        order: [
            [5, 'desc']
        ],
        pageLength: 25,
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Set default to today and hide custom date range initially
    $('#periodFilter').val('today');
    $('#customDateRange').hide();

    // Handle period filter change
    $('#periodFilter').change(function() {
        const period = $(this).val();
        const customDateRange = $('#customDateRange');

        if (period === 'custom') {
            customDateRange.show();
            // Set default dates for custom range only when custom is selected
            const today = new Date().toISOString().split('T')[0];
            $('#startDate').val(today);
            $('#endDate').val(today);
        } else {
            customDateRange.hide();
            // Clear custom date values when not using custom
            $('#startDate').val('');
            $('#endDate').val('');
        }

        table.draw();
    });

    $('#statusFilter, #paymentFilter, #startDate, #endDate').change(function() {
        table.draw();
    });

    $('#transactionsTable').on('click', '.view-transaction', function() {
        const transactionId = $(this).data('id');

        $.get(`/transactions/${transactionId}/details`)
            .done(function(response) {
                if (response.error) {
                    toastr.error(response.message || 'Failed to load transaction details');
                    return;
                }
                updateModalWithTransactionDetails(response);
                transactionModal.show();
            })
            .fail(function(error) {
                toastr.error(error.responseJSON?.message || 'Failed to load transaction details');
            });
    });

    function getStatusClass(status) {
        const classes = {
            completed: 'success',
            pending: 'warning',
            declined: 'danger'
        };
        return classes[status] || 'secondary';
    }
});

function updateInsights() {
    $.ajax({
        url: '{{ route("transactions.insights") }}',
        method: 'GET',
        success: function(data) {
            $('#todayTotal').text(data.today_total || 0);
            $('#todayAmount').text('₱' + (parseFloat(data.today_amount) || 0).toFixed(2));
            $('#cashOnHand').text('₱' + (parseFloat(data.cash_on_hand) || 0).toFixed(2));
            $('#averageTransaction').text('₱' + (parseFloat(data.average_transaction) || 0).toFixed(2));
            $('#pendingCount').text(data.pending_count || 0);
        }
    });
}

$(document).ready(function() {
    updateInsights();
    setInterval(updateInsights, 300000);
});

function updateModalWithTransactionDetails(response) {
    const transaction = response.transaction;
    $('#transactionDetailsModal').data('transaction-id', transaction.id);
    const items = response.items || [];

    $('#modalTransactionNumber').text('#' + (transaction.transaction_number || ''));
    $('#modalDateTime').text(moment(transaction.created_at).format('MMM D, YYYY h:mm A'));
    $('#modalPaymentType').text(
        transaction.payment_type ? transaction.payment_type.toUpperCase() : 'CASH'
    );
    $('#modalProcessedBy').text(transaction.user?.name || 'System');

    const status = (transaction.status || 'pending').toLowerCase();
    const statusClass = {
        'completed': 'bg-success',
        'pending': 'bg-warning text-dark',
        'cancelled': 'bg-danger'
    } [status] || 'bg-secondary';

    $('#modalStatus').html(`
            <span class="badge ${statusClass}">
                ${status.charAt(0).toUpperCase() + status.slice(1)}
            </span>
        `);

    let itemsHtml = '';
    if (items.length > 0) {
        items.forEach(item => {
            itemsHtml += `
                    <tr>
                        <td class="ps-0 text-nowrap">${item.product_name || 'Unknown Product'}</td>
                        <td>${item.unit || '-'}</td>
                        <td class="text-end">${item.quantity || 0}</td>
                        <td class="text-end">₱${parseFloat(item.unit_price || 0).toFixed(2)}</td>
                        <td class="text-end pe-0">₱${parseFloat(item.subtotal || 0).toFixed(2)}</td>
                    </tr>
                `;
        });
    } else {
        itemsHtml = '<tr><td colspan="5" class="text-center">No items found</td></tr>';
    }
    $('#modalItemsList').html(itemsHtml);

    $('#modalSubtotal').text('₱' + parseFloat(transaction.subtotal || 0).toFixed(2));
    $('#modalDiscount').text('-₱' + parseFloat(transaction.discount || 0).toFixed(2));
    $('#modalTotal').text('₱' + parseFloat(transaction.total_amount || 0).toFixed(2));

    const creditDetails = $('#creditPaymentDetails');
    if (transaction.payment_type?.toLowerCase() === 'credit') {
        creditDetails.show();
        $('#modalChargeType').text(transaction.charge_type || 'N/A');
        $('#modalChargeTo').text(transaction.charge_to || 'N/A');
        $('#modalDepartment').text(transaction.department || 'N/A');

        if (transaction.due_date) {
            const dueDate = moment(transaction.due_date);
            const today = moment();
            const daysUntilDue = dueDate.diff(today, 'days');

            let dueDateClass = 'text-success';
            if (daysUntilDue < 0) {
                dueDateClass = 'text-danger';
            } else if (daysUntilDue <= 7) {
                dueDateClass = 'text-warning';
            }

            $('#modalDueDate').addClass(dueDateClass);
        }
    } else {
        creditDetails.hide();
    }
}

function printReceipt() {
    const transactionId = $('#transactionDetailsModal').data('transaction-id');
    if (!transactionId) return;

    const receiptWindow = window.open(
        `/transactions/${transactionId}/print`,
        'ReceiptPreview',
        'width=400,height=600,menubar=no,toolbar=no,location=no,status=no'
    );
}
</script>
@endpush

@endsection