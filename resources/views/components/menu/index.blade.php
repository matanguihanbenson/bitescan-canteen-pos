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

.menu-management-card {
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

.modal-content.loading {
    position: relative;
}

.modal-content.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 30px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 1001;
}

@keyframes spin {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }

    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
}

.opacity-hover .btn {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card:hover .opacity-hover .btn {
    opacity: 1;
}

.card-img-overlay {
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card:hover .card-img-overlay {
    opacity: 1;
}

.dataTables_wrapper .dataTables_filter {
    display: none;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    text-align: right;
}

.dataTables_wrapper .dataTables_length {
    float: left;
}

.dataTables_wrapper .dataTables_info {
    float: left;
}

.list-category-item {
    cursor: pointer;
    transition: all 0.3s ease;
}

.list-category-item:hover {
    background-color: #f8f9fa;
}

.list-category-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.list-category-item.active .text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
    border-color: #86b7fe;
}

.card-body {
    position: relative;
}

.list-group {
    color: rgba(255, 255, 255, 0.7) !important;
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
    border-color: #86b7fe;
}

.card-body {
    position: relative;
}

.list-group {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, .2) transparent;
}

.list-group::-webkit-scrollbar {
    width: 6px;
}

.list-group::-webkit-scrollbar-track {
    background: transparent;
}

.list-group::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, .2);
    border-radius: 3px;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6;
}

.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 300px;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    background: none;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background: none;
    border-bottom-color: #0d6efd;
    color: #0d6efd;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
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
                    <p class="text-muted mb-0">Manage menu items, categories, and pricing for your canteen</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Menu</li>
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
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Menu Items</h6>
                            <h3 id="totalMenuItems" class="mb-1 text-dark fw-semibold">Loading...</h3>
                            <p class="text-muted mb-0 small">Across all categories</p>
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

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Active Items</h6>
                            <h3 id="activeItems" class="mb-1 text-dark fw-semibold">Loading...</h3>
                            <p class="text-muted mb-0 small">Currently available</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
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
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Categories</h6>
                            <h3 id="categoriesCount" class="mb-1 text-dark fw-semibold">Loading...</h3>
                            <p class="text-muted mb-0 small">Menu categories</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-info bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-grid text-white fs-3"></i>
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
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Most Ordered</h6>
                            <h3 id="topSeller" class="mb-1 text-dark fw-semibold">Loading...</h3>
                            <p id="topSellerCount" class="text-muted mb-0 small">No orders yet</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-star text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Management Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card menu-management-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-grid-3x3-gap-fill me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Menu Management</h5>
                            <small class="text-white opacity-90">Organize and manage your menu items and
                                categories</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs border-0" id="menuTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active px-4 py-3" id="active-tab" data-bs-toggle="tab" href="#active"
                                role="tab">
                                <i class="bi bi-menu-button-wide me-2"></i>Active Menu Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="archived-tab" data-bs-toggle="tab" href="#archived"
                                role="tab">
                                <i class="bi bi-archive me-2"></i>Archived Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3" id="categories-tab" data-bs-toggle="tab" href="#categories"
                                role="tab">
                                <i class="bi bi-grid me-2"></i>Menu Categories
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="menuTabContent">
        <!-- Active Menu Items Tab -->
        <div class="tab-pane fade show active" id="active" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-8">

                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                        <i class="bi bi-plus-lg"></i> Add Menu Item
                    </button>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            Bulk Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-archive"></i> Archive Selected</a>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-up"></i> Export Selected</a>
                            </li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete
                                    Selected</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Table Filters -->
                    <div class="row mb-3" id="table-filters">
                        <div class="col-md-3">
                            <select class="form-select" id="status-filter">
                                <option value="">All Items</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="category-filter">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="subcategory-filter">
                                <option value="">All Subcategories</option>
                                @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Yajra DataTable -->
                    {!! $dataTable->table(['class' => 'table table-hover']) !!}
                </div>
            </div>
        </div>

        <!-- Archived Items Tab -->
        <div class="tab-pane fade" id="archived" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-secondary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-archive-fill me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Archived Menu Items</h5>
                            <small class="text-white opacity-90">Items that have been archived from the active
                                menu</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="position-relative">
                                <input type="text" class="form-control" id="searchArchived"
                                    placeholder="Search archived items..." autocomplete="off">
                                <i
                                    class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Replace table with card grid -->
                    <div class="row" id="archivedItems">

                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div class="tab-pane fade" id="categories" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div
                            class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grid-fill me-2 fs-5 text-white"></i>
                                <div>
                                    <h5 class="card-title mb-0 fw-semibold text-white">Categories</h5>
                                    <small class="text-white opacity-90">Organize your menu items</small>
                                </div>
                            </div>
                            <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addCategoryModal">
                                <i class="bi bi-plus-lg"></i> Add Category
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="px-3 py-2 border-bottom">
                                <input type="text" class="form-control" id="searchCategories"
                                    placeholder="Search categories...">
                            </div>
                            <div class="p-3">
                                <div class="text-center py-5" id="emptyCategoriesState"
                                    style="display: none; height: 300px;">
                                    <img src="/images/no-category.png" alt="No categories" class="mb-3"
                                        style="max-width: 50px;opacity:.5;">
                                    <h5>No Categories Yet</h5>
                                    <p class="text-muted">Create categories to organize your menu items</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addCategoryModal">
                                        <i class="bi bi-plus-lg"></i> Add First Category
                                    </button>
                                </div>
                                <div class="list-group" id="categoriesList" style="height: 300px; overflow-y: auto;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm" style="height:440px;">
                        <div
                            class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grid-3x2-gap-fill me-2 fs-5 text-white"></i>
                                <div>
                                    <h5 class="card-title mb-0 fw-semibold text-white">Subcategories</h5>
                                    <small class="text-white opacity-90">Further organize your items</small>
                                </div>
                            </div>
                            <button class="btn btn-outline-light btn-sm" id="addSubcategoryBtn" style="display: none;"
                                data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">
                                <i class="bi bi-plus-lg"></i> Add Subcategory
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="px-3 py-2 border-bottom">
                                <input type="text" class="form-control" id="searchSubcategories"
                                    placeholder="Search subcategories...">
                            </div>
                            <div class="p-3">
                                <div class="text-center py-5" id="noSubcategoriesState" style="height: 300px;">
                                    <img src="/images/no-subcategory.png" alt="No subcategories" class="mb-3"
                                        style="max-width: 50px;opacity:.5;">
                                    <h5>No Subcategories</h5>
                                    <p class="text-muted">Select a category to manage its subcategories</p>
                                </div>
                                <div class="list-group" id="subcategoriesList" style="height: 300px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode Modal -->
    <div class="modal fade" id="barcodeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Print Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <svg id="barcodePreview"></svg>
                    </div>
                    <input type="hidden" id="menuCodeToPrint">
                    <div class="form-group">
                        <label for="barcodeQuantity">Number of Copies:</label>
                        <input type="number" class="form-control" id="barcodeQuantity" min="1" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printBarcodePDF()">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreConfirmModal" tabindex="-1" style="height:max-content; padding:20px;">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-arrow-counterclockwise text-primary fs-1 mb-3"></i>
                    <h5 class="modal-title mb-2">Restore Item</h5>
                    <p class="text-muted small mb-4">Are you sure you want to restore this item to the active menu?</p>
                    <input type="hidden" id="restoreItemId">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary px-4" id="confirmRestoreBtn">
                            <i class="bi bi-check-lg me-1"></i> Restore
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@include('components.menu.modals.add-menu')
@include('components.menu.modals.edit-menu')
@include('components.menu.modals.delete-menu')
@include('components.menu.modals.add-category')
@include('components.menu.modals.edit-category')
@include('components.menu.modals.add-subcategory')
@include('components.menu.modals.delete-category')
@include('components.menu.modals.edit-subcategory')
@include('components.menu.modals.delete-subcategory')


