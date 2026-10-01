@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="d-flex align-items-center">
    <img src="{{ $row->product->product_image && Storage::disk('public')->exists($row->product->product_image) 
            ? asset('storage/' . $row->product->product_image) 
            : asset('images/no-image.png') }}" 
         class="border p-2 mr-2" 
         width="40" 
         height="40"
         alt="{{ $row->product->name ?? 'No Image' }}">
    <div style="margin-left: 10px;line-height: 1.2;">
        <small class="text-muted d-block">Code: {{ $row->product->product_code }}</small>
        <span class="fw-bold">{{ $row->product->product_name }}</span>
    </div>
</div>
