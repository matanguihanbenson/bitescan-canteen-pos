<div id="orderItemsList">
    <div class="empty-state" id="emptyOrderState">
        <i class="bi bi-cart text-white"></i>
        <p>No items in order</p>
    </div>
    <div id="orderItems">
    </div>
</div>

<script>
let orderItems = [];

window.addToOrder = function(productData) {
    if (productData.type === 'menu') {
        const orderKey = `${productData.product_code}-${productData.type}`;
        const existingItem = orderItems.find(item => item.orderKey === orderKey);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            orderItems.push({
                orderKey,
                code: productData.product_code,
                name: productData.product_name,
                price: productData.unit_price,
                quantity: 1,
                unit: 'piece',
                type: 'menu'
            });
        }
        
        updateOrderList();
        return;
    }

    if (!productData || !productData.product_code) {
        toastr.error('Invalid product data');
        return;
    }

    try {
        const $productCard = $(`#card-${productData.product_code}`);
        let originalStockLevel = null;
        
        if ($productCard.length) {
            originalStockLevel = {
                current_stock: parseInt($productCard.attr('data-original-current-stock') || $productCard.data('current-stock')) || 0,
                base_quantity: parseInt($productCard.attr('data-original-base-quantity') || $productCard.data('base-quantity')) || 0
            };
            
            if (!$productCard.attr('data-original-current-stock')) {
                $productCard.attr('data-original-current-stock', originalStockLevel.current_stock);
                $productCard.attr('data-original-base-quantity', originalStockLevel.base_quantity);
            }
        }
        
        const orderKey = `${productData.product_code}-${productData.type}`;
        const existingItemIndex = orderItems.findIndex(item => item.orderKey === orderKey);
        const currentQuantityInOrder = existingItemIndex >= 0 ? orderItems[existingItemIndex].quantity : 0;
        
        const allItemsOfProduct = orderItems.filter(item => item.code === productData.product_code);
        let totalGroupsInOrder = 0;
        let totalPiecesInOrder = 0;
        
        allItemsOfProduct.forEach(item => {
            if (item.type === 'group') {
                totalGroupsInOrder += item.quantity;
                totalPiecesInOrder += item.quantity * (item.units_per_group || 1);
            } else if (item.type === 'piece') {
                totalPiecesInOrder += item.quantity;
            }
        });
        
        if (originalStockLevel) {
            if (productData.type === 'group') {
                const originalAvailableGroups = originalStockLevel.current_stock;
                const newTotalGroups = totalGroupsInOrder + 1;
                if (newTotalGroups > originalAvailableGroups) {
                    toastr.error(`Not enough ${productData.unit || 'units'}s available. Only ${originalAvailableGroups} in stock, ${totalGroupsInOrder} already in order.`);
                    return;
                }
            } else if (productData.type === 'piece') {
                const originalAvailablePieces = originalStockLevel.base_quantity;
                const newTotalPieces = totalPiecesInOrder + 1;
                if (newTotalPieces > originalAvailablePieces) {
                    toastr.error(`Not enough pieces available. Only ${originalAvailablePieces} pieces in stock, ${totalPiecesInOrder} already in order.`);
                    return;
                }
            }
        }
        
        if (existingItemIndex >= 0) {
            orderItems[existingItemIndex].quantity++;
        } else {
            orderItems.push({
                orderKey,
                code: productData.product_code,
                name: productData.product_name,
                price: productData.unit_price,
                quantity: 1,
                type: productData.type,
                unit: productData.unit,
                units_per_group: productData.units_per_group || 1
            });
        }

        if (originalStockLevel) {
            const allItemsOfProduct = orderItems.filter(item => item.code === productData.product_code);
            let totalGroupsInOrder = 0;
            let totalPiecesInOrder = 0;
            
            allItemsOfProduct.forEach(item => {
                if (item.type === 'group') {
                    totalGroupsInOrder += item.quantity;
                    totalPiecesInOrder += item.quantity * (item.units_per_group || 1);
                } else if (item.type === 'piece') {
                    totalPiecesInOrder += item.quantity;
                }
            });
            
            const remainingCurrentStock = Math.max(0, originalStockLevel.current_stock - totalGroupsInOrder);
            const remainingBaseQuantity = Math.max(0, originalStockLevel.base_quantity - totalPiecesInOrder);
            
            window.updateStockLevel(productData.product_code, remainingCurrentStock, remainingBaseQuantity);
            
            updateProductCardDisplay(productData.product_code, remainingCurrentStock, remainingBaseQuantity);
        }

        updateOrderList();
        toastr.success(`${productData.product_name} added to order`);

    } catch (error) {
        toastr.error('Error adding item to order');
    }
};