<!-- Custom CSS -->
<style>
.dataTables_wrapper .dataTables_filter {
    display: none;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    text-align: right;
}

.dataTables_wrapper .dataTables_length {
    float: left;
}

.dataTables_wrapper .dataTables_info {
    float: left;
}

.list-category-item {
    cursor: pointer;
    transition: all 0.3s ease;
}

.list-category-item:hover {
    background-color: #f8f9fa;
}

.list-category-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.list-category-item.active .text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
    border-color: #86b7fe;
}

.card-body {
    position: relative;
}

.list-group {
    color: rgba(255, 255, 255, 0.7) !important;
}

.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
    border-color: #86b7fe;
}

.card-body {
    position: relative;
}

.list-group {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, .2) transparent;
}

.list-group::-webkit-scrollbar {
    width: 6px;
}

.list-group::-webkit-scrollbar-track {
    background: transparent;
}

.list-group::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, .2);
    border-radius: 3px;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6;
}

.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 300px;
}
</style>

@endsection

@push('scripts')
{!! $dataTable->scripts() !!}
@include('components.menu.scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
function openBarcodeModal(menuCode) {
    $('#menuCodeToPrint').val(menuCode);

    JsBarcode("#barcodePreview", menuCode, {
        format: "CODE128",
        width: 2,
        height: 100,
        displayValue: true,
        fontSize: 12,
        textAlign: "center",
        lineColor: "#000"
    });

    $('#barcodeModal').modal('show');
}

function printBarcodePDF() {
    const menuCode = $('#menuCodeToPrint').val();
    const quantity = $('#barcodeQuantity').val();

    const modalBody = $('#barcodeModal .modal-body');
    modalBody.addClass('loading');

    window.open(`/menu/print-barcode/${encodeURIComponent(menuCode)}/${quantity}`, '_blank');
    modalBody.removeClass('loading');
    $('#barcodeModal').modal('hide');
}

document.addEventListener('DOMContentLoaded', function() {
    fetch('/menu/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                document.getElementById('totalMenuItems').textContent = data.stats.totalMenuItems;
                document.getElementById('activeItems').textContent = data.stats.activeItems;
                document.getElementById('categoriesCount').textContent = data.stats.categoriesCount;

                if (data.stats.mostOrdered) {
                    document.getElementById('topSeller').textContent = data.stats.mostOrdered.name;
                    document.getElementById('topSellerCount').textContent =
                        `Ordered ${data.stats.mostOrdered.count} times`;
                } else {
                    document.getElementById('topSeller').textContent = 'No orders yet';
                    document.getElementById('topSellerCount').textContent = '';
                }
            }
        })
        .catch(error => {
            });


});
</script>
@endpush

<style>
.modal-body.loading {
    position: relative;
    pointer-events: none;
}

.modal-body.loading:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
}

.modal-body.loading:before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 30px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 1;
}

@keyframes spin {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }

    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}
</style>