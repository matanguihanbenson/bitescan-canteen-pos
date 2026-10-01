<div class="btn-group">
    <button type="button" class="btn btn-sm btn-success stock-in" 
            data-id="{{ $row->id }}" 
            data-bs-toggle="tooltip" 
            title="Stock In">
        <i class="bi bi-box-arrow-in-down"></i>
    </button>
    
    <button type="button" class="btn btn-sm btn-warning stock-out" 
            data-id="{{ $row->id }}" 
            data-bs-toggle="tooltip" 
            title="Stock Out">
        <i class="bi bi-box-arrow-up"></i>
    </button>
    
    <button type="button" class="btn btn-sm btn-info stock-adjust" 
            data-id="{{ $row->id }}" 
            data-bs-toggle="tooltip" 
            title="Stock Adjustment">
        <i class="bi bi-sliders"></i>
    </button>
    
    <button type="button" class="btn btn-sm btn-secondary stock-history" 
            data-id="{{ $row->id }}" 
            data-bs-toggle="tooltip" 
            title="Stock History">
        <i class="bi bi-clock-history"></i>
    </button>
</div>