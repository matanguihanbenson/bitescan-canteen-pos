@extends('layouts.app')

@section('content')
<style>
    .pos-container { background: #1a1a1a; }
    .pos-main { background: #2a2a2a; box-shadow: 0 0 10px rgba(0,0,0,0.3); }
    .pos-sidebar { background: #333; }
    .table-header { border-bottom: 2px solid #007bff; padding: 12px 0; }
    .table-row { border-bottom: 1px solid #444; transition: background 0.2s; }
    .table-row:hover { background: rgba(255,255,255,0.05); }
    .input-group .form-control { background: #fff; border: 1px solid #555; color: black; }
    .input-group .form-control:focus {  border-color: #007bff; box-shadow: none; }
    .icon-btn { transition: all 0.3s; padding: 15px; background: rgba(0,0,0,0.1);}
    .icon-btn:hover {  cursor: pointer;}
    .icon-btn i { font-size: 1.5rem; }
    .btn-payment { transition: all 0.3s; border: 2px solid #555; color:white;}
    .btn-payment.active { background: #007bff; border-color: #007bff; }
    .btn-payment:hover:not(.active) { background: rgba(255,255,255,0.1); }
    .total-section { background: #252525; border-top: 2px solid #007bff; }
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .empty-state p {
        margin-bottom: 0;
        font-size: 1.1rem;
    }
    .popover {
        background-color: #333;
        color: white;
    }
    .popover-header {
        background-color: #2a2a2a;
        color: white;
        border-bottom: 1px solid #444;
    }
    .popover-body {
        color: #ddd;
    }
    .bs-popover-auto[data-popper-placement^=top]>.popover-arrow::after, 
    .bs-popover-top>.popover-arrow::after {
        border-top-color: #333;
    }
    .numpad-container {
        max-width: 300px;
        margin: 0 auto;
    }

    .numpad-btn, .quick-amount {
        height: 50px;
        font-size: 1.2rem;
    }

    #paymentAmount:focus {
        outline: none;
        box-shadow: none;
        border-color: #ced4da;
    }

    .payment-summary {
        font-size: 1.1rem;
    }

    .quick-amounts {
        margin-top: 1rem;
    }
</style>

<div class="d-flex pos-container" style="height:100vh;">
    <div class="flex-grow-1 d-flex flex-column pos-main">
        <div class="p-3 text-white flex-grow-1 overflow-auto">
            <div class="row fw-bold table-header">
                <div class="col-2">Item Code</div>
                <div class="col-4">Item Name</div>
                <div class="col-2">Price</div>
                <div class="col-2">Quantity</div>
                <div class="col-2">Subtotal</div>
            </div>
            @include('components.pos.order-items-list')
        </div>
        @include('components.pos.total-section')
    </div>
    <div class="d-flex flex-column pos-sidebar" style="width:30%;">
        <div class="p-3 text-white">
            @include('components.pos.input-group')
        </div>
        @include('components.pos.icon-bar')
        
        <div class="mt-auto p-3">
            @include('components.pos.payment-options')
            <button class="btn btn-primary w-100 py-3 fw-bold" id="completeOrderBtn">Complete Order</button>
        </div>
    </div>
</div>

@include('components.pos.credit-payment')
@include('components.pos.products-modal')
@include('components.pos.menu-modal')
@include('components.pos.discount-modal')
@include('components.pos.payment-modal')
@include('components.pos.unit-selection-modal')

<!-- POS Settings Integration -->
<script src="{{ asset('js/pos-settings-integration.js') }}"></script>

<script>

$(document).ready(function() {
    var paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    var unitSelectionModal = new bootstrap.Modal(document.getElementById('unitSelectionModal'));
    

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000
    };
    
    document.getElementById('completeOrderBtn').addEventListener('click', function() {
        if (!orderItems || orderItems.length === 0) {
            toastr.error('Please add items to the order before proceeding.', 'Empty Order');
            return;
        }

        if (document.getElementById('cashPaymentBtn').classList.contains('active')) {
            const modalAmountDue = document.getElementById('modalAmountDue');
            const totalAmountElement = document.querySelector('.grand-total');
            
            if (!totalAmountElement) {
                return;
            }
            
            if (!modalAmountDue) {
                return;
            }
            
            modalAmountDue.textContent = totalAmountElement.textContent;
            // Initialize payment input when showing modal
            const due = parseFloat(totalAmountElement.textContent.replace('₱', '')) || 0;
            const paymentAmountInput = document.getElementById('paymentAmount');
            if (paymentAmountInput) {
                paymentAmountInput.value = (due === 0 ? '0.00' : '');
                const event = new Event('input', { bubbles: true });
                paymentAmountInput.dispatchEvent(event);
            }
            paymentModal.show();
        }
    });

    window.clearOrder = function() {
        if (orderItems.length === 0) {
            toastr.info('Order is already empty');
            return;
        }
        
        if (confirm('Are you sure you want to clear all items from the order?')) {
            orderItems.forEach(item => {
                if (item.type !== 'menu') {
                    const stockLevel = window.getStockLevel(item.code);
                    if (stockLevel) {
                        let currentStock = stockLevel.current_stock;
                        let currentBaseStock = stockLevel.base_quantity;
                        
                        if (item.unitsPerGroup === 1) {
                            currentStock += item.quantity;
                        } else if (item.type === 'piece') {
                            currentBaseStock += item.quantity;
                            currentStock = Math.floor(currentBaseStock / item.unitsPerGroup);
                        } else {
                            currentStock += item.quantity;
                            currentBaseStock += (item.quantity * item.unitsPerGroup);
                        }
                        
                        window.updateStockLevel(item.code, currentStock, currentBaseStock);
                    }
                }
            });
            
            orderItems = [];
            updateOrderList();
            
            $('.subtotal').text('₱0.00');
            $('.discount').text('₱0.00');
            $('.vat-rate').text('0');
            $('.vat-amount').text('₱0.00');
            $('.amount-due').text('₱0.00');
            $('.grand-total').text('₱0.00');
            
            toastr.success('Order cleared successfully');
        }
    };
});
</script>
@endsection