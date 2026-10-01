@php
    $data = json_decode($row);
@endphp
<div>
    <div class="text-muted small">Processed by:</div>
    <div class="fw-semibold">{{ $data->processed_by }}</div>
</div>