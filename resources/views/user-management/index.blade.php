@extends('layouts.app')

@push('styles')
<style>
    .stats-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .avatar-sm {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-management-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%) !important;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">User Management</h4>
                    <p class="text-muted mb-0">Manage system users, roles, and permissions for your organization</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">User Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Users</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $stats['total_users'] ?? 0 }}</h3>
                            <p class="text-muted mb-0 small">Registered users</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-people text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Active Users</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $stats['active_users'] ?? 0 }}</h3>
                            <p class="text-muted mb-0 small">Currently active</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-check text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">User Roles</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $stats['total_roles'] ?? 0 }}</h3>
                            <p class="text-muted mb-0 small">Different roles</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-info bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-shield-check text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Today's Logins</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $stats['total_logins_today'] ?? 0 }}</h3>
                            <p class="text-muted mb-0 small">{{ $stats['unique_logins_today'] ?? 0 }} unique users</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-box-arrow-in-right text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Section -->
    <div class="row">
        <div class="col-12">
            <div class="card user-management-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-gear me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">System Users</h5>
                                <small class="text-white opacity-90">Manage user accounts, roles, and access controls</small>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="/users/permissions" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-shield-lock me-1"></i> Permissions
                            </a>
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-person-plus me-1"></i> Add User
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    {!! $dataTable->table(['class' => 'table table-hover align-middle']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-person-plus me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lastname" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select class="form-select" name="role_id" required>
                                <option value="">Select Role</option>
                                <!-- Roles will be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-person-plus me-1"></i>Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-pencil-square me-2"></i>Edit User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="firstname" id="edit_firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lastname" id="edit_lastname" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select class="form-select" name="role_id" id="edit_role_id" required>
                                <!-- Roles will be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" class="form-control" name="password">
                            <div class="form-text">Leave blank to keep current password</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-1"></i>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! $dataTable->scripts() !!}
<script>
    $(document).ready(function() {
        // Check if DataTables is loaded
        if (typeof $.fn.DataTable === 'undefined') {
            return;
        }

        // Debug: Check if DataTable was initialized
        setTimeout(function() {
            if (window.LaravelDataTables && window.LaravelDataTables['users-table']) {
                // Add error handling to the DataTable
                window.LaravelDataTables['users-table'].on('error.dt', function(e, settings, techNote, message) {
                    });

                // Force reload to see if there are issues
                window.LaravelDataTables['users-table'].ajax.reload(function(json) {
                    }, false);
            } else {
                // Check if table element exists
                // Try manual initialization if DataTable failed
                if ($('#users-table').length > 0 && !$.fn.DataTable.isDataTable('#users-table')) {
                    $('#users-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '/users/data',
                            error: function(xhr, error, thrown) {
                                }
                        },
                        columns: [{
                                data: 'user_details',
                                name: 'user_details',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'role_badge',
                                name: 'role_badge',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'status_badge',
                                name: 'status_badge',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'last_login_formatted',
                                name: 'last_login_formatted',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                            emptyTable: 'No users found',
                            zeroRecords: 'No matching users found'
                        }
                    });
                }
            }
        }, 2000);

        // Load roles for dropdowns
        loadRoles();

        // Form submissions and other event handlers
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const formData = {
                firstname: $form.find('input[name="firstname"]').val(),
                lastname: $form.find('input[name="lastname"]').val(),
                email: $form.find('input[name="email"]').val(),
                role_id: $form.find('select[name="role_id"]').val(),
                password: $form.find('input[name="password"]').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.post('/users', formData)
                .done(function(response) {
                    if (response.success) {
                        toastr.success('User added successfully');
                        
                        // Properly close modal and remove backdrop
                        const modalEl = document.getElementById('addUserModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Remove any lingering backdrops and modal-open class
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('overflow', '');
                        $('body').css('padding-right', '');
                        
                        $('#addUserForm')[0].reset();
                        
                        // Reload DataTable
                        if (window.LaravelDataTables && window.LaravelDataTables['users-table']) {
                            window.LaravelDataTables['users-table'].ajax.reload();
                        }
                    } else {
                        toastr.error(response.message || 'Error adding user');
                    }
                })
                .fail(function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Error adding user');
                    }
                });
        });

        // Edit user form submission
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const userId = $('#edit_user_id').val();
            const formData = {
                firstname: $('#edit_firstname').val(),
                lastname: $('#edit_lastname').val(),
                email: $('#edit_email').val(),
                role_id: $('#edit_role_id').val(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT'
            };

            const password = $form.find('input[name="password"]').val();
            if (password) {
                formData.password = password;
            }
            
            $.post(`/users/${userId}`, formData)
                .done(function(response) {
                    if (response.success) {
                        toastr.success('User updated successfully');
                        
                        // Properly close modal and remove backdrop
                        const modalEl = document.getElementById('editUserModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Remove any lingering backdrops and modal-open class
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('overflow', '');
                        $('body').css('padding-right', '');
                        
                        // Reload DataTable
                        if (window.LaravelDataTables && window.LaravelDataTables['users-table']) {
                            window.LaravelDataTables['users-table'].ajax.reload();
                        }
                    } else {
                        toastr.error(response.message || 'Error updating user');
                    }
                })
                .fail(function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Error updating user');
                    }
                });
        });

        // Edit and delete handlers
        $(document).on('click', '.edit-user', function() {
            const userId = $(this).data('id');

            $.get(`/users/${userId}`)
                .done(function(user) {
                    $('#edit_user_id').val(user.id);
                    $('#edit_firstname').val(user.firstname);
                    $('#edit_lastname').val(user.lastname);
                    $('#edit_email').val(user.email);
                    $('#edit_role_id').val(user.role_id);
                    $('#editUserModal').modal('show');
                })
                .fail(function() {
                    toastr.error('Error loading user data');
                });
        });

        $(document).on('click', '.toggle-status', function() {
            const userId = $(this).data('id');
            const currentStatus = $(this).data('status');
            const action = currentStatus === 'active' ? 'deactivate' : 'activate';
            const actionCapitalized = action.charAt(0).toUpperCase() + action.slice(1);
            
            Swal.fire({
                title: `${actionCapitalized} User?`,
                text: `Are you sure you want to ${action} this user?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: currentStatus === 'active' ? '#ffc107' : '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${action}!`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/users/${userId}/toggle-status`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: `User ${action}d successfully`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            window.LaravelDataTables['users-table'].ajax.reload();
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error updating user status',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.delete-user', function() {
            const userId = $(this).data('id');
            
            Swal.fire({
                title: 'Delete User?',
                text: 'This action cannot be undone. Are you sure you want to delete this user?',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/users/${userId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'User has been deleted successfully',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                window.LaravelDataTables['users-table'].ajax.reload();
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Error deleting user',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error deleting user',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    });

    function loadRoles() {
        // Load roles for the dropdowns
        $.get('/users/roles/list')
            .done(function(response) {
                populateRoleSelects(response || []);
            })
            .fail(function() {
                // Fallback roles
                const defaultRoles = [{
                        id: 1,
                        name: 'System Admin'
                    },
                    {
                        id: 2,
                        name: 'Manager'
                    },
                    {
                        id: 3,
                        name: 'Cashier'
                    },
                    {
                        id: 4,
                        name: 'Staff'
                    }
                ];
                populateRoleSelects(defaultRoles);
            });
    }

    function populateRoleSelects(roles) {
        const selects = $('select[name="role_id"]');

        selects.each(function() {
            const select = $(this);
            const currentValue = select.val();
            select.find('option:not(:first)').remove();

            roles.forEach(role => {
                select.append('<option value="' + role.id + '">' + role.name + '</option>');
            });

            select.val(currentValue);
        });
    }
</script>
@endsection