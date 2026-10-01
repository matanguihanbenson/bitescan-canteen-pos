@extends('layouts.app')

@section('title', 'POS Settings')

@push('styles')
<style>
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .badge-custom {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">POS Settings</h4>
                    <p class="text-muted mb-0">Configure Point of Sale system settings and options</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
                        <li class="breadcrumb-item active">POS Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Departments -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Departments</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Manage department options for credit transactions</p>
                    
                    <!-- Add Department Form -->
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="newDepartment" placeholder="Add new department...">
                            <button class="btn btn-primary" type="button" id="addDepartmentBtn">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Departments List -->
                    <div class="list-group" id="departmentsList">
                        @foreach($settings['departments'] as $dept)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $dept }}</span>
                            <button class="btn btn-sm btn-outline-danger remove-department" data-value="{{ $dept }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Discount Types -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-percent me-2"></i>Discount Types</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Configure available discount types and percentages</p>
                    
                    <!-- Add Discount Form -->
                    <div class="mb-3">
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" class="form-control" id="newDiscountName" placeholder="Discount name...">
                            <input type="number" class="form-control" id="newDiscountValue" placeholder="%" style="max-width: 80px;" min="0" max="100">
                            <button class="btn btn-success" type="button" id="addDiscountBtn">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Discounts List -->
                    <div class="list-group" id="discountsList">
                        @foreach($settings['discount_types'] as $discount)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-medium">{{ $discount['name'] }}</span>
                                <span class="badge bg-success badge-custom ms-2">{{ $discount['value'] }}%</span>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-warning edit-discount" data-name="{{ $discount['name'] }}" data-value="{{ $discount['value'] }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger remove-discount" data-name="{{ $discount['name'] }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit User Types -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Credit User Types</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Define user types eligible for credit purchases</p>
                    
                    <!-- Add User Type Form -->
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="newUserType" placeholder="Add user type...">
                            <button class="btn btn-info" type="button" id="addUserTypeBtn">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- User Types List -->
                    <div class="list-group" id="userTypesList">
                        @foreach($settings['credit_user_types'] as $type)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $type }}</span>
                            <button class="btn btn-sm btn-outline-danger remove-user-type" data-value="{{ $type }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <h6 class="text-info"><i class="bi bi-info-circle me-2"></i>How These Settings Work</h6>
                    <ul class="mb-0 small text-muted">
                        <li><strong>Departments:</strong> Available when creating credit transactions in POS</li>
                        <li><strong>Discount Types:</strong> Quick-select discount options during checkout</li>
                        <li><strong>Credit User Types:</strong> Categories of users who can purchase on credit</li>
                        <li>All changes are saved automatically and take effect immediately</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('settings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Settings
        </a>
        <button type="button" class="btn btn-primary" id="saveAllSettings">
            <i class="bi bi-check-lg me-1"></i> Save All Changes
        </button>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    let departments = @json($settings['departments']);
    let discounts = @json($settings['discount_types']);
    let userTypes = @json($settings['credit_user_types']);
    
    // Track initial state for unsaved changes detection
    const initialState = {
        departments: JSON.parse(JSON.stringify(departments)),
        discounts: JSON.parse(JSON.stringify(discounts)),
        userTypes: JSON.parse(JSON.stringify(userTypes))
    };
    
    let hasUnsavedChanges = false;
    
    // Function to check if there are unsaved changes
    function checkForChanges() {
        const currentState = {
            departments: JSON.parse(JSON.stringify(departments)),
            discounts: JSON.parse(JSON.stringify(discounts)),
            userTypes: JSON.parse(JSON.stringify(userTypes))
        };
        
        hasUnsavedChanges = JSON.stringify(initialState) !== JSON.stringify(currentState);
        
        // Update save button appearance
        if (hasUnsavedChanges) {
            $('#saveAllSettings').addClass('btn-warning').removeClass('btn-primary');
            $('#saveAllSettings').html('<i class="bi bi-exclamation-triangle me-1"></i> Save Changes (Unsaved)');
        } else {
            $('#saveAllSettings').addClass('btn-primary').removeClass('btn-warning');
            $('#saveAllSettings').html('<i class="bi bi-check-lg me-1"></i> Save All Changes');
        }
        
        return hasUnsavedChanges;
    }

    // Add Department
    $('#addDepartmentBtn').click(function() {
        const value = $('#newDepartment').val().trim();
        if (value && !departments.includes(value)) {
            departments.push(value);
            renderDepartments();
            $('#newDepartment').val('');
            checkForChanges();
        } else {
            toastr.warning('Department already exists or is empty');
        }
    });

    // Remove Department
    $(document).on('click', '.remove-department', function() {
        const value = $(this).data('value');
        departments = departments.filter(d => d !== value);
        renderDepartments();
        checkForChanges();
    });

    // Add/Update Discount
    $('#addDiscountBtn').click(function() {
        const name = $('#newDiscountName').val().trim();
        const value = parseFloat($('#newDiscountValue').val());
        const editing = $(this).data('editing');
        
        if (name && value >= 0 && value <= 100) {
            if (editing) {
                // Update existing discount
                const index = discounts.findIndex(d => d.name === editing);
                if (index !== -1) {
                    discounts[index] = { name: name, value: value };
                    $(this).text('Add').removeData('editing');
                    $('#newDiscountName').prop('readonly', false);
                }
                renderDiscounts();
                $('#newDiscountName').val('');
                $('#newDiscountValue').val('');
                checkForChanges();
            } else {
                // Add new discount
                if (!discounts.some(d => d.name === name)) {
                    discounts.push({ name: name, value: value });
                    renderDiscounts();
                    $('#newDiscountName').val('');
                    $('#newDiscountValue').val('');
                    checkForChanges();
                } else {
                    toastr.warning('Discount type already exists');
                }
            }
        } else {
            toastr.warning('Please provide valid discount name and percentage');
        }
    });

    // Edit Discount
    $(document).on('click', '.edit-discount', function() {
        const name = $(this).data('name');
        const value = $(this).data('value');
        $('#newDiscountName').val(name).prop('readonly', true);
        $('#newDiscountValue').val(value);
        $('#addDiscountBtn').text('Update').data('editing', name);
        $('#newDiscountName').focus();
    });

    // Remove Discount
    $(document).on('click', '.remove-discount', function() {
        const name = $(this).data('name');
        discounts = discounts.filter(d => d.name !== name);
        renderDiscounts();
        checkForChanges();
    });

    // Add User Type
    $('#addUserTypeBtn').click(function() {
        const value = $('#newUserType').val().trim();
        if (value && !userTypes.includes(value)) {
            userTypes.push(value);
            renderUserTypes();
            $('#newUserType').val('');
            checkForChanges();
        } else {
            toastr.warning('User type already exists or is empty');
        }
    });

    // Remove User Type
    $(document).on('click', '.remove-user-type', function() {
        const value = $(this).data('value');
        userTypes = userTypes.filter(t => t !== value);
        renderUserTypes();
        checkForChanges();
    });

    // Render Functions
    function renderDepartments() {
        let html = '';
        departments.forEach(dept => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${dept}</span>
                    <button class="btn btn-sm btn-outline-danger remove-department" data-value="${dept}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
        });
        $('#departmentsList').html(html || '<div class="text-muted small text-center p-3">No departments added</div>');
    }

    function renderDiscounts() {
        let html = '';
        discounts.forEach(discount => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-medium">${discount.name}</span>
                        <span class="badge bg-success badge-custom ms-2">${discount.value}%</span>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-warning edit-discount" data-name="${discount.name}" data-value="${discount.value}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger remove-discount" data-name="${discount.name}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        $('#discountsList').html(html || '<div class="text-muted small text-center p-3">No discounts added</div>');
    }

    function renderUserTypes() {
        let html = '';
        userTypes.forEach(type => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${type}</span>
                    <button class="btn btn-sm btn-outline-danger remove-user-type" data-value="${type}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
        });
        $('#userTypesList').html(html || '<div class="text-muted small text-center p-3">No user types added</div>');
    }

    // Save All Settings
    $('#saveAllSettings').click(function() {
        $.ajax({
            url: '{{ route("settings.pos.update") }}',
            method: 'POST',
            data: {
                departments: departments,
                discount_types: discounts,
                credit_user_types: userTypes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update initial state after successful save
                    initialState.departments = JSON.parse(JSON.stringify(departments));
                    initialState.discounts = JSON.parse(JSON.stringify(discounts));
                    initialState.userTypes = JSON.parse(JSON.stringify(userTypes));
                    
                    checkForChanges();
                    toastr.success(response.message || 'POS settings saved successfully!');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error saving settings');
            }
        });
    });

    // Enter key handlers
    $('#newDepartment').keypress(function(e) {
        if (e.which === 13) $('#addDepartmentBtn').click();
    });
    $('#newUserType').keypress(function(e) {
        if (e.which === 13) $('#addUserTypeBtn').click();
    });
    $('#newDiscountValue').keypress(function(e) {
        if (e.which === 13) $('#addDiscountBtn').click();
    });
    
    // Warn before leaving page with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });
    
    // Intercept navigation links to show SweetAlert
    $(document).on('click', 'a:not([target="_blank"])', function(e) {
        if (hasUnsavedChanges && !$(this).hasClass('allow-navigation')) {
            e.preventDefault();
            const href = $(this).attr('href');
            
            // Don't intercept if it's a javascript: or # link
            if (!href || href === '#' || href.startsWith('javascript:')) {
                return;
            }
            
            Swal.fire({
                title: 'Unsaved Changes',
                text: 'You have unsaved changes. Do you want to save before leaving?',
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save & Leave',
                denyButtonText: 'Leave Without Saving',
                cancelButtonText: 'Stay on Page',
                confirmButtonColor: '#3085d6',
                denyButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Save and then navigate
                    $.ajax({
                        url: '{{ route("settings.pos.update") }}',
                        method: 'POST',
                        data: {
                            departments: departments,
                            discount_types: discounts,
                            credit_user_types: userTypes,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                hasUnsavedChanges = false;
                                toastr.success('Settings saved successfully!');
                                window.location.href = href;
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Error saving settings');
                        }
                    });
                } else if (result.isDenied) {
                    // Leave without saving
                    hasUnsavedChanges = false;
                    window.location.href = href;
                }
            });
        }
    });
});
</script>
@endsection
@endsection