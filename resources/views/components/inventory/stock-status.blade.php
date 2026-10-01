<div>
    <h4 class="mb-2">
        {{ $row->current_stock }} <span class="small" style="font-size:14px;">{{ $row->unit }}</span>
        @php
            $groupUnits = ['pack', 'box', 'carton', 'bag', 'bundle', 'sack', 'dozen', 'half-dozen', 'pair', 'bottle', 'can', 'case', 'set'];
        @endphp
        @if ($row->base_quantity && in_array($row->unit, $groupUnits))
            <br>
            <span class="small text-muted" style="font-size:12px;">
                ({{ $row->base_quantity }} pcs)
            </span>
        @endif
    </h4>
    @php
        $stockStatus = 'danger';
        $statusText = 'Out of Stock';
        $icon = 'bi bi-x-circle';
        
        if ($row->current_stock > $row->min_stock) {
            $stockStatus = 'success';
            $statusText = 'High Stock';
            $icon = 'bi bi-check-circle';
        } elseif ($row->current_stock > 0) {
            $stockStatus = 'warning text-dark';
            $statusText = 'Low Stock';
            $icon = 'bi bi-exclamation-triangle';
        }
    @endphp
    <span class="badge bg-{{ $stockStatus }} mr-1 p-2" style="font-weight:normal;">
        <i class="{{ $icon }}" style="margin-right: 2px;"></i> {{ $statusText }}
    </span>
    <span class="badge bg-primary p-2" style="font-weight:normal;">
        <i class="bi bi-arrow-down-circle" style="margin-right: 2px;"></i> Min: {{ $row->min_stock }}
    </span>
</div>