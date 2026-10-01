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

.inventory-logs-card {
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

/* Fix DataTables styling */
.dataTables_wrapper .dataTables_filter label {
    color: #495057 !important;
    font-weight: 500;
}

.dataTables_wrapper .dataTables_length label {
    color: #495057 !important;
    font-weight: 500;
}

.dataTables_wrapper .dataTables_info {
    color: #6c757d !important;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Stock Movement History</h4>
                    <p class="text-muted mb-0">Track and monitor all inventory movements and adjustments</p>
                </div>
                <div class="page-title-right">
                    <div class="d-flex align-items-center">
                        <ol class="breadcrumb m-0 me-3">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Inventory Logs</li>
                        </ol>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="refreshTable">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Movements</h6>
                            <h3 id="totalMovements" class="mb-1 text-dark fw-semibold">Loading...</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-box text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Stock Ins</h6>
                            <h3 id="totalStockIns" class="mb-1 text-dark fw-semibold">Loading...</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-box-arrow-in-down text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Stock Outs</h6>
                            <h3 id="totalStockOuts" class="mb-1 text-dark fw-semibold">Loading...</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-danger bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-box-arrow-up text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Adjustments</h6>
                            <h3 id="totalAdjustments" class="mb-1 text-dark fw-semibold">Loading...</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-gear text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card inventory-logs-card border-0 shadow-sm">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex align-items-center">
                <i class="bi bi-clock-history me-2 fs-5 text-white"></i>
                <div>
                    <h5 class="card-title mb-0 fw-semibold text-white">Movement History</h5>
                    <small class="text-white opacity-90">Detailed log of all inventory movements and changes</small>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Add Filters -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">From Date</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="dateFrom">
                        <button class="btn btn-outline-secondary" type="button" id="clearDateFrom">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">To Date</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="dateTo">
                        <button class="btn btn-outline-secondary" type="button" id="clearDateTo">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Movement Type</label>
                    <select class="form-select" id="movementType">
                        <option value="">All Types</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Search Product</label>
                    <input type="text" class="form-control" id="productSearch" placeholder="Search by product name">
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="button" class="btn btn-primary w-100" id="applyFilters">
                        <i class="bi bi-funnel me-1"></i>Apply Filters
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="logsTable" class="table table-hover align-middle" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-bold text-dark px-4 py-3">Transaction Details</th>
                            <th class="border-0 fw-bold text-dark py-3">Product</th>
                            <th class="border-0 fw-bold text-dark py-3">Movement Details</th>
                            <th class="border-0 fw-bold text-dark py-3">User Details</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Export Filter Modal -->
<div class="modal fade" id="exportFilterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-download me-2"></i>Export Settings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label class="form-label">Select Date</label>
                        <input type="date" class="form-control" id="exportFilterDate"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Movement Type</label>
                        <select class="form-select" id="exportMovementType">
                            <option value="">All Types</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fields to Export</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="date" id="field_date" checked>
                            <label class="form-check-label" for="field_date">Date</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="reference" id="field_reference"
                                checked>
                            <label class="form-check-label" for="field_reference">Reference</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="product" id="field_product" checked>
                            <label class="form-check-label" for="field_product">Product</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="type" id="field_type" checked>
                            <label class="form-check-label" for="field_type">Movement Type</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="quantity" id="field_quantity"
                                checked>
                            <label class="form-check-label" for="field_quantity">Quantity</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="user" id="field_user" checked>
                            <label class="form-check-label" for="field_user">Updated By</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="remarks" id="field_remarks" checked>
                            <label class="form-check-label" for="field_remarks">Remarks</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmExport">Export</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    function updateSummaryCards() {
        $.get("{{ route('inventory.logs.summary') }}", function(data) {
            $('#totalMovements').text(data.total);
            $('#totalStockIns').text(data.stock_ins);
            $('#totalStockOuts').text(data.stock_outs);
            $('#totalAdjustments').text(data.adjustments);
        });
    }

    updateSummaryCards();

    $('#clearDateFrom').click(function() {
        $('#dateFrom').val('');
        table.draw();
    });

    $('#clearDateTo').click(function() {
        $('#dateTo').val('');
        table.draw();
    });

    var table = $('#logsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('inventory.logs.data') }}",
            data: function(d) {
                return {
                    ...d,
                    dateFrom: $('#dateFrom').val(),
                    dateTo: $('#dateTo').val(),
                    movementType: $('#movementType').val(),
                    productSearch: $('#productSearch').val()
                };
            },
            error: function(xhr, error, thrown) {
                }
        },
        columns: [{
                data: null,
                orderData: 4,
                orderable: false,
                render: function(data) {
                    return `<div class="h-100">
                        <div class="transaction-details">
                            <div class="reference-number">
                                <i class="bi bi-hash me-1"></i>${data.reference}
                            </div>
                            <div class="timestamp">
                                <i class="bi bi-calendar3 me-1"></i>${moment(data.date).format('MMM DD, YYYY h:mm A')}
                            </div>
                        </div>
                    </div>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    return `<div class="info-box">
                        <div class="text-muted mb-1" style="font-size: 0.85rem;">
                            <i class="bi bi-upc-scan me-1"></i>${data.product_code}
                        </div>
                        <span class="fw-medium">${data.product_name}</span>
                    </div>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    const icons = {
                        'in': 'bi-box-arrow-in-down text-success',
                        'out': 'bi-box-arrow-up text-danger',
                        'adjustment': 'bi-gear text-warning'
                    };
                    const badgeClass = {
                        'in': 'bg-success-subtle text-success border-success',
                        'out': 'bg-danger-subtle text-danger border-danger',
                        'adjustment': 'bg-warning-subtle text-warning border-warning'
                    };
                    return `<div class="info-box">
                        <div class="mb-2">
                            <span class="fw-semibold">${data.quantity}</span>
                            <span class="text-muted">${data.unit}</span>
                        </div>
                        <div class="badge ${badgeClass[data.type]} mt-auto">
                            <i class="bi ${icons[data.type]} me-1"></i>${data.type.toUpperCase()}
                        </div>
                    </div>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    return `<div class="info-box">
                        <div class="user-info">
                            <i class="bi bi-person me-1 text-primary"></i>
                            <span class="fw-medium">${data.user_name}</span>
                        </div>
                        <div class="text-muted mt-auto">
                            <i class="bi bi-chat-left-text me-1"></i>
                            ${data.remarks || 'No remarks provided'}
                        </div>
                    </div>`;
                }
            },
            {
                data: 'date',
                visible: false
            }
        ],
        order: [
            [4, 'desc']
        ],
        pageLength: 10,
        dom: '<"row mb-3"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        buttons: [{
            extend: 'collection',
            text: '<i class="bi bi-download me-1"></i>Export Data',
            className: 'export-button',
            buttons: [{
                    text: '<i class="bi bi-clipboard me-2"></i>Copy to Clipboard',
                    action: function() {
                        exportType = 'copy';
                        $('#exportFilterModal').modal('show');
                    }
                },
                {
                    text: '<i class="bi bi-file-earmark-excel me-2"></i>Export to Excel',
                    action: function() {
                        exportType = 'excel';
                        $('#exportFilterModal').modal('show');
                    }
                },
                {
                    text: '<i class="bi bi-file-earmark-text me-2"></i>Export to CSV',
                    action: function() {
                        exportType = 'csv';
                        $('#exportFilterModal').modal('show');
                    }
                },
                {
                    text: '<i class="bi bi-file-earmark-pdf me-2"></i>Export to PDF',
                    action: function() {
                        exportType = 'pdf';
                        $('#exportFilterModal').modal('show');
                    }
                },
                {
                    text: '<i class="bi bi-printer me-2"></i>Print',
                    action: function() {
                        exportType = 'print';
                        $('#exportFilterModal').modal('show');
                    }
                }
            ],
            autoClose: true
        }],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>'
        }
    });

    $('#applyFilters').click(function() {
        table.ajax.reload();
        updateSummaryCards();
    });

    $('#dateFrom, #dateTo, #movementType, #productSearch').on('change keyup', function() {});

    $('#refreshTable').on('click', function() {
        $(this).find('i').addClass('rotate-animation');
        $('#dateFrom').val('');
        $('#dateTo').val('');
        $('#movementType').val('');
        $('#productSearch').val('');
        table.ajax.reload();
        updateSummaryCards();
        setTimeout(() => $(this).find('i').removeClass('rotate-animation'), 1000);
    });

    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    $('#confirmExport').on('click', function() {
        const selectedFields = [];
        $('input[type="checkbox"]:checked').each(function() {
            selectedFields.push($(this).val());
        });

        const filters = {
            filterDate: $('#exportFilterDate').val(),
            movementType: $('#exportMovementType').val(),
            fields: selectedFields.join(',')
        };

        const params = new URLSearchParams(filters);

        switch (exportType) {
            case 'pdf':
                const url = "{{ route('inventory.logs.export-pdf') }}";
                $.ajax({
                    url: url + '?' + params.toString(),
                    method: 'GET',
                    success: function(response) {
                        if (response.success === false) {
                            toastr.warning(response.message);
                        } else {
                            window.location.href = url + '?' + params.toString();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            toastr.warning(xhr.responseJSON.message);
                        } else {
                            toastr.error('Error generating PDF');
                        }
                    }
                });
                break;
        }

        $('#exportFilterModal').modal('hide');
    });
});
</script>
<style>
.rotate-animation {
    animation: rotate 1s linear;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>
@endpush