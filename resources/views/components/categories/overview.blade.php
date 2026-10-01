<!-- Main Content Area -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient-primary text-white">
        <div class="d-flex align-items-center">
            <i class="bi bi-grid-3x3-gap-fill me-2 fs-5 text-white"></i>
            <div>
                <h5 class="card-title mb-0 fw-semibold text-white">Category Management</h5>
                <small class="text-white opacity-90">Organize and manage product categories and subcategories</small>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-tabs border-0" role="tablist" id="categoryTabs">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 py-3" id="overview-tab" data-bs-toggle="tab"
                    data-bs-target="#overview" type="button" role="tab">
                    <i class="bi bi-grid me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-3" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories"
                    type="button" role="tab">
                    <i class="bi bi-folder me-2"></i>Categories
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-3" id="subcategories-tab" data-bs-toggle="tab"
                    data-bs-target="#subcategories" type="button" role="tab">
                    <i class="bi bi-folder2 me-2"></i>Subcategories
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview">
                <!-- Search and Filters -->
                <div class="row g-3 mb-4 d-flex align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" style="height:50px;" id="overviewSearch"
                                class="form-control border-start-0 ps-0" placeholder="Search categories...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="overviewSort">
                            <option value="name">Sort by Name</option>
                            <option value="subcategories">Sort by Subcategories</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="overviewStatus">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="deactivated">Deactivated</option>
                        </select>
                    </div>
                </div>

                <!-- Categories Grid -->
                <div class="row g-3" id="categoriesContainer">
                    @forelse ($categories as $category)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <!-- Card Header -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1 me-2">
                                        <h6 class="card-title fw-bold mb-1 text-truncate" style="max-width: 150px;"
                                            title="{{ $category->name }}">
                                            {{ $category->name }}
                                        </h6>
                                        <span
                                            class="badge {{ $category->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} small">
                                            <i
                                                class="bi {{ $category->status === 'active' ? 'bi-check-circle' : 'bi-dash-circle' }} me-1"></i>
                                            {{ $category->status }}
                                        </span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-light btn-sm" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="editCategory({{ $category->id }})">
                                                    <i class="bi bi-pencil me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item text-danger" href="#"
                                                    onclick="deleteCategory({{ $category->id }})">
                                                    <i class="bi bi-trash me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Card Description -->
                                <p class="text-muted small mb-2"
                                    style="font-size: 0.8rem; height: 2.4em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    {{ $category->description ?? 'No description available' }}
                                </p>

                                <!-- Category Stats -->
                                <div class="d-flex gap-1 mb-2">
                                    <span class="badge bg-primary-subtle text-primary small">
                                        <i class="bi bi-folder me-1"></i>{{ $category->subcategories->count() }}
                                        subcategories
                                    </span>
                                </div>

                                <!-- Subcategories List -->
                                <div class="subcategories-list" style="max-height: 120px; overflow-y: auto;">
                                    @if ($category->subcategories->count() > 0)
                                    <div class="list-group list-group-flush small">
                                        @foreach ($category->subcategories->take(3) as $subcategory)
                                        <div class="list-group-item px-0 py-1 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-truncate me-2" style="min-width: 0;">
                                                    <span
                                                        class="fw-medium d-block text-truncate">{{ $subcategory->name }}</span>
                                                    <div class="text-muted small">
                                                        <i
                                                            class="bi bi-box me-1"></i>{{ $subcategory->products_count ?? 0 }}
                                                        products
                                                    </div>
                                                </div>
                                                <div class="subcategory-actions flex-shrink-0">
                                                    <button class="btn btn-icon btn-light btn-sm"
                                                        onclick="editSubcategory({{ $subcategory->id }})" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @if($category->subcategories->count() > 3)
                                        <div class="list-group-item px-0 py-1 text-center bg-light">
                                            <span class="badge bg-secondary-subtle text-secondary small">
                                                +{{ $category->subcategories->count() - 3 }} more
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <div class="text-center py-2">
                                        <div class="text-muted small">
                                            <i class="bi bi-folder2-open h6 mb-1 d-block"></i>
                                            <p class="mb-0 small">No subcategories</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-transparent border-0 p-2">
                                <button class="btn btn-sm btn-outline-primary w-100" style="font-size: 0.75rem;"
                                    onclick="showAddSubcategoryModal({{ $category->id }})">
                                    <i class="bi bi-plus-circle me-1"></i>Add Subcategory
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div id="overviewEmptyState" class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-folder2-open display-1 text-secondary opacity-25"></i>
                            </div>
                            <h5>No Categories Found</h5>
                            <p class="text-muted mb-4">Start by adding your first category or adjust your search
                                filters.</p>
                            <button class="btn btn-primary d-inline-flex align-items-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                Add Category
                            </button>
                        </div>
                    </div>
                    @endforelse
                </div>

                <style>
                    .subcategories-list {
                        scrollbar-width: thin;
                        scrollbar-color: rgba(0, 0, 0, .2) transparent;
                    }

                    .subcategories-list::-webkit-scrollbar {
                        width: 3px;
                    }

                    .subcategories-list::-webkit-scrollbar-track {
                        background: transparent;
                    }

                    .subcategories-list::-webkit-scrollbar-thumb {
                        background-color: rgba(0, 0, 0, .2);
                        border-radius: 4px;
                    }

                    .card {
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                    }

                    .btn-icon.btn-sm {
                        width: 24px;
                        height: 24px;
                        padding: 0;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .list-group-item {
                        transition: background-color 0.2s ease;
                    }

                    .list-group-item:hover {
                        background-color: rgba(0, 0, 0, .01);
                    }

                    .card-title {
                        font-size: 0.9rem;
                    }

                    .badge {
                        font-size: 0.7rem;
                        padding: 0.25em 0.5em;
                    }

                    /* Fix text overflow issues */
                    .text-truncate {
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                    }
                </style>
            </div>

            <!-- Categories Tab -->
            <div class="tab-pane fade" id="categories">
                <div class="table-responsive">
                    <div class="row g-3 mb-4 d-flex align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" style="height:50px;" class="form-control border-start-0 ps-0"
                                    id="categoryTableSearch" placeholder="Search categories...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryTableSort">
                                <option value="name">Sort by Name</option>
                                <option value="subcategories">Sort by Subcategories</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryTableStatus">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <table class="table table-hover align-middle w-100" id="categoriesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-bold text-dark px-4 py-3">Name</th>
                                <th class="border-0 fw-bold text-dark py-3">Description</th>
                                <th class="border-0 fw-bold text-dark py-3">Subcategories</th>
                                <th class="border-0 fw-bold text-dark py-3">Status</th>
                                <th class="border-0 fw-bold text-dark text-end py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $category->name }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $category->description }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary">
                                        <i class="bi bi-folder me-1"></i>{{ $category->subcategories->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $category->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        <i
                                            class="bi {{ $category->status === 'active' ? 'bi-check-circle' : 'bi-dash-circle' }} me-1"></i>{{ $category->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-light btn-sm"
                                            onclick="editCategory({{ $category->id }})">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>
                                        <button class="btn btn-light btn-sm text-danger"
                                            onclick="deleteCategory({{ $category->id }})">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div id="categoriesEmptyState" class="empty-state">
                                        <div class="text-center py-5">
                                            <div class="mb-4">
                                                <i class="bi bi-folder display-1 text-secondary opacity-25"></i>
                                            </div>
                                            <h5>No Categories Available</h5>
                                            <p class="text-muted mb-4">Your categories list is empty. Add some
                                                categories to get started.</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addCategoryModal">
                                                <i class="bi bi-plus-lg me-2"></i>Add Category
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Subcategories Tab -->
            <div class="tab-pane fade" id="subcategories">
                <div class="table-responsive">
                    <div class="row g-3 mb-4 d-flex align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" style="height:50px;" class="form-control border-start-0 ps-0"
                                    id="subcategoryTableSearch" placeholder="Search subcategories...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="subcategoryTableSort">
                                <option value="name">Sort by Name</option>
                                <option value="parent">Sort by Parent</option>
                                <option value="products">Sort by Products</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="subcategoryTableStatus">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <table class="table table-hover align-middle w-100" id="subcategoriesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-bold text-dark px-4 py-3">Name</th>
                                <th class="border-0 fw-bold text-dark py-3">Parent Category</th>
                                <th class="border-0 fw-bold text-dark py-3">Products</th>
                                <th class="border-0 fw-bold text-dark py-3">Status</th>
                                <th class="border-0 fw-bold text-dark text-end py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subcategoriesTableBody">
                            @php
                            $allSubcategories = collect();
                            foreach($categories as $category) {
                            foreach($category->subcategories as $subcategory) {
                            $subcategory->parent_category_name = $category->name;
                            $allSubcategories->push($subcategory);
                            }
                            }
                            @endphp

                            @forelse($allSubcategories as $subcategory)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-medium">{{ $subcategory->name }}</div>
                                    @if($subcategory->description)
                                    <div class="text-muted small">{{ $subcategory->description }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $subcategory->parent_category_name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-box me-1"></i>{{ $subcategory->products_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $subcategory->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        <i
                                            class="bi {{ $subcategory->status === 'active' ? 'bi-check-circle' : 'bi-dash-circle' }} me-1"></i>
                                        {{ $subcategory->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-light btn-sm"
                                            onclick="editSubcategory({{ $subcategory->id }})">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>
                                        <button class="btn btn-light btn-sm text-danger"
                                            onclick="deleteSubcategory({{ $subcategory->id }})">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-folder2-open display-1 opacity-25 d-block mb-3"></i>
                                        <h5>No Subcategories Available</h5>
                                        <p class="mb-4">Your subcategories list is empty. Add some subcategories to get
                                            started.</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addSubcategoryModal">
                                            <i class="bi bi-plus-lg me-2"></i>Add Subcategory
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable for subcategories when tab is shown
        $('#subcategories-tab').on('shown.bs.tab', function() {
            if (!$.fn.DataTable.isDataTable('#subcategoriesTable')) {
                $('#subcategoriesTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: [4]
                        } // Actions column
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search subcategories...",
                        emptyTable: "No subcategories found"
                    }
                });
            }
        });

        // Search functionality for subcategories
        $('#subcategoryTableSearch').on('keyup', function() {
            if ($.fn.DataTable.isDataTable('#subcategoriesTable')) {
                $('#subcategoriesTable').DataTable().search(this.value).draw();
            }
        });
    });

    // Functions for subcategory actions
    function editSubcategory(id) {
    }

    function deleteSubcategory(id) {
        if (confirm('Are you sure you want to delete this subcategory?')) {
            }
    }
</script>