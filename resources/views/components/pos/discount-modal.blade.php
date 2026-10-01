<!-- Discount Modal -->
<div class="modal fade" id="discountModal" tabindex="-1">
<div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:16px; max-height:70vh; overflow-y:auto;">
                <div class="mb-3">
                    <label class="form-label">Discount Type</label>
                    <select class="form-select" id="discountType">
                        <option value="scholar">Scholar</option>
                        <option value="amount">Fixed Amount</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
                <div class="mb-2" id="regularDiscountInput">
                    <label class="form-label">Value</label>
                    <input type="number" class="form-control form-control-sm" id="discountValue" min="0" step="0.01">
                    <div class="form-text" id="discountHelp">Enter value between 0-100 for percentage</div>
                </div>
                
                <!-- Scholar ID Input -->
                <div class="mb-2 d-none" id="scholarDiscountInput">
                    <label class="form-label">Student ID</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" id="studentId" placeholder="Enter Student ID">
                        <button class="btn btn-outline-primary btn-sm" type="button" id="validateStudentBtn">
                            <i class="bi bi-search"></i> Validate
                        </button>
                    </div>
                    <div class="form-text" id="scholarHelp">Enter the student ID to validate scholar eligibility</div>
                </div>
                
                <!-- Scholar Info Display -->
                <div class="mb-2 d-none" id="scholarInfo">
                    <div class="p-2 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="flex-grow-1">
                                <div class="fw-semibold small" id="scholarName"></div>
                                <div class="text-muted small" id="scholarStudentId"></div>
                            </div>
                            <div class="text-end">
                                <div class="small">Balance</div>
                                <div class="fw-bold text-success" id="scholarBalance">₱0.00</div>
                            </div>
                        </div>
                        <div class="mt-2 small">
                            Available: <span class="fw-semibold" id="scholarAvailable">₱0.00</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label small mb-1">Discount Amount</label>
                        <input type="number" class="form-control form-control-sm" id="scholarDiscountValue" min="0" step="0.01" placeholder="Enter discount amount">
                        <div class="form-text">Max: ₱<span id="maxDiscountAmount">0.00</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2" style="position:absolute; width:100%; right:0;bottom:0; background:#fff; border-top: 1px solid #e9ecef;height:80px !important;">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-lg " id="applyDiscount" onclick="calculateDiscount()">Apply Discount</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentScholar = null;

function calculateDiscount() {
    const type = $('#discountType').val();
    const subtotal = parseFloat($('.subtotal').text().replace('₱', ''));
    let discountAmount = 0;
    
    // Handle preset discount types from database
    if (type && type.startsWith('preset_')) {
        const $option = $('#discountType option:selected');
        const percentage = parseFloat($option.data('percentage'));
        
        if (percentage > 0) {
            discountAmount = (subtotal * percentage) / 100;
            window.scholarDiscount = null;
        }
    } else if (type === 'scholar') {
        if (!currentScholar) {
            alert('Please validate a student ID first');
            return;
        }
        
        const scholarDiscountValue = parseFloat($('#scholarDiscountValue').val() || 0);
        if (scholarDiscountValue <= 0) {
            alert('Please enter a discount amount');
            return;
        }
        
        if (scholarDiscountValue > currentScholar.current_balance) {
            alert('Discount amount cannot exceed scholar balance');
            return;
        }
        
        if (scholarDiscountValue > subtotal) {
            alert('Discount amount cannot exceed subtotal');
            return;
        }
        
        discountAmount = scholarDiscountValue;
        
        window.scholarDiscount = {
            scholar_id: currentScholar.id,
            discount_type: 'scholar',
            amount: discountAmount
        };
        
    } else {
        const value = parseFloat($('#discountValue').val() || 0);
        
        if (type === 'percentage') {
            if (value > 100) {
                alert('Discount percentage cannot exceed 100%');
                return;
            }
            discountAmount = (subtotal * value) / 100;
        } else {
            if (value > subtotal) {
                alert('Discount amount cannot exceed subtotal');
                return;
            }
            discountAmount = value;
        }
        
        window.scholarDiscount = null;
    }
    
    // Calculate VAT on the amount after discount
    const amountAfterDiscount = subtotal - discountAmount;
    
    // Use the VAT calculation helper
    let vatResult;
    if (window.calculateTotalWithTax) {
        vatResult = window.calculateTotalWithTax(amountAfterDiscount);
    } else {
        vatResult = {
            subtotal: amountAfterDiscount.toFixed(2),
            tax: '0.00',
            taxRate: 0,
            total: amountAfterDiscount.toFixed(2)
        };
    }
    
    $('.discount').text(`₱${discountAmount.toFixed(2)}`);
    $('.vat-rate').text(vatResult.taxRate);
    $('.vat-amount').text(`₱${vatResult.tax}`);
    $('.amount-due').text(`₱${vatResult.total}`);
    $('.grand-total').text(`₱${vatResult.total}`);
    
    $('#discountModal').modal('hide');
}

