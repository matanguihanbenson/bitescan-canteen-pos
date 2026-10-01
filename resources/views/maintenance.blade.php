@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-{{ $maintenance && $maintenance->is_maintenance ? 'danger' : 'success' }} text-white">
                    <h4 class="mb-0">System Status: 
                        @if($maintenance && $maintenance->is_maintenance)
                            <span class="badge bg-white text-danger">MAINTENANCE MODE</span>
                        @else
                            <span class="badge bg-white text-success">OPERATIONAL</span>
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    @if($maintenance && $maintenance->admin_notes)
                        <div class="alert alert-info">
                            <strong>Current Notice:</strong><br>
                            {{ $maintenance->admin_notes }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Maintenance Status</h5>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{ route('toggleMaintenance') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>Set System Status:</strong></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="is_maintenance" value="0" id="operational" {{ (!$maintenance || !$maintenance->is_maintenance) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="operational">Operational</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="is_maintenance" value="1" id="maintenance" {{ ($maintenance && $maintenance->is_maintenance) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance">Maintenance Mode</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="admin_notes" class="form-label"><strong>Maintenance Notice:</strong></label>
                            <textarea name="admin_notes" id="admin_notes" rows="4" class="form-control" placeholder="Enter a message to display during maintenance...">{{ $maintenance ? $maintenance->admin_notes : '' }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection