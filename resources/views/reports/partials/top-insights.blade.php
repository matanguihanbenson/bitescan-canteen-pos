<!-- Top 10 Insights Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-3 rounded shadow-sm">
            <div>
                <h5 class="mb-1 font-size-16 fw-semibold">Top 10 Analytics & Insights</h5>
                <p class="text-muted mb-0">Key performance metrics and rankings across inventory and sales</p>
            </div>
        </div>
    </div>
</div>

<!-- Section Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-pills nav-justified" id="insightsNav">
            <li class="nav-item">
                <a class="nav-link active" id="inventory-insights-tab" data-bs-toggle="pill" href="#inventory-insights" role="tab">
                    <i class="bi bi-boxes me-2"></i>Inventory Top 10
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sales-insights-tab" data-bs-toggle="pill" href="#sales-insights" role="tab">
                    <i class="bi bi-graph-up me-2"></i>Sales Top 10
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="insightsTabContent">
    <!-- Inventory Top 10 Section -->
    <div class="tab-pane fade show active" id="inventory-insights" role="tabpanel">
        <h6 class="text-muted text-uppercase fw-bold mb-3">INVENTORY TOP 10 INSIGHTS</h6>

        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted text-uppercase fw-bold mb-2 small">Inventory Value</h6>
                                <h3 class="mb-0 fw-bold text-info" id="invSummaryValue">₱0.00</h3>
                                <small class="text-muted" id="invSummaryItems">0 items</small>
                            </div>
                            <div class="metric-icon bg-info">
                                <i class="bi bi-boxes"></i>
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
                                <h3 class="mb-0 fw-bold text-warning" id="invSummaryLowStock">0</h3>
                                <small class="text-muted">Needs restock</small>
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
                                <h3 class="mb-0 fw-bold text-danger" id="invSummaryOutOfStock">0</h3>
                                <small class="text-muted">Requires action</small>
                            </div>
                            <div class="metric-icon bg-danger">
                                <i class="bi bi-x-circle"></i>
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
                                <h6 class="text-muted text-uppercase fw-bold mb-2 small">Avg Stock/Item</h6>
                                <h3 class="mb-0 fw-bold text-secondary" id="invSummaryAvgStock">0</h3>
                                <small class="text-muted">Average on hand</small>
                            </div>
                            <div class="metric-icon bg-secondary">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4 mb-4">
    <div class="col-lg-6">
        <!-- Top 10 Best Selling Products -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-trophy-fill me-2"></i>Top 10 Best Selling Products</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Qty Sold</th>
                                <th class="border-0">Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="top-selling-products-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Loading best selling products...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <!-- Top 10 Items Needing Restock -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Top 10 Items Needing Restock</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Current Stock</th>
                                <th class="border-0">Min Level</th>
                                <th class="border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody id="restock-needed-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Loading restock alerts...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <!-- Top 10 Out of Stock Items -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-x-circle-fill me-2"></i>Top 10 Out of Stock Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Last Sale</th>
                                <th class="border-0">Priority</th>
                            </tr>
                        </thead>
                        <tbody id="top-out-of-stock-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Loading out of stock items...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <!-- Top 10 Highest Value Products -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-gem me-2"></i>Top 10 Highest Value Products</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Stock Value</th>
                                <th class="border-0">Unit Price</th>
                                <th class="border-0">Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="highest-value-products-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Loading highest value products...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <!-- Top 10 Fastest Moving Items -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Top 10 Fastest Moving Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Velocity</th>
                                <th class="border-0">Stock Level</th>
                                <th class="border-0">Days to Stock Out</th>
                            </tr>
                        </thead>
                        <tbody id="top-fastest-moving-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
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
        <!-- Top 10 Slowest Moving Items -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-hourglass me-2"></i>Top 10 Slowest Moving Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Days Since Last Sale</th>
                                <th class="border-0">Stock Level</th>
                                <th class="border-0">Action Needed</th>
                            </tr>
                        </thead>
                        <tbody id="top-slowest-moving-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
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

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <!-- Top 10 Items Expiring Soon -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-calendar-x me-2"></i>Top 10 Items Expiring Soon</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Product</th>
                                <th class="border-0">Expiry Date</th>
                                <th class="border-0">Days Left</th>
                                <th class="border-0">Stock</th>
                            </tr>
                        </thead>
                        <tbody id="expiring-soon-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Loading expiry alerts...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <!-- Top Categories by Revenue -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; color: white;">
                <h6 class="mb-0 text-white"><i class="bi bi-bar-chart-fill me-2"></i>Top Categories by Revenue</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Category</th>
                                <th class="border-0">Revenue</th>
                                <th class="border-0">Items Sold</th>
                                <th class="border-0">Avg Price</th>
                            </tr>
                        </thead>
                        <tbody id="top-categories-table">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Loading category performance...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>

    <!-- Sales Top 10 Section -->
    <div class="tab-pane fade" id="sales-insights" role="tabpanel">
        <h6 class="text-muted text-uppercase fw-bold mb-3">SALES TOP 10 INSIGHTS</h6>
        
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Revenue</h6>
                                <h3 class="mb-0 fw-bold text-success" id="salesSummaryRevenue">₱0.00</h3>
                                <small class="text-muted">Selected period</small>
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
                                <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Transactions</h6>
                                <h3 class="mb-0 fw-bold text-primary" id="salesSummaryTransactions">0</h3>
                                <small class="text-muted">Completed</small>
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
                                <h6 class="text-muted text-uppercase fw-bold mb-2 small">Avg Transaction</h6>
                                <h3 class="mb-0 fw-bold text-info" id="salesSummaryAvg">₱0.00</h3>
                                <small class="text-muted">Average value</small>
                            </div>
                            <div class="metric-icon bg-info">
                                <i class="bi bi-bar-chart"></i>
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
                                <h6 class="text-muted text-uppercase fw-bold mb-2 small">Revenue Growth</h6>
                                <h3 class="mb-0 fw-bold text-success" id="salesSummaryGrowth">0.0%</h3>
                                <small class="text-muted">vs prev. period</small>
                            </div>
                            <div class="metric-icon bg-success">
                                <i class="bi bi-graph-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Sales Top 10 Rankings -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <!-- Top 10 Revenue Generators -->
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-trophy-fill me-2"></i>Top 10 Revenue Generators</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">#</th>
                                        <th class="border-0">Product</th>
                                        <th class="border-0">Revenue</th>
                                        <th class="border-0">Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="top-revenue-generators-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Loading revenue generators...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-person-check-fill me-2"></i>Top 10 Best Cashiers</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">#</th>
                                        <th class="border-0">Cashier</th>
                                        <th class="border-0">Sales</th>
                                        <th class="border-0">Orders</th>
                                    </tr>
                                </thead>
                                <tbody id="top-cashiers-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Loading top cashiers...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <!-- Top 10 Peak Days -->
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Top 10 Peak Sales Days</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">#</th>
                                        <th class="border-0">Date</th>
                                        <th class="border-0">Revenue</th>
                                        <th class="border-0">Orders</th>
                                    </tr>
                                </thead>
                                <tbody id="peak-days-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Loading peak days...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <!-- Top 10 High Value Transactions -->
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-gem me-2"></i>Top 10 High Value Transactions</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">#</th>
                                        <th class="border-0">Transaction</th>
                                        <th class="border-0">Amount</th>
                                        <th class="border-0">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="high-value-transactions-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Loading high value transactions...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Define renderTop10Insights as a global function
window.renderTop10Insights = function(inventoryData, salesData) {
    try {
        // Populate summary cards (inventory)
        if (inventoryData && inventoryData.summary) {
            $('#invSummaryValue').text('₱' + Number(inventoryData.summary.total_value || 0).toLocaleString());
            $('#invSummaryItems').text((inventoryData.summary.total_items || 0) + ' items');
        }
        if (inventoryData && inventoryData.performance_metrics) {
            // Keep first-row metrics only
            $('#invSummaryLowStock').text(inventoryData.performance_metrics.restock_needed_count || 0);
            $('#invSummaryOutOfStock').text(inventoryData.performance_metrics.out_of_stock_count || 0);
            $('#invSummaryAvgStock').text(Number(inventoryData.performance_metrics.average_stock_level || 0).toLocaleString());
        }
        
        // Populate summary cards (sales)
        if (salesData && salesData.summary) {
            $('#salesSummaryRevenue').text('₱' + Number(salesData.summary.total_revenue || 0).toLocaleString());
            $('#salesSummaryTransactions').text(Number(salesData.summary.total_transactions || 0).toLocaleString());
            $('#salesSummaryAvg').text('₱' + Number(salesData.summary.average_transaction || 0).toLocaleString());
            $('#salesSummaryGrowth').text(Number(salesData.summary.revenue_growth || 0).toFixed(1) + '%');
        }

        // Update inventory top 10 tables (map to backend fields)
        if (inventoryData) {
            // Top selling products are driven by sales, not inventory; handled below.
            try { window.updateRestockNeededTable(inventoryData.low_stock_alerts || (inventoryData.top_10_insights ? inventoryData.top_10_insights.lowest_stock : []) || []); } catch (e) { }
            try { window.updateTopOutOfStockTable(inventoryData.out_of_stock || []); } catch (e) { }
            try { window.updateHighestValueProductsTable(inventoryData.top_value_items || (inventoryData.top_10_insights ? inventoryData.top_10_insights.most_valuable : []) || []); } catch (e) { }
            try { window.updateFastestMovingTable((inventoryData.top_10_insights ? inventoryData.top_10_insights.fastest_moving : []) || []); } catch (e) { }
            try { window.updateSlowestMovingTable((inventoryData.top_10_insights ? inventoryData.top_10_insights.slowest_moving : []) || []); } catch (e) { }
            try { window.updateExpiringSoonTable(inventoryData.expiry_alerts || []); } catch (e) { }
            try { window.updateTopCategoriesTable(inventoryData.category_distribution || []); } catch (e) { }
        }
        
        // Update sales performance metrics and tables
        if (salesData) {
            try { window.updateSalesPerformanceMetrics(salesData); } catch (e) { }
            try { 
                window.updateTopSellingProductsTable(salesData.top_products || []); 
                } catch (e) { }
            try { window.updateTopRevenueGeneratorsTable(salesData.top_products || []); } catch (e) { }
            try { window.updateTopCashiersTable(salesData.top_10_insights ? salesData.top_10_insights.top_cashiers || [] : []); } catch (e) { }
            try { window.updatePeakDaysTable(salesData.revenue_trends || []); } catch (e) { }
            try { window.updateHighValueTransactionsTable(salesData.top_10_insights ? salesData.top_10_insights.highest_transactions || [] : []); } catch (e) { }
        } else {
            }
    } catch (err) {
        // Ensure placeholders are not stuck
        const emptyRow = '<tr><td colspan="10" class="text-center text-muted py-4">No data available</td></tr>';
        [
            '#top-selling-products-table', '#restock-needed-table', '#top-out-of-stock-table',
            '#highest-value-products-table', '#top-fastest-moving-table', '#top-slowest-moving-table',
            '#expiring-soon-table', '#top-categories-table', '#top-revenue-generators-table',
            '#top-cashiers-table', '#peak-days-table', '#high-value-transactions-table'
        ].forEach(sel => { try { $(sel).html(emptyRow); } catch(e) {} });
    }
};

