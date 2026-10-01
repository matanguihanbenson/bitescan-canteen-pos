<div class="btn-group">
    <button type="button" class="btn btn-sm btn-primary edit-menu-btn" data-bs-toggle="modal" data-bs-target="#editMenuModal" data-id="{{ $menu->id }}" title="Edit">
        <i class="bi bi-pencil"></i>
    </button>
    <button type="button" class="btn btn-sm btn-warning archive-menu" data-id="{{ $menu->id }}" title="Archive">
        <i class="bi bi-archive"></i>
    </button>
    <button type="button" class="btn btn-sm btn-info print-barcode" 
            data-code="{{ $menu->menu_code }}" 
            data-name="{{ $menu->name }}"
            title="Print Barcode">
        <i class="bi bi-printer"></i>
    </button>
</div>
