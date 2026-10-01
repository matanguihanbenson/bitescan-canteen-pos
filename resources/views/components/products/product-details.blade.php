@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="d-flex align-items-start">
    <div class="product-image me-3">
        <img src="{{ $row->product_image && Storage::disk('public')->exists($row->product_image) 
                ? asset('storage/' . $row->product_image) 
                : asset('images/no-image.png') }}" 
             alt="{{ $row->product_name }}" 
             class="border p-2"
             style="width: 40px; height: 40px; object-fit: cover;">
    </div>
    <div class="product-info" style="line-height: 1.2;">
        <div class="product-code text-muted small">Code: {{ $row->product_code }}</div>
        <div class="product-name fw-bold">{{ $row->product_name }}</div>
    </div>
</div>
