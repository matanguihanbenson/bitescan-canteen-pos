<div class="total-section p-3 text-white">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>Subtotal</div>
        <div class="subtotal">₱{{ number_format($subtotal ?? 0, 2) }}</div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>Discount</div>
        <div class="discount">₱{{ number_format($discount ?? 0, 2) }}</div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>VAT (<span class="vat-rate">0</span>%)</div>
        <div class="vat-amount">₱0.00</div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>Amount Due</div>
        <div class="amount-due">₱{{ number_format($amountDue ?? 0, 2) }}</div>
    </div>
    <div class="d-flex justify-content-between align-items-center border-top pt-3">
        <div>
            <h5 class="mb-0">Total Amount</h5>
            <small class="text-white">Including VAT</small>
        </div>
        <h3 class="mb-0 grand-total">₱{{ number_format($total ?? 0, 2) }}</h3>
    </div>
</div>
