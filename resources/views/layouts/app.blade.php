<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bitescan POS System') }}</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    .content {
        transition: margin-left 0.3s ease;
        margin-left: 100px;
        /* Initial state with collapsed sidenav */
        width: calc(100% - 100px);
        min-height: 100vh;
    }

    .content.expanded {
        margin-left: 250px;
        /* Expanded state */
        width: calc(100% - 250px);
    }

    /* Custom Toastr Styles */
    .toast-success {
        background-color: #198754 !important;
    }

    .toast-error {
        background-color: #dc3545 !important;
    }

    .toast-info {
        background-color: #0dcaf0 !important;
    }

    .toast-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    #toast-container .toast {
        opacity: 1 !important;
    }

    #toast-container>div {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border-radius: 0.375rem !important;
    }
    </style>
    @stack('styles')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>

<body>
    <div id="app">
        @php
        use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Route;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\Storage;
        $request = request();
        @endphp

        @if(!$request->is('login') && !$request->is('register') && !$request->is('password/*'))
        <div class="d-flex">
            <div class="sidenav" id="mySidenav">
                <div class="sidenav-content">
                    <span class="hamburger" onclick="toggleNav()">&#9776; <span class="nav-title-text">BiteScan
                            POS</span></span>
                    @if(Auth::check() && Auth::user()->hasPermission('view_dashboard'))
                    <a href="{{route('dashboard')}}"
                        class="nav-icon sidenav-link {{ Route::is('dashboard') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                        <i class="bi bi-house-door"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    @endif

                    @if(Auth::check() && (Auth::user()->hasPermission('view_sales') || Auth::user()->hasPermission('manage_inventory')))
                    <a href="{{ route('reports.dashboard') }}"
                        class="nav-icon sidenav-link {{ Route::is('reports.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Reports Dashboard">
                        <i class="bi bi-bar-chart-fill"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                    @endif
                    @if(Auth::check() && Auth::user()->hasPermission('access_pos'))
                    <a href="{{route('pos.index')}}"
                        class="nav-icon sidenav-link {{ Route::is('pos.index') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="POS Machine"><i
                            class="bi bi-cash-stack"></i><span class="nav-text">POS Machine</span></a>
                    @endif
                    @if(Auth::check() && Auth::user()->hasPermission('view_sales'))

                    <a href="#" class="nav-icon sidenav-link {{ Route::is('sales.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Sales"
                        onclick="toggleSubNav('salesSubNav', this)"><i class="bi bi-bar-chart-line"></i><span
                            class="nav-text">Sales</span><i class="bi bi-chevron-down ms-auto"></i></a>
                    <div id="salesSubNav" class="subnav collapsed {{ Route::is('sales.*') ? 'expanded' : '' }}">
                        <a href="{{route('sales.index')}}"
                            class="nav-icon {{ Route::is('sales.index') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Sales Overview"><i class="bi bi-graph-up"></i><span
                                class="nav-text">Sales Overview</span></a>
                        <a href="{{route('sales.transactions')}}"
                            class="nav-icon {{ Route::is('sales.transactions') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Transactions"><i
                                class="bi bi-receipt"></i><span class="nav-text">Transactions</span></a>
                        <a href="{{route('sales.credit')}}"
                            class="nav-icon {{ Route::is('sales.credit') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Credit"><i class="bi bi-credit-card"></i><span
                                class="nav-text">Credit</span></a>
                    </div>

                    @endif

                    @if(Auth::check() && Auth::user()->hasPermission('manage_inventory'))

                    <a href="#"
                        class="nav-icon sidenav-link {{ Route::is('inventory.*') || Route::is('categories.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Inventory"
                        onclick="toggleSubNav('inventorySubNav', this)"><i class="bi bi-box-seam"></i><span
                            class="nav-text">Inventory</span><i class="bi bi-chevron-down ms-auto"></i></a>
                    <div id="inventorySubNav"
                        class="subnav collapsed {{ Route::is('inventory.*') || Route::is('categories.*') ? 'expanded' : '' }}">
                        <a href="{{route('inventory.index')}}"
                            class="nav-icon {{ Route::is('inventory.index') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Inventory Report"><i
                                class="bi bi-file-earmark-bar-graph"></i><span class="nav-text">Inventory
                                Report</span></a>
                        <a href="{{ route('inventory.products') }}"
                            class="nav-icon {{ Route::is('inventory.products') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Manage Products"><i
                                class="bi bi-pencil-square"></i><span class="nav-text">Manage Products</span></a>

                        <a href="{{ route('inventory.stock') }}"
                            class="nav-icon {{ Route::is('inventory.stock') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Manage Stock"><i class="bi bi-box"></i><span
                                class="nav-text">Manage Stock</span></a>
                        <a href="{{ route('categories.index') }}"
                            class="nav-icon {{ Route::is('categories.index') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Manage Categories"><i
                                class="bi bi-cart"></i><span class="nav-text">Manage Categories</span></a>


                        <hr style="border-color: white;margin:10px 30px">

                        <a href="{{route('inventory.logs')}}"
                            class="nav-icon {{ Route::is('inventory.logs') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Stock History"><i class="bi bi-clock-history"></i><span
                                class="nav-text">Stock Logs</span></a>

                    </div>
                    @endif

                    @if(Auth::check() && Auth::user()->hasPermission('manage_menu'))

                    <a href="#" class="nav-icon sidenav-link {{ Route::is('menu.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Menu"
                        onclick="toggleSubNav('menuSubNav', this)"><i class="bi bi-card-list"></i><span
                            class="nav-text">Menu</span><i class="bi bi-chevron-down ms-auto"></i></a>
                    <div id="menuSubNav" class="subnav collapsed {{ Route::is('menu.*') ? 'expanded' : '' }}">
                        <a href="{{route('menu.index')}}" class="nav-icon {{ Route::is('menu.index') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Manage Menu"><i
                                class="bi bi-gear"></i><span class="nav-text">Manage Menu</span></a>
                        <a href="{{route('menu.load-menu')}}"
                            class="nav-icon {{ Route::is('menu.load-menu') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Load Menu"><i class="bi bi-box-arrow-in-down"></i><span
                                class="nav-text">Load Menu</span></a>
                    </div>

                    @endif

                    @if(Auth::check() && (Auth::user()->hasPermission('manage_scholars') ||
                    Auth::user()->hasPermission('view_scholar_reports')))

                    <a href="#" class="nav-icon sidenav-link {{ Route::is('scholars.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Scholars"
                        onclick="toggleSubNav('scholarSubNav', this)"><i class="bi bi-mortarboard"></i><span
                            class="nav-text">Scholars</span><i class="bi bi-chevron-down ms-auto"></i></a>
                    <div id="scholarSubNav" class="subnav collapsed {{ Route::is('scholars.*') ? 'expanded' : '' }}">
                        @if(Auth::check() && Auth::user()->hasPermission('manage_scholars'))
                        <a href="{{route('scholars.index')}}"
                            class="nav-icon {{ Route::is('scholars.index') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Scholar Management"><i class="bi bi-people"></i><span
                                class="nav-text">Manage Scholars</span></a>
                        @endif
                        @if(Auth::check() && Auth::user()->hasPermission('view_scholar_reports'))
                        <a href="{{route('scholars.reports')}}"
                            class="nav-icon {{ Route::is('scholars.reports') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Scholar Reports"><i
                                class="bi bi-file-earmark-bar-graph"></i><span class="nav-text">Scholar
                                Reports</span></a>
                        @endif
                    </div>
                    @endif

                    @if(Auth::check() && Auth::user()->hasPermission('manage_users'))

                    <a href="#"
                        class="nav-icon sidenav-link {{ Route::is('users.*') || Route::is('admin.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Users"
                        onclick="toggleSubNav('userSubNav', this)"><i class="bi bi-people"></i><span
                            class="nav-text">Users</span><i class="bi bi-chevron-down ms-auto"></i></a>

                    <div id="userSubNav"
                        class="subnav collapsed {{ Route::is('users.*') || Route::is('admin.*') ? 'expanded' : '' }}">
                        <a href="{{route('users.index')}}"
                            class="nav-icon {{ Route::is('users.index') ? 'active' : '' }}" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Manage Users"><i class="bi bi-person-lines-fill"></i><span
                                class="nav-text">Manage Users</span></a>

                        <a href="{{route('users.permissions')}}"
                            class="nav-icon {{ Route::is('users.permissions') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Permissions"><i
                                class="bi bi-shield-lock"></i><span class="nav-text">Permissions</span></a>
                        <a href="{{route('admin.password-requests')}}"
                            class="nav-icon {{ Route::is('admin.password-requests') ? 'active' : '' }}"
                            data-bs-toggle="tooltip" data-bs-placement="right" title="Password Reset"><i
                                class="bi bi-key"></i><span class="nav-text">Password Reset</span></a>

                    </div>
                    @endif

                    @if(Auth::check() && Auth::user()->hasPermission('maintenance'))

                    <a href="{{route('maintenance')}}"
                        class="nav-icon sidenav-link {{ Route::is('maintenance') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="System Maintenance"><i
                            class="bi bi-tools"></i><span class="nav-text">System Maintenance</span></a>
                    @endif

                    @if(Auth::check() && Auth::user()->hasPermission('view_system_logs'))

                    <a href="{{route('system-logs.index')}}"
                        class="nav-icon sidenav-link {{ Route::is('system-logs.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="System Logs"><i
                            class="bi bi-clock-history"></i><span class="nav-text">System Logs</span></a>
                    @endif

                    <a href="{{route('settings.index')}}"
                        class="nav-icon sidenav-link {{ Route::is('settings.*') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Settings">
                        <i class="bi bi-gear"></i>
                        <span class="nav-text">Settings</span>
                    </a>
                </div>
                <div class="profile-container mt-auto">
                    <a href="#" class="nav-icon {{ Route::is('profile') ? 'active' : '' }}" data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        title="{{ Auth::check() && Auth::user() ? Auth::user()->firstname . ' ' . Auth::user()->lastname : 'Guest User' }}"
                        id="profileLink">
                        @if(Auth::check() && Auth::user())
                        @php
                        $userImage = Auth::user()->image;
                        // Normalize path and ensure it exists
                        if ($userImage) {
                            $userImage = str_replace('\\', '/', $userImage);
                            $imagePath = storage_path('app/public/' . $userImage);
                            $imageUrl = file_exists($imagePath) ? asset('storage/' . $userImage) : asset('images/user.png');
                        } else {
                            $imageUrl = asset('images/user.png');
                        }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="Profile"
                             style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;"
                             onerror="this.onerror=null;this.src='{{ asset('images/user.png') }}';">
                        <span class="nav-text">{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}</span>
                        @else
                        <i class="bi bi-person-circle"></i>
                        <span class="nav-text">Guest User</span>
                        @endif
                    </a>
                    @if(Auth::check())
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}" class="nav-icon" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Logout"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bi bi-box-arrow-right"></i><span class="nav-text">Logout</span></a>
                    @endif
                </div>
            </div>

            <!-- Profile Modal -->
            <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="profileModalLabel">Edit Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="profileForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="profileUserId" name="id" value="{{ Auth::id() }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center mb-3">
                                            <div style="width: 150px; height: 150px; margin: 0 auto;">
                                                @php
                                                $previewImage = Auth::user()->image;
                                                if ($previewImage) {
                                                    $previewImage = str_replace('\\', '/', $previewImage);
                                                    $previewPath = storage_path('app/public/' . $previewImage);
                                                    $previewUrl = file_exists($previewPath) ? asset('storage/' . $previewImage) : asset('images/user.png');
                                                } else {
                                                    $previewUrl = asset('images/user.png');
                                                }
                                                @endphp
                                                <img id="profileImagePreview"
                                                    src="{{ $previewUrl }}"
                                                    class="img-fluid rounded border"
                                                    style="width: 150px; height: 150px; object-fit: cover;"
                                                    onerror="this.onerror=null;this.src='{{ asset('images/user.png') }}';">
                                            </div>
                                            <div class="mt-2">
                                                <label for="profileImage" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-image me-1"></i>Choose Photo
                                                </label>
                                                <input type="file" id="profileImage" name="image" accept="image/*"
                                                    class="d-none">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="profileFirstName" class="form-label">First Name</label>
                                                    <input type="text" class="form-control" id="profileFirstName"
                                                        name="firstname" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="profileMiddleName" class="form-label">Middle
                                                        Name</label>
                                                    <input type="text" class="form-control" id="profileMiddleName"
                                                        name="middlename">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="profileLastName" class="form-label">Last Name</label>
                                                    <input type="text" class="form-control" id="profileLastName"
                                                        name="lastname" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label for="profileUsername" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="profileUsername"
                                                        name="username" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label for="profileEmail" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="profileEmail"
                                                        name="email" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                                    id="profileChangePasswordBtn">
                                                    <i class="bi bi-key me-1"></i>Change Password
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Change Password Modal -->
            <div class="modal fade" id="profileChangePasswordModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="profileChangePasswordForm">
                                <div class="mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword"
                                        name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="profileNewPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="profileNewPassword" name="password"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="profileConfirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="profileConfirmPassword" required>
                                    <div class="invalid-feedback">Passwords do not match</div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="saveProfilePasswordBtn">Update
                                Password</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load jQuery first -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <!-- Add this before other scripts -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <div class="content" id="mainContent">
                @yield('content')
            </div>
        </div>
        @else
        <div>
            @yield('content')
        </div>
        @endif
    </div>
    <script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function(xhr) {
            if (xhr.status === 401 && xhr.responseJSON && xhr.responseJSON.deactivated) {
                Swal.fire({
                    icon: 'error',
                    title: 'Account Deactivated',
                    text: xhr.responseJSON.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    window.location.href = '/login';
                });
            }
        }
    });

    function toggleNav() {
        const sidenav = document.getElementById("mySidenav");
        const mainContent = document.getElementById("mainContent");

        if (sidenav.style.width === "250px") {
            sidenav.style.width = "100px";
            mainContent.classList.remove('expanded');
        } else {
            sidenav.style.width = "250px";
            mainContent.classList.add('expanded');
        }
    }

    $(document).ready(function() {
        @if(Auth::check())
        $('#profileLink').click(function(e) {
            e.preventDefault();
            $.get('/users/{{ Auth::id() }}')
                .done(function(response) {
                    $('#profileFirstName').val(response.firstname);
                    $('#profileMiddleName').val(response.middlename);
                    $('#profileLastName').val(response.lastname);
                    $('#profileUsername').val(response.username);
                    $('#profileEmail').val(response.email);

                    // Decide image preview URL safely
                    let previewUrl = null;
                    if (response.image_url) {
                        previewUrl = response.image_url;
                    } else if (response.image) {
                        const safePath = String(response.image).replace(/\\\\/g, '/');
                        previewUrl = '/storage/' + safePath;
                    } else {
                        previewUrl = '{{ asset('images/user.png') }}';
                    }
                    if (/^[a-zA-Z]:\\\\/.test(previewUrl)) {
                        previewUrl = '{{ asset('images/user.png') }}';
                    }
                    $('#profileImagePreview').attr('src', previewUrl);

                    const profileModal = new bootstrap.Modal(document.getElementById(
                        'profileModal'));
                    profileModal.show();
                })
                .fail(function(xhr, status, error) {
                    toastr.error('Error loading profile data');
                });
        });

        // Handle profile image preview on file selection
        $('#profileImage').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#profileImagePreview').attr('src', event.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: '/users/{{ Auth::id() }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'profileModal'));
                        modal.hide();
                        toastr.success('Profile updated successfully');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        toastr.error(response.message || 'Error updating profile');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Error updating profile');
                    }
                }
            });
        });

        $('#saveProfilePasswordBtn').click(function() {
            const currentPassword = $('#currentPassword').val();
            const password = $('#profileNewPassword').val();
            const confirm = $('#profileConfirmPassword').val();

            if (password.length < 6) {
                toastr.error('Password must be at least 6 characters long');
                return;
            }

            if (password !== confirm) {
                $('#profileConfirmPassword').addClass('is-invalid');
                toastr.error('Passwords do not match');
                return;
            }

            $('#profileConfirmPassword').removeClass('is-invalid');

            $.ajax({
                url: '/users/{{ Auth::id() }}/update-password',
                type: 'POST',
                data: {
                    current_password: currentPassword,
                    password: password
                },
                success: function(response) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'profileChangePasswordModal'));
                    modal.hide();
                    toastr.success('Password updated successfully');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        toastr.error(xhr.responseJSON.message ||
                            'Invalid current password');
                    } else {
                        toastr.error('Error updating password');
                    }
                }
            });
        });
        @endif

    });
    </script>

    <!-- Other scripts can follow -->

    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SweetAlert Utilities -->
    <script src="{{ asset('js/sweetalert-utils.js') }}"></script>

    @yield('scripts')
    @stack('scripts')

    <script>
    function toggleNav() {
        const sidenav = document.getElementById("mySidenav");
        const mainContent = document.getElementById("mainContent");

        if (sidenav.style.width === "250px") {
            sidenav.style.width = "100px";
            mainContent.classList.remove('expanded');
        } else {
            sidenav.style.width = "250px";
            mainContent.classList.add('expanded');
        }
    }
    </script>

    <!-- SweetAlert Flash Messages -->
    @include('components.sweetalert')

</body>

</html>

</html>