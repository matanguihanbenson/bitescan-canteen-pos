<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div class="payment-summary mb-4">
                    <div class="row mb-2">
                        <div class="col-6">Amount Due:</div>
                        <div class="col-6 text-end" id="modalAmountDue">₱0.00</div>
                    </div>
                    <div class="row align-items-center mb-3">
                        <div class="col-6">Payment Amount:</div>
                        <div class="col-6">
                            <div class="input-group">
                                <span class="input-group-text" style="height:40px;">₱</span>
                                <input type="text" class="form-control text-end" id="paymentAmount" inputmode="numeric" style="height:40px;">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNumpad" style="height:40px;">
                                    <i class="bi bi-calculator"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">Change:</div>
                        <div class="col-6 text-end" id="changeAmount">₱0.00</div>
                    </div>
                </div>

                <!-- Numpad -->
                <div class="numpad-container" style="display: none;">
                    <div class="row g-2 mb-2">
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">7</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">8</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">9</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">4</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">5</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">6</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">1</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">2</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">3</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">.</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100 numpad-btn">0</button></div>
                        <div class="col-4"><button class="btn btn-outline-secondary w-100" id="numpadClear">C</button></div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><button class="btn btn-secondary w-100" id="numpadClose">Close Numpad</button></div>
                        <div class="col-6"><button class="btn btn-primary w-100" id="numpadEnter">Enter</button></div>
                    </div>
                </div>

                <!-- Quick Amount Buttons -->
                <div class="quick-amounts mb-3">
                    <div class="row g-2">
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 quick-amount" data-amount="1">₱1</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 quick-amount" data-amount="10">₱10</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 quick-amount" data-amount="50">₱50</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 quick-amount" data-amount="100">₱100</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 quick-amount" data-amount="500">₱500</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-primary w-100 quick-amount" data-amount="1000">₱1000</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="completePayment" disabled onclick="processPayment()">Complete Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const paymentAmount = $('#paymentAmount');
    const completePaymentBtn = $('#completePayment');
    
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
        preventDuplicates: true,
    };

    function updateChange() {
        const amountDue = parseFloat($('#modalAmountDue').text().replace('₱', '')) || 0;
        const paid = parseFloat(paymentAmount.val()) || 0;
        const change = paid - amountDue;
        
        $('#changeAmount').text(`₱${Math.max(0, change).toFixed(2)}`);
        completePaymentBtn.prop('disabled', paid < amountDue);
    }

    $('.quick-amount').click(function() {
        const amount = parseFloat($(this).data('amount'));
        const currentAmount = parseFloat(paymentAmount.val()) || 0;
        paymentAmount.val((currentAmount + amount).toFixed(2));
        updateChange();
    });

    paymentAmount.on('input', updateChange);

    $('.numpad-btn').click(function() {
        const digit = $(this).text();
        const currentValue = paymentAmount.val();
        
        if (digit === '.' && currentValue.includes('.')) return;
        
        paymentAmount.val(currentValue + digit);
        updateChange();
    });

    $('#numpadClear').click(function() {
        paymentAmount.val('');
        updateChange();
    });

    $('#toggleNumpad').click(function() {
        $('.numpad-container').slideToggle();
        $('.quick-amounts').slideToggle();
    });

    $('#numpadClose').click(function() {
        $('.numpad-container').slideUp();
        $('.quick-amounts').slideDown();
    });

    $('#paymentModal').on('show.bs.modal', function() {
        paymentAmount.val('');
        updateChange();
        $('.numpad-container').hide();
        $('.quick-amounts').show();
    });

    function generateTransactionNumber() {
        const timestamp = moment().format('YYYYMMDD-HHmmss');
        const random = Math.floor(Math.random() * 1000000000).toString().padStart(9, '0');
        return `TRX-${timestamp}-${random}`;
    }

    window.processPayment = function() {
        const amountDue = parseFloat($('#modalAmountDue').text().replace('₱', ''));
        const paid = parseFloat($('#paymentAmount').val());
        const change = paid - amountDue;
        
        const transactionNumber = $('#transactionNumber').val();
        
        if (!transactionNumber) {
            toastr.error('Invalid transaction number');
            return;
        }

        if (!Array.isArray(orderItems) || orderItems.length === 0) {
            toastr.error('No items in cart');
            return;
        }

        if (paid >= amountDue) {
            const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            paymentModal.hide();

            const formattedItems = orderItems.map(item => ({
                code: item.code,
                name: item.name,
                quantity: parseFloat(item.quantity),
                unit_price: parseFloat(item.price),
                unit: item.unit,
                type: item.type,
                unitsPerGroup: item.unitsPerGroup,
                total_price: parseFloat(item.price) * parseFloat(item.quantity)
            }));

            const transactionData = {
                transaction_number: transactionNumber,
                items: JSON.stringify(formattedItems),
                subtotal: parseFloat($('.subtotal').text().replace('₱', '')),
                discount: parseFloat($('.discount').text().replace('₱', '')) || 0,
                total_amount: amountDue,
                amount_paid: paid,
                change_amount: change,
                payment_type: 'cash',
                status: 'completed'
            };
            
            if (window.scholarDiscount) {
                transactionData.scholar_id = window.scholarDiscount.scholar_id;
                transactionData.discount_type = window.scholarDiscount.discount_type;
                transactionData.is_scholar_transaction = true;
            }

            $.ajax({
                url: '/transactions/store',
                method: 'POST',
                data: transactionData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        processInventoryUpdates(orderItems)
                            .then(() => {
                                const receiptData = {
                                    transaction_number: transactionNumber,
                                    ...transactionData,
                                    total: amountDue,
                                    amountPaid: paid,
                                    change: change,
                                    items: formattedItems
                                };
                                handleTransactionSuccess(receiptData, paid, change);
                            })
                            .catch(handleTransactionError);
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Error processing transaction';
                    toastr.error(errorMessage);
                    $('#paymentModal').modal('show');
                }
            });
        } else {
            toastr.error('Insufficient payment amount');
        }
    };

    window.processCreditPayment = function(creditData) {
        const amountDue = parseFloat($('#modalAmountDue').text().replace('₱', ''));
        const transactionNumber = $('#transactionNumber').val();

        if (!transactionNumber) {
            toastr.error('Invalid transaction number');
            return;
        }

        if (!Array.isArray(orderItems) || orderItems.length === 0) {
            toastr.error('No items in cart');
            return;
        }

        const formattedItems = orderItems.map(item => ({
            code: item.code,
            name: item.name,
            quantity: parseFloat(item.quantity),
            unit_price: parseFloat(item.price),
            unit: item.unit,
            total_price: parseFloat(item.price) * parseFloat(item.quantity)
        }));

        const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
        paymentModal.hide();

        const transactionData = {
            transaction_number: transactionNumber,
            items: JSON.stringify(formattedItems),
            subtotal: parseFloat($('.subtotal').text().replace('₱', '')),
            discount: parseFloat($('.discount').text().replace('₱', '')) || 0,
            total_amount: amountDue,
            amount_paid: amountDue,
            change_amount: 0,
            payment_type: 'credit',
            status: 'completed',
            charge_to: creditData.chargeTo,
            charge_type: creditData.chargeType
        };
        
        if (window.scholarDiscount) {
            transactionData.scholar_id = window.scholarDiscount.scholar_id;
            transactionData.discount_type = window.scholarDiscount.discount_type;
            transactionData.is_scholar_transaction = true;
        }

        $.ajax({
            url: '/transactions/store',
            method: 'POST',
            data: transactionData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    processInventoryUpdates(orderItems)
                        .then(() => {
                            const receiptData = {
                                transaction_number: transactionNumber,
                                ...transactionData,
                                items: formattedItems
                            };
                            
                            window.storeReceiptData({
                                transaction_number: transactionNumber,
                                items: JSON.stringify(formattedItems),
                                amountPaid: amountDue.toFixed(2),
                                change: '0.00',
                                total: amountDue,
                                subtotal: transactionData.subtotal,
                                discount: transactionData.discount,
                                payment_type: 'credit',
                                charge_to: creditData.chargeTo,
                                charge_type: creditData.chargeType
                            });

                            handleTransactionSuccess(receiptData, amountDue, 0);
                            
                            window.generateNewTransactionNumber();
                        })
                        .catch(handleTransactionError);
                }
            },
            error: function(xhr) {
                handleTransactionError(xhr);
            }
        });
    };

    function processInventoryUpdates(items) {
        const updates = items.map(item => {
            return $.ajax({
                url: `/products/${item.code}/adjust-inventory`,
                method: 'POST',
                data: {
                    quantity: parseFloat(item.quantity),
                    type: item.type || 'piece',
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        return Promise.all(updates);
    }

    function handleTransactionSuccess(transactionData, paid, change) {
        window.storeReceiptData({
            transaction_number: transactionData.transaction_number,
            items: JSON.stringify(transactionData.items),
            amountPaid: paid.toFixed(2),
            change: change.toFixed(2),
            total: transactionData.total_amount,
            subtotal: transactionData.subtotal,
            discount: transactionData.discount,
            payment_type: transactionData.payment_type,
            charge_to: transactionData.charge_to,
            charge_type: transactionData.charge_type
        });

        const receiptParams = new URLSearchParams({
            transaction_number: transactionData.transaction_number,
            ...transactionData,
            items: JSON.stringify(transactionData.items),
            amountPaid: paid.toFixed(2),
            change: change.toFixed(2)
        }).toString();

        const width = 350;
        const height = 600;
        const left = Math.round((screen.width - width) / 2);
        const top = Math.round((screen.height - height) / 2);

        const receiptWindow = window.open(
            `/pos/receipt?${receiptParams}`,
            'Receipt',
            `width=${width},height=${height},top=${top},left=${left}`
        );

        if (receiptWindow) {
            receiptWindow.focus();
        }

        toastr.success(`
            Payment Successful!<br>
            Amount: ₱${paid.toFixed(2)}
            ${change > 0 ? `<br>Change: ₱${change.toFixed(2)}` : ''}
        `, 'Success');

        resetPOSInterface();
        
        window.generateNewTransactionNumber();
    }

    function handleTransactionError(error) {
        toastr.error(error.responseJSON?.message || 'Error processing transaction');
        $('#paymentModal').modal('show');
    }

    function resetPOSInterface() {
        orderItems = [];    
        updateOrderList();
        $('.subtotal').text('₱0.00');
        $('.discount').text('₱0.00');
        $('.amount-due').text('₱0.00');
        $('.grand-total').text('₱0.00');
        $('#modalAmountDue').text('₱0.00');
        
        window.scholarDiscount = null;
    }

    $('#paymentModal').on('hidden.bs.modal', function () {
        $('#paymentAmount').val('');
        $('#changeAmount').text('₱0.00');
        $('#completePayment').prop('disabled', true);
    });
});
</script>

<style>
/* Custom toastr styles */
.toast-success {
    background-color: #0d6efd !important;
}
.toast-error {
    background-color: #dc3545 !important;
}
#toast-container > div {
    opacity: 1;
    border-radius: 4px;
    padding: 15px 15px 15px 50px;
}
.toast-message {
    color: white !important;
}
.toast-title {
    font-weight: bold;
    color: white !important;
}
.toast-close-button {
    color: white;
    opacity: 0.7;
}
.toast-close-button:hover {
    color: white;
    opacity: 1;
}
.toast-progress {
    background-color: rgba(255, 255, 255, 0.3);
}
</style>