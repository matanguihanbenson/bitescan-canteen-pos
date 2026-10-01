<!-- View Category Modal -->
<div class="modal fade" id="viewCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-folder me-2"></i>Category Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body p-3">
                                <h6 class="card-title text-primary mb-3 fw-bold border-bottom border-primary border-opacity-25 pb-2">
                                    <i class="bi bi-info-circle me-2"></i>Basic Information
                                </h6>
                                <div class="mb-3">
                                    <label class="text-muted small fw-semibold text-uppercase">Category Name</label>
                                    <p id="view_category_name" class="fw-bold mb-0 fs-5 text-dark"></p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small fw-semibold text-uppercase">Description</label>
                                    <p id="view_category_description" class="mb-0 text-dark"></p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small fw-semibold text-uppercase">Status</label>
                                    <p id="view_category_status" class="mb-0"></p>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title text-secondary mb-3 fw-bold border-bottom border-secondary border-opacity-25 pb-2">
                                    <i class="bi bi-clock-history me-2"></i>Timestamps
                                </h6>
                                <div class="mb-2">
                                    <label class="text-muted small fw-semibold text-uppercase">Created At</label>
                                    <p id="view_category_created" class="mb-0 text-dark"></p>
                                </div>
                                <div>
                                    <label class="text-muted small fw-semibold text-uppercase">Last Updated</label>
                                    <p id="view_category_updated" class="mb-0 text-dark"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body p-3 d-flex flex-column">
                                <div class="mb-3">
                                    <h6 class="card-title text-success mb-3 fw-bold border-bottom border-success border-opacity-25 pb-2">
                                        <i class="bi bi-diagram-2 me-2"></i>Subcategories
                                        <span id="subcategory_count" class="badge bg-success ms-2">0</span>
                                    </h6>
                                </div>
                                <div id="subcategories_list" class="flex-grow-1" style="max-height: 300px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-primary edit-mode-btn" id="editButton">
                    <i class="bi bi-pencil me-2"></i>Edit
                </button>
                <button type="button" class="btn btn-success save-mode-btn d-none" id="saveButton">
                    <i class="bi bi-check-lg me-2"></i>Save Changes
                </button>
                <button type="button" class="btn btn-warning cancel-mode-btn d-none" id="cancelButton">
                    <i class="bi bi-x-lg me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enhanced styling for view category modal */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }

    /* Modal height management */
    .modal-dialog-scrollable {
        max-height: 90vh;
    }

    .modal-dialog-scrollable .modal-content {
        max-height: 90vh;
    }

    .modal-dialog-scrollable .modal-body {
        overflow-y: auto;
        max-height: calc(90vh - 200px);
    }

    /* Scrollbar styling for subcategories list */
    #subcategories_list {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, .2) transparent;
    }

    #subcategories_list::-webkit-scrollbar {
        width: 6px;
    }

    #subcategories_list::-webkit-scrollbar-track {
        background: transparent;
    }

    #subcategories_list::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, .2);
        border-radius: 3px;
    }

    /* Card styling improvements */
    .card-title {
        font-size: 0.9rem;
    }

    /* Badge and status styling */
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    /* List group item styling */
    .list-group-item {
        border: none;
        border-bottom: 1px solid rgba(0, 0, 0, .1);
        background: transparent;
        transition: background-color 0.2s ease;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .list-group-item:hover {
        background-color: rgba(0, 0, 0, .02);
    }

    /* Button styling */
    .modal-footer .btn {
        min-width: 100px;
        font-weight: 500;
    }

    /* Improved empty state styling */
    .empty-subcategories {
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Card border improvements */
    .card {
        border: 1px solid rgba(0, 0, 0, .08);
    }

    /* Text styling improvements */
    .text-uppercase {
        letter-spacing: 0.5px;
    }
</style>