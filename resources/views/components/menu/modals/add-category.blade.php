<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-plus-circle me-2"></i>Add Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <form id="addCategoryForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
                        <div class="form-text">Choose a descriptive name for your category</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter category description (optional)"></textarea>
                        <div class="form-text">Provide additional details about this category</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary px-4" id="saveCategoryBtn">
                    <i class="bi bi-check-lg me-1"></i>Save Category
                </button>
            </div>
        </div>
    </div>
</div>


<style>
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

    .modal-body {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, .2) transparent;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, .2);
        border-radius: 3px;
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }
</style>