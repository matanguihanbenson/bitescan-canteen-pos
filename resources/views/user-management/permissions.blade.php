@extends('layouts.app')

@push('styles')
<style>
    .permissions-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
    }
    
    /* Make role permission checkboxes truly unclickable */
    .permission-checkbox[data-role-permission="1"],
    .permission-checkbox.role-permission-locked {
        pointer-events: none !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
    }
    
    /* Also disable the label */
    .form-check:has(.permission-checkbox[data-role-permission="1"]),
    .form-check:has(.permission-checkbox.role-permission-locked) {
        opacity: 0.8;
    }
    
    .form-check:has(.permission-checkbox[data-role-permission="1"]) label,
    .form-check:has(.permission-checkbox.role-permission-locked) label {
        cursor: not-allowed !important;
        pointer-events: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">User Permissions</h4>
                    <p class="text-muted mb-0">Manage user permissions and access control settings</p>
                </div>
                <div class="page-title-right">
                    <div class="d-flex align-items-center">
                        <ol class="breadcrumb m-0 me-3">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/users">User Management</a></li>
                            <li class="breadcrumb-item active">Permissions</li>
                        </ol>
                        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#assignPermissionModal">
                            <i class="bi bi-shield-plus me-1"></i> Assign Permissions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Management -->
    <div class="row">
        <div class="col-12">
            <div class="card permissions-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-lock me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">User Permissions Overview</h5>
                            <small class="text-white opacity-90">Review and manage user access permissions</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="roleFilter">
                                <option value="">Filter by Role</option>
                                @if(isset($roles))
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="permissionFilter">
                                <option value="">Filter by Permission</option>
                                @if(isset($permissions))
                                @foreach($permissions as $permission)
                                <option value="{{ $permission->name }}">
                                    {{ $permission->display_name ?? $permission->name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="searchInput"
                                    placeholder="Search users...">
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Table -->
                    <div class="table-responsive">
                        <table id="permissionsTable" class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="20px">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th class="align-middle text-center">ID</th>
                                    <th class="align-middle text-center">User</th>
                                    <th class="align-middle">Permissions</th>
                                    <th class="align-middle text-center">Role</th>
                                    <th class="align-middle text-center" width="150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Permission Modal -->
<div class="modal fade" id="assignPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-shield-plus me-2"></i>Assign Permissions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="assignPermissionForm">
                    <div class="mb-3">
                        <label for="user" class="form-label fw-semibold">Select User</label>
                        <select class="form-select" id="user" name="user_id" required>
                            <option value="">Select user...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Permissions</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            @if(isset($permissions))
                            @foreach($permissions as $permission)
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input permission-checkbox"
                                    id="permission{{ $permission->id }}" name="permissions[]"
                                    value="{{ $permission->id }}">
                                <label class="form-check-label" for="permission{{ $permission->id }}">
                                    {{ $permission->display_name ?? $permission->name }}
                                    <span class="role-permission-indicator text-primary small fw-semibold" style="display: none;">
                                        <i class="bi bi-shield-check"></i> (Role Permission - Default)
                                    </span>
                                </label>
                            </div>
                            @endforeach
                            @else
                            <p class="text-muted">No permissions available</p>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4" id="savePermissionsBtn">
                    <i class="bi bi-check-lg me-1"></i>Save Permissions
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const CURRENT_USER_ID = {{ auth()->id() }};
    
    // GLOBAL variables for edit mode tracking
    let isEditingUser = false;
    let editingUserId = null;
    let permissionsDataTable = null;

    $(document).ready(function() {
        // Load users for permissions table
        loadPermissionsTable();
        
        // Modal functionality
        $('#assignPermissionModal').on('show.bs.modal', function() {
            // Only reset if we're not in edit mode
            if (!isEditingUser) {
                resetPermissionModal();
                loadUsersForModal();
            }
        });
        
        // Reset edit mode flag when modal closes
        $('#assignPermissionModal').on('hidden.bs.modal', function() {
            isEditingUser = false;
            editingUserId = null;
        });

        $('#user').on('change', function() {
            const userId = $(this).val();
            // Don't reload if we're programmatically setting the value during edit
            if (userId && !isEditingUser) {
                fetchAndRenderPermissions(userId, false);
            } else if (!userId) {
                clearPermissionChecks();
            }
        });
        
        // Role filter
        $('#roleFilter').on('change', function() {
            const roleId = $(this).val();
            if (permissionsDataTable) {
                permissionsDataTable.column(4).search(roleId ? '^' + escapeRegex(getRoleName(roleId)) + '$' : '', true, false).draw();
            }
        });
        
        // Permission filter (not really needed for this table, but keeping for consistency)
        $('#permissionFilter').on('change', function() {
            const permission = $(this).val();
            if (permissionsDataTable) {
                permissionsDataTable.column(3).search(permission ? permission : '').draw();
            }
        });
        
        // Search input
        $('#searchInput').on('keyup', function() {
            if (permissionsDataTable) {
                permissionsDataTable.search(this.value).draw();
            }
        });
        
        // ABSOLUTE PROTECTION: Prevent ANY interaction with role permission checkboxes
        $(document).on('click mousedown mouseup change keydown keyup', '.permission-checkbox[data-role-permission="1"], .permission-checkbox.role-permission-locked', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const wasChecked = $(this).prop('checked');
            $(this).prop('checked', true); // Force it to stay checked
            if (!wasChecked) {
                }
            return false;
        });
        
        // Also prevent label clicks for role permissions
        $(document).on('click', 'label[for^="permission"]', function(e) {
            const checkboxId = $(this).attr('for');
            const $checkbox = $('#' + checkboxId);
            if ($checkbox.attr('data-role-permission') === '1' || $checkbox.hasClass('role-permission-locked')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        });
        
        // Prevent space/enter key on focused role permissions
        $(document).on('keypress keydown', '.permission-checkbox[data-role-permission="1"], .permission-checkbox.role-permission-locked', function(e) {
            if (e.which === 32 || e.which === 13) { // Space or Enter
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Handle edit button from table
        $(document).on('click', '.edit-user-permissions', function() {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            // Open modal
            $('#assignPermissionModal').modal('show');
            
            // Set user ID directly (don't show selector)
            $('#user').val(userId);
            $('#user').closest('.mb-3').hide();
            
            // Update modal title
            $('#assignPermissionModal .modal-title').html('<i class="bi bi-pencil me-2"></i>Edit Permissions for ' + userName);
            
            // Load permissions
            fetchAndRenderPermissions(userId, false);
        });

        // Save button handler
        $('#savePermissionsBtn').click(function() {
            const userId = $('#user').val();
            
            if (!userId || userId === '') {
                toastr.error('Please select a user');
                return;
            }

            if (parseInt(userId) === CURRENT_USER_ID) {
                toastr.error("You cannot edit your own permissions.");
                return;
            }

            // Gather only editable (non-role) checked permissions
            const editableChecked = [];
            $('input.permission-checkbox:checked:not([data-role-permission="1"])').each(function() {
                editableChecked.push($(this).val());
            });

            $.ajax({
                url: '/permissions/save',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    user_id: userId,
                    permissions: editableChecked
                }
            }).done(function(res) {
                if (res.success) {
                    toastr.success('Permissions updated successfully');
                    $('#assignPermissionModal').modal('hide');
                    loadPermissionsTable();
                } else {
                    toastr.error(res.message || 'Failed to update permissions');
                }
            }).fail(function(xhr) {
                const msg = xhr.responseJSON?.message || 'Failed to update permissions';
                toastr.error(msg);
            });
        });
    });

    function resetPermissionModal() {
        $('#assignPermissionForm')[0].reset();
        clearPermissionChecks();
        toggleViewMode(false);
        $('#savePermissionsBtn').prop('disabled', false).show();
        $('#user').prop('disabled', false);
        // Always show the user selector on assign permission button
        $('#user').closest('.mb-3').show();
        // Reset modal title
        $('#assignPermissionModal .modal-title').html('<i class="bi bi-shield-plus me-2"></i>Assign Permissions');
    }

    function clearPermissionChecks() {
        $('input.permission-checkbox')
            .prop('checked', false)
            .prop('disabled', false)
            .attr('data-role-permission', '0')
            .closest('.form-check')
            .find('.role-permission-indicator')
            .hide();
    }

    function toggleViewMode(readOnly) {
        if (readOnly) {
            $('input.permission-checkbox').prop('disabled', true);
            $('#savePermissionsBtn').hide();
        } else {
            // Re-enable only checkboxes that are not role-permissions
            $('input.permission-checkbox').each(function() {
                if ($(this).data('role-permission') !== 1) {
                    $(this).prop('disabled', false);
                }
            });
            $('#savePermissionsBtn').show();
        }
    }

    function fetchAndRenderPermissions(userId, readOnly = false) {
        if (parseInt(userId) === CURRENT_USER_ID) {
            // Block self-edit; allow view only
            readOnly = true;
        }
        $.get('/permissions/' + userId)
            .done(function(res) {
                const userPerms = res.userPermissions || [];
                const rolePerms = res.rolePermissions || [];
                clearPermissionChecks();

                // First, check and LOCK role permissions (default)
                rolePerms.forEach(function(id) {
                    const $cb = $('#permission' + id);
                    
                    if ($cb.length === 0) {
                        return;
                    }
                    
                    const $formCheck = $cb.closest('.form-check');
                    const $label = $formCheck.find('label');
                    
                    // Set as role permission with multiple protections
                    $cb.prop('checked', true)
                       .prop('disabled', true)
                       .attr('data-role-permission', '1')
                       .data('role-permission', 1)
                       .addClass('role-permission-locked');
                    
                    // Remove ALL existing handlers and add SUPER STRONG new ones
                    $cb.off('click change mousedown mouseup keydown keypress')
                       .on('click change mousedown mouseup keydown keypress', function(e) {
                           e.preventDefault();
                           e.stopPropagation();
                           e.stopImmediatePropagation();
                           $(this).prop('checked', true); // Always force checked
                           return false;
                       });
                    
                    // Make checkbox completely non-interactive at DOM level
                    $cb[0].onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    };
                    $cb[0].onchange = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.checked = true;
                        return false;
                    };
                    $cb[0].onkeydown = function(e) {
                        e.preventDefault();
                        return false;
                    };
                    
                    // Also set readonly (additional protection)
                    $cb.attr('readonly', 'readonly');
                    $cb.prop('readonly', true);
                    
                    // Show indicator with "Default Permission" text
                    const $indicator = $formCheck.find('.role-permission-indicator');
                    $indicator.html('<i class="bi bi-shield-check"></i> <strong>Default Permission</strong> (Cannot be removed)').show();
                    // Completely disable the label
                    $label.css({
                        'cursor': 'not-allowed',
                        'user-select': 'none',
                        'pointer-events': 'none'
                    }).off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        return false;
                    });
                    
                    // Also disable on the parent div
                    $formCheck.css('pointer-events', 'auto'); // Enable form-check
                    $formCheck.find('*').not($label).css('pointer-events', 'auto'); // Enable other elements
                    
                    });
                
                // Then check user-specific permissions
                userPerms.forEach(function(id) {
                    const $cb = $('#permission' + id);
                    // Only check if not already checked by role
                    if (!$cb.prop('checked')) {
                        $cb.prop('checked', true);
                    }
                });

                toggleViewMode(readOnly);

                if (readOnly) {
                    $('#savePermissionsBtn').hide();
                }
            })
            .fail(function() {
                toastr.error('Failed to load user permissions');
            });
    }

    function loadPermissionsTable() {
        // Load real users from database
        $.get('/users/all')
            .done(function(response) {
                if (response.success && response.users) {
                    populatePermissionsTable(response.users);
                } else {
                    showEmptyPermissionsTable();
                }
            })
            .fail(function(xhr, status, error) {
                showEmptyPermissionsTable();
            });
    }

    function populatePermissionsTable(users) {
        // First, destroy any existing DataTable
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#permissionsTable')) {
            $('#permissionsTable').DataTable().clear().destroy();
        }

        const tbody = $('#permissionsTable tbody');
        tbody.empty(); // Clear all rows

        if (users.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-gear fs-1 d-block mb-3 text-muted opacity-50"></i>
                            <h6 class="fw-semibold">No Users Found</h6>
                            <p class="mb-0 text-muted">No users available for permissions management</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        // Build all rows first
        let tableRows = '';
        users.forEach((user, index) => {
            const isAdminRole = (user.role_name || '').toLowerCase() === 'admin' || (user.role_name || '').toLowerCase() === 'system admin';
            const isSelf = parseInt(user.id) === CURRENT_USER_ID;
            const actions = (isAdminRole || isSelf)
                ? `<div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" disabled title="Protected">
                            <i class="bi bi-shield-lock"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="viewUserPermissions(${user.id})" title="View Permissions">
                            <i class="bi bi-eye"></i>
                        </button>
                   </div>`
                : `<div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editUserPermissions(${user.id})" title="Edit Permissions">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="viewUserPermissions(${user.id})" title="View Permissions">
                            <i class="bi bi-eye"></i>
                        </button>
                   </div>`;

            tableRows += `
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" value="${user.id}">
                    </td>
                    <td class="text-center">${user.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-person text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-medium">${user.firstname || ''} ${user.lastname || ''}</div>
                                <div class="text-muted small">${user.email || ''}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info">Default Permissions</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary">${user.role_name || 'User'}</span>
                    </td>
                    <td class="text-center">
                        ${actions}
                    </td>
                </tr>
            `;
        });

        // Set all rows at once
        tbody.html(tableRows);

        // Initialize DataTable after a small delay to ensure DOM is ready
        setTimeout(() => {
            try {
                permissionsDataTable = $('#permissionsTable').DataTable({
                    searching: true,
                    paging: true,
                    info: true,
                    ordering: true,
                    pageLength: 10,
                    responsive: true,
                    destroy: true, // Allow re-initialization
                    columnDefs: [{
                            orderable: false,
                            targets: [0, 5]
                        } // Checkbox and Actions columns not orderable
                    ],
                    language: {
                        search: "Search users:",
                        lengthMenu: "Show _MENU_ users per page",
                        emptyTable: "No users found"
                    },
                    dom: 'lrtip' // Hide default search box since we have custom filters
                });
                
                // Store reference globally for filters
                window.permissionsDataTable = permissionsDataTable;
                
                } catch (error) {
                }
        }, 100);

        }

    function showEmptyPermissionsTable() {
        // Destroy DataTable if it exists
        if ($.fn.DataTable.isDataTable('#permissionsTable')) {
            $('#permissionsTable').DataTable().destroy();
        }

        $('#permissionsTable tbody').html(
            '<tr>' +
            '<td colspan="6" class="text-center py-5">' +
            '<div class="text-muted">' +
            '<i class="bi bi-gear fs-1 d-block mb-3 text-muted opacity-50"></i>' +
            '<h6 class="fw-semibold">No Users Found</h6>' +
            '<p class="mb-0 text-muted">No users available for permissions management</p>' +
            '</div>' +
            '</td>' +
            '</tr>'
        );
    }

    function loadUsersForModal() {
        const select = $('#user');
        const currentVal = select.val();

        return $.get('/users/all')
            .done(function(response) {
                select.empty().append('<option value="">Select user...</option>');

                if (response.success && response.users) {
                    response.users.forEach(user => {
                        const userName = (user.firstname || '') + ' ' + (user.lastname || '');
                        select.append('<option value="' + user.id + '">' + userName.trim() + '</option>');
                    });
                }

                // Preserve preselected value if any (e.g., from table Manage action)
                if (currentVal) {
                    select.val(currentVal);
                }
            })
            .fail(function() {
                toastr.error('Failed to load users for selection');
            });
    }
    
    function editUserPermissions(userId) {
        // Set edit mode flag BEFORE opening modal
        isEditingUser = true;
        editingUserId = userId;
        // Reset modal first
        resetPermissionModal();
        
        // Load users first, then set the selected user
        loadUsersForModal().then(function() {
            // Set userId without triggering change event
            const $userSelect = $('#user');
            $userSelect.val(userId);
            $userSelect.closest('.mb-3').show(); // Keep it visible so user can see who they're editing
            $userSelect.prop('disabled', true); // Disable it so they can't change
            // Update modal title
            $.get('/users/' + userId)
                .done(function(user) {
                    const userName = (user.firstname || '') + ' ' + (user.lastname || '');
                    $('#assignPermissionModal .modal-title').html('<i class="bi bi-pencil me-2"></i>Edit Permissions for ' + userName.trim());
                    });
            
            // Load permissions
            fetchAndRenderPermissions(userId, false);
            
            // Show modal after everything is set up
            $('#assignPermissionModal').modal('show');
            });
    }


    function viewUserPermissions(userId) {
        $('#assignPermissionModal').modal('show');
        $('#user').val(userId);
        // View mode: always read-only
        fetchAndRenderPermissions(userId, true);
        $('#savePermissionsBtn').hide();
        $('#user').prop('disabled', true);
    }
    
    // Helper function to escape regex characters
    function escapeRegex(value) {
        return value.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    }
    
    // Helper function to get role name by ID
    function getRoleName(roleId) {
        const option = $('#roleFilter option[value="' + roleId + '"]');
        return option.text() || '';
    }
</script>
@endsection
@endsection
