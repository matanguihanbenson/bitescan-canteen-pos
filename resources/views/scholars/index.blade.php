@extends('layouts.app')

@section('title', 'Scholar Management')

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

    .scholar-table-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Fix DataTables styling */
    .dataTables_wrapper .dataTables_filter label {
        color: #495057 !important;
        font-weight: 500;
    }

    .dataTables_wrapper .dataTables_length label {
        color: #495057 !important;
        font-weight: 500;
    }

    .dataTables_wrapper .dataTables_info {
        color: #6c757d !important;
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Scholar Management</h4>
                    <p class="text-muted mb-0">Manage scholar information, allowances, and balance tracking</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Scholars</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Scholars</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $stats['total_scholars'] }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-people text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Claimed Today</h6>
                            <h3 class="mb-1 text-dark fw-semibold">{{ $stats['claimed_today'] }}</h3>
                            <p class="text-muted mb-0 small">
                                <span class="badge bg-success">{{ $stats['percentage_claimed'] }}%</span>
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Allowance Today</h6>
                            <h3 class="mb-1 text-dark fw-semibold">
                                ₱{{ number_format($stats['total_allowance_today'], 2) }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-info bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-cash-coin text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Balance</h6>
                            <h3 class="mb-1 text-dark fw-semibold">
                                ₱{{ number_format($stats['total_balance_remaining'], 2) }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-wallet2 text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card scholar-table-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-mortarboard me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">Scholars List</h5>
                                <small class="text-white opacity-90">Manage student scholarship information and
                                    balances</small>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="location.reload();">
                                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                            </button>
                            @php
                                $settingsService = app(\App\Services\SettingsService::class);
                                $scholarSettings = $settingsService->getScholarSettings();
                                $autoGrantEnabled = $scholarSettings['auto_grant_allowance'] ?? false;
                            @endphp
                            @if(!$autoGrantEnabled)
                            <button type="button" class="btn btn-warning btn-sm" id="resetDailyBtn">
                                <i class="bi bi-arrow-repeat me-1"></i> Reset Daily Allowance
                            </button>
                            @endif
                            <button type="button" class="btn btn-info btn-sm" onclick="window.location.href='{{ route('settings.scholars') }}'">
                                <i class="bi bi-gear me-1"></i> Settings
                            </button>
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addScholarModal">
                                <i class="bi bi-plus-circle me-1"></i> Add Scholar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="scholarsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold text-dark px-4 py-3">Student ID</th>
                                    <th class="border-0 fw-bold text-dark py-3">Full Name</th>
                                    <th class="border-0 fw-bold text-dark py-3">Program</th>
                                    <th class="border-0 fw-bold text-dark py-3">Year Level</th>
                                    <th class="border-0 fw-bold text-dark py-3">Balance</th>
                                    <th class="border-0 fw-bold text-dark py-3">Last Claim</th>
                                    <th class="border-0 fw-bold text-dark py-3">Status</th>
                                    <th class="border-0 fw-bold text-dark text-center py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scholars as $scholar)
                                <tr class="{{ !$scholar->is_active ? 'table-light bg-opacity-50' : '' }}">
                                    <td class="align-middle px-4">
                                        <span class="fw-bold text-dark">{{ $scholar->student_id }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center"
                                                style="width: 35px; height: 35px;">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <span class="fw-medium">{{ $scholar->first_name }}
                                                {{ $scholar->last_name }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted">{{ $scholar->program ?? '-' }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted">{{ $scholar->year_level ?? '-' }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                        $balance = $scholar->current_balance ?? 0;
                                        $color = $balance > 50 ? 'success' : ($balance > 20 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $color }}">₱{{ number_format($balance, 2) }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @if($scholar->last_allowance_date)
                                        @php
                                        $isToday = $scholar->last_allowance_date->isToday();
                                        $class = $isToday ? 'bg-success' : 'bg-warning';
                                        @endphp
                                        <span
                                            class="badge {{ $class }}">{{ $scholar->last_allowance_date->format('M d, Y') }}</span>
                                        @else
                                        <span class="badge bg-secondary">Never</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge {{ $scholar->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $scholar->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('scholars.show', $scholar->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('scholars.edit', $scholar->id) }}"
                                                class="btn btn-sm btn-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-info view-history"
                                                data-id="{{ $scholar->id }}" title="View History">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                            <form action="{{ route('scholars.toggle-status', $scholar->id) }}"
                                                method="POST" style="display: inline;" class="toggle-status-form"
                                                data-scholar-name="{{ $scholar->first_name }} {{ $scholar->last_name }}"
                                                data-action="{{ $scholar->is_active ? 'deactivate' : 'activate' }}">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm {{ $scholar->is_active ? 'btn-warning' : 'btn-success' }} toggle-status-btn"
                                                    title="{{ $scholar->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i
                                                        class="bi {{ $scholar->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted opacity-50"></i>
                                            <h6 class="fw-semibold">No scholars found</h6>
                                            <p class="mb-0 text-muted">Click "Add Scholar" to get started</p>
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
</div>

<!-- Add Scholar Modal -->
<div class="modal fade" id="addScholarModal" tabindex="-1" aria-labelledby="addScholarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold" id="addScholarModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Scholar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('scholars.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('student_id') is-invalid @enderror"
                                    id="student_id" name="student_id" value="{{ old('student_id') }}" required>
                                @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                    id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                    id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                                    id="contact_number" name="contact_number" value="{{ old('contact_number') }}">
                                @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program" class="form-label">Program</label>
                                <input type="text" class="form-control @error('program') is-invalid @enderror"
                                    id="program" name="program" value="{{ old('program') }}">
                                @error('program')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="year_level" class="form-label">Year Level</label>
                                <select class="form-select @error('year_level') is-invalid @enderror" id="year_level"
                                    name="year_level">
                                    <option value="">Select Year Level</option>
                                    <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st
                                        Year</option>
                                    <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd
                                        Year</option>
                                    <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd
                                        Year</option>
                                    <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th
                                        Year</option>
                                    <option value="Graduate" {{ old('year_level') == 'Graduate' ? 'selected' : '' }}>
                                        Graduate</option>
                                </select>
                                @error('year_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="daily_allowance" class="form-label">Daily Allowance</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01"
                                        class="form-control @error('daily_allowance') is-invalid @enderror"
                                        id="daily_allowance" name="daily_allowance"
                                        value="{{ old('daily_allowance', '70.00') }}">
                                    @error('daily_allowance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-1"></i>Add Scholar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scholar History Modal -->
<div class="modal fade" id="scholarHistoryModal" tabindex="-1" aria-labelledby="scholarHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title fw-semibold" id="scholarHistoryModalLabel">
                    <i class="bi bi-clock-history me-2"></i>Scholar Balance History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="historyContent">
                    <p class="text-center">Loading...</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        if ($.fn.DataTable) {
            $('#scholarsTable').DataTable({
                pageLength: 10,
                responsive: true,
                language: {
                    search: "Search scholars:",
                    lengthMenu: "Show _MENU_ scholars per page",
                    emptyTable: "No scholars found"
                },
                columnDefs: [{
                    orderable: false,
                    targets: [7] // Actions column
                }]
            });
            }

        // Handle view history button click
        $(document).on('click', '.view-history', function(e) {
            e.preventDefault();
            const scholarId = $(this).data('id');
            if (!scholarId) {
                alert('Error: Scholar ID not found');
                return;
            }

            // Show loading in modal
            $('#historyContent').html(
                '<div class="text-center p-4"><div class="spinner-border" role="status"></div><p>Loading...</p></div>'
            );
            $('#scholarHistoryModal').modal('show');

            // Make AJAX call
            $.get(`/scholars/${scholarId}/history`)
                .done(function(response) {
                    if (response.success) {
                        const scholar = response.scholar;
                        const history = response.history;

                        let historyHtml = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h5>${scholar.full_name}</h5>
                                <p class="text-muted">Student ID: ${scholar.student_id}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6>Current Balance: <span class="text-success">₱${scholar.current_balance}</span></h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Balance After</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                        if (history && history.length > 0) {
                            history.forEach(record => {
                                const badgeClass = record.type === 'Credit' ? 'bg-success' :
                                    'bg-danger';
                                historyHtml += `
                                <tr>
                                    <td>${record.date}</td>
                                    <td><span class="badge ${badgeClass}">${record.type}</span></td>
                                    <td>₱${record.amount}</td>
                                    <td>${record.description}</td>
                                    <td>₱${record.new_balance}</td>
                                </tr>
                            `;
                            });
                        } else {
                            historyHtml +=
                                '<tr><td colspan="5" class="text-center">No history found</td></tr>';
                        }

                        historyHtml += '</tbody></table></div>';
                        $('#historyContent').html(historyHtml);
                    } else {
                        $('#historyContent').html(
                            '<p class="text-danger text-center">Failed to load history</p>');
                    }
                })
                .fail(function(xhr) {
                    $('#historyContent').html(
                        '<p class="text-danger text-center">Error loading history</p>');
                });
        });

        // Manual reset daily allowance
        $('#resetDailyBtn').on('click', function() {
            if (!confirm('Reset daily allowance for all active scholars who have not claimed today?')) {
                return;
            }
            $.ajax({
                url: '{{ route('scholars.reset-daily') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(resp) {
                if (resp && resp.success) {
                    alert(resp.message || 'Daily allowances reset.');
                    location.reload();
                } else {
                    alert(resp.message || 'Failed to reset.');
                }
            }).fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Error resetting allowances');
            });
        });

        // Handle toggle status with improved SweetAlert handling
        $(document).on('click', '.toggle-status-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const form = $(this).closest('.toggle-status-form');
            const scholarName = form.data('scholar-name');
            const action = form.data('action');

            const title = action === 'deactivate' ? 'Deactivate Scholar?' : 'Activate Scholar?';
            const text = `Are you sure you want to ${action} ${scholarName}?`;
            const confirmButtonText = action === 'deactivate' ? 'Yes, deactivate!' : 'Yes, activate!';

            if (typeof SweetAlert !== 'undefined' && SweetAlert.confirm) {
                const options = {
                    confirmButtonColor: action === 'deactivate' ? '#ffc107' : '#198754',
                    confirmButtonText: confirmButtonText
                };

                SweetAlert.confirm(title, text, options)
                    .then((result) => {
                        if (result.isConfirmed) {
                            if (SweetAlert.loading) {
                                SweetAlert.loading('Processing...',
                                    `${action === 'deactivate' ? 'Deactivating' : 'Activating'} scholar...`
                                );
                            }
                            form.submit();
                        }
                    })
                    .catch((error) => {
                        // Fallback to native confirm
                        if (confirm(text)) {
                            form.submit();
                        }
                    });
            } else if (typeof window.Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: action === 'deactivate' ? '#ffc107' : '#198754',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmButtonText
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: `${action === 'deactivate' ? 'Deactivating' : 'Activating'} scholar...`,
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        form.submit();
                    }
                });
            } else {
                if (confirm(text)) {
                    form.submit();
                }
            }
        });

        // Auto-show modal if there are validation errors
        @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('addScholarModal'));
        modal.show();
        @endif

    });
</script>
@endsection