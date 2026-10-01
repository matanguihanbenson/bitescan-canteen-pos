@extends('layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">General Settings</h4>
                    <p class="text-muted mb-0">Configure system-wide settings and preferences</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
                        <li class="breadcrumb-item active">General</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form id="generalSettingsForm">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Application Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Application Name</label>
                            <input type="text" class="form-control" name="app_name" value="{{ $settings['app_name'] }}" required>
                            <small class="text-muted">Displayed in page titles and headers</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" value="{{ $settings['company_name'] }}">
                            <small class="text-muted">Shown on receipts and reports</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Receipt Footer Text</label>
                            <textarea class="form-control" name="receipt_footer" rows="3">{{ $settings['receipt_footer'] }}</textarea>
                            <small class="text-muted">Appears at bottom of receipts</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tax Rate (%)</label>
                                <input type="number" step="0.01" class="form-control" name="tax_rate" value="{{ $settings['tax_rate'] }}" min="0" max="100">
                                <small class="text-muted">Applied to transactions</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Currency</label>
                                <select class="form-control" name="currency" required>
                                    <option value="PHP" {{ $settings['currency'] == 'PHP' ? 'selected' : '' }}>PHP (₱)</option>
                                    <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6><i class="bi bi-info-circle me-2"></i>Settings Info</h6>
                        <p class="small text-muted">These settings affect the entire system. Changes take effect immediately.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#generalSettingsForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("settings.general.update") }}',
            method: 'POST',
            data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error updating settings');
            }
        });
    });
});
</script>
@endsection
@endsection