<div class="transaction-header mb-3">
    <div class="d-flex align-items-center bg-light rounded p-3 border">
        <div class="flex-grow-1">
            <label class="small text-muted mb-1">Transaction Reference</label>
            <div class="input-group">
                <span class="input-group-text border-primary bg-primary text-white" style="min-width: 45px;">
                    <i class="bi bi-upc-scan"></i>
                </span>
                <input type="text" 
                    class="form-control form-control-lg border-primary bg-white" 
                    id="transactionNumber" 
                    readonly 
                    value="{{ 'TRX-' . date('Ymd') . '-' . str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT) }}" 
                    style="font-family: 'Roboto Mono', monospace; font-size: .7rem; font-weight: 500;">
                <button class="btn btn-outline-primary" type="button" onclick="generateNewTransactionNumber()" title="Generate New Reference">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="input-group mb-1">
    <input type="text" class="form-control rounded-start" placeholder="Enter Product Code" aria-label="Enter Product Code" id="barcodeInput" style="height: 45px;">
    <button class="btn btn-primary px-4" type="button" id="addProductBtn" style="height: 45px;">Add</button>
</div>

<style>
.transaction-header input:read-only {
    cursor: default;
    background-color: #fff !important;
}

.transaction-header .input-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.transaction-header .form-control:focus {
    box-shadow: none;
}
@media (max-width: 768px) {
    .transaction-header .form-control-lg {
        font-size: 0.95rem;
    }
}
</style>