// Confirm function is loaded
// Commented out - not currently used
// function updateInventoryPerformanceMetrics(metrics) {
//     $('#stock-turnover-rate').text((metrics.stock_turnover_rate || 0).toFixed(1) + 'x');
//     $('#avg-days-to-sell').text(Math.round(metrics.average_days_to_sell || 0));
//     $('#restock-needed-count').text(metrics.restock_needed_count || 0);
//     $('#out-of-stock-count').text(metrics.out_of_stock_count || 0);
// }

window.updateSalesPerformanceMetrics = function(salesData) {
    if (!salesData) return;
    
    const summary = salesData.summary || {};
    const analytics = salesData.analytics || {};
    
    $('#sales-conversion-rate').text((summary.revenue_growth || 0).toFixed(1) + '%');
    $('#avg-transaction-value').text('₱' + Number(summary.average_transaction || 0).toLocaleString());
    $('#peak-sales-hour').text(analytics.peak_hour ? analytics.peak_hour.hour + ':00' : '00:00');
    $('#total-transactions-count').text(Number(summary.total_transactions || 0).toLocaleString());
}

window.updateTopSellingProductsTable = function(products) {
    const tbody = $('#top-selling-products-table');
    tbody.empty();
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No sales data available</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const pType = product.type || product.order_type || 'product';
        const typeClass = pType === 'menu' ? 'success' : 'primary';
        const rankClass = index < 3 ? 'bg-warning' : 'bg-primary';
        tbody.append(`
            <tr>
                <td><span class="badge ${rankClass}">${index + 1}</span></td>
                <td class="fw-medium">${product.name}</td>
                <td><span class="badge bg-${typeClass}">${pType}</span></td>
                <td class="text-center">${Number(product.total_quantity || product.quantity || 0).toLocaleString()}</td>
                <td class="text-success fw-bold">₱${Number(product.total_sales || 0).toLocaleString()}</td>
            </tr>
        `);
    });
    }

