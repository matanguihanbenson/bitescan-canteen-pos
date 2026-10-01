<!-- Sales Reports Section -->
<div class="row g-4 mb-4">
    <!-- Summary Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Revenue</h6>
                        <h3 class="mb-0 fw-bold text-success" id="sales-total-revenue">₱0.00</h3>
                        <small class="text-success" id="sales-revenue-growth">+0%</small>
                    </div>
                    <div class="metric-icon bg-success">
                        <i class="bi bi-cash-coin"></i>
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
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Transactions</h6>
                        <h3 class="mb-0 fw-bold text-primary" id="sales-total-transactions">0</h3>
                        <small class="text-muted" id="sales-avg-transaction">Avg: ₱0.00</small>
                    </div>
                    <div class="metric-icon bg-primary">
                        <i class="bi bi-receipt"></i>
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
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Cash Sales</h6>
                        <h3 class="mb-0 fw-bold text-info" id="sales-cash-revenue">₱0.00</h3>
                        <small class="text-muted" id="sales-cash-percentage">0%</small>
                    </div>
                    <div class="metric-icon bg-info">
                        <i class="bi bi-cash-stack"></i>
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
                        <h6 class="text-muted text-uppercase fw-bold mb-2 small">Credit Sales</h6>
                        <h3 class="mb-0 fw-bold text-warning" id="sales-credit-revenue">₱0.00</h3>
                        <small class="text-muted" id="sales-credit-percentage">0%</small>
                    </div>
                    <div class="metric-icon bg-warning">
                        <i class="bi bi-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Trends Chart -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card chart-container border-0">
            <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Revenue Trends</h5>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="revenuePeriod" id="revenueDaily" value="daily">
                        <label class="btn btn-outline-primary btn-sm" for="revenueDaily">Daily</label>
                        
                        <input type="radio" class="btn-check" name="revenuePeriod" id="revenueWeekly" value="weekly" checked>
                        <label class="btn btn-outline-primary btn-sm" for="revenueWeekly">Weekly</label>
                        
                        <input type="radio" class="btn-check" name="revenuePeriod" id="revenueMonthly" value="monthly">
                        <label class="btn btn-outline-primary btn-sm" for="revenueMonthly">Monthly</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesRevenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Performance Analytics -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Hourly Performance</h6>
            </div>
            <div class="card-body">
                <canvas id="hourlyPerformanceChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Payment Methods</h6>
            </div>
            <div class="card-body">
                <canvas id="paymentMethodsChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Selling Products</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Rank</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Quantity Sold</th>
                                <th class="border-0">Total Sales</th>
                                <th class="border-0">Avg Price</th>
                            </tr>
                        </thead>
                        <tbody id="top-products-table">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Loading top selling products...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Peak Hours</h6>
            </div>
            <div class="card-body">
                <div id="peak-hours-list">
                    <p class="text-muted">Loading peak hours...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Insights -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Customer Insights</h6>
            </div>
            <div class="card-body">
                <div class="row g-3" id="customer-insights">
                    <div class="col-md-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="mb-1 text-primary" id="avg-items-per-transaction">0</h4>
                            <small class="text-muted">Avg Items per Transaction</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="mb-1 text-success" id="transactions-per-day">0</h4>
                            <small class="text-muted">Transactions per Day</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-star me-2"></i>Performance Summary</h6>
            </div>
            <div class="card-body" id="performance-summary">
                <p class="text-muted">Loading performance summary...</p>
            </div>
        </div>
    </div>
</div>

<script>
let salesRevenueChart, hourlyPerformanceChart, paymentMethodsChart;

function renderSalesReports(data) {
    // Update summary cards
    updateSalesSummary(data.summary);
    
    // Update charts
    updateSalesRevenueChart(data.revenue_trends);
    updateHourlyPerformanceChart(data.hourly_performance);
    updatePaymentMethodsChart(data.payment_breakdown);
    
    // Update tables and insights
    updateTopProductsTable(data.top_products);
    updateCustomerInsights(data.customer_insights);
    updatePerformanceSummary(data);
}

function updateSalesSummary(summary) {
    $('#sales-total-revenue').text('₱' + Number(summary.total_revenue || 0).toLocaleString());
    $('#sales-revenue-growth').text((summary.revenue_growth > 0 ? '+' : '') + (summary.revenue_growth || 0) + '%');
    $('#sales-total-transactions').text(Number(summary.total_transactions || 0).toLocaleString());
    $('#sales-avg-transaction').text('Avg: ₱' + Number(summary.average_transaction || 0).toLocaleString());
    $('#sales-cash-revenue').text('₱' + Number(summary.cash_revenue || 0).toLocaleString());
    $('#sales-credit-revenue').text('₱' + Number(summary.credit_revenue || 0).toLocaleString());
    
    const totalRevenue = summary.total_revenue || 1;
    const cashPercentage = ((summary.cash_revenue || 0) / totalRevenue * 100).toFixed(1);
    const creditPercentage = ((summary.credit_revenue || 0) / totalRevenue * 100).toFixed(1);
    
    $('#sales-cash-percentage').text(cashPercentage + '%');
    $('#sales-credit-percentage').text(creditPercentage + '%');
}

