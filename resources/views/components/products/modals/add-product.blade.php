<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addProductForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-0">
                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basic-info" type="button">
                                <i class="bi bi-info-circle me-1"></i>Basic Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#categories" type="button">
                                <i class="bi bi-tags me-1"></i>Categories
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pricing" type="button">
                                <i class="bi bi-currency-dollar me-1"></i>Pricing
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inventory" type="button">
                                <i class="bi bi-box me-1"></i>Inventory
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic-info">
                            <div class="row">
                                <!-- Image Preview Column -->
                                <div class="col-md-4 text-center">
                                    <div class="image-preview-container mb-3">
                                        <img id="preview_image" src="/images/no-image.png" 
                                             class="img-thumbnail product-image mb-2" 
                                             style="width: 200px; height: 200px; object-fit: cover;">
                                        <div class="mt-2">
                                            <input type="file" class="form-control form-control-sm d-none" 
                                                   name="product_image" id="product_image" accept="image/*">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="chooseImageBtn">
                                                <i class="bi bi-camera"></i> Choose Image
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Recommended: 500x500px, Max 2MB
                                        </small>
                                    </div>
                                </div>

                                <!-- Product Details Column -->
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Product Code <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="product_code" id="product_code" readonly>
                                                <button class="btn btn-outline-secondary" type="button" id="regenerateCode">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="product_name">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="product_description" rows="3" placeholder="Enter product description..."></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="active">Active</option>
                                                <option value="deactivated">Deactivated</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Tab -->
                        <div class="tab-pane fade" id="categories">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Subcategories</label>
                                    <select class="form-select select2-multiple" name="subcategories[]" multiple="multiple">
                                        @foreach($subcategories as $subcategory)
                                            <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Tab -->
                        <div class="tab-pane fade" id="pricing">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Base Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="base_price" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="selling_price" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Tab -->
                        <div class="tab-pane fade" id="inventory">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Current Stock <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="current_stock" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select class="form-select" name="unit" id="unit" onchange="handleUnitChange()">
                                        <option value="">Select Unit</option>
                                        <optgroup label="Single Units">
                                            <option value="pc">Piece (pc)</option>
                                            <option value="unit">Unit (unit)</option>
                                            <option value="item">Item (item)</option>
                                            <option value="ea">Each (ea)</option>
                                            <option value="serving">Serving</option>
                                            <option value="g">Gram (g)</option>
                                            <option value="kg">Kilogram (kg)</option>
                                            <option value="oz">Ounce (oz)</option>
                                            <option value="lb">Pound (lb)</option>
                                        </optgroup>
                                        <optgroup label="Group Units">
                                            <option value="pack">Pack</option>
                                            <option value="box">Box</option>
                                            <option value="carton">Carton</option>
                                            <option value="bag">Bag</option>
                                            <option value="bundle">Bundle</option>
                                            <option value="sack">Sack</option>
                                            <option value="dozen">Dozen (12 units)</option>
                                            <option value="half-dozen">Half-Dozen (6 units)</option>
                                            <option value="pair">Pair (2 units)</option>
                                            <option value="bottle">Bottle</option>
                                            <option value="can">Can</option>
                                            <option value="case">Case</option>
                                            <option value="set">Set</option>
                                        </optgroup>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 group-unit-field" style="display: none;">
                                    <label class="form-label">Units per Group <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="units_per_group" min="1">
                                </div>

                                <div class="col-md-6 group-unit-field" style="display: none;">
                                    <label class="form-label">Base Quantity (Total Quantity) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="base_quantity" min="1" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Minimum Stock Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="min_stock" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Expiration Date</label>
                                    <input type="date" class="form-control" name="exp_date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#chooseImageBtn').click(function() {
            $('#product_image').click();
        });

        $('#product_image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_image').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#addProductModal').on('hidden.bs.modal', function() {
            $('#preview_image').attr('src', '/images/no-image.png');
            $('#product_image').val('');
        });

        function handleUnitChange() {
            const groupUnits = [
                'pack', 'box', 'carton', 'bag', 'bundle', 'sack',
                'dozen', 'half-dozen', 'pair', 'bottle', 'can', 'case', 'set'
            ];
            
            const selectedUnit = $('#unit').val();
            const isGroupUnit = groupUnits.includes(selectedUnit);
            
            if (isGroupUnit) {
                $('.group-unit-field').show();
                $('[name="units_per_group"]').prop('required', true);
                calculateBaseQuantity();
            } else {
                $('.group-unit-field').hide();
                $('[name="units_per_group"]').prop('required', false)
                    .removeClass('is-invalid')
                    .val('');
                $('[name="base_quantity"]').val('');
            }
        }

        function calculateBaseQuantity() {
            const currentStock = parseInt($('[name="current_stock"]').val()) || 0;
            const unitsPerGroup = parseInt($('[name="units_per_group"]').val()) || 0;
            
            if (currentStock > 0 && unitsPerGroup > 0) {
                $('[name="base_quantity"]').val(currentStock * unitsPerGroup);
            } else {
                $('[name="base_quantity"]').val('');
            }
        }

        $('[name="current_stock"], [name="units_per_group"]').on('input', calculateBaseQuantity);

        $('#unit').on('change', handleUnitChange);

        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();
            
            const selectedUnit = $('#unit').val();
            const groupUnits = [
                'pack', 'box', 'carton', 'bag', 'bundle', 'sack',
                'dozen', 'half-dozen', 'pair', 'bottle', 'can', 'case', 'set'
            ];
            const isGroupUnit = groupUnits.includes(selectedUnit);
            
            let requiredFieldsByTab = {
                'basic-info': {
                    'product_name': 'Product Name'
                },
                'categories': {
                    'category_id': 'Category'
                },
                'pricing': {
                    'base_price': 'Base Price',
                    'selling_price': 'Selling Price'
                },
                'inventory': {
                    'current_stock': 'Current Stock',
                    'unit': 'Unit',
                    'min_stock': 'Minimum Stock Level'
                }
            };

            if (isGroupUnit) {
                requiredFieldsByTab.inventory = {
                    ...requiredFieldsByTab.inventory,
                    'units_per_group': 'Units per Group',
                    'base_quantity': 'Base Quantity'
                };
            }

            let isValid = true;
            let missingFieldsByTab = {};

            Object.keys(requiredFieldsByTab).forEach(tabId => {
                let tabFields = requiredFieldsByTab[tabId];
                let missingFields = [];

                Object.keys(tabFields).forEach(field => {
                    let value = $(`[name="${field}"]`).val();
                    if (!value || value.trim() === '') {
                        isValid = false;
                        missingFields.push(tabFields[field]);
                        $(`[name="${field}"]`).addClass('is-invalid');
                    } else {
                        $(`[name="${field}"]`).removeClass('is-invalid');
                    }
                });

                if (missingFields.length > 0) {
                    missingFieldsByTab[tabId] = missingFields;
                }
            });

            if (!isValid) {
                Object.keys(missingFieldsByTab).forEach(tabId => {
                    let tabName = $(`[data-bs-target="#${tabId}"]`).text().trim();
                    let missingFields = missingFieldsByTab[tabId];
                    
                    toastr.options = {
                        "closeButton": true,
                        "timeOut": "8000",
                        "extendedTimeOut": "2000"
                    };
                    
                    toastr.error(
                        `Missing fields in ${tabName}:<br>${missingFields.join('<br>')}`,
                        `${tabName} Tab`
                    );
                });

                let firstErrorTab = Object.keys(missingFieldsByTab)[0];
                $(`[data-bs-target="#${firstErrorTab}"]`).tab('show');
                
                return false;
            }
            
            let formData = new FormData(this);
            
            if (!isGroupUnit) {
                formData.delete('units_per_group');
                formData.delete('base_quantity');
            }
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        
                        $('#addProductModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('overflow', '');
                        $('body').css('padding-right', '');
                        
                        if (typeof productsTable !== 'undefined') {
                            productsTable.ajax.reload();
                        }
                        
                        $('#addProductForm')[0].reset();
                        $('#preview_image').attr('src', '/images/no-image.png');
                    } else {
                        toastr.error(response.message || 'An error occurred');
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                            $(`[name="${key}"]`).addClass('is-invalid');
                        });
                    } else {
                        toastr.error('An error occurred while saving the product');
                    }
                }
            });
        });

        $(document).on('input', '.form-control, .form-select', function() {
            $(this).removeClass('is-invalid');
        });

        $('#addProductModal').on('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('overflow', '');
            $('body').css('padding-right', '');
        });
    });
</script>

<style>
    .image-preview-container {
        border: 2px dashed #ddd;
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }

    .image-preview-container:hover {
        border-color: #0d6efd;
    }

    .product-image {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .product-image:hover {
        transform: scale(1.02);
    }

    .is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
</style>