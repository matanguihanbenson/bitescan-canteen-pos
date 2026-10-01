<div class="row g-4 mb-4">
    <!-- Total Categories -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Categories</h6>
                        <h3 class="mb-1 text-dark fw-semibold">{{ $totalCategories }}</h3>
                        <p class="text-muted mb-0 small">Across all types</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-folder2 text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Subcategories -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Subcategories</h6>
                        <h3 class="mb-1 text-dark fw-semibold">{{ $totalSubcategories }}</h3>
                        <p class="text-muted mb-0 small">Under categories</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-diagram-2 text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Products</h6>
                        <h3 class="mb-1 text-dark fw-semibold">{{ $totalProducts }}</h3>
                        <p class="text-muted mb-0 small">In inventory</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm bg-info bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-box text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Active Categories</h6>
                        <h3 class="mb-1 text-dark fw-semibold">{{ $categories->where('status', 'active')->count() }}</h3>
                        <p class="text-muted mb-0 small">Currently active</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-check-circle text-white fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>