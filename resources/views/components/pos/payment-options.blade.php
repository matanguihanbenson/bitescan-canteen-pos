<div class="d-flex flex-column mb-3 text-white">
    <p class="mb-2 fs-5">Payment Options</p>
    <div class="row">
        <div class="col-6">
            <button class="btn btn-payment w-100 mb-2 active" type="button" id="cashPaymentBtn">Cash</button>
        </div>
        <div class="col-6">
            <button class="btn btn-payment w-100 mb-2" type="button" id="creditPaymentBtn" 
                    data-bs-toggle="popover" 
                    data-bs-trigger="hover focus"
                    data-bs-placement="top"
                    data-bs-html="true"
                    data-bs-content=""
                    onclick="selectCreditPayment()">Credit</button>
        </div>
    </div>
</div>

<script>
function selectCreditPayment() {
    if (orderItems.length === 0) {
        toastr.error('Please add items to your order before selecting credit payment.', 'Empty Order', {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000,
            extendedTimeOut: 1000,
            preventDuplicates: true,
            toastClass: "bg-danger text-white"
        });
        document.getElementById('creditPaymentBtn').classList.remove('active');
        document.getElementById('cashPaymentBtn').classList.add('active');
        return;
    }
    
    document.getElementById('cashPaymentBtn').classList.remove('active');
    document.getElementById('creditPaymentBtn').classList.add('active');
    $('#creditPaymentModal').modal('show');
}

document.getElementById('cashPaymentBtn').addEventListener('click', function() {
    document.getElementById('creditPaymentBtn').classList.remove('active');
    this.classList.add('active');
});
</script>

<style>
.toast-error {
    background-color: #dc3545 !important;
}
</style>
