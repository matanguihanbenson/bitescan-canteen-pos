<!-- Inventory Reports Section -->
<div class="row g-4 mb-4">
    <!-- Summary Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Items</h6>
                        <h3 class="mb-0 fw-bold text-primary" id="inv-total-items">0</h3>
                        <small class="text-muted" id="inv-categories">0 categories</small>
                    </div>
                    <div class="metric-icon bg-primary">
                        <i class="bi bi-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Inventory Value</h6>
                        <h3 class="mb-0 fw-bold text-success" id="inv-total-value">₱0.00</h3>
                        <small class="text-muted" id="inv-avg-stock">Avg: 0 units</small>
                    </div>
                    <div class="metric-icon bg-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Low Stock</h6>
                        <h3 class="mb-0 fw-bold text-warning" id="inv-low-stock">0</h3>
                        <small class="text-muted">Need restocking</small>
                    </div>
                    <div class="metric-icon bg-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Out of Stock</h6>
                        <h3 class="mb-0 fw-bold text-danger" id="inv-out-of-stock">0</h3>
                        <small class="text-muted">Critical attention</small>
                    </div>
                    <div class="metric-icon bg-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card chart-container border-0">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">Stock Movement Trends</h5>
            </div>
            <div class="card-body">
                <canvas id="stockMovementChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-container border-0">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title mb-0">Category Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryDistChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Insights -->
<div class="row g-4 mb-4">
    <!-- Low Stock Alerts -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="border-0">Product</th>
                                <th class="border-0">Stock</th>
                                <th class="border-0">Min</th>
                                <th class="border-0">Category</th>
                                <th class="border-0">Value</th>
                            </tr>
                        </thead>
                        <tbody id="low-stock-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Loading low stock alerts...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Out of Stock -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-x-circle me-2"></i>Out of Stock Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="border-0">Product</th>
                                <th class="border-0">Category</th>
                                <th class="border-0">Last Updated</th>
                                <th class="border-0">Price</th>
                            </tr>
                        </thead>
                        <tbody id="inv-out-of-stock-table">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Loading out of stock items...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Value Items -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-gem me-2"></i>Top Value Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Rank</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Stock</th>
                                <th class="border-0">Price</th>
                                <th class="border-0">Total Value</th>
                                <th class="border-0">Category</th>
                            </tr>
                        </thead>
                        <tbody id="top-value-table">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Loading top value items...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiry Alerts -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-calendar-x me-2"></i>Expiry Alerts</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="border-0">Product</th>
                                <th class="border-0">Expires</th>
                                <th class="border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody id="expiry-alerts-table">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    Loading expiry alerts...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top 10 Insights -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Fastest Moving Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Rank</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Units Moved</th>
                                <th class="border-0">Category</th>
                            </tr>
                        </thead>
                        <tbody id="inv-fastest-moving-table">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Loading fastest moving items...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-hourglass me-2"></i>Slowest Moving Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Rank</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Units Moved</th>
                                <th class="border-0">Category</th>
                            </tr>
                        </thead>
                        <tbody id="inv-slowest-moving-table">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Loading slowest moving items...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let stockMovementChart, categoryDistChart;

function renderInventoryReports(data) {
    // Update summary cards
    updateInventorySummary(data.summary);
    
    // Update charts
    updateStockMovementChart(data.stock_movement_trends);
    updateCategoryDistributionChart(data.category_distribution);
    
    // Update tables
    updateLowStockTable(data.low_stock_alerts);
    updateInvOutOfStockTable(data.out_of_stock);
    updateTopValueTable(data.top_value_items);
    updateExpiryAlertsTable(data.expiry_alerts);
    
    // Update Top 10 insights
    updateTop10InventoryInsights(data.top_10_insights);
}

function updateInventorySummary(summary) {
    $('#inv-total-items').text(Number(summary.total_items || 0).toLocaleString());
    $('#inv-categories').text((summary.categories_count || 0) + ' categories');
    $('#inv-total-value').text('₱' + Number(summary.total_value || 0).toLocaleString());
    $('#inv-avg-stock').text('Avg: ' + Number(summary.average_stock_level || 0).toLocaleString() + ' units');
    $('#inv-low-stock').text(Number(summary.low_stock_count || 0).toLocaleString());
    $('#inv-out-of-stock').text(Number(summary.out_of_stock_count || 0).toLocaleString());
}

