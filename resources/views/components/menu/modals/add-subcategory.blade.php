<!-- Add Subcategory Modal -->
<div class="modal fade" id="addSubcategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-plus-circle me-2"></i>Add Subcategory
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addSubcategoryForm">
                    @csrf
                    <input type="hidden" id="parentCategoryId" name="menucategory_id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Parent Category</label>
                        <input type="text" class="form-control bg-light" id="parentCategoryName" readonly>
                        <div class="form-text">The category this subcategory will belong to</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Enter subcategory name" required>
                        <div class="form-text">Choose a descriptive name for your subcategory</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Enter subcategory description (optional)"></textarea>
                        <div class="form-text">Provide additional details about this subcategory</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success px-4" id="saveSubcategoryBtn">
                    <i class="bi bi-check-lg me-1"></i>Save Subcategory
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Ensure better contrast for gradient headers */
    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
    }
</style>