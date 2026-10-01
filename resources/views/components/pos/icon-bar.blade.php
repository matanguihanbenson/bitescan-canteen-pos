<div class="icon-bar">
    <div class="icon-row">
        <div class="icon-btn" onclick="$('#productsModal').modal('show')">
            <i class="bi bi-box-seam"></i>
            <span>Products</span>
        </div>
        <div class="icon-btn" onclick="$('#menuModal').modal('show')">
            <i class="bi bi-list"></i>
            <span>Menu</span>
        </div>
        <div class="icon-btn" onclick="$('#discountModal').modal('show')">
            <i class="bi bi-percent"></i>
            <span>Discount</span>
        </div>
        <div class="icon-btn" onclick="clearOrder()">
            <i class="bi bi-x-circle"></i>
            <span>Clear Order</span>
        </div>
    </div>
    
</div>
<div class="receipt-buttons col-12">
        <div class="icon-btn col-6" onclick="showLastReceipt()">
            <i class="bi bi-eye"></i>
            <span>Show Receipt</span>
        </div>
        <div class="icon-btn col-6" onclick="printLastReceipt()">
            <i class="bi bi-printer"></i>
            <span>Print Receipt</span>
        </div>
    </div>
<script>
let lastReceiptData = null;

window.storeReceiptData = function(receiptData) {
    lastReceiptData = receiptData;
    sessionStorage.setItem('lastReceiptData', JSON.stringify(receiptData));
};

function showLastReceipt() {
    const receiptData = lastReceiptData || JSON.parse(sessionStorage.getItem('lastReceiptData'));
    
    if (!receiptData) {
        toastr.error('No recent receipt available');
        return;
    }

    const receiptParams = new URLSearchParams(receiptData).toString();
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
}

function printLastReceipt() {
    const receiptData = lastReceiptData || JSON.parse(sessionStorage.getItem('lastReceiptData'));
    
    if (!receiptData) {
        toastr.error('No recent receipt available');
        return;
    }

    const receiptParams = new URLSearchParams(receiptData).toString();
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
        receiptWindow.onload = function() {
            receiptWindow.print();
        };
    }
}
</script>

<style>
.icon-bar {
    display: flex;
    flex-direction: column;
    gap: 10px;
    
}
.receipt-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding-inline: 20px;
}
.receipt-buttons .icon-btn {
    width: 50%;
}
.icon-row {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    padding-inline: 20px;  
    margin-bottom: 10px;
}

.icon-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding:10px !important;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.2s;
    width: calc(25% - 8px); /* 4 buttons per row with gap consideration */
}

.icon-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.icon-btn i {
    font-size: 1.5rem;
    margin-bottom: 5px;
}

.icon-btn span {
    font-size: 0.8rem;
    white-space: nowrap;
}
</style>
