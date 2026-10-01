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

    .categories-management-card {
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

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%) !important;
    }

    .modal-dialog.modal-lg {
        max-width: 800px;
        height: 90vh;
    }

    .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .card {
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background-color: var(--bs-light);
    }

    .btn-group>.btn {
        padding: 0.5rem 0.75rem;
        margin: 0 0.1rem;
    }

    .subcategory-actions .btn {
        padding: 0.4rem 0.8rem;
        transition: all 0.2s ease;
    }

    .subcategory-actions .btn:hover {
        background-color: var(--bs-light);
    }

    .nav-tabs .nav-link {
        color: var(--bs-gray-600);
        border: none;
        padding: 0.75rem 1rem;
        margin-right: 0.5rem;
    }

    .nav-tabs .nav-link.active {
        color: var(--bs-primary);
        border-bottom: 2px solid var(--bs-primary);
        background: none;
    }

    .btn {
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }

    .form-control,
    .form-select {
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
    }

    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Categories Management</h4>
                    <p class="text-muted mb-0">Organize and manage product categories and subcategories</p>
                </div>
                <div class="page-title-right">
                    <div class="d-flex align-items-center">
                        <ol class="breadcrumb m-0 me-3">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#addCategoryModal">
                            <i class="bi bi-plus-lg me-2"></i>Add Category
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.categories.statistics-card')
    @include('components.categories.overview')
</div>

@include('components.categories.modals.add-category')
@include('components.categories.modals.add-subcategory')
@include('components.categories.modals.edit-subcategory')

<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-pencil me-2"></i>Edit Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_category_id">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="edit_category_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_category_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="edit_category_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                                <h6 class="card-title text-primary mb-3 fw-bold">
                                    <i class="bi bi-info-circle me-2"></i>Basic Information
                                </h6>
                                <div class="mb-3">
                                    <label class="text-muted small fw-semibold">Category Name</label>
                                    <p id="view_category_name" class="fw-bold mb-0 fs-5 text-dark"></p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small fw-semibold">Description</label>
                                    <p id="view_category_description" class="mb-0 text-dark"></p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small fw-semibold">Status</label>
                                    <p id="view_category_status" class="mb-0"></p>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title text-primary mb-3 fw-bold">
                                    <i class="bi bi-clock-history me-2"></i>Timestamps
                                </h6>
                                <div class="mb-2">
                                    <label class="text-muted small fw-semibold">Created At</label>
                                    <p id="view_category_created" class="mb-0 text-dark"></p>
                                </div>
                                <div>
                                    <label class="text-muted small fw-semibold">Last Updated</label>
                                    <p id="view_category_updated" class="mb-0 text-dark"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body p-3">
                                <h6 class="card-title text-primary mb-3 fw-bold">
                                    <i class="bi bi-diagram-2 me-2"></i>Subcategories
                                    <span id="subcategory_count" class="badge bg-primary ms-2">0</span>
                                </h6>
                                <div id="subcategories_list" class="list-group list-group-flush"
                                    style="max-height: 300px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
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

