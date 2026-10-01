<script>
$(document).ready(function() {
    const CONFIG = {
        URLS: {
            CATEGORIES: '/menu/categories',
            SUBCATEGORIES: '/menu/categories/:id/subcategories',
            MENU_ITEMS: '/menu/items',
            DASHBOARD_STATS: '/menu/dashboard-stats'
        },
        SELECTORS: {
            MODALS: {
                EDIT_MENU: '#editMenuModal',
                ADD_CATEGORY: '#addCategoryModal',
                DELETE_CATEGORY: '#deleteCategoryModal'
            },
            FORMS: {
                EDIT_MENU: '#editMenuForm',
                ADD_CATEGORY: '#addCategoryForm'
            }
        }
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class MenuManager {
        constructor() {
            this.initializeEventListeners();
            this.initializeDataTable();
            this.loadCategories();
            this.loadDashboardStats();
            this.restoreActiveTab();
            this.initializeArchivedItemsTab();
            this.initializeDataTables();
        }

        restoreActiveTab() {
            const activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $(`#menuTabs a[href="#${activeTab}"]`).tab('show');
                localStorage.removeItem('activeTab');
            }
        }

        initializeArchivedItemsTab() {
            $('#menuTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', (e) => {
                if (e.target.id === 'archived-tab') {
                    this.loadArchivedItems();
                }
            });

            $('#searchArchived').on('keyup', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                $('.archived-item-card').each(function() {
                    const itemName = $(this).find('.card-title').text().toLowerCase();
                    const itemDesc = $(this).find('.card-text').text().toLowerCase();
                    const itemCategory = $(this).find('.category-name').text().toLowerCase();
                    const matches = itemName.includes(searchTerm) || 
                                  itemDesc.includes(searchTerm) || 
                                  itemCategory.includes(searchTerm);
                    $(this).toggleClass('d-none', !matches);
                });
            });

            $(document).on('click', '.restore-item', (e) => {
                const itemId = $(e.currentTarget).data('id');
                $.post(`/menu/${itemId}/restore`, (response) => {
                    if (response.success) {
                        toastr.success('Item restored successfully');
                        this.loadArchivedItems();
                        if (window.LaravelDataTables['menu-table']) {
                            window.LaravelDataTables['menu-table'].ajax.reload();
                        }
                    } else {
                        toastr.error('Error restoring item');
                    }
                });
            });
        }

        loadArchivedItems() {
            $('#archivedItems').html(`
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);

            $.ajax({
                url: '/menu/archived',
                type: 'GET',
                dataType: 'json',
                success: (response) => {
                    let html = '';
                    
                    if (!response.success || !response.items || response.items.length === 0) {
                        html = `
                            <div class="col-12 text-center py-5">
                                <img src="/images/empty-archive.png" alt="No archived items" class="mb-3" style="max-width: 64px; opacity: 0.5;">
                                <h5>No Archived Items</h5>
                                <p class="text-muted">Items that are archived will appear here</p>
                            </div>
                        `;
                    } else {
                        response.items.forEach(item => {
                            html += `
                                <div class="col-md-3 mb-3 archived-item-card" data-id="${item.id}">
                                    <div class="card h-100 border-0 shadow-sm menu-card">
                                        <div class="position-relative">
                                            <img src="${item.image_url ? item.image_url : '/images/menu.png'}" 
                                                 class="card-img-top" 
                                                 alt="${item.name}"
                                                 style="height: 160px; object-fit: cover;"
                                                 onerror="this.src='/images/menu.png'">
                                            <button class="btn btn-sm btn-success restore-item position-absolute top-0 end-0 m-2" 
                                                    data-id="${item.id}" 
                                                    title="Restore Item">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                                <span class="small">Restore</span>
                                            </button>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 text-truncate">${item.name}</h6>
                                                <span class="badge bg-secondary ms-2">Archived</span>
                                            </div>
                                            <p class="card-text text-muted small mb-2 text-truncate">${item.description || 'No description'}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-primary fw-bold">₱${parseFloat(item.price).toFixed(2)}</span>
                                                <small class="category-name text-muted">
                                                    <i class="bi bi-folder me-1"></i>${item.category ? item.category.name : 'No category'}
                                                </small>
                                            </div>
                                            <div class="text-muted small mt-2 border-top pt-2">
                                                <i class="bi bi-clock"></i> ${new Date(item.archived_at).toLocaleDateString()}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    
                    $('#archivedItems').html(html);
                    },
                error: (xhr, status, error) => {
                    $('#archivedItems').html(`
                        <div class="col-12 text-center py-5 text-danger">
                            <i class="bi bi-exclamation-circle-fill fs-1"></i>
                            <h5 class="mt-2">Error Loading Archived Items</h5>
                            <p class="text-muted">Please try refreshing the page</p>
                        </div>
                    `);
                }
            });
        }

        renderArchivedItem(item) {
            return `
                <div class="col-md-3 mb-3 archived-item-card">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-body">
                            <h6 class="card-title mb-1">${item.name}</h6>
                            <p class="card-text text-muted small mb-2">${item.description || 'No description'}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">₱${parseFloat(item.price).toFixed(2)}</span>
                                <small class="text-muted">
                                    <i class="bi bi-folder me-1"></i>${item.category ? item.category.name : 'No category'}
                                </small>
                            </div>
                            <div class="text-muted small mt-2">
                                <i class="bi bi-clock"></i> Archived on ${item.archived_at}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        showArchivedError() {
            $('#archivedItems').html(`
                <div class="col-12 text-center py-5">
                    <div class="text-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        Error loading archived items
                    </div>
                </div>
            `);
        }

        initializeEventListeners() {
            $('#menuTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', (e) => {
                const tabId = $(e.target).attr('href').replace('#', '');
                localStorage.setItem('activeTab', tabId);
            });

            $('#searchMenu').on('keyup', () => {
                $('#menuTable').DataTable().search($('#searchMenu').val()).draw();
            });

            $('#selectAll').on('click', function() {
                $('.selectItem').prop('checked', this.checked);
            });

            $('#bulkDeleteBtn').on('click', function() {
            });

            $('#bulkExportBtn').on('click', function() {
            });

            $(document).on('click', '.edit-menu-btn', (e) => {
                const modal = $(CONFIG.SELECTORS.MODALS.EDIT_MENU);
                const allData = $(e.currentTarget).data('all');
                const id = $(e.currentTarget).data('id');
                modal.find('form')[0].reset();
                modal.find('.is-invalid').removeClass('is-invalid');
                modal.find('.invalid-feedback').empty();
                $('#currentImagePreview').empty();
                modal.find('.modal-content').addClass('loading');
                if (allData) {
                    this.populateForm(allData);
                    modal.find('.modal-content').removeClass('loading');
                } else {
                    $.ajax({
                        url: `/menu/${id}`,
                        type: 'GET',
                        success: (response) => {
                            if (response.success) {
                                this.populateForm(response.data);
                            } else {
                                toastr.error('Error loading menu item details');
                            }
                        },
                        error: () => {
                            toastr.error('Failed to load menu item details');
                        },
                        complete: () => {
                            modal.find('.modal-content').removeClass('loading');
                        }
                    });
                }
            });

            $('#editMenuForm').off('submit').on('submit', (e) => {
                e.preventDefault();
                const form = $(e.currentTarget);
                const submitBtn = form.find('button[type="submit"]');
                const modal = $(CONFIG.SELECTORS.MODALS.EDIT_MENU);
                const menuId = $('#edit_menu_id').val();
                const categoryId = $('#edit_menu_category').val();
                const currentSubcategoryId = $('#edit_menu_subcategory').val();

                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').empty();
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
                modal.find('.modal-content').addClass('loading');

                const formData = new FormData(e.currentTarget);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/menu/${menuId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (response.success) {
                            toastr.success('Menu item updated successfully');
                            
                            $.get(`/menu/categories/${categoryId}/subcategories`, (subResponse) => {
                                if (subResponse.success && subResponse.subcategories) {
                                    let options = '<option value="">Select Subcategory</option>';
                                    subResponse.subcategories.forEach((subcategory) => {
                                        options += `<option value="${subcategory.id}" ${subcategory.id == currentSubcategoryId ? 'selected' : ''}>
                                            ${subcategory.name}
                                        </option>`;
                                    });
                                    $('#edit_menu_subcategory').html(options);
                                    
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                }
                            });
                        } else {
                            toastr.error(response.message || 'Error updating menu item');
                            submitBtn.prop('disabled', false).html('Save Changes');
                            modal.find('.modal-content').removeClass('loading');
                        }
                    },
                    error: (xhr) => {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(field => {
                                $(`#edit_menu_${field}`).addClass('is-invalid')
                                    .siblings('.invalid-feedback')
                                    .text(errors[field][0]);
                            });
                            toastr.error('Please check the form for errors');
                        } else {
                            toastr.error('Error updating menu item');
                        }
                        submitBtn.prop('disabled', false).html('Save Changes');
                        modal.find('.modal-content').removeClass('loading');
                    }
                });
            });

            $('#category-filter').on('change', () => {
                const categoryId = $('#category-filter').val();
                if (categoryId) {
                    $.get(`/menu/categories/${categoryId}/subcategories`, (data) => {
                        let options = '<option value="">All Subcategories</option>';
                        if (data.success && data.subcategories) {
                            data.subcategories.forEach((subcategory) => {
                                options += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                            });
                        }
                        $('#subcategory-filter').html(options);
                        window.LaravelDataTables['menu-table'].draw();
                    });
                } else {
                    $('#subcategory-filter').html('<option value="">All Subcategories</option>');
                    window.LaravelDataTables['menu-table'].draw();
                }
            });

            $('#subcategory-filter').on('change', () => {
                window.LaravelDataTables['menu-table'].draw();
            });

            $('#status-filter').on('change', () => {
                if (window.LaravelDataTables['menu-table']) {
                    window.LaravelDataTables['menu-table'].ajax.reload();
                }
            });

            $('.check-all').on('click', function() {
                $('.dt-checkboxes').prop('checked', $(this).prop('checked')).trigger('change');
            });

            $(document).on('change', '.dt-checkboxes', () => {
                const checked = $('.dt-checkboxes:checked').length;
                const total = $('.dt-checkboxes').length;
                $('.check-all').prop('checked', checked === total);
                if (checked > 0) {
                    $('.bulk-actions').removeClass('d-none');
                    $('.card-tools').addClass('d-none');
                } else {
                    $('.bulk-actions').addClass('d-none');
                    $('.card-tools').removeClass('d-none');
                }
            });

            $('#saveCategoryBtn').click(() => {
                const formData = $('#addCategoryForm').serialize();
                $.ajax({
                    type: 'POST',
                    url: CONFIG.URLS.CATEGORIES,
                    data: formData,
                    success: (response) => {
                        if (response.success) {
                            localStorage.setItem('activeTab', 'categories');
                            window.location.reload();
                        }
                    },
                    error: (xhr) => {
                        toastr.error(xhr.responseJSON?.message || 'Error adding category');
                    }
                });
            });

            $('#addCategoryModal').on('hidden.bs.modal', () => {
                $('#addCategoryForm')[0].reset();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });

            $('#confirmDeleteCategory').on('click', () => {
                const categoryId = $('#deleteCategoryId').val();
                $.ajax({
                    type: 'DELETE',
                    url: `/menu/categories/${categoryId}`,
                    success: (response) => {
                        if (response.success) {
                            $('#deleteCategoryModal').modal('hide');
                            toastr.success('Category deleted successfully');
                            localStorage.setItem('activeTab', 'categories');
                            window.location.reload();
                        }
                    },
                    error: (xhr) => {
                        toastr.error('Error deleting category');
                    }
                });
            });

            $('#confirmDeleteSubcategoryBtn').click(() => {
                const subcategoryId = $('#deleteSubcategoryId').val();
                $.ajax({
                    type: 'DELETE',
                    url: `/menu/subcategories/${subcategoryId}`,
                    success: (response) => {
                        if (response.success) {
                            $('#deleteSubcategoryModal').modal('hide');
                            toastr.success('Subcategory deleted successfully');
                            localStorage.setItem('activeTab', 'categories');
                            window.location.reload();
                        }
                    },
                    error: (xhr) => {
                        toastr.error(xhr.responseJSON?.message || 'Error deleting subcategory');
                    }
                });
            });

            $('#updateCategoryBtn').click(() => {
                const categoryId = $('#edit_category_id').val();
                const formData = $('#editCategoryForm').serialize();
                $.ajax({
                    type: 'PUT',
                    url: `/menu/categories/${categoryId}`,
                    data: formData,
                    success: (response) => {
                        if (response.success) {
                            toastr.success('Category updated successfully!');
                            localStorage.setItem('activeTab', 'categories');
                            window.location.reload();
                        }
                    },
                    error: (xhr) => {
                        toastr.error(xhr.responseJSON?.message || 'Error updating category');
                    }
                });
            });

            $('#addMenuModal').on('show.bs.modal', () => {
                this.loadMenuCategories();
            });

            $('#menuCategory').on('change', () => {
                const categoryId = $('#menuCategory').val();
                if (categoryId) {
                    this.loadMenuSubcategories(categoryId);
                    $('#menuSubcategory').prop('disabled', false);
                } else {
                    $('#menuSubcategory').html('<option value="">Select Category First</option>').prop('disabled', true);
                }
            });

            $('#saveMenuBtn').click(() => {
                const formData = new FormData($('#addMenuForm')[0]);
                $.ajax({
                    type: 'POST',
                    url: CONFIG.URLS.MENU_ITEMS,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (response.success) {
                            $('#addMenuModal').modal('hide');
                            toastr.success('Menu item added successfully');
                            window.location.reload();
                        }
                    },
                    error: (xhr) => {
                        toastr.error(xhr.responseJSON?.message || 'Error adding menu item');
                    }
                });
            });

            $('#addMenuModal').on('hidden.bs.modal', () => {
                $('#addMenuForm')[0].reset();
                $('#menuSubcategory').html('<option value="">Select Category First</option>').prop('disabled', true);
            });

            $('#addSubcategoryForm').on('submit', (e) => {
                e.preventDefault();
                this.submitSubcategory();
            });

            $('#saveSubcategoryBtn').on('click', () => {
                this.submitSubcategory();
            });

            $('#addSubcategoryModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
            });

            $(document).off('click', '[data-bs-target="#editMenuModal"]').on('click', '[data-bs-target="#editMenuModal"]', (e) => {
                const id = $(e.currentTarget).data('id');
                const modal = $(CONFIG.SELECTORS.MODALS.EDIT_MENU);
                modal.find('.is-invalid').removeClass('is-invalid');
                modal.find('.invalid-feedback').empty();
                $('#currentImagePreview').empty();
                modal.find('.modal-content').addClass('loading');
                
                $.get(`/menu/${id}`, (response) => {
                    if (response.success) {
                        const item = response.data;
                        
                        $('#edit_menu_id').val(item.id);
                        $('#edit_menu_name').val(item.name);
                        $('#edit_menu_price').val(item.price);
                        $('#edit_menu_description').val(item.description);
                        $(`#edit_status_${item.status}`).prop('checked', true);
                        $('#edit_menu_code').val(item.menu_code);
                        if (item.menucategory_id) {
                            $('#edit_menu_category').val(item.menucategory_id);
                            this.loadSubcategoriesAndSelect(item.menucategory_id, item.menusubcategory_id);
                        }
                        
                        if (item.image_url) {
                            $('#currentImagePreview').html(`
                                <div class="position-relative">
                                    <img src="${item.image_url}" class="img-thumbnail" alt="${item.name}">
                                </div>
                            `);
                        } else {
                            $('#currentImagePreview').html(`
                                <div class="no-image">
                                    <i class="bi bi-image text-muted"></i><br>
                                    No image
                                </div>
                            `);
                        }
                    } else {
                        toastr.error('Error loading menu item details');
                    }
                    modal.find('.modal-content').removeClass('loading');
                }).fail(() => {
                    modal.find('.modal-content').removeClass('loading');
                    toastr.error('Failed to load menu item details');
                });
            });

            $('#edit_menu_category').off('change').on('change', function() {
                const categoryId = $(this).val();
                const subcategorySelect = $('#edit_menu_subcategory');
                subcategorySelect.html('<option value="">Select Subcategory</option>');
                
                if (categoryId) {
                    $.get(`/menu/categories/${categoryId}/subcategories`, (response) => {
                        if (response.success && response.subcategories) {
                            response.subcategories.forEach((subcategory) => {
                                subcategorySelect.append(`
                                    <option value="${subcategory.id}">${subcategory.name}</option>
                                `);
                            });
                        }
                    });
                }
            });

            $('.check-all').on('click', function() {
                $('.dt-checkboxes').prop('checked', $(this).prop('checked')).trigger('change');
            });

            $(document).on('change', '.dt-checkboxes', () => {
                const checked = $('.dt-checkboxes:checked').length;
                const total = $('.dt-checkboxes').length;
                $('.check-all').prop('checked', checked === total);
                if (checked > 0) {
                    $('.bulk-actions').removeClass('d-none');
                    $('.card-tools').addClass('d-none');
                } else {
                    $('.bulk-actions').addClass('d-none');
                    $('.card-tools').removeClass('d-none');
                }
            });

            $(document).on('click', '.archive-menu', (e) => {
                const id = $(e.currentTarget).data('id');
                const name = $(e.currentTarget).data('name');
                
                if (confirm(`Are you sure you want to archive "${name}"?`)) {
                    $.ajax({
                        url: `/menu/${id}/archive`,
                        type: 'POST',
                        success: (response) => {
                            if (response.success) {
                                toastr.success('Menu item archived successfully');
                                window.LaravelDataTables['menu-table'].ajax.reload();
                                if ($('#archivedTable').DataTable()) {
                                    $('#archivedTable').DataTable().ajax.reload();
                                }
                            } else {
                                toastr.error(response.message || 'Error archiving menu item');
                            }
                        },
                        error: (xhr) => {
                            toastr.error('Error archiving menu item');
                        },
                        complete: () => {
                            }
                    });
                }
            });

            $('#searchCategories').on('keyup', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                $('#categoriesList .list-category-item').each(function() {
                    const categoryName = $(this).find('.category-title').text().toLowerCase();
                    const categoryDesc = $(this).find('.category-desc').text().toLowerCase();
                    const matches = categoryName.includes(searchTerm) || categoryDesc.includes(searchTerm);
                    $(this).toggle(matches);
                });
            });

            $('#searchSubcategories').on('keyup', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                $('#subcategoriesList .list-group-item').each(function() {
                    const name = $(this).find('h6').text().toLowerCase();
                    const desc = $(this).find('small').text().toLowerCase();
                    const matches = name.includes(searchTerm) || desc.includes(searchTerm);
                    $(this).toggle(matches);
                });
            });

            $('.list-category-item').on('click', () => {
                $('#searchSubcategories').val('').trigger('keyup');
            });

            $('#menuTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', () => {
                $('#searchCategories, #searchSubcategories').val('').trigger('keyup');
            });

            $(document).on('click', '.restore-item', (e) => {
                const id = $(e.currentTarget).data('id');
                $('#restoreItemId').val(id);
                $('#restoreConfirmModal').modal('show');
            });

            $('#menuTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', (e) => {
                if (e.target.getAttribute('href') === '#archived') {
                    this.loadArchivedItems();
                }
            });

            $('#confirmRestoreBtn').on('click', function() {
                const id = $('#restoreItemId').val();
                const modal = $('#restoreConfirmModal');
                const btn = $(this);
                const archivedCard = $(`.archived-item-card[data-id="${id}"]`);
                
                btn.prop('disabled', true)
                   .html('<span class="spinner-border spinner-border-sm me-1"></span> Restoring...');
                
                $.ajax({
                    url: `/menu/${id}/restore`,
                    type: 'POST',
                    success: (response) => {
                        if (response.success) {
                            modal.modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            
                            archivedCard.fadeOut(300, function() {
                                $(this).remove();
                                if ($('.archived-item-card').length === 0) {
                                    $('#archivedItems').html(`
                                        <div class="col-12 text-center py-5">
                                            <img src="/images/empty-archive.png" alt="No archived items" class="mb-3" style="max-width: 64px; opacity: 0.5;">
                                            <h5>No Archived Items</h5>
                                            <p class="text-muted">Items that are archived will appear here</p>
                                        </div>
                                    `);
                                }
                            });
                            
                            toastr.success('Item restored successfully');
                            
                            if (window.LaravelDataTables['menu-table']) {
                                window.LaravelDataTables['menu-table'].ajax.reload();
                            }
                        } else {
                            toastr.error(response.message || 'Error restoring item');
                        }
                    },
                    error: () => {
                        toastr.error('Error restoring item');
                    },
                    complete: () => {
                        btn.prop('disabled', false)
                           .html('<i class="bi bi-check-lg me-1"></i> Restore');
                        modal.modal('hide');
                    }
                });
            });

            $('#searchArchived').on('keyup', (e) => {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                const totalCards = $('.archived-item-card').length;
                let visibleCards = 0;
                
                $('.archived-item-card').each(function() {
                    const card = $(this);
                    const itemName = card.find('.card-title').text().toLowerCase();
                    const itemDesc = card.find('.card-text').text().toLowerCase();
                    const itemCategory = card.find('.category-name').text().toLowerCase();
                    const itemPrice = card.find('.text-primary').text().toLowerCase();
                    
                    const matches = itemName.includes(searchTerm) || 
                                   itemDesc.includes(searchTerm) || 
                                   itemCategory.includes(searchTerm) ||
                                   itemPrice.includes(searchTerm);
                    
                    if (matches) {
                        visibleCards++;
                        card.fadeIn(200);
                    } else {
                        card.fadeOut(200);
                    }
                });
                
                if (visibleCards === 0 && searchTerm !== '') {
                    if (!$('#noSearchResults').length) {
                        $('#archivedItems').append(`
                            <div id="noSearchResults" class="col-12 text-center py-5" style="display: none;">
                                <img src="/images/search.png" alt="No results" class="mb-3" style="max-width: 64px; opacity: 0.5;">
                                <h5>No Matches Found</h5>
                                <p class="text-muted">No items match your search "${searchTerm}"</p>
                            </div>
                        `);
                        $('#noSearchResults').fadeIn(200);
                    }
                } else {
                    $('#noSearchResults').fadeOut(200, function() {
                        $(this).remove();
                    });
                }
                
                if (searchTerm !== '') {
                    $('.search-status').remove();
                    $('#searchArchived').after(`
                        <div class="search-status small text-muted mt-1">
                            Found ${visibleCards} of ${totalCards} items
                        </div>
                    `);
                } else {
                    $('.search-status').remove();
                }
            });
        }

        initializeDataTable() {
            $('#menuTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/menu',
                    type: 'GET',
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.category = $('#category-filter').val();
                        d.subcategory = $('#subcategory-filter').val();
                    },
                    error: function(xhr, error, thrown) {
                        toastr.error('Error loading menu items');
                    }
                },
                columns: [
                    { 
                        data: 'image', 
                        name: 'image', 
                        orderable: false,
                        searchable: false
                    },
                    { data: 'name', name: 'menus.name' },
                    { data: 'price', name: 'menus.price' },
                    { 
                        data: 'category.name', 
                        name: 'category.name', 
                        defaultContent: '-'
                    },
                    { 
                        data: 'subcategory.name', 
                        name: 'subcategory.name', 
                        defaultContent: '-'
                    },
                    { data: 'status', name: 'menus.status' },
                    { 
                        data: 'action', 
                        name: 'action', 
                        orderable: false, 
                        searchable: false 
                    }
                ]
            });

            
        }

        loadCategories() {
            this.showCategoryLoading();
            $.ajax({
                type: 'GET',
                url: CONFIG.URLS.CATEGORIES,
                success: (response) => {
                    if (!response || !response.success) {
                        toastr.error('Error loading categories');
                        $('#emptyCategoriesState').show();
                        $('#categoriesList').hide();
                        return;
                    }

                    const categories = response.categories;
                    $('#categoriesList').empty();

                    if (!Array.isArray(categories) || categories.length === 0) {
                        $('#emptyCategoriesState').show();
                        $('#categoriesList').hide();
                        return;
                    }

                    $('#emptyCategoriesState').hide();
                    $('#categoriesList').show();

                    categories.forEach((category, index) => {
                        const isFirst = index === 0;
                        const categoryHtml = `
                            <div class="list-group-item list-category-item d-flex justify-content-between align-items-center ${isFirst ? 'active' : ''}" data-id="${category.id}">
                                <div>
                                    <h6 class="mb-1 category-title">${category.name}</h6>
                                    <small class="text-muted category-desc">${category.description || 'No description'}</small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-${isFirst ? 'light' : 'primary'} edit-category-btn" 
                                        data-id="${category.id}" 
                                        data-name="${category.name}" 
                                        data-description="${category.description || ''}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editCategoryModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-${isFirst ? 'light' : 'primary'} delete-category-btn"
                                        data-id="${category.id}"
                                        data-name="${category.name}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteCategoryModal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        $('#categoriesList').append(categoryHtml);
                    });

                    if (categories.length > 0) {
                        const firstCategory = categories[0];
                        $('#parentCategoryId').val(firstCategory.id);
                        $('#parentCategoryName').val(firstCategory.name);
                        this.loadSubcategoriesByParent(firstCategory.id);
                    }

                    this.attachCategoryEventHandlers();
                },
                error: (xhr) => {
                    toastr.error('Error loading categories');
                    $('#emptyCategoriesState').show();
                    $('#categoriesList').hide();
                },
                complete: () => {
                    $('.loading-spinner').remove();
                }
            });
        }

        loadSubcategoriesByParent(categoryId) {
            if (!categoryId) {
                this.showEmptySubcategories();
                return;
            }
            this.showSubcategoryLoading();
            $('#addSubcategoryBtn').show();
            
            const url = CONFIG.URLS.SUBCATEGORIES.replace(':id', categoryId);
            
            $.ajax({
                type: 'GET',
                url: url,
                success: (response) => {
                    if (!response.success || !response.subcategories || !Array.isArray(response.subcategories) || response.subcategories.length === 0) {
                        $('#noSubcategoriesState').show();
                        $('#subcategoriesList').hide();
                        return;
                    }
                    $('#noSubcategoriesState').hide();
                    $('#subcategoriesList').show().empty();
                    let html = '';
                    response.subcategories.forEach((subcategory) => {
                        html += `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">${subcategory.name}</h6>
                                    <small class="text-muted">${subcategory.description || 'No description'}</small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-subcategory-btn" 
                                        data-id="${subcategory.id}"
                                        data-name="${subcategory.name}"
                                        data-description="${subcategory.description || ''}"
                                        data-category-id="${categoryId}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editSubcategoryModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-subcategory-btn"
                                        data-id="${subcategory.id}"
                                        data-name="${subcategory.name}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteSubcategoryModal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    $('#subcategoriesList').html(html);
                    this.attachSubcategoryEventHandlers();
                },
                error: (xhr) => {
                    $('#subcategoriesList').hide();
                    $('#noSubcategoriesState').show();
                    toastr.error('Error loading subcategories');
                }
            });
        }

        attachCategoryEventHandlers() {
            $('.list-category-item').off('click').on('click', (e) => {
                if (!$(e.target).closest('.btn-group').length) {
                    $('.list-category-item').removeClass('active');
                    $('.list-category-item .btn').removeClass('btn-outline-light').addClass('btn-outline-primary');
                    $(e.currentTarget).addClass('active');
                    $(e.currentTarget).find('.btn').removeClass('btn-outline-primary').addClass('btn-outline-light');
                    const categoryId = $(e.currentTarget).data('id');
                    const categoryName = $(e.currentTarget).find('.category-title').text();
                    $('#parentCategoryId').val(categoryId);
                    $('#parentCategoryName').val(categoryName);
                    this.loadSubcategoriesByParent(categoryId);
                }
            });

            $('.edit-category-btn').off('click').on('click', (e) => {
                e.stopPropagation();
                const id = $(e.currentTarget).data('id');
                const name = $(e.currentTarget).data('name');
                const description = $(e.currentTarget).data('description');
                $('#edit_category_id').val(id);
                $('#edit_category_name').val(name);
                $('#edit_category_description').val(description);
            });

            $('.delete-category-btn').off('click').on('click', (e) => {
                e.stopPropagation();
                const id = $(e.currentTarget).data('id');
                const name = $(e.currentTarget).data('name');
                $('#deleteCategoryId').val(id);
                $('#deleteCategoryName').text(name);
                $('#deleteCategoryModal').modal('show');
            });
        }

        attachSubcategoryEventHandlers() {
            $('.edit-subcategory-btn').off('click').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');
                const categoryId = $(this).data('category-id');
                const categoryName = $('#parentCategoryName').val();
                $('#edit_subcategory_id').val(id);
                $('#edit_subcategory_name').val(name);
                $('#edit_subcategory_description').val(description);
                $('#edit_menucategory_id').val(categoryId);
                $('#edit_parent_category_name').val(categoryName);
            });

            $('.delete-subcategory-btn').off('click').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#deleteSubcategoryId').val(id);
                $('#deleteSubcategoryName').text(name);
            });
        }

        showCategoryLoading() {
            $('#categoriesList').html(`
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">Loading categories...</div>
                </div>
            `);
        }

        showSubcategoryLoading() {
            $('#subcategoriesList').html(`
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
        }

        showEmptySubcategories() {
            $('#noSubcategoriesState').show();
            $('#addSubcategoryBtn').show();
            $('#subcategoriesList').hide().empty();
        }


        loadMenuCategories() {
            $.ajax({
                type: 'GET',
                url: CONFIG.URLS.CATEGORIES,
                success: (response) => {
                    if (!response || !response.success) {
                        toastr.error('Error loading categories');
                        return;
                    }

                    const categories = response.categories || [];
                    let html = '<option value="">Select Category</option>';
                    
                    if (Array.isArray(categories)) {
                        categories.forEach((category) => {
                            html += `<option value="${category.id}">${category.name}</option>`;
                        });
                    }
                    
                    $('#menuCategory').html(html);
                },
                error: (xhr) => {
                    toastr.error('Error loading categories');
                }
            });
        }

        loadMenuSubcategories(categoryId) {
            if (!categoryId) return;
            
            const url = CONFIG.URLS.SUBCATEGORIES.replace(':id', categoryId);
            
            $.ajax({
                type: 'GET',
                url: url,
                success: (response) => {
                    if (response.success) {
                        let html = '<option value="">Select Subcategory</option>';
                        response.subcategories.forEach((subcategory) => {
                            html += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                        });
                        $('#menuSubcategory').html(html);
                    }
                },
                error: (xhr) => {
                    toastr.error('Error loading subcategories');
                }
            });
        }

        submitSubcategory() {
            const formData = $('#addSubcategoryForm').serialize();
            const categoryId = $('#parentCategoryId').val();
            
            $.ajax({
                type: 'POST',
                url: `/menu/subcategories`,
                data: formData,
                success: (response) => {
                    if (response.success) {
                        localStorage.setItem('activeTab', 'categories');
                        window.location.reload();
                    }
                },
                error: (xhr) => {
                    toastr.error(xhr.responseJSON?.message || 'Error adding subcategory');
                }
            });
        }

        
        populateForm(data) {
            $('#edit_menu_id').val(data.id);
            $('#edit_menu_name').val(data.name);
            $('#edit_menu_price').val(data.price);
            $('#edit_menu_description').val(data.description);
            $(`#edit_status_${data.status}`).prop('checked', true);
            if (data.category_id) {
                $('#edit_menu_category').val(data.category_id).trigger('change');
                if (data.subcategory_id) {
                    setTimeout(() => {
                        $('#edit_menu_subcategory').val(data.subcategory_id);
                    }, 300);
                }
            }
            if (data.image_path) {
                $('#currentImagePreview').html(`
                    <div class="position-relative d-inline-block">
                        <img src="${data.image_path}" class="img-thumbnail mt-2">
                        <div class="small text-muted mt-1">Current image</div>
                    </div>
                `);
            }
        }

        loadSubcategoriesAndSelect(categoryId, selectedSubcategoryId) {
            const subcategorySelect = $('#edit_menu_subcategory');
            subcategorySelect.html('<option value="">Select Subcategory</option>');
            
            $.get(`/menu/categories/${categoryId}/subcategories`, (response) => {                if (response.success && response.subcategories) {
                    response.subcategories.forEach((subcategory) => {
                        subcategorySelect.append(`
                            <option value="${subcategory.id}" ${subcategory.id == selectedSubcategoryId ? 'selected' : ''}>
                                ${subcategory.name}
                            </option>
                        `);
                    });
                }
            });
        }

        initializeArchivedTable() {
            const table = $('#archivedTable').DataTable({
                processing: true,
                serverSide: false,
                destroy: true,
                data: [],
                columns: [
                    { 
                        data: null,
                        name: 'menu_details',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex align-items-center">
                                    <img src="${row.image_path ? '/storage/' + row.image_path : '/images/menu.png'}" 
                                         class="border p-2 me-2" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">${row.name || ''}</div>
                                        <small class="text-muted">${row.menu_code || ''}</small>
                                        <div class="small text-muted">${row.description || ''}</div>
                                    </div>
                                </div>`;
                        }
                    },
                    { 
                        data: 'category.name',
                        defaultContent: 'Uncategorized'
                    },
                    { 
                        data: 'price',
                        render: function(data) {
                            return `₱${parseFloat(data || 0).toFixed(2)}`;
                        }
                    },
                    { 
                        data: 'archived_at',
                        render: function(data) {
                            return data ? moment(data).format('MMM D, YYYY h:mm A') : '';
                        }
                    },
                    {
                        data: 'id',
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-success restore-item" 
                                        data-id="${data}" 
                                        title="Restore Item">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>`;
                        }
                    }
                ],
                order: [[3, 'desc']],
                language: {
                    emptyTable: "No archived items found"
                }
            });

            return table;
        }

        fetchArchivedData() {
            $.ajax({
                url: '/menu/archived',
                type: 'GET',
                success: (response) => {
                    if (response.success) {
                        const table = $('#archivedTable').DataTable();
                        table.clear();
                        if (response.items && response.items.length > 0) {
                            table.rows.add(response.items);
                        }
                        table.draw();
                        } else {
                        toastr.error('Error loading archived items');
                    }
                },
                error: (xhr) => {
                    toastr.error('Failed to load archived items');
                }
            });
        }

        restoreArchivedItem(id) {
            if (confirm('Are you sure you want to restore this item?')) {
                $.ajax({
                    url: `/menu/${id}/restore`,
                    type: 'POST',
                    success: (response) => {
                        if (response.success) {
                            toastr.success('Item restored successfully');
                            this.fetchArchivedData();
                            if (window.LaravelDataTables['menu-table']) {
                                window.LaravelDataTables['menu-table'].ajax.reload();
                            }
                        } else {
                            toastr.error(response.message || 'Error restoring item');
                        }
                    },
                    error: () => {
                        toastr.error('Error restoring item');
                    }
                });
            }
        }
    }

    new MenuManager();
});
</script>