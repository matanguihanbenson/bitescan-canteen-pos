@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="d-flex align-items-center">
    <div class="product-image me-3">
        <img src="{{ $row->image_path && Storage::disk('public')->exists($row->image_path) 
                ? asset('storage/' . $row->image_path) 
                : asset('images/menu.png') }}" 
             alt="{{ $row->name }}" 
             class="border p-2"
             style="width: 40px; height: 40px; object-fit: cover;">
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <div class="text-muted small">
                <i class="bi bi-upc me-1"></i>{{ $row->menu_code }}
            </div>
            
        </div>
        <div class="fw-bold">{{ $row->name }}</div>
        <small class="text-muted">{{ $row->description ?? 'No description' }}</small>
    </div>
</div>
