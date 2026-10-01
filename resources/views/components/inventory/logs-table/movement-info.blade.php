<div class="d-flex flex-column">
    <div class="mb-1">
        <span class="font-weight-bold">{{ $quantity }}</span>
        <span class="text-muted">{{ $unit }}</span>
    </div>
    @php
        $badgeClass = match($type) {
            'in' => 'primary',
            'out' => 'error',
            default => 'warning'
        };
    @endphp
    <span class="badge bg-{{ $badgeClass }}">{{ strtoupper($type) }}</span>
</div>
