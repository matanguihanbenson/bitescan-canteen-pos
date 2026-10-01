<div>
    <div class="mb-1">
        <small class="text-muted fw-bold"><i class="bi bi-tag-fill"></i> Selling Price:</small>
        <span class="fw-bold">₱{{ number_format($row->selling_price, 2) }}</span>
    </div>
    <div>
        <small class="text-muted"><i class="bi bi-tags"></i> Base Price:</small>
        <span>₱{{ number_format($row->base_price, 2) }}</span>
    </div>
</div>
