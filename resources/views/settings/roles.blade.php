@extends('layouts.app')

@section('title', 'Role Management')

@push('styles')
<style>
    .role-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .role-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
    }
    .role-card.selected {
        border: 2px solid #0d6efd;
        background-color: #f8f9ff;
    }
    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 0.75rem;
    }
    .permission-item {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        transition: background-color 0.2s ease;
    }
    .permission-item:hover {
        background-color: #f8f9fa;
    }
    .permission-item.checked {
        background-color: #e7f3ff;
        border-color: #0d6efd;
    }
    .permission-item.disabled,
    .permission-item:has(.permission-check:disabled) {
        opacity: 0.6;
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    .permission-check:disabled {
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">Role & Permission Management</h4>
                    <p class="text-muted mb-0">Manage system roles and their default permissions</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
                        <li class="breadcrumb-item active">Role Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Roles List -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>System Roles</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        <i class="bi bi-plus-lg"></i> Add
                    </button>
                </div>
                <div class="card-body p-3">
                    <div id="rolesList">
                        @foreach($roles as $role)
                        <div class="role-card card mb-2 shadow-sm" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $role->display_name ?? $role->name }}</h6>
                                        <small class="text-muted">{{ $role->description ?? 'No description' }}</small>
                                    </div>
                                    @if(!in_array($role->name, ['admin', 'System Admin']))
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary edit-role" data-id="{{ $role->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger delete-role" data-id="{{ $role->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    @else
                                    <span class="badge bg-warning">Protected</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-info shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="text-info"><i class="bi bi-info-circle me-2"></i>About Roles</h6>
                    <ul class="mb-0 small text-muted">
                        <li><strong>Admin</strong> and <strong>System Admin</strong> roles are protected</li>
                        <li>Each role has default permissions automatically applied</li>
                        <li>User-specific permissions override role permissions</li>
                        <li>Changes take effect immediately for new logins</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Permissions Panel -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-key me-2"></i>Default Role Permissions</h5>
                    <small id="selectedRoleName">Select a role to manage permissions</small>
                </div>
                <div class="card-body">
                    <div id="permissionsPanel">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-shield-lock fs-1 d-block mb-3 opacity-50"></i>
                            <p>Click on a role to view and manage its default permissions</p>
                        </div>
                    </div>
                    
                    <div id="permissionsEditor" style="display: none;">
                        <div class="permission-grid" id="permissionsList">
                            @foreach($permissions as $permission)
                            <div class="permission-item">
                                <div class="form-check">
                                    <input class="form-check-input permission-check" type="checkbox" 
                                           id="perm_{{ $permission->id }}" 
                                           value="{{ $permission->id }}"
                                           data-permission-name="{{ $permission->name }}">
                                    <label class="form-check-label fw-medium" for="perm_{{ $permission->id }}">
                                        {{ $permission->display_name ?? $permission->name }}
                                    </label>
                                    @if($permission->description)
                                    <small class="text-muted d-block">{{ $permission->description }}</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <button class="btn btn-outline-secondary" id="clearSelection">
                                <i class="bi bi-x-circle me-1"></i> Clear Selection
                            </button>
                            <button class="btn btn-success" id="saveRolePermissions">
                                <i class="bi bi-check-lg me-1"></i> Save Permissions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('settings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Settings
        </a>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Role</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRoleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., supervisor">
                        <small class="text-muted">Lowercase, no spaces (use underscores)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="display_name" required placeholder="e.g., Supervisor">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief description of this role"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRoleForm">
                <input type="hidden" name="role_id" id="edit_role_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="display_name" id="edit_display_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-1"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    let currentRoleId = null;
    let currentRoleName = '';
    const roles = @json($roles);

    // Role card click handler
    $(document).on('click', '.role-card', function() {
        if ($(this).hasClass('selected')) return;
        
        $('.role-card').removeClass('selected');
        $(this).addClass('selected');
        
        currentRoleId = $(this).data('role-id');
        currentRoleName = $(this).data('role-name');
        
        loadRolePermissions(currentRoleId);
    });

    // Load role permissions
    function loadRolePermissions(roleId) {
        $('#permissionsPanel').hide();
        $('#permissionsEditor').show();
        
        // Check if role is protected
        const isProtectedRole = ['admin', 'system admin'].includes(currentRoleName.toLowerCase());
        
        if (isProtectedRole) {
            $('#selectedRoleName').html('<span class="badge bg-warning me-2">Protected Role</span> ' + currentRoleName + ' <small class="text-muted">(Default permissions cannot be modified)</small>');
        } else {
            $('#selectedRoleName').text('Editing: ' + currentRoleName);
        }
        
        // Clear all checkboxes first
        $('.permission-check').prop('checked', false).prop('disabled', false);
        $('.permission-item').removeClass('checked');
        
        // Get role permissions from server
        $.get(`/permissions/role/${roleId}`, function(response) {
            if (response.permissions) {
                response.permissions.forEach(permId => {
                    const checkbox = $(`#perm_${permId}`);
                    checkbox.prop('checked', true);
                    checkbox.closest('.permission-item').addClass('checked');
                    
                    // Disable checkboxes for protected roles
                    if (isProtectedRole) {
                        checkbox.prop('disabled', true);
                    }
                });
            }
            
            // Disable/enable action buttons
            if (isProtectedRole) {
                $('#clearSelection').prop('disabled', true).addClass('disabled');
                $('#saveRolePermissions').prop('disabled', true).addClass('disabled');
            } else {
                $('#clearSelection').prop('disabled', false).removeClass('disabled');
                $('#saveRolePermissions').prop('disabled', false).removeClass('disabled');
            }
        }).fail(function() {
            // Load default permissions based on role name
            loadDefaultPermissions(currentRoleName);
            
            // Disable/enable action buttons
            if (isProtectedRole) {
                $('.permission-check').prop('disabled', true);
                $('#clearSelection').prop('disabled', true).addClass('disabled');
                $('#saveRolePermissions').prop('disabled', true).addClass('disabled');
            } else {
                $('#clearSelection').prop('disabled', false).removeClass('disabled');
                $('#saveRolePermissions').prop('disabled', false).removeClass('disabled');
            }
        });
    }

    // Load default permissions (fallback)
    function loadDefaultPermissions(roleName) {
        const defaults = {
            'admin': ['manage_users', 'view_dashboard', 'maintenance', 'view_system_logs', 'role_management'],
            'system admin': ['manage_users', 'view_dashboard', 'maintenance', 'view_system_logs', 'role_management'],
            'manager': ['view_dashboard', 'access_pos', 'manage_inventory', 'manage_menu', 'view_sales', 'manage_scholars', 'view_scholar_reports'],
            'cashier': ['view_dashboard', 'access_pos', 'view_sales'],
            'staff': ['view_dashboard', 'access_pos']
        };
        
        const rolePerms = defaults[roleName.toLowerCase()] || [];
        rolePerms.forEach(permName => {
            const checkbox = $(`.permission-check[data-permission-name="${permName}"]`);
            checkbox.prop('checked', true);
            checkbox.closest('.permission-item').addClass('checked');
        });
    }

    // Permission checkbox change
    $(document).on('change', '.permission-check', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.permission-item').addClass('checked');
        } else {
            $(this).closest('.permission-item').removeClass('checked');
        }
    });

    // Clear selection
    $('#clearSelection').click(function() {
        $('.permission-check').prop('checked', false);
        $('.permission-item').removeClass('checked');
    });

    // Save role permissions
    $('#saveRolePermissions').click(function() {
        if (!currentRoleId) {
            toastr.error('No role selected');
            return;
        }

        const selectedPermissions = [];
        $('.permission-check:checked').each(function() {
            selectedPermissions.push($(this).val());
        });

        $.ajax({
            url: '/permissions/role/save',
            method: 'POST',
            data: {
                role_id: currentRoleId,
                permissions: selectedPermissions,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Role permissions updated successfully!');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error saving permissions');
            }
        });
    });

    // Add new role
    $('#addRoleForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/roles/create',
            method: 'POST',
            data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: function(response) {
                if (response.success) {
                    toastr.success('Role created successfully!');
                    $('#addRoleModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error creating role');
            }
        });
    });

    // Edit role
    $(document).on('click', '.edit-role', function(e) {
        e.stopPropagation();
        const roleId = $(this).data('id');
        const role = roles.find(r => r.id == roleId);
        
        if (role) {
            $('#edit_role_id').val(role.id);
            $('#edit_display_name').val(role.display_name || role.name);
            $('#edit_description').val(role.description || '');
            $('#editRoleModal').modal('show');
        }
    });

    // Update role
    $('#editRoleForm').submit(function(e) {
        e.preventDefault();
        const roleId = $('#edit_role_id').val();
        
        $.ajax({
            url: `/roles/${roleId}/update`,
            method: 'POST',
            data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: function(response) {
                if (response.success) {
                    toastr.success('Role updated successfully!');
                    $('#editRoleModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error updating role');
            }
        });
    });

    // Delete role
    $(document).on('click', '.delete-role', function(e) {
        e.stopPropagation();
        const roleId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
            $.ajax({
                url: `/roles/${roleId}/delete`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Role deleted successfully!');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting role');
                }
            });
        }
    });
});
</script>
@endsection
@endsection