function updateSalesRevenueChart(trends) {
    const ctx = document.getElementById('salesRevenueChart');
    if (salesRevenueChart) salesRevenueChart.destroy();
    
    const labels = trends.map(t => t.label ? t.label : new Date(t.date).toLocaleDateString());
    
    salesRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Revenue',
                    data: trends.map(t => t.revenue),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                },
                {
                    label: 'Cash Revenue',
                    data: trends.map(t => t.cash_revenue),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: false,
                    borderWidth: 2,
                    borderDash: [5, 5]
                },
                {
                    label: 'Credit Revenue',
                    data: trends.map(t => t.credit_revenue),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: false,
                    borderWidth: 2,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function updateHourlyPerformanceChart(hourlyData) {
    const ctx = document.getElementById('hourlyPerformanceChart');
    if (hourlyPerformanceChart) hourlyPerformanceChart.destroy();
    
    hourlyPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hourlyData.map(h => h.hour),
            datasets: [{
                label: 'Revenue',
                data: hourlyData.map(h => h.total_revenue),
                backgroundColor: '#3b82f6',
                borderColor: '#3b82f6',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Transactions',
                data: hourlyData.map(h => h.transaction_count),
                type: 'line',
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

function updatePaymentMethodsChart(paymentData) {
    const ctx = document.getElementById('paymentMethodsChart');
    if (paymentMethodsChart) paymentMethodsChart.destroy();
    
    const total = paymentData.reduce((sum, p) => sum + p.total_amount, 0);
    
    paymentMethodsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(p => p.payment_type.charAt(0).toUpperCase() + p.payment_type.slice(1)),
            datasets: [{
                data: paymentData.map(p => p.total_amount),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
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
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${context.label}: ₱${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function updateTopProductsTable(topProducts) {
    const tbody = $('#top-products-table');
    tbody.empty();
    
    if (!topProducts || topProducts.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">No sales data available</td></tr>');
        return;
    }
    
    topProducts.slice(0, 10).forEach((product, index) => {
        const typeClass = product.type === 'menu' ? 'success' : 'primary';
        tbody.append(`
            <tr>
                <td><span class="badge bg-primary">${index + 1}</span></td>
                <td class="fw-medium">${product.name}</td>
                <td><span class="badge bg-${typeClass}">${product.type}</span></td>
                <td class="text-center">${Number(product.total_quantity).toLocaleString()}</td>
                <td class="text-success fw-bold">₱${Number(product.total_sales).toLocaleString()}</td>
                <td class="text-muted">₱${Number(product.average_price).toLocaleString()}</td>
            </tr>
        `);
    });
}

function updateCustomerInsights(insights) {
    if (!insights) return;
    
    $('#avg-items-per-transaction').text(Number(insights.average_items_per_transaction || 0).toFixed(1));
    $('#transactions-per-day').text(Number(insights.repeat_customer_indicators?.transactions_per_day || 0).toFixed(1));
    
    // Update peak hours
    if (insights.peak_hours) {
        let peakHoursHtml = '';
        const sortedHours = Object.entries(insights.peak_hours).slice(0, 5);
        
        sortedHours.forEach(([hour, count], index) => {
            const timeLabel = hour + ':00';
            peakHoursHtml += `
                <div class="d-flex justify-content-between align-items-center py-2 ${index > 0 ? 'border-top' : ''}">
                    <span class="fw-medium">${timeLabel}</span>
                    <span class="badge bg-primary">${count} orders</span>
                </div>
            `;
        });
        
        $('#peak-hours-list').html(peakHoursHtml || '<p class="text-muted">No peak hours data</p>');
    }
}

function updatePerformanceSummary(data) {
    const analytics = data.analytics || {};
    let summaryHtml = '<div class="row g-3">';
    
    if (analytics.peak_hour) {
        summaryHtml += `
            <div class="col-md-6">
                <div class="text-center p-3 bg-light rounded">
                    <h5 class="mb-1 text-primary">${analytics.peak_hour.hour}:00</h5>
                    <small class="text-muted">Peak Sales Hour</small>
                </div>
            </div>
        `;
    }
    
    if (analytics.peak_day) {
        const peakDate = new Date(analytics.peak_day.date);
        summaryHtml += `
            <div class="col-md-6">
                <div class="text-center p-3 bg-light rounded">
                    <h5 class="mb-1 text-success">${peakDate.toLocaleDateString()}</h5>
                    <small class="text-muted">Best Sales Day</small>
                </div>
            </div>
        `;
    }
    
    summaryHtml += '</div>';
    $('#performance-summary').html(summaryHtml);
}

// Event handlers for revenue chart period selection
$(document).on('change', 'input[name="revenuePeriod"]', function() {
    const period = $(this).val();
    // Reload revenue trends with selected period
    });
</script>