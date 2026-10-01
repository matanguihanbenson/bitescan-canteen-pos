@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div
                class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">Product Management</h4>
                    <p class="text-muted mb-0">Manage products, inventory levels, and product information</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Products -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Products</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $summaries['total_products'] }}</h3>
                            <p class="text-muted mb-0 small">In your inventory</p>
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

        <!-- Active Products -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Active Products</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $summaries['active_products'] }}</h3>
                            <p class="text-muted mb-0 small">
                                <span class="text-danger">{{ $summaries['deactivated_products'] }} Deactivated</span>
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-toggle-on text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Categories</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $summaries['total_categories'] }}</h3>
                            <p class="text-muted mb-0 small">
                                {{ $summaries['uncategorized_products'] }} Uncategorized
                            </p>
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

        <!-- Product Images -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Product Images</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $summaries['with_images'] }}</h3>
                            <p class="text-muted mb-0 small">
                                {{ $summaries['without_images'] }} Missing Images
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-images text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-boxes me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">Product Inventory</h5>
                                <small class="text-white opacity-90">Manage product information and inventory
                                    levels</small>
                            </div>
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addProductModal">
                                <i class="bi bi-plus-lg me-1"></i> Add New Product
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="table-filters" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select id="status-filter" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="deactivated">Deactivated</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="category-filter" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{ $dataTable->table(['class' => 'table table-hover align-middle']) }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.products.modals.add-product')

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
                    <div class="product-name mb-2" style="font-weight: bold; font-size: 12px;"></div>
                    <svg id="barcodePreview"></svg>
                </div>
                <input type="hidden" id="productCodeToPrint">
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

@include('components.products.modals.manage')

<div class="modal fade" id="statusConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                    Confirm Status Change
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-toggle-on display-4 mb-3 text-primary"></i>
                <p class="mb-0">Are you sure you want to <span id="statusAction" class="fw-bold"></span> this product?
                </p>
                <input type="hidden" id="productIdToToggle">
                <input type="hidden" id="newStatus">
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="confirmStatusChange()">
                    <i class="bi bi-check-lg me-1"></i>Confirm
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
<script>
function initializeSelect2() {
    $('.select2-multiple').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#addProductModal'),
        placeholder: 'Select Subcategories',
        allowClear: true
    });
}

initializeSelect2();

$('#addProductModal').on('shown.bs.modal', function() {
    initializeSelect2();
    $('#product_code').val(generateProductCode());
    handleUnitChange();
});