<script>
$(document).ready(function() {
    if (!window.currentStockLevels) {
        window.currentStockLevels = new Map();
    }

    window.generateNewTransactionNumber = function() {
        const date = moment().format('YYYYMMDD');
        const random = Math.floor(Math.random() * 1000000000).toString().padStart(9, '0');
        const newRef = `TRX-${date}-${random}`;
        
        $('#transactionNumber').fadeOut(200, function() {
            $(this).val(newRef).fadeIn(200);
        });
    }

    function addProductByCode(code) {
        if (code.startsWith('F-')) {
            $.get(`${window.location.origin}/menu/code/${code}`)
                .then(response => {
                    if (response.success) {
                        window.addToOrder({
                            product_code: response.menu.code,
                            product_name: response.menu.name,
                            unit_price: parseFloat(response.menu.price),
                            type: 'menu',
                            unit: 'piece',
                            units_per_group: 1
                        });
                        $('#barcodeInput').val('');
                    } else {
                        throw new Error('Menu item not found');
                    }
                })
                .catch(() => {
                    toastr.error('Menu item not found');
                    $('#barcodeInput').val('');
                });
        } else {
            $.get(`${window.location.origin}/inventory/products/code/${code}`)
                .then(response => {
                    if (response.success) {
                        const product = response.product;
                        const isGroupUnit = isProductGroupUnit(product.unit);
                        
                        $.get(`/inventory/get-stock-levels/${product.product_code}`)
                            .then(stockResponse => {
                                let originalCurrentStock, originalBaseQuantity;
                                
                                if (stockResponse.success) {
                                    originalCurrentStock = stockResponse.stock_levels.current_stock;
                                    originalBaseQuantity = stockResponse.stock_levels.base_quantity;
                                } else {
                                    originalCurrentStock = product.current_stock || 0;
                                    originalBaseQuantity = product.base_quantity || 0;
                                }
                                
                                let consumedCurrentStock = 0;
                                let consumedBaseQuantity = 0;
                                
                                if (typeof orderItems !== 'undefined' && orderItems.length > 0) {
                                    const orderItemsOfThisProduct = orderItems.filter(item => item.code === product.product_code);
                                    orderItemsOfThisProduct.forEach(item => {
                                        if (item.type === 'group') {
                                            consumedCurrentStock += item.quantity;
                                            consumedBaseQuantity += item.quantity * (item.units_per_group || 1);
                                        } else if (item.type === 'piece') {
                                            consumedBaseQuantity += item.quantity;
                                        }
                                    });
                                }
                                
                                const remainingCurrentStock = Math.max(0, originalCurrentStock - consumedCurrentStock);
                                const remainingBaseQuantity = Math.max(0, originalBaseQuantity - consumedBaseQuantity);
                                
                                window.updateStockLevel(
                                    product.product_code, 
                                    remainingCurrentStock,
                                    remainingBaseQuantity
                                );
                                
                                let $tempCard = $(`#card-${product.product_code}`);
                                if ($tempCard.length === 0) {
                                    $('body').append(`<div id="card-${product.product_code}" style="display: none;" 
                                        data-original-current-stock="${originalCurrentStock}"
                                        data-original-base-quantity="${originalBaseQuantity}"
                                        data-current-stock="${remainingCurrentStock}"
                                        data-base-quantity="${remainingBaseQuantity}"></div>`);
                                } else {
                                    $tempCard.attr('data-original-current-stock', originalCurrentStock)
                                             .attr('data-original-base-quantity', originalBaseQuantity)
                                             .attr('data-current-stock', remainingCurrentStock)
                                             .attr('data-base-quantity', remainingBaseQuantity);
                                }
                                
                                if (isGroupUnit && product.units_per_group > 1) {
                                    if (remainingCurrentStock > 0 || remainingBaseQuantity > 0) {
                                        showUnitSelectionModal(product);
                                    } else {
                                        toastr.error(`Product is out of stock. Already ${consumedCurrentStock} ${product.unit}s and ${consumedBaseQuantity} pieces in order.`);
                                    }
                                } else {
                                    if (remainingCurrentStock > 0) {
                                        window.addToOrder({
                                            product_code: product.product_code,
                                            product_name: product.product_name,
                                            unit_price: parseFloat(product.selling_price),
                                            type: 'piece',
                                            unit: product.unit,
                                            units_per_group: parseInt(product.units_per_group) || 1
                                        });
                                    } else {
                                        toastr.error(`Not enough ${product.unit}s available. Only ${originalCurrentStock} in stock, ${consumedCurrentStock} already in order.`);
                                    }
                                }
                            })
                            .catch(() => {
                                toastr.error('Unable to verify stock levels. Please try again.');
                            });
                        $('#barcodeInput').val('');
                    } else {
                        throw new Error('Product not found');
                    }
                })
                .catch(error => {
                    toastr.error('Product not found');
                    $('#barcodeInput').val('');
                });
        }
    }

    function showUnitSelectionModal(product) {
        const pricePerPiece = parseFloat(product.selling_price);
        const groupPrice = pricePerPiece * product.units_per_group;
        
        $('#unitModalProductName').text(product.product_name);
        $('#unitModalProductCode').text(product.product_code);
        $('#groupUnitName').text(product.unit);
        $('#groupUnitPrice').text(groupPrice.toFixed(2));
        $('#pieceUnitPrice').text(pricePerPiece.toFixed(2));
        
        $('#groupUnitButton').off('click').on('click', function() {
            window.addToOrder({
                product_code: product.product_code,
                product_name: product.product_name,
                unit_price: groupPrice,
                type: 'group',
                unit: product.unit,
                units_per_group: product.units_per_group
            });
            $('#unitSelectionModal').modal('hide');
        });
        
        $('#pieceUnitButton').off('click').on('click', function() {
            window.addToOrder({
                product_code: product.product_code,
                product_name: product.product_name,
                unit_price: pricePerPiece,
                type: 'piece',
                unit: 'piece',
                units_per_group: product.units_per_group
            });
            $('#unitSelectionModal').modal('hide');
        });
        
        $('#unitSelectionModal').modal('show');
    }

    function isProductGroupUnit(unit) {
        const groupUnits = ['pack', 'box', 'carton', 'bag', 'bundle', 'sack', 'dozen', 'half-dozen', 'pair', 'bottle', 'can', 'case', 'set'];
        return groupUnits.includes(unit.toLowerCase());
    }

    $('#barcodeInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const code = $(this).val().trim();
            if (code) {
                addProductByCode(code);
            }
        }
    });

    $('#addProductBtn').on('click', function() {
        const code = $('#barcodeInput').val().trim();
        if (code) {
            addProductByCode(code);
        }
    });
});
</script>
