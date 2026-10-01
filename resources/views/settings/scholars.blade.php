@extends('layouts.app')

@section('title', 'Scholar Settings')

@push('styles')
<style>
    .scholar-settings-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Ensure better contrast for gradient headers */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }

    .avatar-sm {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
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
                    <h4 class="mb-1 font-size-18 fw-semibold">Scholar Settings</h4>
                    <p class="text-muted mb-0">Configure default settings for scholar management and allowances</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
                        <li class="breadcrumb-item active">Scholar Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="row">
        <div class="col-12">
            <form id="scholarSettingsForm">
                <div class="row g-4">
                    <div class="col-xl-8">
                        <!-- Allowance Settings -->
                        <div class="card scholar-settings-card border-0 shadow-sm mb-4">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-white bg-opacity-20 rounded-3 me-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cash-coin text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-white">Allowance Settings</h6>
                                        <small class="text-white opacity-90">Default daily allowance and auto-grant behavior</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="defaultAllowance" class="form-label">Default Daily Allowance</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" step="0.01" class="form-control" id="defaultAllowance" name="default_allowance" value="{{ number_format($settings['default_allowance'] ?? 70, 2, '.', '') }}" min="0" max="999.99" required>
                                        </div>
                                        <small class="form-text text-muted">Used as baseline when scholar-specific value is not set.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Auto-Grant Allowance</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="autoGrantAllowance" name="auto_grant_allowance" {{ ($settings['auto_grant_allowance'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="autoGrantAllowance">Grant allowance upon POS validation</label>
                                        </div>
                                        <small class="form-text text-muted">Triggered when a valid scholar is used in POS.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reset Settings -->
                        <div class="card scholar-settings-card border-0 shadow-sm">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-white bg-opacity-20 rounded-3 me-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-clock-history text-white fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-white">Daily Reset</h6>
                                        <small class="text-white opacity-90">Reset to daily allowance without carryover</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="autoReset">Auto Reset</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="autoReset" name="auto_reset" {{ ($settings['auto_reset'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="autoReset">Enable daily reset to daily allowance</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="resetTime">Reset Time</label>
                                        <input type="time" class="form-control" id="resetTime" name="reset_time" value="{{ $settings['reset_time'] ?? '04:00' }}" required>
                                        <small class="form-text text-muted">Scheduler runs at this time each day.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-2"><i class="bi bi-info-circle me-2"></i>How It Works</h6>
                                <ul class="mb-0 small text-muted">
                                    <li>Each scholar has a daily allowance (non-carryover).</li>
                                    <li>Auto reset sets balance to daily allowance at the configured time.</li>
                                    <li>POS validation can auto-grant allowance for the day.</li>
                                    <li>All changes are recorded in balance history.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card border-0 bg-light shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-title mb-0"><i class="bi bi-bar-chart-line me-2"></i>Current Statistics</h6>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="refreshStats">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                                <div class="row text-center" id="scholarStats">
                                    <div class="col-6 mb-3">
                                        <h5 class="text-primary mb-1" id="totalActiveScholars">-</h5>
                                        <p class="text-muted mb-0 small">Active Scholars</p>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h5 class="text-success mb-1" id="todayAllowances">-</h5>
                                        <p class="text-muted mb-0 small">Today's Allowances</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-info mb-1" id="totalBalance">-</h5>
                                        <p class="text-muted mb-0 small">Total Balance</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-warning mb-1" id="pendingAllowances">-</h5>
                                        <p class="text-muted mb-0 small">Pending Today</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Settings
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        loadScholarStats();

        $('#refreshStats').on('click', function() {
            loadScholarStats();
        });

        $('#scholarSettingsForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                default_allowance: $('#defaultAllowance').val(),
                auto_grant_allowance: $('#autoGrantAllowance').is(':checked') ? 1 : 0,
                auto_reset: $('#autoReset').is(':checked') ? 1 : 0,
                reset_time: $('#resetTime').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: '{{ route("settings.scholars.update") }}',
                method: 'POST',
                dataType: 'json',
                headers: { 'Accept': 'application/json' },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Settings updated successfully');
                    } else {
                        toastr.error('Failed to save settings');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'An error occurred while updating settings';
                    toastr.error(message);
                }
            });
        });
    });

    function loadScholarStats() {
        $.get('{{ route("scholars.summary") }}', function(response) {
            if (response.success) {
                const stats = response.stats;
                $('#totalActiveScholars').text(stats.total_scholars || 0);
                $('#todayAllowances').text(stats.claimed_today || 0);
                $('#totalBalance').text('₱' + Number(stats.total_balance_remaining || 0).toFixed(2));
                $('#pendingAllowances').text((stats.total_scholars - stats.claimed_today) || 0);
            }
        }).fail(function() {
            toastr.warning('Could not load scholar statistics');
        });
    }
</script>
@endsection