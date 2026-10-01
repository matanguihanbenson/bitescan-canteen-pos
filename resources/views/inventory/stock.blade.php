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

    .inventory-management-card {
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

    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    }

    // ...existing styles...
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Inventory Management</h4>
                    <p class="text-muted mb-0">Monitor and manage product stock levels and inventory movements</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Inventory</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card inventory-management-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-boxes me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">Stock Management</h5>
                                <small class="text-white opacity-90">Monitor inventory levels and manage stock
                                    movements</small>
                            </div>
                        </div>
                        <div class="bulk-actions d-none">
                            <button class="btn btn-outline-light btn-sm me-2 delete-selected">
                                <i class="bi bi-trash3"></i> Delete Selected
                            </button>
                            <button class="btn btn-light btn-sm cancel-selection">
                                <i class="bi bi-x-lg"></i> Cancel Selection
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="table-filters" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select id="stock-filter" class="form-select">
                                    <option value="">All Stock Levels</option>
                                    <option value="in_stock">In Stock</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                    <option value="low_stock">Low Stock</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="category-filter" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach(\App\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="expiry-filter" class="form-select">
                                    <option value="">All Expiry Status</option>
                                    <option value="valid">Valid</option>
                                    <option value="expiring_soon">Expiring Soon (30 days)</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{ $dataTable->table(['class' => 'table table-hover']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportFilterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-download me-2"></i>Export PDF Filters
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.export.pdf') }}" method="GET" id="exportForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Fields to Export</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAllFields">
                                <label class="form-check-label" for="selectAllFields">Select All</label>
                            </div>
                        </div>
                        <div class="export-fields-container border rounded p-2"
                            style="max-height: 200px; overflow-y: auto;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="product_name" checked id="field_product_name">
                                        <label class="form-check-label" for="field_product_name">Product Name</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="product_code" checked id="field_product_code">
                                        <label class="form-check-label" for="field_product_code">Product Code</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="current_stock" checked
                                            id="field_current_stock">
                                        <label class="form-check-label" for="field_current_stock">Current Stock</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="base_price" id="field_base_price">
                                        <label class="form-check-label" for="field_base_price">Base Price</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="selling_price" checked
                                            id="field_selling_price">
                                        <label class="form-check-label" for="field_selling_price">Selling Price</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="exp_date" checked id="field_exp_date">
                                        <label class="form-check-label" for="field_exp_date">Expiry Date</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="min_stock" id="field_min_stock">
                                        <label class="form-check-label" for="field_min_stock">Minimum Stock</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input export-field"
                                            name="export_fields[]" value="unit" id="field_unit">
                                        <label class="form-check-label" for="field_unit">Unit</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-text">Select the fields you want to include in the PDF</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="start_date">
                            <span class="input-group-text">to</span>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Status</label>
                        <select class="form-select" name="stock_status">
                            <option value="">All Status</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="high_stock">High Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort By</label>
                        <select class="form-select" name="sort_by">
                            <option value="product_name">Product Name</option>
                            <option value="current_stock">Stock Level</option>
                            <option value="exp_date">Expiry Date</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock History Modal -->
<div class="modal fade" id="stockHistoryModal" tabindex="-1" aria-labelledby="stockHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-secondary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-clock-history me-2"></i>Stock Movement History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="product-info mb-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Movement Summary</h6>
                                <button type="button" class="btn btn-sm btn-primary see-all-movements">
                                    See All Movements
                                </button>
                            </div>
                            <hr class="my-2">
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-light">
                                <div class="card-body p-2 text-center">
                                    <h6 class="mb-0">Total</h6>
                                    <span class="fs-5" id="total_movements">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-light">
                                <div class="card-body p-2 text-center">
                                    <h6 class="mb-0 text-success">Stock In</h6>
                                    <span class="fs-5 text-success" id="stock_in_count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-light">
                                <div class="card-body p-2 text-center">
                                    <h6 class="mb-0 text-danger">Stock Out</h6>
                                    <span class="fs-5 text-danger" id="stock_out_count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-light">
                                <div class="card-body p-2 text-center">
                                    <h6 class="mb-0 text-warning">Adjustments</h6>
                                    <span class="fs-5 text-warning" id="adjustment_count">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="historyTable">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Reference</th>
                                <th>Remarks</th>
                                <th>Updated By</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                        </tbody>
                    </table>
                    <div id="historyTableInfo" class="text-muted small mt-2 text-center d-none">
                        Showing 5 most recent movements out of <span id="totalMovementsCount">0</span> total movements
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Out Modal -->
<div class="modal fade" id="stockOutModal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-warning text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-box-arrow-up me-2"></i>Stock Out
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockOutForm">
                <div class="modal-body">
                    <div class="col-12">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="stock_out_reference" readonly>
                    </div>
                    <input type="hidden" id="stock_out_product_id">
                    <input type="hidden" id="stock_out_units_per_group">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Code</label>
                            <input type="text" class="form-control" id="stock_out_product_code" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="stock_out_product_name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Stock</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="stock_out_current_stock" readonly>
                                <span class="input-group-text" id="stock_out_unit_display"></span>
                                <span class="input-group-text d-none" id="stock_out_last_movement_label">
                                    <small class="text-muted"></small>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 d-none group-unit-fields">
                            <label class="form-label">Base Quantity <span class="text-muted">(pcs)</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="stock_out_base_quantity" readonly>
                                <span class="input-group-text">pcs</span>
                                <span class="input-group-text d-none" id="stock_out_last_base_movement_label">
                                    <small class="text-muted"></small>
                                </span>
                            </div>
                            <div id="stock_out_calculation_info" class="form-text text-muted"></div>
                        </div>
                        <div class="col-md-6 d-none group-unit-fields">
                            <label class="form-label">Adjustment Type</label>
                            <select class="form-select" id="stock_out_type">
                                <option value="current_stock">Adjust Current Stock</option>
                                <option value="base_quantity">Adjust Base Quantity</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantity to Remove <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock_out_quantity" min="1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <select class="form-select" id="stock_out_reason" required>
                                <option value="">Select Reason</option>
                                <option value="sale">Sale</option>
                                <option value="damage">Damaged/Broken</option>
                                <option value="expired">Expired</option>
                                <option value="return">Return to Supplier</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-12 remarks-container d-none">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" id="stock_out_remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-1"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustModal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-sliders me-2"></i>Stock Adjustment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockAdjustForm">
                <div class="modal-body">
                    <div class="col-12 mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="stock_adjust_reference" readonly>
                    </div>
                    <input type="hidden" id="stock_adjust_product_id">
                    <input type="hidden" id="stock_adjust_units_per_group">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Code</label>
                            <input type="text" class="form-control" id="stock_adjust_product_code" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="stock_adjust_product_name" readonly>
                        </div>

                        <!-- Stock Adjustment Controls -->
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="number" class="form-control"
                                                    id="stock_adjust_current_stock" min="0" placeholder="Current Stock">
                                                <span class="input-group-text" id="stock_adjust_unit_display"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 group-unit-fields">
                                            <div class="input-group">
                                                <input type="number" class="form-control"
                                                    id="stock_adjust_base_quantity" min="0" placeholder="Base Quantity">
                                                <span class="input-group-text">pcs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="stock_adjust_type" required>
                                <option value="">Select Type</option>
                                <option value="correction">Stock Count Correction</option>
                                <option value="damaged">Damaged/Spoiled</option>
                                <option value="lost">Lost/Missing</option>
                                <option value="returned">Returned to Supplier</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-12 remarks-container d-none">
                            <label class="form-label">Remarks <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="stock_adjust_remarks" rows="3"></textarea>
                            <div class="form-text">Please provide detailed explanation for the adjustment</div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-check-lg me-1"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Stock In
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockInForm">
                <div class="modal-body">
                    <div class="col-12 mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="stock_in_reference" readonly>
                    </div>
                    <input type="hidden" id="stock_in_product_id">
                    <input type="hidden" id="stock_in_units_per_group">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Code</label>
                            <input type="text" class="form-control" id="stock_in_product_code"
                                name="stock_in_product_code" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="stock_in_product_name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Stock</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="stock_in_current_stock" readonly>
                                <span class="input-group-text" id="stock_in_unit_display"></span>
                                <span class="input-group-text d-none" id="stock_in_last_movement_label">
                                    <small class="text-muted"></small>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 d-none group-unit-fields">
                            <label class="form-label">Base Quantity <span class="text-muted">(pcs)</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="stock_in_base_quantity" readonly>
                                <span class="input-group-text">pcs</span>
                                <span class="input-group-text d-none" id="stock_in_last_base_movement_label">
                                    <small class="text-muted"></small>
                                </span>
                            </div>
                            <div id="stock_in_calculation_info" class="form-text text-muted"></div>
                        </div>
                        <div class="col-md-6 d-none group-unit-fields">
                            <label class="form-label">Adjustment Type</label>
                            <select class="form-select" id="stock_in_type">
                                <option value="current_stock">Adjust Current Stock</option>
                                <option value="base_quantity">Adjust Base Quantity</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock_in_quantity" min="1"
                                name="stock_in_quantity" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" id="stock_in_remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();

        $(document).on('click', '.stock-in', function() {
            const productId = $(this).data('id');

            $('#stock_in_reference').val(generateReference());

            $.ajax({
                url: `/inventory/stock/${productId}/data`,
                method: 'GET',
                success: function(response) {
                    $('#stock_in_product_id').val(response.stock.id);
                    $('#stock_in_product_code').val(response.stock.product_code);
                    $('#stock_in_product_name').val(response.product_name);
                    $('#stock_in_current_stock').val(response.stock.current_stock);
                    $('#stock_in_unit_display').text(response.stock.unit);
                    $('#stock_in_units_per_group').val(response.stock.units_per_group);
                    $('#stock_in_base_quantity').val(response.stock.base_quantity);

                    $('#stock_in_current_stock')
                        .val(response.stock.current_stock)
                        .data('original', response.stock.current_stock);

                    $('#stock_in_base_quantity')
                        .val(response.stock.base_quantity)
                        .data('original', response.stock.base_quantity);

                    const isGroupUnit = isProductGroupUnit(response.stock.unit);
                    $('.group-unit-fields').toggleClass('d-none', !isGroupUnit);

                    const lastMovementBadge = $('#last_movement_badge');
                    if (response.last_movement) {
                        lastMovementBadge
                            .html(formatLastMovement(response.last_movement))
                            .removeClass('d-none')
                            .attr('title', `Last movement ${response.last_movement.date}`);
                    } else {
                        lastMovementBadge.addClass('d-none');
                    }

                    $('[title]').tooltip();

                    $('#stockInModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Error loading product details');
                }
            });
        });

        $(document).on('click', '.stock-out', function() {
            const productId = $(this).data('id');

            $('#stock_out_reference').val(generateReference());

            $('#stock_out_date').val(new Date().toISOString().split('T')[0]);

            $.ajax({
                url: `/inventory/stock/${productId}/data`,
                method: 'GET',
                success: function(response) {
                    $('#stock_out_product_id').val(response.stock.id);
                    $('#stock_out_product_code').val(response.stock.product_code);
                    $('#stock_out_product_name').val(response.product_name);
                    $('#stock_out_current_stock').val(response.stock.current_stock);
                    $('#stock_out_unit_display').text(response.stock.unit);
                    $('#stock_out_units_per_group').val(response.stock.units_per_group);
                    $('#stock_out_base_quantity').val(response.stock.base_quantity);

                    $('#stock_out_current_stock')
                        .val(response.stock.current_stock)
                        .data('original', response.stock.current_stock);

                    $('#stock_out_base_quantity')
                        .val(response.stock.base_quantity)
                        .data('original', response.stock.base_quantity);

                    const isGroupUnit = isProductGroupUnit(response.stock.unit);
                    $('.group-unit-fields').toggleClass('d-none', !isGroupUnit);

                    const lastMovementBadge = $('#stock_out_last_movement_badge');
                    if (response.last_movement) {
                        lastMovementBadge
                            .html(formatLastMovement(response.last_movement))
                            .removeClass('d-none')
                            .attr('title', `Last movement ${response.last_movement.date}`);
                    } else {
                        lastMovementBadge.addClass('d-none');
                    }

                    $('[title]').tooltip();

                    $('#stockOutModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Error loading product details');
                }
            });
        });

        $('#stock_out_reason').on('change', function() {
            const reasonValue = $(this).val();
            const remarksContainer = $('.remarks-container');
            const remarksTextarea = $('#stock_out_remarks');

            if (reasonValue === 'other') {
                remarksContainer.removeClass('d-none');
                remarksTextarea.prop('required', true);
                remarksTextarea.val('');
            } else {
                remarksContainer.addClass('d-none');
                remarksTextarea.prop('required', false);
                remarksTextarea.val($(this).find('option:selected').text());
            }
        });

        $('#stockOutForm').on('submit', function(e) {
            e.preventDefault();

            const reasonSelect = $('#stock_out_reason');
            const reasonValue = reasonSelect.val();
            const type = $('#stock_out_type').val() || 'current_stock';

            if (!reasonValue) {
                reasonSelect.addClass('is-invalid');
                toastr.error('Please select a reason');
                return false;
            }

            const remarks = reasonValue === 'other' ?
                $('#stock_out_remarks').val() :
                reasonSelect.find('option:selected').text();

            const data = {
                product_id: $('#stock_out_product_id').val(),
                quantity: parseInt($('#stock_out_quantity').val()),
                type: type,
                date: $('#stock_out_date').val(),
                reference: $('#stock_out_reference').val(),
                reason: reasonValue,
                remarks: remarks,
                unit: $('#stock_out_unit_display').text(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: '/inventory/stock-out',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Stock removed successfully');
                        $('#stockOutModal').modal('hide');
                        $('#stockOutForm')[0].reset();
                        window.LaravelDataTables['products-table'].ajax.reload();
                    } else {
                        toastr.error(response.error || 'Error removing stock');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'Error removing stock';
                    toastr.error(error);
                }
            });
        });

        $('#stockOutModal').on('hidden.bs.modal', function() {
            $('#stockOutForm')[0].reset();
            $('.remarks-container').addClass('d-none');
            $('#stock_out_remarks').prop('required', false);
            $('#stock_out_last_movement_badge').addClass('d-none').html('');
            $('#stock_out_reason').removeClass('is-invalid');
            $('#stock_out_remarks').removeClass('is-invalid');
        });

        $(document).on('click', '.stock-history', function(e) {
            e.preventDefault();
            const productId = $(this).data('id');

            $('#historyTableBody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');


            $("#stockHistoryModal").modal('show');

            $.ajax({
                url: `/inventory/stock/${productId}/history`,
                method: 'GET',
                success: function(response) {
                    $('#history_product_code').text(response.product.product_code);
                    $('#history_product_name').text(response.product.product_name);
                    $('#history_current_stock').text(response.product.current_stock);
                    $('#history_last_updated').text(response.product.updated_at);

                    $('#total_movements').text(response.counts.total);
                    $('#stock_in_count').text(response.counts.in);
                    $('#stock_out_count').text(response.counts.out);
                    $('#adjustment_count').text(response.counts.adjustment);

                    let tableContent = '';
                    if (response.history && response.history.length > 0) {
                        response.history.forEach(item => {
                            const typeClass = {
                                'in': 'text-success',
                                'out': 'text-danger',
                                'adjustment': 'text-warning'
                            } [item.type] || '';

                            const typeIcon = {
                                'in': 'bi-plus-circle-fill',
                                'out': 'bi-dash-circle-fill',
                                'adjustment': 'bi-arrow-repeat'
                            } [item.type] || '';

                            tableContent += `
                            <tr>
                                <td>${item.formatted_date}</td>
                                <td>
                                    <span class="${typeClass}">
                                        <i class="bi ${typeIcon} me-1"></i>
                                        ${item.type.toUpperCase()}
                                    </span>
                                </td>
                                <td class="${typeClass}">
                                    ${item.type === 'in' ? '' : item.type === 'out' ? '' : ''}${item.quantity}
                                </td>
                                <td>${item.unit}</td>
                                <td>${item.reference || '-'}</td>
                                <td>${item.remarks || '-'}</td>
                                <td>${item.user_name}</td>
                            </tr>
                        `;
                        });
                        $('#historyTableBody').html(tableContent);
                    } else {
                        $('#historyTableBody').html(
                            '<tr><td colspan="6" class="text-center">No movement history found</td></tr>'
                        );
                    }
                    const tableInfo = $('#historyTableInfo');
                    if (response.counts.total > 5) {
                        $('#totalMovementsCount').text(response.counts.total);
                        tableInfo.removeClass('d-none');
                    } else {
                        tableInfo.addClass('d-none');
                    }
                },
                error: function(xhr) {
                    $('#historyTableBody').html(
                        '<tr><td colspan="6" class="text-center text-danger">Error loading history</td></tr>'
                    );
                    toastr.error('Error loading stock history');
                }
            });
        });

        function generateReference() {
            const timestamp = new Date().getTime().toString().slice(-6);
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `REF-${timestamp}-${random}`;
        }

        function formatLastMovement(movement) {
            if (!movement) return '';

            let icon, colorClass, prefix;
            switch (movement.type) {
                case 'in':
                    icon = 'bi-plus-circle-fill';
                    colorClass = 'text-success';
                    prefix = '+';
                    break;
                case 'out':
                    icon = 'bi-dash-circle-fill';
                    colorClass = 'text-danger';
                    prefix = '-';
                    break;
                case 'adjustment':
                    icon = 'bi-arrow-repeat';
                    colorClass = 'text-warning';
                    prefix = '~';
                    break;
            }

            return `<i class="bi ${icon} me-1"></i><span class="${colorClass}">${prefix}${movement.quantity}</span>`;
        }

        $('#stockInForm').on('submit', function(e) {
            e.preventDefault();

            const type = $('#stock_in_type').val() || 'current_stock';
            const quantity = parseInt($('#stock_in_quantity').val());
            const currentStock = parseInt($('#stock_in_current_stock').val());
            const baseQuantity = parseInt($('#stock_in_base_quantity').val());
            const unit = type === 'base_quantity' ? 'pcs' : $('#stock_in_unit_display').text() || $inventory
                .unit;

            const data = {
                stock_in_product_id: $('#stock_in_product_id').val(),
                stock_in_quantity: quantity,
                stock_in_type: type,
                stock_in_reference: $('#stock_in_reference').val(),
                stock_in_remarks: $('#stock_in_remarks').val(),
                stock_in_unit: unit,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: '/inventory/stock-in',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Stock added successfully');
                        $('#stockInModal').modal('hide');
                        $('#stockInForm')[0].reset();
                        window.LaravelDataTables['products-table'].ajax.reload();
                    } else {
                        toastr.error(response.error || 'Error adding stock');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'Error adding stock';
                    toastr.error(error);
                }
            });
        });

        $(document).on('click', '.stock-adjust', function() {
            const productId = $(this).data('id');

            $('#stock_adjust_reference').val(generateReference());

            $('#stock_adjust_date').val(new Date().toISOString().split('T')[0]);

            $.ajax({
                url: `/inventory/stock/${productId}/data`,
                method: 'GET',
                success: function(response) {
                    $('#stock_adjust_product_id').val(response.stock.id);
                    $('#stock_adjust_product_code').val(response.stock.product_code);
                    $('#stock_adjust_product_name').val(response.product_name);
                    $('#stock_adjust_current_stock').val(response.stock.current_stock);
                    $('#stock_adjust_base_quantity').val(response.stock.base_quantity);
                    $('#stock_adjust_unit_display').text(response.stock.unit);
                    $('#stock_adjust_units_per_group').val(response.stock.units_per_group);

                    const isGroupUnit = isProductGroupUnit(response.stock.unit);
                    $('.group-unit-fields').toggleClass('d-none', !isGroupUnit);

                    $('#stock_adjust_type').val('');
                    $('.remarks-container').addClass('d-none');
                    $('#stock_adjust_remarks').val('').prop('required', false);

                    $('#stockAdjustModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Error loading product details');
                }
            });
        });

        $('#stock_adjust_type').on('change', function() {
            const type = $(this).val();
            const remarksContainer = $('.remarks-container');
            const remarksField = $('#stock_adjust_remarks');

            if (type === 'other') {
                remarksContainer.removeClass('d-none');
                remarksField.prop('required', true);
                remarksField.val('');
            } else {
                remarksContainer.addClass('d-none');
                remarksField.prop('required', false);
                remarksField.val($(this).find('option:selected').text());
            }
        });

        $('#stockAdjustForm').on('submit', function(e) {
            e.preventDefault();

            const currentStock = parseInt($('#stock_adjust_current_stock').val());
            const baseQuantity = parseInt($('#stock_adjust_base_quantity').val());
            const originalStock = parseInt($('#stock_adjust_current_stock').data('original'));
            const originalBaseQty = parseInt($('#stock_adjust_base_quantity').data('original'));
            const type = $('#stock_adjust_type').val();

            if (!$.isNumeric(currentStock)) {
                toastr.error('Please enter a valid current stock value');
                $('#stock_adjust_current_stock').addClass('is-invalid');
                return false;
            }

            if (!$.isNumeric(baseQuantity)) {
                toastr.error('Please enter a valid base quantity value');
                $('#stock_adjust_base_quantity').addClass('is-invalid');
                return false;
            }

            if (!type) {
                toastr.error('Please select adjustment type');
                $('#stock_adjust_type').addClass('is-invalid');
                return false;
            }

            const remarks = type === 'other' ? $('#stock_adjust_remarks').val() : $(
                '#stock_adjust_type option:selected').text();

            if (type === 'other' && !remarks.trim()) {
                toastr.error('Please provide remarks for the adjustment');
                $('#stock_adjust_remarks').addClass('is-invalid');
                return false;
            }

            const data = {
                product_id: $('#stock_adjust_product_id').val(),
                current_stock: currentStock,
                base_quantity: baseQuantity,
                type: type,
                date: $('#stock_adjust_date').val(),
                reference: $('#stock_adjust_reference').val(),
                remarks: remarks,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: '/inventory/stock-adjust',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Stock adjusted successfully');
                        $('#stockAdjustModal').modal('hide');
                        $('#stockAdjustForm')[0].reset();
                        window.LaravelDataTables['products-table'].ajax.reload();
                    } else {
                        toastr.error(response.error || 'Error adjusting stock');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.error || 'Error adjusting stock';
                    toastr.error(error);
                }
            });
        });

        function initializeStockAdjust(response) {
            const currentStock = parseInt(response.stock.current_stock);
            const baseQuantity = parseInt(response.stock.base_quantity);
            const unitsPerGroup = parseInt(response.stock.units_per_group) || 1;
            const unit = response.stock.unit;
            const isGroupUnit = isProductGroupUnit(unit);

            $('#stock_adjust_current_stock')
                .val(currentStock)
                .data('original', currentStock);

            $('#stock_adjust_base_quantity')
                .val(baseQuantity)
                .data('original', baseQuantity);

            $('#stock_adjust_units_per_group').val(unitsPerGroup);

            $('#stock_adjust_unit_display').text(unit);

            $('.group-unit-fields').toggleClass('d-none', !isGroupUnit);

            $('#stock_adjust_current_stock, #stock_adjust_base_quantity').removeClass('is-invalid');
            $('#stock_adjust_current_diff, #stock_adjust_base_diff, #stock_adjust_calculation_info').empty();

            handleStockAdjustment();
        }

        function handleStockAdjustment() {
            const currentStock = parseInt($('#stock_adjust_current_stock').val()) || 0;
            const baseQuantity = parseInt($('#stock_adjust_base_quantity').val()) || 0;
            const originalStock = parseInt($('#stock_adjust_current_stock').data('original')) || 0;
            const originalBaseQty = parseInt($('#stock_adjust_base_quantity').data('original')) || 0;
            const unit = $('#stock_adjust_unit_display').text();

            const stockDiff = currentStock - originalStock;
            const baseDiff = baseQuantity - originalBaseQty;

            if (stockDiff !== 0) {
                const stockDiffText = stockDiff > 0 ?
                    `<span class="text-success">+${stockDiff} ${unit}</span>` :
                    `<span class="text-danger">${stockDiff} ${unit}</span>`;
                $('#stock_adjust_current_diff').html(`Change: ${stockDiffText}`);
            } else {
                $('#stock_adjust_current_diff').html('');
            }

            if (baseDiff !== 0) {
                const baseDiffText = baseDiff > 0 ?
                    `<span class="text-success">+${baseDiff} pcs</span>` :
                    `<span class="text-danger">${baseDiff} pcs</span>`;
                $('#stock_adjust_base_diff').html(`Change: ${baseDiffText}`);
            } else {
                $('#stock_adjust_base_diff').html('');
            }

            let infoText = [];
            if (stockDiff !== 0) {
                infoText.push(
                    `Stock will ${stockDiff > 0 ? 'increase' : 'decrease'} by ${Math.abs(stockDiff)} ${unit}`);
            }
            if (baseDiff !== 0) {
                infoText.push(
                    `Base quantity will ${baseDiff > 0 ? 'increase' : 'decrease'} by ${Math.abs(baseDiff)} pcs`);
            }
            $('#stock_adjust_calculation_info').html(infoText.length ? `<small>${infoText.join('<br>')}</small>` :
                '');
        }

        $(document).ready(function() {

            $('#stock_adjust_current_stock, #stock_adjust_base_quantity').on('input', function() {
                if (!$.isNumeric($(this).val())) {
                    $(this).addClass('is-invalid');
                    return;
                }
                $(this).removeClass('is-invalid');
                handleStockAdjustment();
            });

            $('#stock_adjust_independent').on('change', function() {
                const isIndependent = $(this).is(':checked');
                const isGroupUnit = isProductGroupUnit($('#stock_adjust_unit_display').text());

                if (isGroupUnit) {
                    $('#stock_adjust_base_quantity')
                        .prop('readonly', !isIndependent)
                        .toggleClass('bg-light', !isIndependent);

                    if (!isIndependent) {
                        handleStockAdjustment();
                    }
                }
            });
        });

        $('#stock_adjust_new_stock').on('input', calculateStockDifference);

        $('#stock_adjust_type').on('change', function() {
            const type = $(this).val();
            const remarksField = $('#stock_adjust_remarks');

            if (type && type !== 'other') {
                remarksField.val($(this).find('option:selected').text());
            } else {
                remarksField.val('');
            }
        });

        $('#stockAdjustModal').on('hidden.bs.modal', function() {
            $('#stockAdjustForm')[0].reset();
            $('#stock_adjust_last_movement_badge').addClass('d-none').html('');
        });

        $('#stock_in_quantity').on('input', function() {
            handleStockInAdjustment();
        });

        $('#stock_in_type').on('change', function() {
            handleStockInAdjustment();
        });
    });

    function updateBulkActionsVisibility() {
        const checkedBoxes = $('.dt-checkboxes:checked').length;
        if (checkedBoxes > 0) {
            $('.bulk-actions').removeClass('d-none');
            $('.card-tools').addClass('d-none');
        } else {
            $('.bulk-actions').addClass('d-none');
            $('.card-tools').removeClass('d-none');
        }
    }

    $('.check-all').on('click', function() {
        $('.dt-checkboxes').prop('checked', $(this).prop('checked'));
        $('.dt-checkboxes').trigger('change');
        updateBulkActionsVisibility();
    });

    $(document).on('change', '.dt-checkboxes', function() {
        $(this).closest('tr').toggleClass('selected-row', this.checked);
        updateBulkActionsVisibility();
    });
    $('.cancel-selection').on('click', function() {
        $('.check-all, .dt-checkboxes').prop('checked', false);
        $('tr').removeClass('selected-row');
        updateBulkActionsVisibility();
    });

    $('.delete-selected').on('click', function() {
        const selectedIds = $('.dt-checkboxes:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length > 0) {
            }
    });

    $(document).on('click', '.see-all-movements', function() {
        const table = $('#stock-movements-table').DataTable();
        if (table) {
            table.page.len(-1).draw();
        }
    });


    function handleStockInAdjustment() {
        const quantity = parseInt($('#stock_in_quantity').val()) || 0;
        const unitsPerGroup = parseInt($('#stock_in_units_per_group').val()) || 1;
        const originalStock = parseInt($('#stock_in_current_stock').data('original')) || 0;
        const originalBaseQty = parseInt($('#stock_in_base_quantity').data('original')) || 0;
        const type = $('#stock_in_type').val();
        const unit = $('#stock_in_unit_display').text();

        if (type === 'base_quantity') {
            const newBaseQuantity = originalBaseQty + quantity;
            const newCurrentStock = originalStock + Math.floor(quantity / unitsPerGroup);
            const stockChange = Math.floor(quantity / unitsPerGroup);

            $('#stock_in_current_stock').val(newCurrentStock);
            $('#stock_in_base_quantity').val(newBaseQuantity);

            $('#stock_in_last_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> +${stockChange} ${unit}`);

            $('#stock_in_last_base_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> +${quantity} pcs`);

            $('#stock_in_calculation_info').html(
                `Adding ${quantity} pcs<br>` +
                `Stock will increase by ${stockChange} ${unit}`
            );
        } else {
            const newCurrentStock = originalStock + quantity;
            const newBaseQuantity = originalBaseQty + (quantity * unitsPerGroup);

            $('#stock_in_current_stock').val(newCurrentStock);
            $('#stock_in_base_quantity').val(newBaseQuantity);

            $('#stock_in_last_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> +${quantity} ${unit}`);

            $('#stock_in_last_base_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> +${quantity * unitsPerGroup} pcs`);

            $('#stock_in_calculation_info').html(
                `Adding ${quantity} ${unit}<br>` +
                `Base quantity will increase by ${quantity * unitsPerGroup} pcs`
            );
        }
    }

    function handleStockOutAdjustment() {
        const quantity = parseInt($('#stock_out_quantity').val()) || 0;
        const unitsPerGroup = parseInt($('#stock_out_units_per_group').val()) || 1;
        const originalStock = parseInt($('#stock_out_current_stock').data('original')) || 0;
        const originalBaseQty = parseInt($('#stock_out_base_quantity').data('original')) || 0;
        const type = $('#stock_out_type').val() || 'current_stock';
        const unit = $('#stock_out_unit_display').text();

        if (type === 'current_stock') {
            const newCurrentStock = Math.max(0, originalStock - quantity);
            const newBaseQuantity = Math.max(0, originalBaseQty - (quantity * unitsPerGroup));

            $('#stock_out_current_stock').val(newCurrentStock);
            $('#stock_out_base_quantity').val(newBaseQuantity);
            $('#stock_out_calculation_info').html(
                `Removing ${quantity} ${unit}<br>` +
                `Base quantity will decrease by ${quantity * unitsPerGroup} pcs`
            );
        } else {
            const newBaseQuantity = Math.max(0, originalBaseQty - quantity);
            const newCurrentStock = Math.max(0, originalStock - Math.ceil(quantity / unitsPerGroup));

            $('#stock_out_current_stock').val(newCurrentStock);
            $('#stock_out_base_quantity').val(newBaseQuantity);
            $('#stock_out_calculation_info').html(
                `Removing ${quantity} pcs<br>` +
                `Stock will decrease by ${Math.ceil(quantity / unitsPerGroup)} ${unit}`
            );
        }
    }

    function handleStockAdjustment() {
        const currentStock = parseInt($('#stock_adjust_current_stock').val()) || 0;
        const baseQuantity = parseInt($('#stock_adjust_base_quantity').val()) || 0;
        const originalStock = parseInt($('#stock_adjust_current_stock').data('original')) || 0;
        const originalBaseQty = parseInt($('#stock_adjust_base_quantity').data('original')) || 0;
        const unit = $('#stock_adjust_unit_display').text();

        const stockDiff = currentStock - originalStock;
        const baseDiff = baseQuantity - originalBaseQty;

        if (stockDiff !== 0) {
            const stockDiffText = stockDiff > 0 ?
                `<span class="text-success">+${stockDiff} ${unit}</span>` :
                `<span class="text-danger">${stockDiff} ${unit}</span>`;
            $('#stock_adjust_current_diff').html(`Change: ${stockDiffText}`);
        } else {
            $('#stock_adjust_current_diff').html('');
        }

        if (baseDiff !== 0) {
            const baseDiffText = baseDiff > 0 ?
                `<span class="text-success">+${baseDiff} pcs</span>` :
                `<span class="text-danger">${baseDiff} pcs</span>`;
            $('#stock_adjust_base_diff').html(`Change: ${baseDiffText}`);
        } else {
            $('#stock_adjust_base_diff').html('');
        }

        let infoText = [];
        if (stockDiff !== 0) {
            infoText.push(`Stock will ${stockDiff > 0 ? 'increase' : 'decrease'} by ${Math.abs(stockDiff)} ${unit}`);
        }
        if (baseDiff !== 0) {
            infoText.push(`Base quantity will ${baseDiff > 0 ? 'increase' : 'decrease'} by ${Math.abs(baseDiff)} pcs`);
        }
        $('#stock_adjust_calculation_info').html(infoText.length ? `<small>${infoText.join('<br>')}</small>` : '');
    }

    $(document).ready(function() {
        $('#stock_in_type, #stock_in_quantity').on('change input', handleStockInAdjustment);

        $('#stock_out_type, #stock_out_quantity').on('change input', handleStockOutAdjustment);

        $('#stock_adjust_current_stock').on('input', handleStockAdjustment);
        $('#stock_adjust_independent').on('change', function() {
            if ($(this).is(':checked')) {
                $('#stock_adjust_base_quantity').prop('readonly', false);
            } else {
                $('#stock_adjust_base_quantity').prop('readonly', true);
                handleStockAdjustment();
            }
        });
    });

    function isProductGroupUnit(unit) {
        const groupUnits = ['pack', 'box', 'carton', 'bag', 'bundle', 'sack', 'dozen', 'half-dozen', 'pair', 'bottle',
            'can', 'case', 'set'
        ];
        return groupUnits.includes(unit.toLowerCase());
    }

    function openManageModal(productId) {
        $.ajax({
            url: `/inventory/stock/${productId}/data`,
            method: 'GET',
            success: function(response) {
                $('#stock_in_unit_display').text(response.stock.unit);
                $('#stock_in_units_per_group').val(response.stock.units_per_group);
                $('#stock_in_base_quantity').val(response.stock.base_quantity);

                $('#stock_out_unit').text(response.stock.unit);
                $('#stock_out_units_per_group').val(response.stock.units_per_group);
                $('#stock_out_base_quantity').val(response.stock.base_quantity);

                $('#stock_adjust_unit').text(response.stock.unit);
                $('#stock_adjust_units_per_group').val(response.stock.units_per_group);
                $('#stock_adjust_base_quantity').val(response.stock.base_quantity);
                const isGroupUnit = isProductGroupUnit(response.stock.unit);
                $('.group-unit-fields').toggleClass('d-none', !isGroupUnit);
            }
        });
    }

    function updateLastMovementLabels(response) {
        if (response.last_movement) {
            const movement = response.last_movement;
            const unit = response.stock.unit;
            const unitsPerGroup = parseInt(response.stock.units_per_group) || 1;

            const prefix = movement.type === 'out' ? '-' : '+';

            let movementText = '';
            let baseMovementText = '';

            if (movement.adjustment_type === 'base_quantity') {
                movementText = `${prefix}${Math.floor(movement.quantity / unitsPerGroup)} ${unit}`;
                baseMovementText = `${prefix}${movement.quantity} pcs`;
            } else {
                movementText = `${prefix}${movement.quantity} ${unit}`;
                baseMovementText = `${prefix}${movement.quantity * unitsPerGroup} pcs`;
            }

            if (movement.unit === 'pcs') {
                movementText = `${prefix}0 ${unit}`;
                baseMovementText = `${prefix}${movement.quantity} pcs`;
            }

            $('#stock_in_last_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> ${movementText}`);

            $('#stock_in_last_base_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> ${baseMovementText}`);

            $('#stock_out_last_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> ${movementText}`);

            $('#stock_out_last_base_movement_label')
                .removeClass('d-none')
                .find('small')
                .html(`<i class="bi bi-clock-fill"></i> ${baseMovementText}`);
        } else {}
    }

    $(document).on('click', '.stock-in, .stock-out', function() {
        const productId = $(this).data('id');

        $.ajax({
            url: `/inventory/stock/${productId}/data`,
            method: 'GET',
            success: function(response) {

                updateLastMovementLabels(response);

            }
        });
    });

    $('#stockInModal, #stockOutModal').on('hidden.bs.modal', function() {
        $('#stock_in_last_movement_label, #stock_in_last_base_movement_label, #stock_out_last_movement_label, #stock_out_last_base_movement_label')
            .addClass('d-none')
            .find('small')
            .html('');
    });

    $('#exportForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const url = $(this).attr('action');

        window.open(`${url}?${formData}`, '_blank');

        $('#exportFilterModal').modal('hide');
    });

    function setCurrentDateTime() {
        const now = new Date();
        const datetime = now.toISOString().slice(0, 16);

        $('#stock_out_date').val(datetime);
        $('#stock_adjust_date').val(datetime);
    }

    $('.stock-out').on('click', function() {
        setCurrentDateTime();
    });

    $('.stock-adjust').on('click', function() {
        setCurrentDateTime();
    });

    $('#exportFilterModal button[type="submit"]').on('click', function(e) {
        e.preventDefault();
        const formData = $('#exportForm').serialize();

        const reportWindow = window.open(
            `/inventory/export/pdf?${formData}`,
            'InventoryReport',
            'width=900,height=1100,menubar=no,toolbar=no,location=no,status=no'
        );
    });
</script>
@endsection

@push('scripts')
{{ $dataTable->scripts() }}
@endpush

@push('styles')

<style>
    .selected-row {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .selected-row:hover {
        background-color: rgba(13, 110, 253, 0.15) !important;
    }

    .export-fields-container::-webkit-scrollbar {
        width: 6px;
    }

    .export-fields-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .export-fields-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .export-fields-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #table-filters select {
        width: 100%;
    }

    .dt-buttons {
        margin-top: .5rem;
    }

    .dataTables_filter {
        margin-bottom: 1rem;
    }

    @media (max-width: 767.98px) {
        .dt-buttons {
            margin-top: -1rem;
        }
    }

    /* Modal height adjustments */
    .modal-dialog-scrollable {
        max-height: 90vh;
    }

    .modal-dialog-scrollable .modal-content {
        max-height: 90vh;
    }

    .modal-dialog-scrollable .modal-body {
        overflow-y: auto;
        max-height: calc(90vh - 200px);
        /* Adjust for header and footer */
    }

    /* Compact form groups */
    .modal .form-group,
    .modal .mb-3 {
        margin-bottom: 0.75rem !important;
    }

    .modal .row.g-3 {
        --bs-gutter-y: 0.75rem;
    }

    /* Make modals slightly wider on larger screens */
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 700px;
            /* Increased from 500px */
        }
    }

    /* Improve input group layouts */
    .modal .input-group {
        flex-wrap: nowrap;
    }

    .modal .input-group-text {
        white-space: nowrap;
        padding: 0.375rem 0.75rem;
    }

    /* Remove duplicate modal styles */
    .modal-dialog-scrollable {
        max-height: 90vh;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* Fixed Modal Footer */
    .modal-footer {
        position: sticky;
        bottom: 0;
        background-color: var(--bs-modal-footer-bg, #f8f9fa);
        border-top: 1px solid var(--bs-modal-footer-border-color, #dee2e6);
        z-index: 1020;
        padding: 1rem;
    }

    /* Adjust modal body padding to prevent content hiding behind fixed footer */
    .modal-dialog-scrollable .modal-body {
        padding-bottom: calc(1.5rem + 57px);
        /* Accounts for footer height */
    }

    /* Ensure consistent modal footer appearance */
    .modal-footer .btn {
        min-width: 80px;
    }

    .is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
</style>
@endpush