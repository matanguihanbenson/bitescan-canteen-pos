<div class="modal fade" id="productsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Products</h5>
                <div class="ms-auto me-2" style="width: 300px;">
                    <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="sticky-tabs">
                    <ul class="nav nav-tabs mb-0" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab"
                                data-bs-target="#all-products" type="button" role="tab">All</button>
                        </li>
                    </ul>
                </div>

                <!-- Scrollable content area -->
                <div class="scrollable-content">
                    <div class="tab-content p-3" id="productTabContent">
                        <div class="tab-pane fade show active" id="all-products" role="tabpanel">
                            <div class="row g-3" id="productsContainer">
                                <!-- Products will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.currentStockLevels = new Map();

    $(document).ready(function() {
        let allProducts = [];
        let currentCategoryId = null;

        window.updateStockLevel = function(productCode, currentStock, baseQuantity) {
            window.currentStockLevels.set(productCode, {
                current_stock: currentStock,
                base_quantity: baseQuantity
            });
        };

        window.getStockLevel = function(productCode) {
            return window.currentStockLevels.get(productCode);
        };

        function loadCategories() {
            $.get('/categories/get', function(response) {
                const tabList = $('#productTabs');
                tabList.find('li:not(:first)').remove();

                $('#productTabContent').find('.tab-pane:not(:first)').remove();

                if (Array.isArray(response)) {
                    response.forEach(category => {
                        const tabId = `category-${category.id}`;
                        
                        tabList.append(`
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="${tabId}-tab" 
                                        data-bs-toggle="tab" data-bs-target="#${tabId}" 
                                        type="button" role="tab">
                                    ${category.name}
                                </button>
                            </li>
                        `);

                        $('#productTabContent').append(`
                            <div class="tab-pane fade" id="${tabId}" role="tabpanel">
                                <div class="row g-3" id="${tabId}-container"></div>
                            </div>
                        `);
                    });
                } else {
                    }
            }).fail(function(error) {
                });
        }

        function showLoading(container) {
            container.html(`
                <div class="d-flex justify-content-center align-items-center w-100" style="min-height: 400px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mb-0">Loading products...</p>
                    </div>
                </div>
            `);
        }

        function showEmptyState(container, title, message) {
            container.html(`
                <div class="d-flex justify-content-center align-items-center w-100" style="min-height: 400px;">
                    <div class="text-center">
                        <img src="/images/no-image.png" alt="No products" class="empty-products-img mb-3" style="width: 120px; height: 120px; opacity: 0.5;">
                        <h5 class="text-muted mb-2">${title}</h5>
                        <p class="text-muted small">${message}</p>
                    </div>
                </div>
            `);
        }

        function loadProducts(categoryId = null) {
            currentCategoryId = categoryId;
            let url = '/inventory/getproducts';
            if (categoryId) {
                url += `?category_id=${categoryId}`;
            }

            const container = categoryId ? 
                $(`#category-${categoryId}-container`) : 
                $('#productsContainer');
            showLoading(container);

            $.get(url, function(products) {
                if (!Array.isArray(products) || products.length === 0) {
                    showEmptyState(container, 'No Products Available', 'There are no active products in this category.');
                    return;
                }

                const availableProducts = products.filter(product => 
                    product.status === 'active' && 
                    (product.current_stock > 0 || product.base_quantity > 0)
                );

                if (availableProducts.length === 0) {
                    showEmptyState(container, 'No Products Available', 'All products in this category are either out of stock or inactive.');
                    return;
                }

                // Use stock levels already provided by getProducts to avoid N+1 requests
                availableProducts.forEach(p => {
                    window.updateStockLevel(p.product_code, p.current_stock, p.base_quantity);
                });
                allProducts = availableProducts;
                displayProducts(availableProducts);
            }).fail(function(error) {
                showEmptyState(container, 'Error Loading Products', 'Failed to load products. Please try again.');
            });
        }

        function displayProducts(products, searchTerm = '') {
            const activeTabId = $('.nav-link.active').attr('id');
            let container;

            if (activeTabId === 'all-tab') {
                container = $('#productsContainer');
            } else {
                const categoryId = activeTabId.replace('-tab', '');
                container = $(`#${categoryId}-container`);
            }

            let filteredProducts = products;
            if (searchTerm) {
                searchTerm = searchTerm.toLowerCase();
                filteredProducts = products.filter(product => {
                    const productName = (product.product_name || '').toString().toLowerCase();
                    const productCode = (product.product_code || '').toString().toLowerCase();

                    return productName.includes(searchTerm) || productCode.includes(searchTerm);
                });
            }

            container.empty();

            if (filteredProducts.length === 0) {
                showEmptyState(container, 'No Products Found', 'There are no products available at the moment.');
                return;
            }

            filteredProducts.forEach(product => {
                const groupUnits = ['pack', 'box', 'carton', 'bag', 'bundle', 'sack', 'dozen', 'half-dozen', 'pair', 'bottle', 'can', 'case', 'set'];
                const price = parseFloat(product.selling_price || 0);
                const isGroupUnit = groupUnits.includes(product.unit.toLowerCase()) && product.units_per_group > 1;
                const groupPrice = price * product.units_per_group;

                let stockLevels = window.currentStockLevels.get(product.product_code) || {
                    current_stock: product.current_stock,
                    base_quantity: product.base_quantity
                };
                
                if (typeof orderItems !== 'undefined' && orderItems.length > 0) {
                    const orderItemsOfThisProduct = orderItems.filter(item => item.code === product.product_code);
                    let totalGroupsInOrder = 0;
                    let totalPiecesInOrder = 0;
                    
                    orderItemsOfThisProduct.forEach(item => {
                        if (item.type === 'group') {
                            totalGroupsInOrder += item.quantity;
                            totalPiecesInOrder += item.quantity * (item.units_per_group || 1);
                        } else if (item.type === 'piece') {
                            totalPiecesInOrder += item.quantity;
                        }
                    });
                    
                    const remainingGroups = Math.max(0, product.current_stock - totalGroupsInOrder);
                    
                    const remainingPieces = Math.max(0, product.base_quantity - totalPiecesInOrder);
                    
                    stockLevels = {
                        current_stock: remainingGroups,
                        base_quantity: remainingPieces
                    };
                    
                    window.updateStockLevel(product.product_code, stockLevels.current_stock, stockLevels.base_quantity);
                }

                const isCompletelyOutOfStock = isGroupUnit ? 
                    (stockLevels.current_stock <= 0 && stockLevels.base_quantity <= 0) :
                    (stockLevels.current_stock <= 0);

                if (!isCompletelyOutOfStock) {

                    container.append(`
                    <div class="col-md-4">
                        <div class="card h-100" id="card-${product.product_code}" 
                             data-current-stock="${stockLevels.current_stock}"
                             data-base-quantity="${stockLevels.base_quantity}"
                             data-original-current-stock="${product.current_stock}"
                             data-original-base-quantity="${product.base_quantity}"
                             data-units-per-group="${product.units_per_group}">
                            <img src="${product.image || '/images/no-image.png'}" class="card-img-top product-img" alt="${product.product_name}">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title text-truncate" title="${product.product_name}">${product.product_name}</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted text-truncate"><i class="bi bi-upc-scan"></i> ${product.product_code}</small>
                                    <small class="text-muted ms-2"><i class="bi bi-box-seam"></i> <span class="current-stock">${stockLevels.current_stock}</span> ${product.unit}</small>
                                </div>
                                ${isGroupUnit ? `
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong class="text-primary"><i class="bi bi-tag"></i> ₱${price.toFixed(2)}/pc</strong>
                                        <small class="text-muted"><i class="bi bi-box"></i> <span class="base-quantity">${stockLevels.base_quantity}</span> pcs</small>
                                    </div>
                                    <div class="mt-auto w-100">
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" class="btn btn-primary btn-sm ${stockLevels.current_stock <= 0 ? 'disabled' : ''}" 
                                                    onclick="window.addToOrder({
                                                        product_code: '${product.product_code}',
                                                        product_name: '${product.product_name}',
                                                        unit_price: ${groupPrice},
                                                        units_per_group: ${product.units_per_group},
                                                        unit: '${product.unit}',
                                                        type: 'group'
                                                    })"
                                                    ${stockLevels.current_stock <= 0 ? 'disabled' : ''}>
                                                <i class="bi bi-box-seam"></i> ${product.unit}<br>₱${groupPrice.toFixed(2)}
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm ${stockLevels.base_quantity <= 0 ? 'disabled' : ''}" 
                                                    onclick="window.addToOrder({
                                                        product_code: '${product.product_code}',
                                                        product_name: '${product.product_name}',
                                                        unit_price: ${price},
                                                        units_per_group: ${product.units_per_group},
                                                        type: 'piece'
                                                    })"
                                                    ${stockLevels.base_quantity <= 0 ? 'disabled' : ''}>
                                                <i class="bi bi-grid"></i> Per Piece<br>₱${price.toFixed(2)}
                                            </button>
                                        </div>
                                    </div>
                                ` : `
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong class="text-primary"><i class="bi bi-tag"></i> ₱${price.toFixed(2)}/${product.unit}</strong>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100 mt-auto ${stockLevels.current_stock <= 0 ? 'disabled' : ''}" 
                                            onclick="window.addToOrder({
                                                product_code: '${product.product_code}',
                                                product_name: '${product.product_name}',
                                                unit_price: ${price},
                                                units_per_group: 1,
                                                type: 'piece'
                                            })"
                                            ${stockLevels.current_stock <= 0 ? 'disabled' : ''}>
                                        <i class="bi bi-plus-circle"></i> Add to Order
                                    </button>
                                `}
                            </div>
                        </div>
                    </div>
                    `);
                }
            });
        }

        let searchTimeout;
        $('#productSearch').on('input', function() {
            const searchTerm = $(this).val().trim();
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                displayProducts(allProducts, searchTerm);
            }, 300);
        });

        $('#productsModal').on('show.bs.modal', function() {
            loadCategories();
            loadProducts();
        });

        $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"]', function(e) {
            const targetId = $(e.target).attr('id');
            $('#productSearch').val('');

            if (targetId === 'all-tab') {
                loadProducts();
            } else {
                const categoryId = targetId.replace('-tab', '').replace('category-', '');
                loadProducts(categoryId);
            }
        });


    });