window.updateRestockNeededTable = function(products) {
    const tbody = $('#restock-needed-table');
    tbody.empty();
    
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">All items are well stocked</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const minStock = product.min_stock ?? product.min_stock_level ?? 0;
        const urgencyClass = product.current_stock <= 0 ? 'danger' : 
                           product.current_stock <= (minStock * 0.5) ? 'warning' : 'info';
        const statusText = product.current_stock <= 0 ? 'Out of Stock' : 'Low Stock';
        const name = product.name || product.product_name || 'Unknown';
        
        tbody.append(`
            <tr>
                <td><span class="badge bg-${urgencyClass}">${index + 1}</span></td>
                <td class="fw-medium">${name}</td>
                <td class="text-center">${product.current_stock}</td>
                <td class="text-center">${minStock}</td>
                <td><span class="badge bg-${urgencyClass}">${statusText}</span></td>
            </tr>
        `);
    });
}

window.updateTopOutOfStockTable = function(products) {
    const tbody = $('#top-out-of-stock-table');
    tbody.empty();
    
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No out of stock items</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const name = product.name || product.product_name || 'Unknown';
        const lastSale = product.last_sale_date || product.last_updated || null;
        const priorityClass = 'danger';
        const priorityText = 'Critical';
        
        tbody.append(`
            <tr>
                <td><span class="badge bg-danger">${index + 1}</span></td>
                <td class="fw-medium">${name}</td>
                <td><span class="badge bg-primary">Product</span></td>
                <td class="text-muted">${lastSale ? new Date(lastSale).toLocaleDateString() : 'N/A'}</td>
                <td><span class="badge bg-${priorityClass}">${priorityText}</span></td>
            </tr>
        `);
    });
}

window.updateHighestValueProductsTable = function(products) {
    const tbody = $('#highest-value-products-table');
    tbody.empty();
    
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No inventory data available</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const name = product.name || product.product_name || 'Unknown';
        const unitPrice = product.unit_price ?? product.selling_price ?? 0;
        const stockValue = product.stock_value || ((product.current_stock || 0) * unitPrice);
        tbody.append(`
            <tr>
                <td><span class="badge bg-primary">${index + 1}</span></td>
                <td class="fw-medium">${name}</td>
                <td class="text-success fw-bold">₱${Number(stockValue).toLocaleString()}</td>
                <td class="text-muted">₱${Number(unitPrice).toLocaleString()}</td>
                <td class="text-center">${Number(product.current_stock || 0).toLocaleString()}</td>
            </tr>
        `);
    });
}

