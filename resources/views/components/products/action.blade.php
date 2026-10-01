<div class="btn-group">
    <button type="button" 
            class="btn btn-sm btn-outline-dark print-barcode" 
            data-id="{{ $row->id }}"
            data-product-code="{{ $row->product_code }}"
            onclick="openBarcodeModal('{{ $row->product_code }}')"
            title="Print Barcode">
        <i class="bi bi-upc-scan"></i>
    </button>
    
    <button type="button" 
        class="btn btn-sm btn-outline-primary manage-product" 
        data-id="{{ $row->id }}"
        onclick="openManageModal({{ $row->id }})"
        title="Manage Product">
        <i class="bi bi-gear"></i>
    </button>
    
    <button type="button" 
            class="btn btn-sm btn-outline-{{ $row->status === 'active' ? 'danger' : 'success' }} toggle-status" 
            onclick="toggleProductStatus({{ $row->id }}, '{{ $row->status }}')"
            data-id="{{ $row->id }}" 
            data-current-status="{{ $row->status }}"
            title="{{ $row->status === 'active' ? 'Deactivate' : 'Activate' }} Product">
        <i class="bi bi-{{ $row->status === 'active' ? 'toggle2-off' : 'toggle2-on' }}"></i>
    </button>
</div>