$('#discountValue').on('input', function() {
    const type = $('#discountType').val();
    const value = parseFloat(this.value || 0);
    const subtotal = parseFloat($('.subtotal').text().replace('₱', ''));
    
    if (type === 'percentage' && value > 100) {
        $('#discountHelp').addClass('text-danger').text('Maximum discount is 100%');
        $('#applyDiscount').prop('disabled', true);
    } else if (type === 'amount' && value > subtotal) {
        $('#discountHelp').addClass('text-danger').text('Discount cannot exceed subtotal');
        $('#applyDiscount').prop('disabled', true);
    } else {
        $('#discountHelp').removeClass('text-danger').text(type === 'percentage' ? 'Enter value between 0-100' : 'Enter discount amount');
        $('#applyDiscount').prop('disabled', false);
    }
});

$('#discountType').on('change', function() {
    const type = $(this).val();
    
    if (type === 'scholar') {
        // Scholar: show student ID validation
        $('#regularDiscountInput').addClass('d-none');
        $('#scholarDiscountInput').removeClass('d-none');
        $('#scholarInfo').addClass('d-none');
        currentScholar = null;
        $('#applyDiscount').prop('disabled', true);
    } else if (type === 'amount') {
        // Fixed Amount: show input for fixed amount
        $('#regularDiscountInput').removeClass('d-none');
        $('#scholarDiscountInput').addClass('d-none');
        $('#scholarInfo').addClass('d-none');
        currentScholar = null;
        $('#applyDiscount').prop('disabled', false);
        $('#discountHelp').removeClass('text-danger').text('Enter fixed discount amount');
        $('#discountValue').attr('placeholder', 'Enter amount').trigger('input');
    } else if (type === 'percentage') {
        // Percentage: show input for percentage
        $('#regularDiscountInput').removeClass('d-none');
        $('#scholarDiscountInput').addClass('d-none');
        $('#scholarInfo').addClass('d-none');
        currentScholar = null;
        $('#applyDiscount').prop('disabled', false);
        $('#discountHelp').removeClass('text-danger').text('Enter value between 0-100');
        $('#discountValue').attr('placeholder', 'Enter percentage').trigger('input');
    } else if (type && type.startsWith('preset_')) {
        // Preset discount from database: no input needed, percentage is preset
        $('#regularDiscountInput').addClass('d-none');
        $('#scholarDiscountInput').addClass('d-none');
        $('#scholarInfo').addClass('d-none');
        currentScholar = null;
        $('#applyDiscount').prop('disabled', false);
    }
});