</script>

<style>
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        border: 1px solid transparent;
        border-bottom: none;
        margin-bottom: -1px;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
    }

    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .modal-body {
        max-height: 80vh;
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .sticky-tabs {
        position: sticky;
        top: 0;
        background: white;
        z-index: 1020;
        border-bottom: 1px solid #dee2e6;
    }

    .scrollable-content {
        flex: 1;
        overflow-y: auto;
        min-height: 400px; /* Increased minimum height */
    }

    .product-img {
        height: 120px;
        object-fit: cover;
        background-color: #f8f9fa;
    }

    .card {
        border: 1px solid rgba(0, 0, 0, .125);
        box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
        transition: box-shadow 0.3s ease-in-out;
        max-height: 100%;
    }

    .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
    }

    .card-body {
        background: #fff;
    }

    .empty-products-img {
        width: 120px;
        height: 120px;
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    #productSearch {
        padding-left: 15px;
        padding-right: 15px;
        border: 1px solid #dee2e6;
    }

    #productSearch:focus {
        box-shadow: none;
        border-color: #80bdff;
    }

    .dropdown-menu {
        background-color: white;
        border: 1px solid rgba(0, 0, 0, .15);
    }

    .dropdown-item {
        color: #212529;
        padding: 0.5rem 1rem;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-center {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        inset: auto auto 0px 0px;
        margin: 0;
        transform: translate(0px, -45px);
        min-width: 100%;
        z-index: 1000;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-menu {
        margin-top: 0;
        border-radius: 0 0 4px 4px;
        border-color: #0d6efd;
        padding: 0;
    }

    .list-group-item {
        cursor: pointer;
        border: none;
        padding: 0.75rem 1rem;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .btn-group .btn {
        flex: 1;
        font-size: 0.75rem;
        padding: 0.35rem 0.5rem;
        white-space: normal;
        height: auto;
        min-height: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 0.25rem;
    }

    .btn-group .btn-outline-primary:hover {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    /* Add styles for centered content */
    .d-flex.justify-content-center.align-items-center {
        min-height: 400px;
    }
</style>