window.updateFastestMovingTable = function(products) {
    const tbody = $('#top-fastest-moving-table');
    tbody.empty();
    
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No movement data available</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const name = product.name || product.product_name || 'Unknown';
        const moved = Number(product.total_moved || product.velocity || 0);
        const velocityClass = moved > 50 ? 'success' : moved > 20 ? 'warning' : 'info';
        
        tbody.append(`
            <tr>
                <td><span class="badge bg-info">${index + 1}</span></td>
                <td class="fw-medium">${name}</td>
                <td><span class="badge bg-${velocityClass}">${moved.toLocaleString()} moved</span></td>
                <td class="text-center">${product.current_stock ?? '-'}</td>
                <td class="text-center">-</td>
            </tr>
        `);
    });
}

window.updateSlowestMovingTable = function(products) {
    const tbody = $('#top-slowest-moving-table');
    tbody.empty();
    
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No slow moving items</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const name = product.name || product.product_name || 'Unknown';
        const moved = Number(product.total_moved || 0);
        const actionClass = moved === 0 ? 'danger' : moved <= 5 ? 'warning' : 'info';
        const actionText = moved === 0 ? 'Promote' : moved <= 5 ? 'Monitor' : 'OK';
        
        tbody.append(`
            <tr>
                <td><span class="badge bg-secondary">${index + 1}</span></td>
                <td class="fw-medium">${name}</td>
                <td class="text-center">${moved.toLocaleString()} moved</td>
                <td class="text-center">${product.current_stock ?? '-'}</td>
                <td><span class="badge bg-${actionClass}">${actionText}</span></td>
            </tr>
        `);
    });
}

window.updateExpiringSoonTable = function(products) {
    const tbody = $('#expiring-soon-table');
    tbody.empty();
    
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No items expiring soon</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const dateStr = product.expiry_date || product.exp_date;
        const expiryDate = dateStr ? new Date(dateStr) : null;
        const daysLeft = product.days_until_expiry ?? (expiryDate ? Math.ceil((expiryDate - new Date()) / (1000 * 60 * 60 * 24)) : 0);
        const urgencyClass = daysLeft <= 3 ? 'danger' : daysLeft <= 7 ? 'warning' : 'info';
        const name = product.name || product.product_name || 'Unknown';
        
        tbody.append(`
            <tr>
                <td><span class="badge bg-${urgencyClass}">${index + 1}</span></td>
                <td class="fw-medium">${name}</td>
                <td class="text-center">${expiryDate ? expiryDate.toLocaleDateString() : '-'}</td>
                <td class="text-center"><span class="badge bg-${urgencyClass}">${daysLeft} days</span></td>
                <td class="text-center">${product.current_stock || 0}</td>
            </tr>
        `);
    });
}

window.updateTopCategoriesTable = function(categories) {
    const tbody = $('#top-categories-table');
    tbody.empty();
    
    if (!categories || categories.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">No category data available</td></tr>');
        return;
    }
    
    // Sort by total_value if present (inventory category distribution)
    const sorted = [...categories].sort((a, b) => (b.total_value || 0) - (a.total_value || 0));
    sorted.slice(0, 10).forEach((category, index) => {
        const name = category.category || category.category_name || 'Uncategorized';
        const totalValue = category.total_value ?? category.total_revenue ?? 0;
        const items = category.total_items ?? category.item_count ?? 0;
        const avgPrice = items > 0 ? (totalValue / items) : 0;
        tbody.append(`
            <tr>
                <td><span class="badge bg-primary">${index + 1}</span></td>
                <td class="fw-medium">${name}</td>
                <td class="text-success fw-bold">₱${Number(totalValue).toLocaleString()}</td>
                <td class="text-center">${Number(items).toLocaleString()}</td>
                <td class="text-muted">₱${Number(avgPrice).toLocaleString()}</td>
            </tr>
        `);
    });
}

