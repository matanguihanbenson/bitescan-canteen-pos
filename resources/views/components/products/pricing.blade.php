<div class="pricing-details">
    @if($row->inventory)
        <div class="d-flex flex-column">
            <div class="price-item mb-1">
                <small class="text-muted d-block mb-1">Base Price:</small>
                <span class="text-dark fw-bold">₱{{ number_format($row->inventory->base_price, 2) }}</span>
            </div>
            <div class="price-item">
                <small class="text-muted d-block mb-1">Selling Price:</small>
                <span class="text-success fw-bold">₱{{ number_format($row->inventory->selling_price, 2) }}</span>
            </div>
            @php
                $margin = $row->inventory->selling_price - $row->inventory->base_price;
                $marginPercentage = ($margin / $row->inventory->base_price) * 100;
            @endphp
            <div class="price-margin mt-1">
                <small class="text-muted">Margin: 
                    <span class="text-{{ $margin > 0 ? 'success' : 'danger' }} fw-semibold">
                        {{ number_format($marginPercentage, 1) }}%
                    </span>
                </small>
            </div>
        </div>
    @else
        <div class="text-center text-muted">
            <small>No pricing information available</small>
        </div>
    @endif
</div>

<style>
.pricing-details {
    padding: 8px;
    border-radius: 6px;
    background-color: #f8f9fa;
}

.price-item {
    line-height: 1.2;
}

.price-item small {
    font-size: 0.75rem;
}

.price-item span {
    font-size: 0.95rem;
}

.price-margin {
    font-size: 0.75rem;
    padding-top: 4px;
    border-top: 1px dashed #dee2e6;
}
</style>
