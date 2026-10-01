/**
 * Inventory Report Generation Functions
 * Enhanced functionality for generating and exporting inventory reports
 */

// Quick export functions
function exportInventory(format) {
    const data = {
        format: format,
        stock_status: 'all',
        sort_by: 'product_name',
        include_analytics: true,
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    // Show loading indicator
    showLoadingState('Generating ' + format.toUpperCase() + ' report...');
    
    // Make the request
    $.ajax({
        url: '/inventory/reports/export',
        method: 'POST',
        data: data,
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob, status, xhr) {
            hideLoadingState();
            
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // Get filename from response header or create default
            const contentDisposition = xhr.getResponseHeader('Content-Disposition');
            let filename = 'inventory-report.' + format;
            if (contentDisposition && contentDisposition.includes('filename=')) {
                filename = contentDisposition.split('filename=')[1].replace(/"/g, '');
            }
            
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            showNotification('Report exported successfully!', 'success');
        },
        error: function(xhr, status, error) {
            hideLoadingState();
            let errorMessage = 'Failed to export report. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showNotification(errorMessage, 'error');
        }
    });
}

function openInventoryReportModal() {
    // Check if modal exists, if not create it
    if (!document.getElementById('inventoryReportModal')) {
        createInventoryReportModal();
    }
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('inventoryReportModal'));
    modal.show();
}

function createInventoryReportModal() {
    const modalHtml = `
    <div class="modal fade" id="inventoryReportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-gradient-success text-white">
                    <h5 class="modal-title fw-semibold text-white">
                        <i class="bi bi-boxes me-2"></i>Inventory Report Generator
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="inventoryReportForm">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Date Range (Optional)</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="start_date" id="inv_start_date">
                                        <span class="input-group-text">to</span>
                                        <input type="date" class="form-control" name="end_date" id="inv_end_date">
                                    </div>
                                    <div class="form-text">Filter by last updated date (leave empty for all)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Report Format</label>
                                    <select class="form-select" name="format" required>
                                        <option value="preview">Preview in Browser</option>
                                        <option value="pdf" selected>PDF Document</option>
                                        <option value="excel">Excel Spreadsheet</option>
                                        <option value="csv">CSV File</option>
                                    </select>
                                    <div class="form-text">Choose how you want to receive the report</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Stock Status Filter</label>
                                    <select class="form-select" name="stock_status">
                                        <option value="all">All Items</option>
                                        <option value="high_stock">High Stock Items</option>
                                        <option value="low_stock">Low Stock Items</option>
                                        <option value="out_of_stock">Out of Stock Items</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Sort By</label>
                                    <select class="form-select" name="sort_by">
                                        <option value="product_name">Product Name</option>
                                        <option value="current_stock">Current Stock (Low to High)</option>
                                        <option value="selling_price">Selling Price (High to Low)</option>
                                        <option value="exp_date">Expiry Date</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block mb-3">Report Options</label>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" name="include_analytics" checked id="inv_include_analytics">
                                        <label class="form-check-label" for="inv_include_analytics">
                                            <strong>Include Analytics & Insights</strong>
                                            <small class="d-block text-muted">Stock distribution, value analysis, and expiry insights</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Tip:</strong> Use "Preview in Browser" to review your report before downloading. 
                                The report will include current stock levels, pricing, and expiry information.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <div class="text-muted small">
                            <i class="bi bi-clock me-1"></i>Report generation may take 5-15 seconds
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-gear-fill me-1"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    `;
    
    // Append modal to body
    $('body').append(modalHtml);
    
    // Setup form submission
    $('#inventoryReportForm').on('submit', function(e) {
        e.preventDefault();
        generateInventoryReport();
    });
}

function generateInventoryReport() {
    const form = document.getElementById('inventoryReportForm');
    const formData = new FormData(form);
    
    const format = formData.get('format');
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    
    // Validation
    if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
        showNotification('Start date cannot be after end date.', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Generating...';
    submitBtn.disabled = true;
    
    // Prepare the request data
    const requestData = {
        start_date: startDate,
        end_date: endDate,
        stock_status: formData.get('stock_status'),
        sort_by: formData.get('sort_by'),
        format: format,
        include_analytics: formData.has('include_analytics'),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    if (format === 'preview') {
        // Open preview in new window
        const params = new URLSearchParams(requestData);
        window.open('/inventory/reports/preview?' + params.toString(), '_blank', 'width=1200,height=800,scrollbars=yes');
        
        // Reset button and close modal
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        bootstrap.Modal.getInstance(document.getElementById('inventoryReportModal')).hide();
    } else {
        // Download report
        $.ajax({
            url: '/inventory/reports/export',
            method: 'POST',
            data: requestData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(blob, status, xhr) {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                
                // Get filename from response header or create default
                const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                let filename = 'inventory-report.' + format;
                if (contentDisposition && contentDisposition.includes('filename=')) {
                    filename = contentDisposition.split('filename=')[1].replace(/"/g, '');
                }
                
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                // Reset button and close modal
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                bootstrap.Modal.getInstance(document.getElementById('inventoryReportModal')).hide();
                
                showNotification('Inventory report generated successfully!', 'success');
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Failed to generate inventory report. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showNotification(errorMessage, 'error');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
}

// Quick generate report button
$(document).on('click', '#generateInventoryReport', function() {
    openInventoryReportModal();
});

// Loading state functions
function showLoadingState(message = 'Processing...') {
    if (!document.getElementById('loadingOverlay')) {
        const overlay = $(`
            <div id="loadingOverlay" class="position-fixed w-100 h-100" 
                 style="top: 0; left: 0; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                <div class="bg-white p-4 rounded shadow text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div id="loadingMessage">${message}</div>
                </div>
            </div>
        `);
        $('body').append(overlay);
    } else {
        $('#loadingMessage').text(message);
        $('#loadingOverlay').show();
    }
}

function hideLoadingState() {
    $('#loadingOverlay').fadeOut(300);
}

// Notification function (if not already defined)
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        const notification = $(`
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
    }
}