$(document).ready(function() {
    var productTabs = new bootstrap.Tab(document.querySelector('#productTabs a[data-bs-toggle="tab"]'));

    $('#productTabs a[data-bs-toggle="tab"]').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $(document).on('click', '.print-barcode', function(e) {
        e.preventDefault();
        const productCode = $(this).data('product-code');
        openBarcodeModal(productCode);
    });

    $(document).on('click', '.manage-product', function(e) {
        e.preventDefault();
        const productId = $(this).data('id');
        openManageModal(productId);
    });

    $(document).on('click', '.toggle-status', function(e) {
        e.preventDefault();
        const productId = $(this).data('id');
        const currentStatus = $(this).data('current-status');
        toggleProductStatus(productId, currentStatus);
    });


    function generateProductCode() {
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += Math.floor(Math.random() * 10);
        }
        return code;
    }

    $('#addProductModal').on('shown.bs.modal', function() {
        $('#product_code').val(generateProductCode());
        initializeSelect2();
    });

    $('#addProductModal').on('hidden.bs.modal', function() {
        $('.select2-multiple').val(null).trigger('change');
        $('#addProductForm')[0].reset();
    });

    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        let emptyFields = [];
        if (!$('input[name="product_name"]').val()) {
            emptyFields.push({
                tab: 'Basic Info',
                field: 'Product Name'
            });
        }
        if (!$('select[name="category_id"]').val()) {
            emptyFields.push({
                tab: 'Categories',
                field: 'Category'
            });
        }
        if (!$('input[name="base_price"]').val()) {
            emptyFields.push({
                tab: 'Pricing',
                field: 'Base Price'
            });
        }
        if (!$('input[name="selling_price"]').val()) {
            emptyFields.push({
                tab: 'Pricing',
                field: 'Selling Price'
            });
        }
        if (!$('input[name="current_stock"]').val()) {
            emptyFields.push({
                tab: 'Inventory',
                field: 'Current Stock'
            });
        }
        if (!$('input[name="min_stock"]').val()) {
            emptyFields.push({
                tab: 'Inventory',
                field: 'Minimum Stock Level'
            });
        }
        if (!$('input[name="base_quantity"]').val()) {
            emptyFields.push({
                tab: 'Inventory',
                field: 'Base Quantity'
            });
        }
        if (!$('input[name="units_per_group"]').val()) {
            emptyFields.push({
                tab: 'Inventory',
                field: 'Units per Group'
            });
        }
        if (!$('select[name="unit"]').val()) {
            emptyFields.push({
                tab: 'Inventory',
                field: 'Unit'
            });
        }
        if (emptyFields.length > 0) {
            let groupedByTab = {};
            emptyFields.forEach(item => {
                if (!groupedByTab[item.tab]) {
                    groupedByTab[item.tab] = [];
                }
                groupedByTab[item.tab].push(item.field);
            });
            Object.keys(groupedByTab).forEach(tab => {
                let fields = groupedByTab[tab].join(', ');
                toastr.error(`${tab} Tab: ${fields} required`);
            });
            return;
        }
        let formData = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    const modal = bootstrap.Modal.getInstance($('#addProductModal'));
                    modal.hide();
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('#addProductForm')[0].reset();
                    $('.select2-multiple').val(null).trigger('change');
                    window.LaravelDataTables['products-table'].ajax.reload(null, false);
                    setTimeout(() => {
                        $(window).trigger('resize');
                        window.LaravelDataTables['products-table'].columns.adjust();
                    }, 100);
                } else {
                    toastr.error(response.message || 'Unknown error occurred');
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        toastr.error(`${key}: ${errors[key][0]}`);
                    });
                } else {
                    toastr.error(`Server Error (${xhr.status}): ${xhr.responseText}`);
                }
            }
        });
    });

    $('#status-filter, #category-filter').on('change', function() {
        window.LaravelDataTables['products-table'].draw();
    });

    $('#regenerateCode').on('click', function() {
        const $btn = $(this);
        const $icon = $btn.find('i');
        const originalIcon = $icon.attr('class');
        $icon.attr('class', 'spinner-border spinner-border-sm');
        $btn.prop('disabled', true);
        setTimeout(() => {
            $('#product_code').val(generateProductCode());
            $icon.attr('class', originalIcon);
            $btn.prop('disabled', false);
        }, 500);
    });

    window.handleUnitChange = function() {
        const unitSelect = document.getElementById('unit');
        const groupUnitFields = document.querySelectorAll('.group-unit-field');
        const baseQuantityInput = document.querySelector('input[name="base_quantity"]');
        const unitsPerGroupInput = document.querySelector('input[name="units_per_group"]');
        const currentStockInput = document.querySelector('input[name="current_stock"]');
        const selectedOption = unitSelect.options[unitSelect.selectedIndex];
        const isGroupUnit = selectedOption.closest('optgroup')?.label === 'Group Units';

        groupUnitFields.forEach(field => {
            if (isGroupUnit) {
                field.style.display = 'block';
                field.querySelector('input').setAttribute('required', 'required');
            } else {
                field.style.display = 'none';
                field.querySelector('input').removeAttribute('required');
                unitsPerGroupInput.value = '1';
                baseQuantityInput.value = currentStockInput.value || '';
            }
        });

        if (isGroupUnit) {
            const calculateBaseQuantity = () => {
                const currentStock = parseInt(currentStockInput.value) || 0;
                const unitsPerGroup = parseInt(unitsPerGroupInput.value) || 0;
                baseQuantityInput.value = currentStock * unitsPerGroup;
            };

            currentStockInput.addEventListener('input', calculateBaseQuantity);
            unitsPerGroupInput.addEventListener('input', calculateBaseQuantity);

            switch (unitSelect.value) {
                case 'dozen':
                    unitsPerGroupInput.value = '12';
                    break;
                case 'half-dozen':
                    unitsPerGroupInput.value = '6';
                    break;
                case 'pair':
                    unitsPerGroupInput.value = '2';
                    break;
                default:
                    unitsPerGroupInput.value = '';
            }
            calculateBaseQuantity();
        }
    };

    $('#addProductModal').on('shown.bs.modal', function() {
        handleUnitChange();
    });

    $('#updateProductForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Product updated successfully');
                    $('#manageProductModal').modal('hide');
                    window.LaravelDataTables['products-table'].ajax.reload();
                } else {
                    toastr.error(response.message || 'Error updating product');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.values(xhr.responseJSON.errors).forEach(function(error) {
                        toastr.error(error[0]);
                    });
                } else {
                    toastr.error('Error updating product: ' + (xhr.responseJSON?.message ||
                        'Unknown error'));
                }
                }
        });
    });

    $('#update_subcategories').select2({
        width: '100%',
        placeholder: 'Select subcategories',
        allowClear: true
    });
    $('#editBtn').on('click', function() {
        $('#update_subcategories').prop('disabled', false);
        toggleEdit();


    });

    $('#cancelBtn').on('click', function() {
        $('#update_subcategories').prop('disabled', true);
        cancelEdit();

    });

    $('#productTabs a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    window.switchToTab = function(tabId) {
        $(`#productTabs a[href="#${tabId}"]`).tab('show');
    };
});

toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

let currentProductId = null;

function toggleEdit() {
    $('#update_product_name').prop('readonly', false);
    $('#update_description').prop('readonly', false);
    $('#update_category, #update_status').prop('disabled', false);
    $('#image-controls').show();
    $('#update_base_price, #update_selling_price, #update_expiration_date').prop('readonly', false);
    $('#editBtn').hide();
    $('#saveBtn, #cancelBtn').show();
}

function cancelEdit() {
    $('#update_product_name, #update_description').prop('readonly', true);
    $('#update_category, #update_status').prop('disabled', true);
    $('#image-controls').hide();
    $('#update_new_product_image').val('');

    $('#saveBtn, #cancelBtn').hide();
    $('#editBtn').show();

    openManageModal(currentProductId);
}

function openManageModal(productId) {
    currentProductId = productId;
    $.ajax({
        url: `/inventory/products/${productId}`,
        method: 'GET',
        success: function(response) {
            const product = response.product;
            $('#update_product_code').val(product.product_code);
            $('#update_product_name').val(product.product_name);
            $('#update_description').val(product.product_description);
            $('#update_status').val(product.status);

            const imagePath = product.product_image ?
                `/storage/${product.product_image}` :
                '/images/no-image.png';
            $('#update_product_image').attr('src', imagePath);

            $('#updateProductForm').attr('action', `/inventory/products/${productId}`);

            const categorySelect = $('#update_category');
            categorySelect.empty();
            response.categories.forEach(category => {
                categorySelect.append(new Option(category.name, category.id));
            });
            categorySelect.val(product.category_id);

            if (product.inventory) {
                $('#update_base_price').val(product.inventory.base_price);
                $('#update_selling_price').val(product.inventory.selling_price);
                if (product.inventory.exp_date) {
                    const expDate = new Date(product.inventory.exp_date);
                    const formattedDate = expDate.toISOString().split('T')[0];
                    $('#update_expiration_date').val(formattedDate);
                }
            }

            $('#update_product_name, #update_description, #update_base_price, #update_selling_price, #update_expiration_date')
                .prop('readonly', true);
            $('#update_category, #update_status').prop('disabled', true);
            $('#image-controls').hide();
            $('#saveBtn, #cancelBtn').hide();
            $('#editBtn').show();

            $('#manageProductModal').modal('show');
        },
        error: function(xhr) {
            toastr.error('Error loading product details');
        }
    });
}

$('#changeImageBtn').click(function() {
    $('#update_new_product_image').click();
});

$('#update_new_product_image').change(function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#update_product_image').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }
});

function saveChanges() {
    const form = document.getElementById('updateProductForm');
    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    $.ajax({
        url: form.getAttribute('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#manageProductModal').modal('hide');
                window.LaravelDataTables['products-table'].ajax.reload();
            } else {
                toastr.error(response.message || 'Error updating product');
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                Object.values(xhr.responseJSON.errors).forEach(function(error) {
                    toastr.error(error[0]);
                });
            } else {
                toastr.error('Error updating product: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
            }
    });
}

function populateManageModal(data) {
    if (data.product.inventory) {
        $('#update_base_price').val(data.product.inventory.base_price);
        $('#update_selling_price').val(data.product.inventory.selling_price);
        $('#update_expiration_date').val(data.product.inventory.exp_date);
    }


}


