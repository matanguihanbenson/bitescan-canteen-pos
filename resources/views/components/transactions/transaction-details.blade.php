@php
    $data = json_decode($row);
@endphp
<div>
    <div class="fw-bold">{{ $data->transaction_number }}</div>
    <small class="text-muted">{{ \Carbon\Carbon::parse($data->created_at)->format('M d, Y h:i A') }}</small>
</div>