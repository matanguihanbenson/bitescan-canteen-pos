<div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom py-2">
                <h5 class="modal-title" id="editMenuModalLabel">Edit Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMenuForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-3" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                    <input type="hidden" id="edit_menu_id" name="id">
                    <input type="hidden" id="edit_menu_code" name="menu_code">
                    
                    <div class="row mb-3">
                        <div class="col-lg-4">
                            <div id="currentImagePreview" class="text-center mb-2">
                            </div>
                            <div class="form-group">
                                <label class="form-label small fw-bold mb-1" for="edit_menu_image">Change Image</label>
                                <input type="file" class="form-control form-control-sm" id="edit_menu_image" name="image" accept="image/*">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group mb-2">
                                <label class="form-label small fw-bold mb-1" for="edit_menu_name">Name</label>
                                <input type="text" class="form-control form-control-sm" id="edit_menu_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group mb-2">
                                <label class="form-label small fw-bold mb-1" for="edit_menu_price">Price</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="edit_menu_price" name="price" step="0.01" required>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-6">
                            <label class="form-label small fw-bold mb-1" for="edit_menu_category">Category</label>
                            <select class="form-select form-select-sm" id="edit_menu_category" name="menucategory_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label small fw-bold mb-1" for="edit_menu_subcategory">Subcategory</label>
                            <select class="form-select form-select-sm" id="edit_menu_subcategory" name="menusubcategory_id">
                                <option value="">Select Subcategory</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1" for="edit_menu_description">Description</label>
                        <textarea class="form-control form-control-sm" id="edit_menu_description" name="description" rows="2"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1 d-block">Status</label>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="status" id="edit_status_active" value="active">
                            <label class="btn btn-outline-success" for="edit_status_active">Active</label>
                            <input type="radio" class="btn-check" name="status" id="edit_status_deactivated" value="deactivated">
                            <label class="btn btn-outline-danger" for="edit_status_deactivated">Deactivated</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 position-sticky bottom-0 bg-white">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal-dialog {
    display: flex;
    flex-direction: column;
    height: auto;
}

.modal-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.modal-body {
    flex: 1;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,.2) transparent;
}

.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: transparent;
}

.modal-body::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,.2);
    border-radius: 3px;
}

.modal-dialog-scrollable {
    max-height: 90vh;
}

#currentImagePreview {
    border: 1px dashed #ddd;
    border-radius: 4px;
    padding: 4px;
    background: #f8f9fa;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#currentImagePreview img {
    max-width: 100%;
    max-height: 150px;
    object-fit: contain;
}

#currentImagePreview .no-image {
    color: #999;
    font-size: 0.875rem;
}

.btn-check:checked + .btn-outline-success {
    color: #fff;
}

.invalid-feedback {
    font-size: 0.75rem;
}
</style>