function resetToViewMode() {
    $('#view_product_name, #view_description').prop('readonly', true);
    $('#view_category, #view_status').prop('disabled', true);
    $('#saveBtn, #cancelBtn').hide();
    $('#editBtn, #closeBtn').show();
}

function openBarcodeModal(productCode) {
    $('#productCodeToPrint').val(productCode);

    JsBarcode("#barcodePreview", productCode, {
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
    const productCode = $('#productCodeToPrint').val();
    const quantity = $('#barcodeQuantity').val();

    const url = `/products/print-barcode/${encodeURIComponent(productCode)}/${quantity}`;
    window.open(url, '_blank');
    $('#barcodeModal').modal('hide');
}

function toggleProductStatus(productId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'deactivated' : 'active';
    const actionText = currentStatus === 'active' ? 'deactivate' : 'activate';

    $('#productIdToToggle').val(productId);
    $('#newStatus').val(newStatus);
    $('#statusAction').text(actionText);

    const modal = $('#statusConfirmModal');
    modal.removeClass('deactivate activate');
    modal.addClass(currentStatus === 'active' ? 'deactivate' : 'activate');

    modal.modal('show');
}

function confirmStatusChange() {
    const productId = $('#productIdToToggle').val();
    const newStatus = $('#newStatus').val();

    $.ajax({
        url: `/inventory/products/${productId}/toggle-status`,
        method: 'POST',
        data: {
            status: newStatus,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#statusConfirmModal').modal('hide');
            toastr.success(response.message);
            window.LaravelDataTables['products-table'].ajax.reload();
        },
        error: function(xhr) {
            toastr.error('Error updating product status');
        }
    });
}
</script>
@endpush

@push('styles')
@include('components.products.styles')
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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const tabList = document.querySelectorAll('#productTabs .nav-link');
    tabList.forEach(function(tab) {
        tab.addEventListener('click', function(event) {
            event.preventDefault();
            const tab = new bootstrap.Tab(this);
            tab.show();
        });
    });

    window.switchToTab = function(tabId) {
        const tab = new bootstrap.Tab(document.querySelector(`#productTabs a[href="#${tabId}"]`));
        tab.show();
    };

});
</script>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#manageProductModal').on('shown.bs.modal', function() {
        const tabButtons = document.querySelectorAll('#productTabs button[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const tab = new bootstrap.Tab(button);
                tab.show();
            });
        });
    });

    window.switchToTab = function(tabId) {
        const tabElement = document.querySelector(`#productTabs button[data-bs-target="#${tabId}"]`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    };

});
</script>
@endpush

@push('scripts')
<script>
function toggleEdit() {
    $('#updateProductForm').find('input, textarea, select').not('#update_product_code').each(function() {
        if ($(this).attr('readonly')) {
            $(this).prop('readonly', false);
        }
        if ($(this).attr('disabled')) {
            $(this).prop('disabled', false);
        }
    });

    $('#image-controls').show();

    $('#editBtn').hide();
    $('#saveBtn, #cancelBtn').show();
}

function cancelEdit() {
    $('#updateProductForm').find('input, textarea, select').each(function() {
        $(this).prop('readonly', true);
        $(this).prop('disabled', true);
    });

    $('#image-controls').hide();
    $('#update_new_product_image').val('');

    $('#saveBtn, #cancelBtn').hide();
    $('#editBtn').show();

    openManageModal(currentProductId);
}

$(document).ready(function() {

    $(document).on('click', '#editBtn', function() {
        toggleEdit();
    });

    $(document).on('click', '#cancelBtn', function() {
        cancelEdit();
    });

});
</script>

<script>
$(document).ready(function() {
    function generateProductCode() {
        return Array.from({
            length: 8
        }, () => Math.floor(Math.random() * 10)).join('');
    }

    $('#addProductModal').on('show.bs.modal', function() {
        $('#product_code').val(generateProductCode());
    });

    $('#regenerateCode').on('click', function() {
        const $btn = $(this);
        const $icon = $btn.find('i');
        const originalIcon = $icon.attr('class');

        $icon.attr('class', 'spinner-border spinner-border-sm');
        $btn.prop('disabled', true);

        setTimeout(() => {
            $('#product_code').val(generateProductCode());
            $icon.attr('class', originalIcon);
            $btn.prop('disabled', false);
        }, 500);
    });

});
</script>
@endpush