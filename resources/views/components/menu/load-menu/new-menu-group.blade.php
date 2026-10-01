<div class="modal fade" id="newMenuGroupModal" tabindex="-1" aria-labelledby="newMenuGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="newMenuGroupModalLabel">
                    <i class="bi bi-plus-circle-fill me-2 text-primary"></i>Create New Menu Group
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="newMenuGroupForm" onsubmit="return false;" class="needs-validation" novalidate>
                    <!-- Basic Information Section -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="groupName" class="form-label">Group Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="groupName" required>
                                    <div class="invalid-feedback">Please enter a group name.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="groupCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="groupCategory" required>
                                        <option value="">Select category...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a category.</div>
                                </div>
                                <div class="col-12">
                                    <label for="groupDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="groupDescription" rows="2" placeholder="Enter a brief description..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items Section -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <button type="button" class="btn btn-primary w-100 mb-3" onclick="openMenuItemsModal()">
                                <i class="bi bi-plus-lg me-1"></i>Select Menu Items
                            </button>

                            <div class="selected-items-container">
                                <div class="border rounded p-2 bg-light" style="min-height: 100px;">
                                    <ul class="list-group" id="selectedItemsList">
                                    </ul>
                                    <div id="emptyState" class="text-center text-muted py-3">
                                        <i class="bi bi-list-task fs-4"></i>
                                        <p class="mb-0 mt-2">No items selected yet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-link text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMenuGroup()">
                    <i class="bi bi-save me-1"></i>Create Group
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Menu Items Selection Modal -->
<div class="modal fade" id="menuItemsModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable h-100 m-0 mx-auto my-md-4">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h5 class="modal-title">Select Menu Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Search input -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="menuSearchInput" placeholder="Search items...">
                    </div>
                </div>

                <!-- Item list with checkboxes -->
                <div class="list-group" id="menuItemsList">
                    @foreach($availableMenuItems as $category => $items)
                    <div class="list-group-item list-group-item-secondary fw-bold">{{ $category }}</div>
                    @foreach($items->where('status', 'active')->where('is_archived', '!=', 'true') as $item)
                    <label class="list-group-item">
                        <input class="form-check-input me-2" type="checkbox" value="{{ $item->id }}">
                        <span class="fw-normal">{{ $item->name }}</span>
                        <span class="text-muted ms-2">₱{{ number_format($item->price, 2) }}</span>
                    </label>
                    @endforeach
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addSelectedMenuItems()">Add Selected</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Update modal spacing */
.modal-body {
    padding: 1rem !important;
}

.card {
    margin-bottom: 1rem !important;
}

.card-body {
    padding: 1rem !important;
}

.selected-items-container .list-group-item {
    padding: 0.5rem 1rem;
    margin-bottom: 0.25rem;
}

.selected-items-container .list-group-item:last-child {
    margin-bottom: 0;
}

#selectedItemsList:not(:empty) + #emptyState {
    display: none;
}

/* Add styles for multiple select */
select[multiple] {
    padding: 0.5rem;
}

select[multiple] option {
    padding: 0.5rem;
    margin-bottom: 2px;
    border-radius: 0.25rem;
}

select[multiple] option:checked {
    background-color: #0d6efd linear-gradient(0deg, #0d6efd 0%, #0d6efd 100%);
    color: white;
}

select[multiple] optgroup {
    font-weight: bold;
    color: #6c757d;
    padding: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

/* Select2 Bootstrap 5 fixes */
.select2-container--bootstrap-5 {
    width: 100% !important;
}

.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    border: 1px solid #dee2e6;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
    padding: 0 0.375rem;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
    background-color: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 2px 6px;
    margin: 2px 4px 2px 0;
}

/* Additional Select2 Bootstrap 5 Theme Fixes */
.select2-container--bootstrap-5.select2-container--focus .select2-selection,
.select2-container--bootstrap-5.select2-container--open .select2-selection {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select2-container--bootstrap-5 .select2-dropdown {
    border-color: #86b7fe;
}

.select2-container--bootstrap-5 .select2-dropdown.select2-dropdown--above {
    border-bottom: 1px solid #86b7fe;
}

.select2-container--bootstrap-5 .select2-dropdown.select2-dropdown--below {
    border-top: 1px solid #86b7fe;
}

/* Add these new styles */
select[multiple] option {
    position: relative;
    padding-left: 25px !important;
    background: white;
}

select[multiple] option:checked {
    color: #212529 !important;
    background: #e9ecef !important;
}

select[multiple] option:checked::before {
    content: '☑';
    position: absolute;
    left: 5px;
}

select[multiple] option::before {
    content: '☐';
    position: absolute;
    left: 5px;
}

select[multiple] optgroup {
    font-weight: bold;
    color: #6c757d;
    padding: 0.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

/* Remove default styling */
select[multiple] option::-ms-check {
    display: none;
}

/* Remove old select styles and add these: */
.bootstrap-select .dropdown-menu {
    max-height: 300px !important;
}

.bootstrap-select .dropdown-menu li small {
    color: #6c757d;
}

.bootstrap-select > .dropdown-toggle {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

/* Add these styles for the menu items modal */
#menuItemsModal .modal-dialog {
    max-width: 800px;
    max-height: calc(100vh - 3.5rem);
}

#menuItemsModal .modal-content {
    max-height: 100%;
}

#menuItemsModal .modal-body {
    height: calc(100% - 116px); /* Subtract header and footer height */
    overflow-y: auto;
}

#menuItemsModal #menuItemsList {
    max-height: calc(100% - 70px); /* Subtract search box height */
    overflow-y: auto;
}

#menuItemsModal .nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

#menuItemsModal .nav-link {
    border: none;
    color: #6c757d;
    padding: 0.5rem 1rem;
    margin-bottom: -2px;
}

#menuItemsModal .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
}

#menuItemsModal .tab-content {
    padding-top: 1rem;
}
</style>