function updateLocalStockLevels(productData, stockLevel) {
    let newCurrentStock = stockLevel.current_stock;
    let newBaseQuantity = stockLevel.base_quantity;
    
    if (productData.units_per_group === 1 || !productData.units_per_group) {
        newCurrentStock--;
    } else if (productData.type === 'group') {
        newCurrentStock--;
        newBaseQuantity -= productData.units_per_group;
    } else {
        newBaseQuantity--;
        newCurrentStock = Math.floor(newBaseQuantity / productData.units_per_group);
    }
    
    window.updateStockLevel(productData.product_code, Math.max(0, newCurrentStock), Math.max(0, newBaseQuantity));
};


function updateOrderList() {
    const container = $('#orderItems');
    const emptyState = $('#emptyOrderState');
    container.empty();
    
    if (orderItems.length === 0) {
        emptyState.show();
        return;
    }
    
    emptyState.hide();
    orderItems.forEach((item, index) => {
        let quantityDisplay;
        if (item.type === 'menu') {
            quantityDisplay = `${item.quantity} piece`;
        } else if (item.type === 'group') {
            quantityDisplay = `${item.quantity} ${item.unit || 'unit'}`;
        } else {
            quantityDisplay = `${item.quantity} piece`;
        }

        container.append(`
            <div class="row table-row py-2 align-items-center">
                <div class="col-2"><i class="bi bi-upc-scan me-1"></i>${item.code}</div>
                <div class="col-3">${item.name}</div>
                <div class="col-2">₱${parseFloat(item.price).toFixed(2)}/${item.type}</div>
                <div class="col-3">
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, -1)">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="text" class="form-control text-center" value="${quantityDisplay}" readonly>
                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, 1)">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="col-2">
                    ₱${(item.price * item.quantity).toFixed(2)}
                    <button class="btn btn-sm btn-danger float-end" onclick="removeItem(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `);
    });
    
    updateTotal();
}

function updateQuantity(index, change) {
    if (!orderItems[index]) return;

    const item = orderItems[index];
    
    if (item.type === 'menu') {
        if (change > 0) {
            item.quantity++;
        } else {
            if (item.quantity <= 1) {
                removeItem(index);
                return;
            }
            item.quantity--;
        }
        updateOrderList();
        return;
    }
    
    const $productCard = $(`#card-${item.code}`);
    let originalStockLevel = null;
    
    if ($productCard.length) {
        originalStockLevel = {
            current_stock: parseInt($productCard.attr('data-original-current-stock')) || 0,
            base_quantity: parseInt($productCard.attr('data-original-base-quantity')) || 0
        };
    }
    
    if (!originalStockLevel) {
        if (change > 0) {
            item.quantity++;
        } else {
            if (item.quantity <= 1) {
                removeItem(index);
                return;
            }
            item.quantity--;
        }
        updateOrderList();
        return;
    }

    if (change > 0) {
        const allItemsOfProduct = orderItems.filter(orderItem => orderItem.code === item.code);
        let totalGroupsInOrder = 0;
        let totalPiecesInOrder = 0;
        
        allItemsOfProduct.forEach(orderItem => {
            if (orderItem.type === 'group') {
                totalGroupsInOrder += orderItem.quantity;
                totalPiecesInOrder += orderItem.quantity * (orderItem.units_per_group || 1);
            } else if (orderItem.type === 'piece') {
                totalPiecesInOrder += orderItem.quantity;
            }
        });
        
        if (item.type === 'group') {
            const newTotalGroups = totalGroupsInOrder + 1;
            if (newTotalGroups > originalStockLevel.current_stock) {
                toastr.error(`Not enough ${item.unit || 'units'}s available. Only ${originalStockLevel.current_stock} in stock, ${totalGroupsInOrder} already in order.`);
                return;
            }
        } else if (item.type === 'piece') {
            const newTotalPieces = totalPiecesInOrder + 1;
            if (newTotalPieces > originalStockLevel.base_quantity) {
                toastr.error(`Not enough pieces available. Only ${originalStockLevel.base_quantity} pieces in stock, ${totalPiecesInOrder} already in order.`);
                return;
            }
        }
    }

    const currentUIStockLevel = window.getStockLevel(item.code) || originalStockLevel;
    let newCurrentStock = currentUIStockLevel.current_stock;
    let newBaseQuantity = currentUIStockLevel.base_quantity;

    if (change > 0) {
        item.quantity++;
        if (item.units_per_group === 1) {
            newCurrentStock--;
        } else if (item.type === 'group') {
            newCurrentStock--;
            newBaseQuantity -= item.units_per_group;
        } else {
            newBaseQuantity--;
            newCurrentStock = Math.floor(newBaseQuantity / item.units_per_group);
        }
    } else {
        if (item.quantity <= 1) {
            removeItem(index);
            return;
        }

        item.quantity--;
        if (item.units_per_group === 1) {
            newCurrentStock++;
        } else if (item.type === 'group') {
            newCurrentStock++;
            newBaseQuantity += item.units_per_group;
        } else {
            newBaseQuantity++;
            newCurrentStock = Math.floor(newBaseQuantity / item.units_per_group);
        }
    }

    window.updateStockLevel(item.code, newCurrentStock, newBaseQuantity);
    
    updateOrderList();
    updateProductCardDisplay(item.code, newCurrentStock, newBaseQuantity);
}

