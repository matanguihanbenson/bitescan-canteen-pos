<!-- Credit Payment Modal -->
<div class="modal fade" id="creditPaymentModal" tabindex="-1" aria-labelledby="creditPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditPaymentModalLabel">Credit Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div class="mb-3">
                    <label class="form-label">Charge to:</label>
                    <select class="form-select" id="chargeType" onchange="toggleChargeTypeForms()">
                        <option value="">Select type...</option>
                    </select>
                </div>
                
                <div id="chargeDetailsForm" class="mb-3" style="display: none;">
                    <div id="departmentForm" class="mb-3" style="display: none;">
                        <label class="form-label">Department:</label>
                        <select class="form-select" id="departmentSelect">
                            <option value="">Select department...</option>
                        </select>
                        <div id="customDepartmentInput" class="mt-2" style="display: none;">
                            <input type="text" class="form-control" id="customDepartmentName" placeholder="Enter department name">
                        </div>
                    </div>
                    
                    <div id="genericNameForm" class="mb-3" style="display: none;">
                        <label class="form-label" id="genericNameLabel">Name:</label>
                        <input type="text" class="form-control" id="genericName" placeholder="Enter name">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmCredit" onclick="confirmCreditPayment()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Credit Summary Modal -->
<div class="modal fade" id="creditSummaryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Credit Payment Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="credit-details mb-4">
                    <h6 class="border-bottom pb-2">Credit Information</h6>
                    <div class="row mb-2">
                        <div class="col-4">Charge Type:</div>
                        <div class="col-8" id="summaryChargeType">-</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4">Name:</div>
                        <div class="col-8" id="summaryName">-</div>
                    </div>
                </div>
                <div class="payment-summary">
                    <h6 class="border-bottom pb-2">Payment Details</h6>
                    <div class="row mb-2">
                        <div class="col-6">Subtotal:</div>
                        <div class="col-6 text-end" id="summaryCreditSubtotal">₱0.00</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">Discount:</div>
                        <div class="col-6 text-end" id="summaryCreditDiscount">₱0.00</div>
                    </div>
                    <div class="row fw-bold">
                        <div class="col-6">Total Amount:</div>
                        <div class="col-6 text-end" id="summaryCreditTotal">₱0.00</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="completeCreditPayment">Complete Credit Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handle department select change to show/hide custom input
$('#departmentSelect').on('change', function() {
    const value = $(this).val();
    if (value === 'custom') {
        $('#customDepartmentInput').show();
    } else {
        $('#customDepartmentInput').hide();
        $('#customDepartmentName').val('');
    }
});

function toggleChargeTypeForms() {
    const chargeType = $('#chargeType').val();
    
    if (!chargeType) {
        $('#chargeDetailsForm').hide();
        $('#departmentForm').hide();
        $('#genericNameForm').hide();
        return;
    }
    
    $('#chargeDetailsForm').show();
    
    if (chargeType === 'department') {
        $('#departmentForm').show();
        $('#genericNameForm').hide();
    } else {
        $('#departmentForm').hide();
        $('#genericNameForm').show();
        
        // Update label based on charge type
        const label = chargeType.charAt(0).toUpperCase() + chargeType.slice(1) + ' Name:';
        $('#genericNameLabel').text(label);
        $('#genericName').attr('placeholder', 'Enter ' + chargeType + ' name');
    }
}

function confirmCreditPayment() {
    const chargeType = $('#chargeType').val();
    if (!chargeType) {
        toastr.error('Please select a charge type');
        return;
    }

    let chargeTo = '';
    
    if (chargeType === 'department') {
        const deptValue = $('#departmentSelect').val();
        if (!deptValue) {
            toastr.error('Please select department');
            return;
        }
        
        if (deptValue === 'custom') {
            chargeTo = $('#customDepartmentName').val().trim();
            if (!chargeTo) {
                toastr.error('Please enter custom department name');
                return;
            }
        } else {
            chargeTo = deptValue;
        }
    } else {
        chargeTo = $('#genericName').val().trim();
        if (!chargeTo) {
            toastr.error('Please enter ' + chargeType + ' name');
            return;
        }
    }

    $('#summaryChargeType').text(chargeType.charAt(0).toUpperCase() + chargeType.slice(1));
    $('#summaryName').text(chargeTo);

    const subtotal = parseFloat($('.subtotal').text().replace('₱', '')) || 0;
    const discount = parseFloat($('.discount').text().replace('₱', '')) || 0;
    const total = parseFloat($('.amount-due').text().replace('₱', '')) || 0;

    $('#summaryCreditSubtotal').text(`₱${subtotal.toFixed(2)}`);
    $('#summaryCreditDiscount').text(`₱${discount.toFixed(2)}`);
    $('#summaryCreditTotal').text(`₱${total.toFixed(2)}`);

    $('#creditSummaryModal').modal('show');
    $('#creditPaymentModal').modal('hide');
}

