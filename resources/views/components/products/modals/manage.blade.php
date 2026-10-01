<!-- Manage Product Modal -->
<div class="modal fade" id="manageProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-gear me-2"></i>Manage Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateProductForm" method="POST" onsubmit="event.preventDefault(); saveChanges();">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i>Basic Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                                <i class="bi bi-card-text me-1"></i>Additional Details
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="product-image-container mb-3 text-center">
                                        <img id="update_product_image" src="" class="img-thumbnail product-image mb-2" alt="Product Image" style="max-width: 200px; height: auto;">
                                        <div id="image-controls" style="display: none;">
                                            <input type="file" id="update_new_product_image" name="product_image" class="form-control form-control-sm mb-2" accept="image/*" style="display: none;">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="changeImageBtn">
                                                <i class="bi bi-camera"></i> Change Image
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Product Code</label>
                                                <input type="text" class="form-control" id="update_product_code" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" id="update_status" name="status" disabled>
                                                    <option value="active">Active</option>
                                                    <option value="deactivated">Deactivated</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" class="form-control" id="update_product_name" name="product_name" readonly>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Category</label>
                                                <select class="form-select" id="update_category" name="category_id" disabled>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">

                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" id="update_description" name="product_description" rows="5" readonly></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details Tab -->
                        <div class="tab-pane fade" id="details" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Base Price</label>
                                        <input type="number" step="0.01" class="form-control" id="update_base_price" name="base_price" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Selling Price</label>
                                        <input type="number" step="0.01" class="form-control" id="update_selling_price" name="selling_price" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Expiration Date</label>
                                        <input type="date" class="form-control" id="update_expiration_date" name="expiration_date" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" id="editBtn" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Product
                    </button>
                    <button type="submit" id="saveBtn" class="btn btn-success" style="display: none;">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                    <button type="button" id="cancelBtn" class="btn btn-danger" style="display: none;">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    $('#update_subcategories').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select subcategories',
        allowClear: true,
        dropdownParent: $('#manageProductModal')
    });

    $('#manageProductModal').on('hidden.bs.modal', function () {
        $('#update_subcategories').select2('destroy');
    });

});

</script>