function updateProductCardDisplay(productCode, currentStock, baseQuantity) {
    const $productCard = $(`#card-${productCode}`);
    if ($productCard.length) {
        $productCard
            .data('current-stock', currentStock)
            .data('base-quantity', baseQuantity)
            .find('.current-stock').text(currentStock)
            .end()
            .find('.base-quantity').text(baseQuantity);

        const $groupButton = $productCard.find('.btn-primary');
        const $pieceButton = $productCard.find('.btn-outline-primary');

        if (currentStock <= 0) {
            $groupButton.prop('disabled', true).addClass('disabled');
        } else {
            $groupButton.prop('disabled', false).removeClass('disabled');
        }

        if (baseQuantity <= 0) {
            $pieceButton.prop('disabled', true).addClass('disabled');
        } else {
            $pieceButton.prop('disabled', false).removeClass('disabled');
        }
    }
}

function removeItem(index) {
    const item = orderItems[index];
    
    if (item.type === 'menu') {
        orderItems.splice(index, 1);
        updateOrderList();
        updateTotal();
        
        if (orderItems.length === 0) {
            $('#emptyOrderState').show();
            $('.subtotal').text('₱0.00');
            $('.discount').text('₱0.00');
            $('.vat-rate').text('0');
            $('.vat-amount').text('₱0.00');
            $('.amount-due').text('₱0.00');
            $('.grand-total').text('₱0.00');
        }
        return;
    }
    
    const stockLevel = window.getStockLevel(item.code);
    
    if (!stockLevel) {
        orderItems.splice(index, 1);
        updateOrderList();
        updateTotal();
        
        if (orderItems.length === 0) {
            $('#emptyOrderState').show();
            $('.subtotal').text('₱0.00');
            $('.discount').text('₱0.00');
            $('.vat-rate').text('0');
            $('.vat-amount').text('₱0.00');
            $('.amount-due').text('₱0.00');
            $('.grand-total').text('₱0.00');
        }
        return;
    }

    let currentStock = stockLevel.current_stock;
    let currentBaseStock = stockLevel.base_quantity;
    
    if (item.units_per_group === 1) {
        currentStock += item.quantity;
    } else {
        if (item.type === 'piece') {
            currentBaseStock += item.quantity;
            currentStock = Math.floor(currentBaseStock / item.units_per_group);
        } else {
            currentStock += item.quantity;
            currentBaseStock += (item.quantity * item.units_per_group);
        }
    }
    
    window.updateStockLevel(item.code, currentStock, currentBaseStock);
    
    const safeProductCode = String(item.code).replace(/[^a-zA-Z0-9-_]/g, '');
    const $productCard = $(`#card-${safeProductCode}`);
    if ($productCard.length) {
        $productCard
            .data('current-stock', currentStock)
            .data('base-quantity', currentBaseStock)
            .find('.current-stock').text(currentStock).end()
            .find('.base-quantity').text(currentBaseStock);

        $productCard.parent().fadeIn();
    }
    
    orderItems.splice(index, 1);
    
    updateOrderList();
    updateTotal();

    if (orderItems.length === 0) {
        $('#emptyOrderState').show();
        $('.subtotal').text('₱0.00');
        $('.discount').text('₱0.00');
        $('.vat-rate').text('0');
        $('.vat-amount').text('₱0.00');
        $('.amount-due').text('₱0.00');
        $('.grand-total').text('₱0.00');
    }
}

function updateTotal() {
    const subtotal = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const currentDiscount = parseFloat($('.discount').text().replace('₱', '') || 0);
    const amountAfterDiscount = subtotal - currentDiscount;
    
    // Calculate VAT using the helper function from pos-settings-integration.js
    let vatResult;
    if (window.calculateTotalWithTax) {
        vatResult = window.calculateTotalWithTax(amountAfterDiscount);
    } else {
        // Fallback if the integration script hasn't loaded yet
        vatResult = {
            subtotal: amountAfterDiscount.toFixed(2),
            tax: '0.00',
            taxRate: 0,
            total: amountAfterDiscount.toFixed(2)
        };
    }
    
    $('.subtotal').text(`₱${subtotal.toFixed(2)}`);
    $('.vat-rate').text(vatResult.taxRate);
    $('.vat-amount').text(`₱${vatResult.tax}`);
    $('.amount-due').text(`₱${vatResult.total}`);
    $('.grand-total').text(`₱${vatResult.total}`);
}
</script>

<style>
#subtotalSection {
    background-color: #2a2a2a;
    border-color: #444 !important;
}
.subtotal {
    color: #fff;
}
</style>
