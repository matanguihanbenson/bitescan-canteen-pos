@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="btn-group">
    <button type="button" 
            class="btn btn-sm btn-outline-dark print-barcode" 
            data-code="{{ $row->menu_code }}"
            onclick="openBarcodeModal('{{ $row->menu_code }}')"
            title="Print Barcode">
        <i class="bi bi-upc-scan"></i>
    </button>
    <button class="btn btn-sm btn-outline-primary edit-menu-btn" 
        data-bs-toggle="modal" 
        data-bs-target="#editMenuModal"
        data-id="{{ $row->id }}"
        data-all="{{ json_encode([
            'id' => $row->id,
            'name' => $row->name,
            'price' => $row->price,
            'description' => $row->description,
            'status' => $row->status,
            'category_id' => optional($row->category)->id,
            'subcategory_id' => optional($row->subcategory)->id,
            'image_url' => $row->image_path ? Storage::disk('public')->url($row->image_path) : null
        ]) }}">
        <i class="bi bi-pencil"></i>
    </button>
    <button class="btn btn-sm btn-outline-warning archive-menu" 
        data-id="{{ $row->id }}"
        data-name="{{ $row->name }}"
        title="Archive Item">
        <i class="bi bi-archive"></i>
    </button>
</div>