$('#validateStudentBtn').on('click', function() {
    const studentId = $('#studentId').val().trim();
    
    if (!studentId) {
        alert('Please enter a Student ID');
        return;
    }
    
    $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Validating...');
    
    $.post('{{ route("pos.validate-student") }}', {
        student_id: studentId,
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.valid) {
            currentScholar = response.scholar;
            
            $('#scholarName').text(currentScholar.full_name);
            $('#scholarStudentId').text('ID: ' + currentScholar.student_id);
            $('#scholarBalance').text('₱' + parseFloat(currentScholar.current_balance).toFixed(2));
            
            const subtotal = parseFloat($('.subtotal').text().replace('₱', ''));
            const maxDiscount = Math.min(currentScholar.current_balance, subtotal);
            
            $('#scholarAvailable').text('₱' + maxDiscount.toFixed(2));
            $('#maxDiscountAmount').text(maxDiscount.toFixed(2));
            $('#scholarDiscountValue').attr('max', maxDiscount).val(maxDiscount);
            
            $('#scholarInfo').removeClass('d-none');
            $('#applyDiscount').prop('disabled', false);
            
            $('#scholarHelp').removeClass('text-danger').text('Student validated successfully!');
            
        } else {
            $('#scholarHelp').addClass('text-danger').text(response.message || 'Invalid student ID');
            $('#scholarInfo').addClass('d-none');
            $('#applyDiscount').prop('disabled', true);
            currentScholar = null;
        }
    })
    .fail(function() {
        $('#scholarHelp').addClass('text-danger').text('Error validating student ID. Please try again.');
        $('#scholarInfo').addClass('d-none');
        $('#applyDiscount').prop('disabled', true);
        currentScholar = null;
    })
    .always(function() {
        $('#validateStudentBtn').prop('disabled', false).html('<i class="bi bi-search"></i> Validate');
    });
});

$('#scholarDiscountValue').on('input', function() {
    if (!currentScholar) return;
    
    const value = parseFloat($(this).val() || 0);
    const maxDiscount = parseFloat($('#maxDiscountAmount').text());
    
    if (value > maxDiscount) {
        $(this).addClass('is-invalid');
        $('#applyDiscount').prop('disabled', true);
    } else {
        $(this).removeClass('is-invalid');
        $('#applyDiscount').prop('disabled', false);
    }
});

$('#studentId').on('keypress', function(e) {
    if (e.which === 13) {
        $('#validateStudentBtn').click();
    }
});

$('#discountModal').on('hidden.bs.modal', function() {
    $('#discountType').val('scholar');
    $('#regularDiscountInput').addClass('d-none');
    $('#scholarDiscountInput').removeClass('d-none');
    $('#scholarInfo').addClass('d-none');
    $('#studentId').val('');
    $('#discountValue').val('');
    $('#scholarDiscountValue').val('');
    $('#scholarHelp').removeClass('text-danger').text('Enter the student ID to validate scholar eligibility');
    currentScholar = null;
});

// Ensure correct UI state when the modal opens (e.g., after page refresh)
$('#discountModal').on('shown.bs.modal', function() {
    const type = $('#discountType').val();
    const validateBtn = $('#validateStudentBtn');
    
    if (type === 'scholar') {
        $('#regularDiscountInput').addClass('d-none');
        $('#scholarDiscountInput').removeClass('d-none');
        $('#scholarInfo').addClass('d-none');
        validateBtn.prop('disabled', false).html('<i class="bi bi-search"></i> Validate').show();
        $('#applyDiscount').prop('disabled', true);
    } else if (type === 'amount') {
        $('#regularDiscountInput').removeClass('d-none');
        $('#scholarDiscountInput').addClass('d-none');
        $('#scholarInfo').addClass('d-none');
        $('#applyDiscount').prop('disabled', false);
        $('#discountHelp').text('Enter fixed discount amount');
    } else if (type === 'percentage') {
        $('#regularDiscountInput').removeClass('d-none');
        $('#scholarDiscountInput').addClass('d-none');
        $('#scholarInfo').addClass('d-none');
        $('#applyDiscount').prop('disabled', false);
        $('#discountHelp').text('Enter value between 0-100');
    }
});
</script>