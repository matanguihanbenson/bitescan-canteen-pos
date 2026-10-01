<div class="modal fade" id="menuModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Menu</h5>
                <div class="ms-auto me-2" style="width: 300px;">
                    <input type="text" class="form-control" id="menuSearch" placeholder="Search menu items...">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="sticky-tabs">
                    <ul class="nav nav-tabs mb-0" id="menuTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-menu-tab" data-bs-toggle="tab" 
                                    data-bs-target="#all-menu-items" type="button" role="tab">All</button>
                        </li>
                    </ul>
                </div>
                
                <!-- Scrollable content area -->
                <div class="scrollable-content">
                    <div class="tab-content p-3" id="menuTabContent">
                        <div class="tab-pane fade show active" id="all-menu-items" role="tabpanel">
                            <div class="row g-3" id="menuContainer">
                                <!-- Menu items will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let allMenuItems = [];
    let currentCategoryId = null;

    function loadMenuCategories() {
        $.get('/menu/categories', function(response) {
            if (response.success && response.categories) {
                const tabList = $('#menuTabs');
                tabList.find('li:not(:first)').remove();
                $('#menuTabContent').find('.tab-pane:not(:first)').remove();
                
                response.categories.forEach(category => {
                    if (category.status === 'active') {
                        const tabId = `menu-category-${category.id}`;
                        tabList.append(`
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="${tabId}-tab" 
                                        data-bs-toggle="tab" data-bs-target="#${tabId}" 
                                        type="button" role="tab">${category.name}</button>
                            </li>
                        `);
                        
                        $('#menuTabContent').append(`
                            <div class="tab-pane fade" id="${tabId}" role="tabpanel">
                                <div class="row g-3" id="${tabId}-container"></div>
                            </div>
                        `);
                    }
                });
                
                loadMenuItems();
            }
        });
    }

    function loadMenuItems(categoryId = null) {
        if (allMenuItems.length === 0 || categoryId === null) {
            $.get('/menu/groups/displayed', function(response) {
                if (response.success) {
                    allMenuItems = response.items;
                    displayMenuItemsByCategory(categoryId);
                }
            });
        } else {
            displayMenuItemsByCategory(categoryId);
        }
    }

    function displayMenuItemsByCategory(categoryId = null) {
        $('#menuContainer').empty();
        $('[id^=menu-category-]').each(function() {
            $(this).find('.row').empty();
        });

        if (categoryId === null) {
            displayMenuItems(allMenuItems, null);
            
            const itemsByCategory = {};
            allMenuItems.forEach(item => {
                if (!itemsByCategory[item.category_id]) {
                    itemsByCategory[item.category_id] = [];
                }
                itemsByCategory[item.category_id].push(item);
            });

            Object.entries(itemsByCategory).forEach(([catId, items]) => {
                displayMenuItems(items, catId);
            });
        } else {
            const categoryItems = allMenuItems.filter(item => 
                parseInt(item.category_id) === parseInt(categoryId)
            );
            displayMenuItems(categoryItems, categoryId);
        }
    }

    function displayMenuItems(items, categoryId = null) {
        const container = categoryId ? 
            $(`#menu-category-${categoryId}-container`) : 
            $('#menuContainer');
        
        if (!items || items.length === 0) {
            container.html(`
                <div class="col-12 text-center py-5">
                    <img src="/images/no-menu.png" alt="No menu items" class="empty-menu-img mb-3">
                    <h5 class="text-muted mb-2">No Menu Items Found</h5>
                    <p class="text-muted small">No menu items are available in this category.</p>
                </div>
            `);
            return;
        }

        items.forEach(item => {
            container.append(`
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <img src="${item.image || '/images/no-image.png'}" 
                             class="card-img-top menu-img" 
                             alt="${item.name}"
                             onerror="this.src='/images/no-image.png'">
                        <div class="card-body d-flex flex-column">
                            <div class="menu-code"><i class="bi bi-upc-scan"></i> ${item.code}</div>
                            <h6 class="card-title text-truncate" title="${item.name}">${item.name}</h6>
                            <p class="card-text small text-muted">${item.description || 'No description'}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <strong class="text-primary">₱${parseFloat(item.price).toFixed(2)}</strong>
                                <button class="btn btn-primary btn-sm" 
                                        onclick="addToOrder({
                                            product_code: '${item.code}',
                                            product_name: '${item.name}',
                                            unit_price: ${parseFloat(item.price)},
                                            price: ${parseFloat(item.price)},
                                            unit: 'piece',
                                            type: 'menu'
                                        })">
                                    <i class="bi bi-plus-circle me-1"></i>Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        });
    }

    let searchTimeout;
    $('#menuSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            const filteredItems = allMenuItems.filter(item => 
                item.name.toLowerCase().includes(searchTerm) ||
                item.code.toLowerCase().includes(searchTerm)
            );
            
            $('#menuContainer').empty();
            $('[id^=menu-category-]').each(function() {
                $(this).find('.row').empty();
            });
            
            const activeTab = $('.nav-link.active').attr('id');
            if (activeTab === 'all-menu-tab') {
                displayMenuItems(filteredItems, null);
            } else {
                const categoryId = activeTab.replace('menu-category-', '').replace('-tab', '');
                const categoryFilteredItems = filteredItems.filter(item => 
                    parseInt(item.category_id) === parseInt(categoryId)
                );
                displayMenuItems(categoryFilteredItems, categoryId);
            }
        }, 300);
    });

    $('#menuModal').on('show.bs.modal', function (e) {
        loadMenuCategories();
        loadMenuItems();
    });

    $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"]', function (e) {
        const targetId = $(e.target).attr('id');
        $('#menuSearch').val('');
        
        if (targetId === 'all-menu-tab') {
            displayMenuItemsByCategory(null);
        } else {
            const categoryId = targetId.replace('menu-category-', '').replace('-tab', '');
            displayMenuItemsByCategory(categoryId);
        }
    });
});
</script>

<style>
.modal-dialog {
    max-width: 800px;  /* Increased from default */
    width: 95%;        /* Responsive width */
    height: 90vh;      /* Changed from max-height to height */
    margin: 1rem auto; /* Reduced margin */
}

.modal-content {
    height: 100%;      /* Changed from 90vh */
    max-height: 90vh;  /* Added max-height */
    display: flex;
    flex-direction: column;
    overflow: hidden;  /* Added to prevent content overflow */
}

.modal-header {
    padding: 0.75rem 1rem; /* Reduced padding */
    flex-shrink: 0;       /* Prevent header from shrinking */
}

.sticky-tabs {
    position: sticky;
    top: 0;
    background: white;
    z-index: 1020;
    border-bottom: 1px solid #dee2e6;
    padding: 0.5rem 1rem 0;
    flex-shrink: 0;      /* Prevent tabs from shrinking */
}

.scrollable-content {
    flex: 1;
    overflow-y: auto;
    min-height: 0;     /* Changed from 200px */
    height: 100%;      /* Added height */
}

.tab-content {
    height: 100%;      /* Added height */
}

.nav-tabs {
    border-bottom: none;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-bottom: none;
    margin-bottom: -1px;
    padding: 0.5rem 1rem;
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-color: #dee2e6 #dee2e6 #fff;
}

.menu-img {
    height: 120px;
    object-fit: cover;
    background-color: #f8f9fa;
    border-top-left-radius: calc(0.375rem - 1px);
    border-top-right-radius: calc(0.375rem - 1px);
}

.card {
    border: 1px solid rgba(0, 0, 0, .125);
    box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
    transition: box-shadow 0.3s ease-in-out;
    margin-bottom: 0;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
}

.card-body {
    padding: 0.5rem;  /* Reduced from 0.75rem */
    background: #fff;
}

.card-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.125rem;  /* Reduced from 0.25rem */
    line-height: 1.1;  /* Reduced from 1.2 */
}

.card-text {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 0.25rem;  /* Reduced from 0.5rem */
    line-height: 1.2;  /* Reduced from 1.3 */
}

/* Add new style for menu code */
.menu-code {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 0.125rem;  /* Small margin */
    line-height: 1;
}

.btn-primary {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.price-tag {
    font-size: 0.875rem;
    font-weight: 600;
    color: #0d6efd;
}

#menuSearch {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
}

.empty-menu-img {
    width: 120px;
    height: 120px;
    opacity: 0.5;
}

/* Preserve the responsive breakpoints */
@media (max-width: 768px) {
    .col-md-4 {
        width: 50%;
    }
}

@media (max-width: 576px) {
    .col-md-4 {
        width: 100%;
    }
}
</style>