// Sales Top 10 Table Functions
window.updateTopRevenueGeneratorsTable = function(products) {
    const tbody = $('#top-revenue-generators-table');
    tbody.empty();
    
    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="4" class="text-center text-muted py-4">No revenue data available</td></tr>');
        return;
    }
    
    products.slice(0, 10).forEach((product, index) => {
        const rankClass = index < 3 ? 'bg-warning' : 'bg-success';
        tbody.append(`
            <tr>
                <td><span class="badge ${rankClass}">${index + 1}</span></td>
                <td class="fw-medium">${product.name}</td>
                <td class="text-success fw-bold">₱${Number(product.total_sales).toLocaleString()}</td>
                <td class="text-center">${Number(product.total_quantity).toLocaleString()}</td>
            </tr>
        `);
    });
}

window.updateTopCashiersTable = function(cashiers) {
    const tbody = $('#top-cashiers-table');
    tbody.empty();
    
    if (!cashiers || cashiers.length === 0) {
        tbody.html('<tr><td colspan="4" class="text-center text-muted py-4">No cashier data available</td></tr>');
        return;
    }
    
    cashiers.slice(0, 10).forEach((cashier, index) => {
        const rankClass = index < 3 ? 'bg-warning' : 'bg-primary';
        tbody.append(`
            <tr>
                <td><span class="badge ${rankClass}">${index + 1}</span></td>
                <td class="fw-medium">${cashier.cashier}</td>
                <td class="text-success fw-bold">₱${Number(cashier.total_sales).toLocaleString()}</td>
                <td class="text-center">${Number(cashier.transaction_count).toLocaleString()}</td>
            </tr>
        `);
    });
}

window.updatePeakDaysTable = function(trends) {
    const tbody = $('#peak-days-table');
    tbody.empty();
    
    if (!trends || trends.length === 0) {
        tbody.html('<tr><td colspan="4" class="text-center text-muted py-4">No peak days data available</td></tr>');
        return;
    }
    
    // Sort by revenue and take top 10
    const sortedTrends = trends.sort((a, b) => (b.revenue || 0) - (a.revenue || 0));
    
    sortedTrends.slice(0, 10).forEach((trend, index) => {
        const rankClass = index < 3 ? 'bg-warning' : 'bg-info';
        const label = trend.label || (trend.date ? new Date(trend.date).toLocaleDateString() : '-');
        tbody.append(`
            <tr>
                <td><span class="badge ${rankClass}">${index + 1}</span></td>
                <td class="fw-medium">${label}</td>
                <td class="text-success fw-bold">₱${Number(trend.revenue || 0).toLocaleString()}</td>
                <td class="text-center">${Number(trend.transactions || 0).toLocaleString()}</td>
            </tr>
        `);
    });
}

window.updateHighValueTransactionsTable = function(transactions) {
    const tbody = $('#high-value-transactions-table');
    tbody.empty();
    
    if (!transactions || transactions.length === 0) {
        tbody.html('<tr><td colspan="4" class="text-center text-muted py-4">No high value transactions available</td></tr>');
        return;
    }
    
    transactions.slice(0, 10).forEach((transaction, index) => {
        const rankClass = index < 3 ? 'bg-warning' : 'bg-warning';
        const date = new Date(transaction.created_at).toLocaleDateString();
        tbody.append(`
            <tr>
                <td><span class="badge ${rankClass}">${index + 1}</span></td>
                <td class="fw-medium">${transaction.transaction_number}</td>
                <td class="text-success fw-bold">₱${Number(transaction.total_amount).toLocaleString()}</td>
                <td class="text-muted">${date}</td>
            </tr>
        `);
    });
}

// Safety net: if placeholders still visible after initial loads, force a re-render
// using any data cached in window.currentData.
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        try {
            var text = document.getElementById('top-selling-products-table')?.innerText || '';
            if (text.toLowerCase().includes('loading')) {
                var inv = (window.currentData && window.currentData.inventory) ? window.currentData.inventory : {};
                var sales = (window.currentData && window.currentData.sales) ? window.currentData.sales : {};
                if (typeof window.renderTop10Insights === 'function') {
                    window.renderTop10Insights(inv, sales);
                } else {
                    }
            }
        } catch (e) { }
    }, 800);
});
</script>
