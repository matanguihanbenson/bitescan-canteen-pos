<div class="modal fade" id="editSubcategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editSubcategoryForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_subcategory_id" name="subcategory_id">
                    <input type="hidden" id="edit_menucategory_id" name="menucategory_id">
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <input type="text" class="form-control" id="edit_parent_category_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subcategory Name</label>
                        <input type="text" class="form-control" name="name" id="edit_subcategory_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_subcategory_description" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateSubcategoryBtn">Update Subcategory</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#updateSubcategoryBtn').click(function() {
        const subcategoryId = $('#edit_subcategory_id').val();
        const formData = $('#editSubcategoryForm').serialize();
        
        $.ajax({
            type: 'PUT',
            url: `/menu/subcategories/${subcategoryId}`,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editSubcategoryModal').modal('hide');
                    toastr.success('Subcategory updated successfully');
                    localStorage.setItem('activeTab', 'categories');
                    window.location.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error updating subcategory');
            }
        });
    });
});
</script>
