<div class="row g-4 mb-4">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Products</h6>
                        <h3 class="mb-1 text-dark fw-semibold" id="totalProducts">
                            {{ $analytics['total_products'] ?? 0 }}
                        </h3>
                        <p class="text-muted mb-0 small">In inventory</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div
                            class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-box text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Low Stock Items</h6>
                        <h3 class="mb-1 text-dark fw-semibold" id="lowStockCount">
                            {{ $analytics['low_stock_items'] ?? 0 }}
                        </h3>
                        <p class="text-muted mb-0 small">Need restocking</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div
                            class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-exclamation-triangle text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Out of Stock -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Out of Stock</h6>
                        <h3 class="mb-1 text-dark fw-semibold" id="outOfStockCount">
                            {{ $analytics['out_of_stock'] ?? 0 }}
                        </h3>
                        <p class="text-muted mb-0 small">Require immediate attention</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div
                            class="avatar-sm bg-danger bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-x-circle text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Value -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Inventory Value</h6>
                        <h3 class="mb-1 text-dark fw-semibold" id="totalInventoryValue">
                            ₱{{ number_format($analytics['total_value'] ?? 0, 2) }}</h3>
                        <p class="text-muted mb-0 small">Current stock value <span id="valueChange" class="ms-2"></span></p>
                    </div>
                    <div class="flex-shrink-0">
                        <div
                            class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-currency-dollar text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Inventory status banner -->
<div class="row mb-2">
    <div class="col-12">
        <div id="inventory-empty-banner" class="alert alert-warning py-2 d-none">
            <small class="mb-0">No inventory items found. Please add products to see analytics.</small>
        </div>
    </div>
</div>
