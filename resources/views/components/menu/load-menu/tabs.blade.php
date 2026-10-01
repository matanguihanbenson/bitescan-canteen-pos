<!-- Tabs Navigation -->
<div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="menuTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                        Active Menu Groups
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab">
                        Archived Menu Groups
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="menuTabsContent">
        <!-- Active Menu Groups Tab -->
        <div class="tab-pane fade show active" id="active" role="tabpanel">
            <div class="row">
            <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search menu items..." id="searchMenu">
                                <button class="btn btn-outline-secondary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="sortBy">
                                <option value="">Sort by...</option>
                                <option value="name">Name</option>
                                <option value="category">Category</option>
                                <option value="price">Price</option>
                                <option value="popularity">Popularity</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Categories</h6>
                        </div>
                        <div class="card-body">
    <div class="list-group list-group-flush" id="categoryFilter">
        <a href="#" class="list-group-item list-group-item-action active" data-category="all">
            All Categories
        </a>
        <a href="#" class="list-group-item list-group-item-action" data-category="loaded">
            <i class="bi bi-check2-circle me-2 text-success"></i>Loaded Menu
        </a>
        @foreach($categories as $category)
            <a href="#" class="list-group-item list-group-item-action" data-category="{{ strtolower($category->name) }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
</div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="row" id="menuGroupsContainer">
                        @if(isset($menuGroups) && count($menuGroups) > 0)
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const menuGroupsData = @json($menuGroups);
                                    renderMenuGroups(menuGroupsData);
                                });
                            </script>
                        @else
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <img src="{{ asset('images/no-menu.png') }}" alt="No menu groups" class="mb-4" style="width: 50px; opacity: 0.5;">
                                    <h4 class="text-muted">No Menu Groups Found</h4>
                                    <p class="text-muted">Create your first menu group to get started</p>
                                    <button class="btn btn-primary" onclick="addMenuGroup()">
                                        <i class="bi bi-plus-circle me-2"></i>Create Menu Group
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Archived Menu Groups Tab -->
        <div class="tab-pane fade" id="archived" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" 
                           class="form-control" 
                           id="archivedSearchInput" 
                           placeholder="Search archived menu groups...">
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <select class="form-select form-select-sm w-auto" id="archiveSortOrder">
                            <option value="desc">Latest Archived First</option>
                            <option value="asc">Oldest Archived First</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div id="archivedGroupsContainer">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archive Menu Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="archiveGroupId">
                <div class="mb-3">
                    <label for="archiveReason" class="form-label">Archive Reason</label>
                    <textarea class="form-control" id="archiveReason" rows="3" 
                        placeholder="Please provide a reason for archiving this menu group"></textarea>
                </div>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action will archive the menu group and remove it from the active list.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmArchive()">
                    <i class="bi bi-archive me-2"></i>Archive
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore Menu Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="restoreGroupId">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    This will restore the menu group to the active list. Are you sure you want to proceed?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmRestore()">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Restore Menu Group
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Menu Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteGroupId">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. Are you sure you want to permanently delete this menu group?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="bi bi-trash me-2"></i>Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Menu Group Modal -->
<div class="modal fade" id="viewMenuGroupModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 900px; height: 90vh;">
        <div class="modal-content border-0 shadow-lg" style="height: 90vh;">
            <div class="modal-header bg-white border-bottom sticky-top shadow-sm" style="z-index: 1020;">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="modal-title mb-0">
                            <span id="viewGroupName" class="fw-bold fs-4" contenteditable="false"></span>
                            <input type="text" id="editGroupName" class="form-control d-none">
                        </h5>
                        <div class="mt-1">
                            <span class="badge bg-primary" id="viewGroupCategory"></span>
                            <small class="text-muted ms-2" id="lastUpdated"></small>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="bg-light border-bottom sticky-top" style="top: 71px; z-index: 1019;">
                <div class="container-fluid py-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <div class="h3 mb-0 fw-bold text-primary" id="viewTotalItems">0</div>
                                    <small class="text-muted fw-medium">Total Items</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <div class="h3 mb-0 fw-bold text-success" id="viewActiveItems">0</div>
                                    <small class="text-muted fw-medium">Active Items</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <div class="h3 mb-0 fw-bold text-danger" id="viewInactiveItems">0</div>
                                    <small class="text-muted fw-medium">Inactive Items</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border-bottom sticky-top" style="top: 190px; z-index: 1018;">
                <div class="container-fluid py-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" 
                               id="viewItemSearch" placeholder="Search items in this group...">
                        <button class="btn btn-primary d-none" id="addItemsBtn" onclick="openAddItemsModal()">
                            <i class="bi bi-plus-lg me-1"></i>Add Items
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-body p-0" style="overflow-y: auto;">
                <div class="container-fluid py-3">
                    <div class="row g-3" id="viewItemsGrid"></div>
                </div>
            </div>

            <div class="modal-footer border-top bg-white">
                <button type="button" class="btn btn-link text-decoration-none" data-bs-dismiss="modal">Close</button>
                <div class="edit-buttons">
                    <button type="button" class="btn btn-primary" id="toggleEditBtn" onclick="toggleEditMode()">
                        <i class="bi bi-pencil me-1"></i>Edit Items
                    </button>
                    <button type="button" class="btn btn-success d-none" id="saveChangesBtn" onclick="saveMenuChanges()">
                        <i class="bi bi-check2 me-1"></i>Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary d-none" id="cancelEditBtn" onclick="cancelEditMode()">
                        <i class="bi bi-x me-1"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Items Modal -->
<div class="modal fade" id="addItemsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Items to Menu Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="addItemSearch" placeholder="Search items...">
                </div>
                <div class="list-group" id="addItemsList">
                    <!-- Items will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addSelectedItems()">
                    <i class="bi bi-plus-lg me-1"></i>Add Selected Items
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-backdrop.show {
    opacity: 0.7;
}

#viewMenuGroupModal .modal-content {
    border-radius: 8px;
}

#viewMenuGroupModal .card {
    transition: transform 0.2s;
}


#viewMenuGroupModal .form-control:focus {
    box-shadow: none;
    border-color: #dee2e6;
}

#viewMenuGroupModal .badge {
    padding: 0.5em 0.8em;
    font-weight: 500;
}

#viewMenuGroupModal .modal-body::-webkit-scrollbar {
    width: 8px;
}

#viewMenuGroupModal .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#viewMenuGroupModal .modal-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}

#viewMenuGroupModal .modal-body::-webkit-scrollbar-thumb:hover {
    background: #bbb;
}
</style>