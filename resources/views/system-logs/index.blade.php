@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>System Activity Logs</h4>
                    <div>
                        <button class="btn btn-light btn-sm" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <button class="btn btn-light btn-sm" id="exportBtn">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3" id="statsRow">
                        <div class="col-md-2">
                            <div class="stat-card">
                                <div class="stat-value" id="statToday">-</div>
                                <div class="stat-label">Today</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <div class="stat-value" id="statWeek">-</div>
                                <div class="stat-label">This Week</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <div class="stat-value" id="statMonth">-</div>
                                <div class="stat-label">This Month</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card">
                                <div class="stat-value" id="statTotal">-</div>
                                <div class="stat-label">Total Logs</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card bg-danger text-white">
                                <div class="stat-value" id="statCritical">-</div>
                                <div class="stat-label">Critical</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-card bg-warning">
                                <div class="stat-value" id="statErrors">-</div>
                                <div class="stat-label">Errors</div>
                            </div>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('system-logs.index') }}" class="mb-4" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label small">Search</label>
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Search..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">User</label>
                                <select name="user_id" class="form-select form-select-sm">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->username }} ({{ $user->firstname }} {{ $user->lastname }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Module</label>
                                <select name="module" class="form-select form-select-sm">
                                    <option value="">All Modules</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                            {{ $module }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Action</label>
                                <select name="action" class="form-select form-select-sm">
                                    <option value="">All Actions</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Severity</label>
                                <select name="severity" class="form-select form-select-sm">
                                    <option value="">All Severities</option>
                                    <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="success" {{ request('severity') == 'success' ? 'selected' : '' }}>Success</option>
                                    <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="error" {{ request('severity') == 'error' ? 'selected' : '' }}>Error</option>
                                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Date From</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Date To</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                                <a href="{{ route('system-logs.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th style="width: 150px;">Date/Time</th>
                                    <th style="width: 150px;">User</th>
                                    <th style="width: 120px;">Module</th>
                                    <th style="width: 100px;">Action</th>
                                    <th>Description</th>
                                    <th style="width: 100px;">Severity</th>
                                    <th style="width: 100px;">IP Address</th>
                                    <th style="width: 80px;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td class="text-muted small">{{ $log->id }}</td>
                                    <td class="small">
                                        <div>{{ $log->created_at->format('M d, Y') }}</div>
                                        <div class="text-muted">{{ $log->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <div class="fw-medium">{{ $log->user->full_name }}</div>
                                            <div class="text-muted small">{{ $log->user->username }}</div>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $log->module }}</span></td>
                                    <td><span class="badge {{ $log->action_badge }}">{{ ucfirst($log->action) }}</span></td>
                                    <td class="small">{{ \Illuminate\Support\Str::limit($log->description, 80) }}</td>
                                    <td><span class="badge {{ $log->severity_badge }}">{{ ucfirst($log->severity) }}</span></td>
                                    <td class="text-muted small">{{ $log->ip_address }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary view-details" 
                                                data-id="{{ $log->id }}" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            <p>No logs found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
                        </div>
                        <div>
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-2px);
}
.stat-value {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
}
.stat-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
}
.bg-danger .stat-label,
.bg-warning .stat-label {
    color: rgba(255,255,255,0.8);
}
</style>

<script>
$(document).ready(function() {
    loadStats();
    
    function loadStats() {
        $.get('{{ route("system-logs.stats") }}', function(data) {
            $('#statToday').text(data.today);
            $('#statWeek').text(data.this_week);
            $('#statMonth').text(data.this_month);
            $('#statTotal').text(data.total);
            $('#statCritical').text(data.critical);
            $('#statErrors').text(data.errors);
        });
    }
    
    $('#refreshBtn').click(function() {
        location.reload();
    });
    
    $('#exportBtn').click(function() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("system-logs.export") }}?' + params.toString();
    });
    
    $('.view-details').click(function() {
        const logId = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
        
        $('#logDetailsContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        modal.show();
        
        $.get('/system-logs/' + logId, function(response) {
            if (response.success) {
                const log = response.log;
                
                const severityBadge = log.severity === 'critical' || log.severity === 'error' ? 'danger' : 
                                     log.severity === 'warning' ? 'warning' : 
                                     log.severity === 'success' ? 'success' : 'info';
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th style="width: 40%;">Log ID</th>
                                    <td><strong>#${log.id}</strong></td>
                                </tr>
                                <tr>
                                    <th>Date & Time</th>
                                    <td>
                                        <div>${log.created_at}</div>
                                        <small class="text-muted">${log.created_at_human}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        <div><strong>${log.user}</strong></div>
                                        <small class="text-muted">${log.username}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action</th>
                                    <td><span class="badge bg-primary">${log.action.toUpperCase()}</span></td>
                                </tr>
                                <tr>
                                    <th>Module</th>
                                    <td><span class="badge bg-secondary">${log.module}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th style="width: 40%;">Severity</th>
                                    <td><span class="badge bg-${severityBadge}">${log.severity.toUpperCase()}</span></td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td><code>${log.ip_address || 'N/A'}</code></td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td><small class="text-muted">${(log.user_agent || 'N/A').substring(0, 50)}${(log.user_agent && log.user_agent.length > 50) ? '...' : ''}</small></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td colspan="2"><em>${log.description}</em></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
                
                // Show changes if any
                if ((log.old_values && Object.keys(log.old_values).length > 0) || 
                    (log.new_values && Object.keys(log.new_values).length > 0)) {
                    html += `<hr class="my-3"><h6 class="text-muted mb-3"><i class="bi bi-arrow-left-right"></i> Changes</h6>`;
                    html += `<div class="row">`;
                    
                    if (log.old_values && Object.keys(log.old_values).length > 0) {
                        html += `
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header py-2">
                                        <small class="fw-bold">Old Values</small>
                                    </div>
                                    <div class="card-body p-2">
                                        <pre class="mb-0 small" style="max-height: 200px; overflow-y: auto;">${JSON.stringify(log.old_values, null, 2)}</pre>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (log.new_values && Object.keys(log.new_values).length > 0) {
                        html += `
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header py-2">
                                        <small class="fw-bold">New Values</small>
                                    </div>
                                    <div class="card-body p-2">
                                        <pre class="mb-0 small" style="max-height: 200px; overflow-y: auto;">${JSON.stringify(log.new_values, null, 2)}</pre>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    html += `</div>`;
                }
                
                $('#logDetailsContent').html(html);
            }
        }).fail(function() {
            $('#logDetailsContent').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> Failed to load log details
                </div>
            `);
        });
    });
});
</script>
@endsection