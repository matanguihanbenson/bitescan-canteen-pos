<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="icon-box mb-3">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                </div>
                <h4 class="modal-title fw-bold mb-3">Delete Category</h4>
                <p class="mb-1">Are you sure you want to delete category</p>
                <p class="fw-bold mb-3">"<span id="deleteCategoryName"></span>"?</p>
                <p class="text-danger small mb-4">
                    <i class="bi bi-info-circle me-1"></i>
                    This action cannot be undone.
                </p>
                <input type="hidden" id="deleteCategoryId">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteCategory">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-body .icon-box {
    height: 80px;
    width: 80px;
    margin: 0 auto;
    border-radius: 50%;
    z-index: 9;
    text-align: center;
    border: 3px solid #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