document.getElementById('completeCreditPayment').addEventListener('click', function() {
    const chargeType = $('#chargeType').val();
    let chargeTo = '';
    
    if (chargeType === 'department') {
        const deptValue = $('#departmentSelect').val();
        if (deptValue === 'custom') {
            chargeTo = $('#customDepartmentName').val().trim();
        } else {
            chargeTo = deptValue;
        }
    } else {
        chargeTo = $('#genericName').val().trim();
    }

    const transactionData = {
        transaction_number: $('#transactionNumber').val(),
        items: JSON.stringify(orderItems.map(item => ({
            ...item,
            unit: item.unit || 'piece',
            type: item.type || 'piece'
        }))),
        subtotal: parseFloat($('.subtotal').text().replace('₱', '')),
        discount: parseFloat($('.discount').text().replace('₱', '')),
        total_amount: parseFloat($('.amount-due').text().replace('₱', '')),
        amount_paid: parseFloat($('.amount-due').text().replace('₱', '')),
        change_amount: 0,
        payment_type: 'credit',
        charge_type: chargeType,
        charge_to: chargeTo,
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
        url: '/transactions/store',
        method: 'POST',
        data: transactionData
    })
    .then(response => {
        if (response.success) {
            const receiptData = {
                transaction_number: $('#transactionNumber').val(),
                items: JSON.stringify(orderItems),
                subtotal: parseFloat($('.subtotal').text().replace('₱', '')),
                discount: parseFloat($('.discount').text().replace('₱', '')),
                total_amount: parseFloat($('.amount-due').text().replace('₱', '')),
                payment_type: 'credit',
                charge_type: chargeType,
                charge_to: chargeTo
            };
            
            window.storeReceiptData(receiptData);

            const receiptParams = new URLSearchParams(receiptData).toString();
            const receiptWindow = window.open(
                `/pos/receipt?${receiptParams}`,
                'Receipt',
                `width=400,height=600,top=${(screen.height-600)/2},left=${(screen.width-400)/2}`
            );

            toastr.success('Credit payment completed successfully');
            
            orderItems = [];
            updateOrderList();
            $('.subtotal').text('₱0.00');
            $('.discount').text('₱0.00');
            $('.vat-rate').text('0');
            $('.vat-amount').text('₱0.00');
            $('.amount-due').text('₱0.00');
            $('.grand-total').text('₱0.00');
            $('#modalAmountDue').text('₱0.00');

            $('#creditSummaryModal').modal('hide');

            window.generateNewTransactionNumber();
        }

        
    })
    .catch(error => {
        toastr.error('Error processing transaction: ' + (error.responseJSON?.message || 'Unknown error'));
    });
});

$('#creditPaymentModal, #creditSummaryModal').on('hidden.bs.modal', function () {
    $('#creditPaymentBtn').removeClass('active');
    $('#cashPaymentBtn').addClass('active');
    
    if ($(this).attr('id') === 'creditPaymentModal') {
        $('#chargeType').val('');
        $('#chargeDetailsForm').hide();
        $('#departmentForm').hide();
        $('#genericNameForm').hide();
        $('#genericName').val('');
        $('#departmentSelect').val('');
        $('#customDepartmentName').val('');
        $('#customDepartmentInput').hide();
    }
});

$('.btn-close, .btn-secondary[data-bs-dismiss="modal"]').on('click', function() {
    $('#creditPaymentBtn').removeClass('active');
    $('#cashPaymentBtn').addClass('active');
});

$(document).ready(function() {
    $('#creditPaymentModal, #creditSummaryModal').attr('data-bs-backdrop', 'true');
    
    $(document).on('click', '.modal-backdrop', function() {
        $('#creditPaymentBtn').removeClass('active');
        $('#cashPaymentBtn').addClass('active');
    });

    $('#creditPaymentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            products: orderItems.map(item => ({
                product_code: item.code,
                quantity: item.quantity,
                type: item.type,
                units_per_group: item.unitsPerGroup
            }))
        };

        $.ajax({
            url: '/transactions/credit',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    const stockUpdates = orderItems.map(item => {
                        const stockLevel = window.getStockLevel(item.code);
                        return $.ajax({
                            url: '/inventory/update-stock',
                            method: 'POST',
                            data: {
                                product_code: item.code,
                                current_stock: stockLevel.current_stock,
                                base_quantity: stockLevel.base_quantity,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    });

                    Promise.all(stockUpdates)
                        .then(() => {
                            toastr.success('Transaction completed successfully');
                            orderItems = [];
                            updateOrderList();
                            $('#creditPaymentModal').modal('hide');
                            $('#creditPaymentForm')[0].reset();
                        })
                        .catch(error => {
                            toastr.error('Error updating stock levels');
                        });
                } else {
                    toastr.error(response.message || 'Transaction failed');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error processing transaction');
            }
        });
    });
});
</script>

<style>
.credit-details, .payment-summary {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
}

.modal-body h6 {
    color: #495057;
    margin-bottom: 1rem;
}

.credit-details .row, .payment-summary .row {
    margin: 0;
    padding: 0.25rem 0;
}

.fw-bold {
    padding-top: 0.5rem;
    margin-top: 0.5rem;
    border-top: 1px solid #dee2e6;
}
</style>