<div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-danger text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected subcategories? This will also remove their associations
                    with any products.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCategoryConfirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-danger text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category? This will also delete:</p>
                <ul class="text-danger">
                    <li>All subcategories under this category</li>
                    <li>All product associations with these subcategories</li>
                </ul>
                <p class="mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCategoryDelete">
                    <i class="bi bi-trash me-2"></i>Delete Category
                </button>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    let originalSubcategories = [];
    let deletedSubcategories = [];
    let isEditMode = false;
    let categoryToDelete = null;

    function toggleEditMode() {
        isEditMode = !isEditMode;
        if (isEditMode) {
            $('#editButton').addClass('d-none');
            $('#saveButton, #cancelButton').removeClass('d-none');
            renderSubcategoriesWithDelete();
        } else {
            $('#saveButton, #cancelButton').addClass('d-none');
            $('#editButton').removeClass('d-none');
            renderSubcategories(originalSubcategories);
            deletedSubcategories = [];
        }
    }

    function renderSubcategoriesWithDelete() {
        const currentSubcategories = originalSubcategories.filter(sub => !deletedSubcategories.includes(sub.id));
        renderSubcategories(currentSubcategories);
    }

    function renderSubcategories(subcategories) {
        const subcategoriesList = $('#subcategories_list');
        subcategoriesList.empty();

        if (subcategories && subcategories.length > 0) {
            subcategories.forEach(sub => {
                subcategoriesList.append(`
                <div class="list-group-item bg-transparent border-0 py-2" data-id="${sub.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-medium">${sub.name}</h6>
                            <small class="text-muted">
                                ${sub.description || 'No description'}
                                <br>
                                <span class="text-primary">
                                    <i class="bi bi-clock me-1"></i>${sub.created_at}
                                </span>
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-${sub.status === 'active' ? 'success' : 'danger'} me-2">
                                ${sub.status}
                            </span>
                            ${isEditMode ? `
                                <button class="btn btn-sm btn-danger delete-subcategory" data-id="${sub.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `);
            });
        } else {
            subcategoriesList.append(`
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                <h6 class="mb-1">No subcategories found</h6>
                <p class="mb-0 small">This category doesn't have any subcategories yet</p>
            </div>
        `);
        }

        $('#subcategory_count').text(subcategories.length);
    }

    function renderCategoryCard(category) {
        const MAX_VISIBLE_SUBCATEGORIES = 2;
        const remainingCount = category.subcategories.length - MAX_VISIBLE_SUBCATEGORIES;

        return `
        <div class="col-md-6 mb-4" data-category-id="${category.id}">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="card-title fw-bold mb-0">${category.name}</h6>
                            <span class="badge ${category.status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'}">
                                <i class="bi ${category.status === 'active' ? 'bi-check-circle' : 'bi-dash-circle'} me-1"></i>${category.status}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-icon btn-primary btn-sm view-category" 
                                data-id="${category.id}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#viewCategoryModal" 
                                title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-icon btn-danger btn-sm" 
                                onclick="deleteCategory(${category.id})" 
                                title="Delete Category">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-muted small mb-2">${category.description || 'No description'}</p>
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary">
                            <i class="bi bi-folder me-1"></i>${category.subcategories.length} subcategories
                        </span>
                    </div>
                    <hr/>
                    <div class="subcategories-list">
                        ${category.subcategories.length > 0 ? `
                            <div class="list-group list-group-flush">
                                ${category.subcategories.slice(0, MAX_VISIBLE_SUBCATEGORIES).map(sub => `
                                    <div class="list-group-item px-0 py-2 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-medium">${sub.name}</span>
                                                <div class="text-muted small">
                                                    <i class="bi bi-box me-1"></i>${sub.products_count} products
                                                </div>
                                            </div>
                                            <div class="subcategory-actions">
                                                <button class="btn btn-icon btn-light" onclick="editSubcategory(${sub.id})" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-icon btn-light ms-1" onclick="unlinkSubcategory(${category.id}, ${sub.id})" title="Unlink">
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                                ${remainingCount > 0 ? `
                                    <div class="list-group-item px-0 py-2 text-center bg-light">
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-plus-lg me-1"></i>${remainingCount} more subcategories
                                        </span>
                                    </div>
                                ` : ''}
                            </div>
                        ` : `
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-folder2-open h4 mb-2 d-block"></i>
                                    <p class="mb-0">No subcategories linked</p>
                                </div>
                            </div>
                        `}
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 p-4 pt-0">
                    <div class="d-flex">
                        <button class="btn btn-outline-primary w-100" style="font-size:12px;" onclick="showAddSubcategoryModal(${category.id})">
                            <i class="bi bi-plus-circle me-2"></i>Add Subcategory
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    function renderCategories(categories) {
        const container = $('#categoriesContainer');
        const emptyState = $('#overviewEmptyState');
        container.empty();

        if (categories.length === 0) {
            container.hide();
            emptyState.removeClass('d-none');
        } else {
            container.show();
            emptyState.addClass('d-none');
            categories.forEach(category => {
                const categoryHtml = renderCategoryCard(category);
                container.append(categoryHtml);
            });
        }
    }

    function editCategory(id) {
        $.ajax({
            url: `/categories/${id}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const category = response.category;
                    $('#edit_category_id').val(category.id);
                    $('#edit_category_name').val(category.name);
                    $('#edit_category_description').val(category.description);
                    $('#edit_category_status').val(category.status);
                    $('#editCategoryModal').modal('show');
                } else {
                    SweetAlert.error('Error loading category data', response.message || 'Unable to load category data');
                }
            },
            error: function(xhr) {
                SweetAlert.error('Error loading category data', 'Unable to load category data');
            }
        });
    }

    function deleteCategory(id) {
        categoryToDelete = id;
        $('#deleteCategoryConfirmationModal').modal('show');
    }

    function editSubcategory(id) {
        $.ajax({
            url: `/categories/subcategory/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const subcategory = response.subcategory;
                    $('#edit_subcategory_id').val(subcategory.id);
                    $('#edit_subcategory_name').val(subcategory.name);
                    $('#edit_subcategory_description').val(subcategory.description);
                    $('#edit_subcategory_category_id').val(subcategory.category_id);
                    $('#edit_subcategory_status').val(subcategory.status);
                    $('#editSubcategoryModal').modal('show');
                } else {
                    SweetAlert.error('Error', response.message || 'Unable to load subcategory data');
                }
            },
            error: function(xhr) {
                SweetAlert.error('Error', 'Unable to load subcategory data');
            }
        });
    }

    function unlinkSubcategory(categoryId, subcategoryId) {
        SweetAlert.confirmDelete(
            'Delete Subcategory',
            'Are you sure you want to delete this subcategory? This will remove all product associations.'
        ).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('categories.subcategories.delete') }}",
                    type: 'POST',
                    data: {
                        subcategories: [subcategoryId],
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            SweetAlert.success('Success', response.message || 'Subcategory deleted successfully');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            SweetAlert.error('Error', response.message || 'Error deleting subcategory');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        SweetAlert.error('Error', response?.message || 'Error deleting subcategory');
                    }
                });
            }
        });
    }

    function showAddSubcategoryModal(categoryId) {
        const modal = new bootstrap.Modal(document.getElementById('addSubcategoryModal'));
        const parentCategorySelect = $('#parentCategory');
        parentCategorySelect.empty();

        @json($categories).forEach(category => {
            const optionHtml =
                `<option value="${category.id}" ${category.id === categoryId ? 'selected' : ''}>${category.name}</option>`;
            parentCategorySelect.append(optionHtml);
        });

        modal.show();
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        renderCategories(@json($categories));

        $(document).on('click', '.view-category', function(e) {
            e.preventDefault();
            const categoryId = $(this).data('id');

            $.ajax({
                url: `/categories/${categoryId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const category = response.category;
                        originalSubcategories = response.category.subcategories;
                        renderSubcategories(originalSubcategories);
                        $('#view_category_name').text(category.name);
                        $('#view_category_description').text(category.description ||
                            'No description available');
                        $('#view_category_status').html(`
                        <span class="badge bg-${category.status === 'active' ? 'success' : 'danger'}">
                            ${category.status.toUpperCase()}
                        </span>
                    `);

                        $('#view_category_created').text(new Date(category.created_at)
                            .toLocaleString());
                        $('#view_category_updated').text(new Date(category.updated_at)
                            .toLocaleString());

                        $('#viewCategoryModal').modal('show');
                    }
                },
                error: function(xhr) {
                    SweetAlert.error('Error', 'Unable to load category details');
                }
            });
        });

        $('#viewCategoryModal').on('hidden.bs.modal', function() {
            isEditMode = false;
            deletedSubcategories = [];

            $('#editButton').removeClass('d-none');
            $('#saveButton, #cancelButton').addClass('d-none');

            renderSubcategories(originalSubcategories);
        });

        $('#addCategoryForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitButton = form.find('button[type="submit"]');

            if (form.data('submitting')) return;
            form.data('submitting', true);
            submitButton.prop('disabled', true);

            $.ajax({
                url: "{{ route('categories.store') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        SweetAlert.success('Success', response.message || 'Category created successfully');
                        $('#addCategoryModal').modal('hide');
                        form[0].reset();
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        SweetAlert.formValidationError(response.errors, 'Validation Error');
                    } else {
                        SweetAlert.error('Error', response?.message || 'Error creating category');
                    }
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                    form.data('submitting', false);
                }
            });
        });

        $('#addSubcategoryForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitButton = form.find('button[type="submit"]');

            if (form.data('submitting')) return;
            form.data('submitting', true);
            submitButton.prop('disabled', true);

            $.ajax({
                url: "{{ route('categories.subcategory.store') }}",
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        SweetAlert.success('Success', response.message || 'Subcategory created successfully');
                        $('#addSubcategoryModal').modal('hide');
                        form[0].reset();
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        SweetAlert.formValidationError(response.errors, 'Validation Error');
                    } else {
                        SweetAlert.error('Error', response?.message || 'Error creating subcategory');
                    }
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                    form.data('submitting', false);
                }
            });
        });

        $('#confirmCategoryDelete').click(function() {
            if (!categoryToDelete) return;

            $.ajax({
                url: `{{ url('categories') }}/${categoryToDelete}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $('#deleteCategoryConfirmationModal').modal('hide');
                        SweetAlert.success('Success', response.message || 'Category deleted successfully');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    SweetAlert.error('Error', response?.message || 'Error deleting category');
                }
            });
        });

        $('#editCategoryForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitButton = form.find('button[type="submit"]');
            const categoryId = $('#edit_category_id').val();

            if (form.data('submitting')) return;
            form.data('submitting', true);
            submitButton.prop('disabled', true);

            const formData = {
                name: $('#edit_category_name').val(),
                description: $('#edit_category_description').val(),
                status: $('#edit_category_status').val(),
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: `{{ url('categories') }}/${categoryId}`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        SweetAlert.success('Success', response.message || 'Category updated successfully');
                        $('#editCategoryModal').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        SweetAlert.formValidationError(response.errors, 'Validation Error');
                    } else {
                        SweetAlert.error('Error', response?.message || 'Error updating category');
                    }
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                    form.data('submitting', false);
                }
            });
        });

        // Edit mode toggle handler
        $(document).on('click', '#editButton', function() {
            toggleEditMode();
        });

        $(document).on('click', '#cancelButton', function() {
            toggleEditMode();
        });

        $(document).on('click', '#saveButton', function() {
            if (deletedSubcategories.length > 0) {
                $.ajax({
                    url: "{{ route('categories.subcategories.delete') }}",
                    type: 'POST',
                    data: {
                        subcategories: deletedSubcategories,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            SweetAlert.success('Success', response.message || 'Changes saved successfully');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            SweetAlert.error('Error', response.message || 'Error saving changes');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        SweetAlert.error('Error', response?.message || 'Error saving changes');
                    }
                });
            } else {
                toggleEditMode();
            }
        });

        // Handle delete subcategory in edit mode
        $(document).on('click', '.delete-subcategory', function() {
            const subcategoryId = $(this).data('id');
            if (!deletedSubcategories.includes(subcategoryId)) {
                deletedSubcategories.push(subcategoryId);
                renderSubcategoriesWithDelete();
            }
        });

        // Handle edit subcategory form submission
        $('#editSubcategoryForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitButton = form.find('button[type="submit"]');
            const subcategoryId = $('#edit_subcategory_id').val();

            if (form.data('submitting')) return;
            form.data('submitting', true);
            submitButton.prop('disabled', true);

            const formData = {
                name: $('#edit_subcategory_name').val(),
                description: $('#edit_subcategory_description').val(),
                category_id: $('#edit_subcategory_category_id').val(),
                status: $('#edit_subcategory_status').val(),
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: `{{ url('categories/subcategory') }}/${subcategoryId}`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        SweetAlert.success('Success', response.message || 'Subcategory updated successfully');
                        $('#editSubcategoryModal').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        SweetAlert.formValidationError(response.errors, 'Validation Error');
                    } else {
                        SweetAlert.error('Error', response?.message || 'Error updating subcategory');
                    }
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                    form.data('submitting', false);
                }
            });
        });
    });
</script>

@endsection