<div class="d-flex align-items-center">
    <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
        <i class="bi bi-person text-primary"></i>
    </div>
    <div>
        <div class="fw-medium">{{ $row->firstname }} {{ $row->lastname }}</div>
        <div class="text-muted small">{{ $row->email }}</div>
    </div>
</div>