function updateStockMovementChart(trends) {
    const ctx = document.getElementById('stockMovementChart');
    if (stockMovementChart) stockMovementChart.destroy();
    
    // Get current date filter to determine label type
    const dateFilter = $('#dateFilter').val() || 'this_week';
    
    let labels = [];
    let stockInData = [];
    let stockOutData = [];
    let adjustmentsData = [];
    
    // If no data or empty trends, generate default labels
    if (!trends || trends.length === 0) {
        // Generate default labels based on filter type
        switch(dateFilter) {
            case 'today':
                // Generate 24 hours (12am to 11pm)
                for (let i = 0; i < 24; i++) {
                    const period = i >= 12 ? 'pm' : 'am';
                    const displayHour = i === 0 ? 12 : (i > 12 ? i - 12 : i);
                    labels.push(`${displayHour}${period}`);
                    stockInData.push(0);
                    stockOutData.push(0);
                    adjustmentsData.push(0);
                }
                break;
            
            case 'this_week':
                // Generate 7 days (Mon to Sun)
                const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                labels = weekDays;
                stockInData = new Array(7).fill(0);
                stockOutData = new Array(7).fill(0);
                adjustmentsData = new Array(7).fill(0);
                break;
            
            case 'this_month':
                // Generate weeks (Week 1 to Week 4)
                for (let i = 1; i <= 4; i++) {
                    labels.push(`Week ${i}`);
                    stockInData.push(0);
                    stockOutData.push(0);
                    adjustmentsData.push(0);
                }
                break;
            
            case 'this_year':
                // Generate 12 months (Jan to Dec)
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                labels = months;
                stockInData = new Array(12).fill(0);
                stockOutData = new Array(12).fill(0);
                adjustmentsData = new Array(12).fill(0);
                break;
            
            default:
                // Default to 7 days
                labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                stockInData = new Array(7).fill(0);
                stockOutData = new Array(7).fill(0);
                adjustmentsData = new Array(7).fill(0);
        }
    } else {
        // Generate labels from the data
        labels = trends.map(t => {
            if (t.label) {
                // Backend provided a label (e.g., "12am", "1pm" for hourly data)
                return t.label;
            } else if (t.hour !== undefined) {
                // Hourly data
                const hour = parseInt(t.hour);
                const period = hour >= 12 ? 'pm' : 'am';
                const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
                return `${displayHour}${period}`;
            } else {
                // Daily/weekly/monthly data
                return new Date(t.date).toLocaleDateString();
            }
        });
        
        stockInData = trends.map(t => t.stock_in || 0);
        stockOutData = trends.map(t => t.stock_out || 0);
        adjustmentsData = trends.map(t => t.adjustments || 0);
    }
    
    stockMovementChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Stock In',
                    data: stockInData,
                    backgroundColor: '#10b981',
                    borderColor: '#10b981',
                    borderWidth: 1
                },
                {
                    label: 'Stock Out',
                    data: stockOutData,
                    backgroundColor: '#ef4444',
                    borderColor: '#ef4444',
                    borderWidth: 1
                },
                {
                    label: 'Adjustments',
                    data: adjustmentsData,
                    backgroundColor: '#f59e0b',
                    borderColor: '#f59e0b',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function updateCategoryDistributionChart(distribution) {
    const ctx = document.getElementById('categoryDistChart');
    if (categoryDistChart) categoryDistChart.destroy();
    
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
    
    categoryDistChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: distribution.map(d => d.category),
            datasets: [{
                data: distribution.map(d => d.total_value),
                backgroundColor: colors.slice(0, distribution.length),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${context.label}: ₱${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function updateLowStockTable(lowStock) {
    const tbody = $('#low-stock-table');
    tbody.empty();
    
    if (!lowStock || lowStock.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-success py-4">✅ No low stock alerts!</td></tr>');
        return;
    }
    
    lowStock.forEach(item => {
        tbody.append(`
            <tr>
                <td class="fw-medium">${item.product_name}</td>
                <td><span class="badge bg-warning">${item.current_stock} ${item.unit}</span></td>
                <td>${item.min_stock}</td>
                <td><small class="text-muted">${item.category}</small></td>
                <td class="text-success fw-medium">₱${Number(item.selling_price).toLocaleString()}</td>
            </tr>
        `);
    });
}

function updateInvOutOfStockTable(outOfStock) {
    const tbody = $('#inv-out-of-stock-table');
    tbody.empty();
    
    if (!outOfStock || outOfStock.length === 0) {
        tbody.html('<tr><td colspan="4" class="text-center text-success py-4">✅ No out of stock items!</td></tr>');
        return;
    }
    
    outOfStock.forEach(item => {
        tbody.append(`
            <tr>
                <td class="fw-medium">${item.product_name}</td>
                <td><small class="text-muted">${item.category}</small></td>
                <td><small class="text-muted">${item.last_updated}</small></td>
                <td class="text-success fw-medium">₱${Number(item.selling_price).toLocaleString()}</td>
            </tr>
        `);
    });
}

function updateTopValueTable(topValue) {
    const tbody = $('#top-value-table');
    tbody.empty();
    
    if (!topValue || topValue.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">No data available</td></tr>');
        return;
    }
    
    topValue.forEach((item, index) => {
        tbody.append(`
            <tr>
                <td><span class="badge bg-primary">${index + 1}</span></td>
                <td class="fw-medium">${item.product_name}</td>
                <td>${item.current_stock}</td>
                <td>₱${Number(item.selling_price).toLocaleString()}</td>
                <td class="text-success fw-bold">₱${Number(item.total_value).toLocaleString()}</td>
                <td><small class="text-muted">${item.category}</small></td>
            </tr>
        `);
    });
}

function updateExpiryAlertsTable(expiryAlerts) {
    const tbody = $('#expiry-alerts-table');
    tbody.empty();
    
    if (!expiryAlerts || expiryAlerts.length === 0) {
        tbody.html('<tr><td colspan="3" class="text-center text-success py-4">✅ No expiry alerts!</td></tr>');
        return;
    }
    
    expiryAlerts.forEach(item => {
        const statusClass = item.status === 'expired' ? 'danger' : 
                          item.status === 'critical' ? 'warning' : 'info';
        const statusText = item.status === 'expired' ? 'Expired' : 
                          item.status === 'critical' ? 'Critical' : 'Warning';
        
        tbody.append(`
            <tr>
                <td class="fw-medium">${item.product_name}</td>
                <td><small class="text-muted">${item.exp_date}</small></td>
                <td><span class="badge bg-${statusClass}">${statusText}</span></td>
            </tr>
        `);
    });
}

function updateTop10InventoryInsights(insights) {
    if (!insights) return;
    
    // Update fastest moving items
    const fastestTbody = $('#inv-fastest-moving-table');
    fastestTbody.empty();
    
    if (insights.fastest_moving && insights.fastest_moving.length > 0) {
        insights.fastest_moving.forEach((item, index) => {
            fastestTbody.append(`
                <tr>
                    <td><span class="badge bg-primary">${index + 1}</span></td>
                    <td class="fw-medium">${item.product_name}</td>
                    <td class="text-success fw-medium">${item.total_moved}</td>
                    <td><small class="text-muted">${item.category}</small></td>
                </tr>
            `);
        });
    } else {
        fastestTbody.html('<tr><td colspan="4" class="text-center text-muted py-4">No movement data available</td></tr>');
    }
    
    // Update slowest moving items
    const slowestTbody = $('#inv-slowest-moving-table');
    slowestTbody.empty();
    
    if (insights.slowest_moving && insights.slowest_moving.length > 0) {
        insights.slowest_moving.forEach((item, index) => {
            slowestTbody.append(`
                <tr>
                    <td><span class="badge bg-secondary">${index + 1}</span></td>
                    <td class="fw-medium">${item.product_name}</td>
                    <td class="text-warning fw-medium">${item.total_moved}</td>
                    <td><small class="text-muted">${item.category}</small></td>
                </tr>
            `);
        });
    } else {
        slowestTbody.html('<tr><td colspan="4" class="text-center text-muted py-4">No movement data available</td></tr>');
    }
}
</script>