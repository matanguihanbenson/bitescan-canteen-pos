@extends('layouts.app')

@push('styles')
<style>
.stats-card {
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.avatar-sm {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sales-analytics-card {
    border: 1px solid rgba(0, 0, 0, 0.05);
}

/* Ensure better contrast for gradient headers */
.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%) !important;
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}

/* Chart container styling */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

/* Additional chart styling */
.chart-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div
                class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">Sales Analytics Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive overview of sales performance and transaction analytics
                    </p>
                </div>
                <div class="page-title-right">
                    <div class="d-flex align-items-center">
                        <ol class="breadcrumb m-0 me-3">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sales Analytics</li>
                        </ol>
                        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#reportOptionsModal">
                            <i class="bi bi-download me-1"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Selector -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                            <span class="fw-semibold text-dark">Date Range:</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="btn-group" role="group" id="dateRangeButtons">
                                <button type="button" class="btn btn-outline-primary btn-sm active"
                                    data-range="today">Today
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-range="week">This Week
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-range="month">This
                                    Month
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-range="year">This Year
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-range="custom">Custom
                                </button>
                            </div>
                            <div id="customDateRange" class="d-none ms-3">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control form-control-sm" id="startDate"
                                        style="width: 140px;">
                                    <span class="text-muted">to</span>
                                    <input type="date" class="form-control form-control-sm" id="endDate"
                                        style="width: 140px;">
                                    <button type="button" class="btn btn-primary btn-sm" id="applyCustomRange">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="ms-3">
                                <span class="badge bg-primary" id="selectedRangeText">Today</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Sales -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small" id="totalSalesLabel">Total Sales
                                Today
                            </h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="totalSalesToday">₱0.00</h3>
                            <p class="text-muted mb-0 small" id="totalSalesSubtext">Revenue generated today</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-primary bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-currency-dollar text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small" id="transactionsLabel">Transactions
                                Today
                            </h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="transactionsToday">0</h3>
                            <p class="text-muted mb-0 small" id="transactionsSubtext">Completed transactions</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-success bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-receipt text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Transaction -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Average Transaction</h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="averageTransaction">₱0.00</h3>
                            <p class="text-muted mb-0 small">Per transaction average</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-info bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-bar-chart text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash vs Credit -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase fw-bold mb-2 small">Payment Methods</h6>
                            <h3 class="mb-1 text-dark fw-semibold" id="cashPercentage">Cash: 0%</h3>
                            <p class="text-muted mb-0 small" id="creditPercentage">Credit: 0%</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div
                                class="avatar-sm bg-warning bg-gradient rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-credit-card text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card sales-analytics-card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-graph-up me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Sales Trend</h5>
                            <small class="text-white opacity-90">Daily sales performance over the last 30 days</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card sales-analytics-card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-pie-chart me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Top Products</h5>
                            <small class="text-white opacity-90">Best selling items this month</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Payment Methods Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card sales-analytics-card border-0 shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-credit-card-2-front me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Payment Methods</h5>
                            <small class="text-white opacity-90">Distribution of payment types</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hourly Sales Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card sales-analytics-card border-0 shadow-sm">
                <div class="card-header bg-gradient-warning text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history me-2 fs-5 text-white"></i>
                        <div>
                            <h5 class="card-title mb-0 fw-semibold text-white">Hourly Sales</h5>
                            <small class="text-white opacity-90">Sales distribution throughout the day</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container">
                        <canvas id="hourlySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card sales-analytics-card border-0 shadow-sm">
                <div class="card-header bg-gradient-secondary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-list-ul me-2 fs-5 text-white"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold text-white">Recent Transactions</h5>
                                <small class="text-white opacity-90">Latest sales transactions</small>
                            </div>
                        </div>
                        <a href="{{ route('sales.transactions') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-right me-1"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="recentTransactionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold text-dark px-4 py-3">Transaction ID</th>
                                    <th class="border-0 fw-bold text-dark py-3">Customer</th>
                                    <th class="border-0 fw-bold text-dark py-3">Amount</th>
                                    <th class="border-0 fw-bold text-dark py-3">Payment</th>
                                    <th class="border-0 fw-bold text-dark py-3">Time</th>
                                    <th class="border-0 fw-bold text-dark py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactionsBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Options Modal -->
<div class="modal fade" id="reportOptionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-semibold text-white">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Sales Report Generator
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportOptionsForm">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Date Range</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="start_date"
                                        value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                                    <span class="input-group-text">to</span>
                                    <input type="date" class="form-control" name="end_date"
                                        value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="form-text">Select the date range for your sales report</div>
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
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block mb-3">Report Sections</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" name="sections[]" value="summary" checked
                                        id="section_summary">
                                    <label class="form-check-label" for="section_summary">
                                        <strong>Executive Summary</strong>
                                        <small class="d-block text-muted">Total revenue, transactions, and growth metrics</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" name="sections[]" value="analytics"
                                        checked id="section_analytics">
                                    <label class="form-check-label" for="section_analytics">
                                        <strong>Analytics Insights</strong>
                                        <small class="d-block text-muted">Peak hours, payment methods, and trends</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" name="sections[]" value="products" checked
                                        id="section_products">
                                    <label class="form-check-label" for="section_products">
                                        <strong>Product Performance</strong>
                                        <small class="d-block text-muted">Top selling products and categories</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" name="sections[]" value="transactions"
                                        id="section_transactions">
                                    <label class="form-check-label" for="section_transactions">
                                        <strong>Transaction Details</strong>
                                        <small class="d-block text-muted">Individual transaction records</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="include_charts" checked id="include_charts">
                            <label class="form-check-label" for="include_charts">
                                <strong>Include Charts & Visualizations</strong>
                                <small class="d-block text-muted">Add graphs and charts to the report (not available for CSV format)</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <div>
                            <strong>Tip:</strong> Use "Preview in Browser" to review your report before downloading. 
                            Large date ranges may take longer to generate.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div class="text-muted small">
                        <i class="bi bi-clock me-1"></i>Report generation may take 10-30 seconds
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-gear-fill me-1"></i>Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentDateRange = 'today';
let currentStartDate = null;
let currentEndDate = null;

$(document).ready(function() {
    // Initialize date inputs with current date
    const today = new Date().toISOString().split('T')[0];
    $('#startDate').val(today);
    $('#endDate').val(today);

    // Set initial date range
    const {
        startDate,
        endDate
    } = getDateRange('today');
    currentStartDate = startDate;
    currentEndDate = endDate;

    // Initialize all components
    initializeCharts();

    // Setup date range button handlers first
    setupDateRangeHandlers();

    // Then load data
    setTimeout(() => {
        loadDashboardData();
        loadRecentTransactions();
    }, 500);

    // Auto-refresh every 5 minutes
    setInterval(function() {
        loadDashboardData();
        loadRecentTransactions();
    }, 300000);
});

function setupDateRangeHandlers() {
    // Remove existing handlers first
    $('#dateRangeButtons button').off('click');
    $('#applyCustomRange').off('click');

    // Date range button clicks
    $('#dateRangeButtons button').on('click', function(e) {
        e.preventDefault();
        const range = $(this).data('range');
        selectDateRange(range);
    });

    // Custom date range application
    $('#applyCustomRange').on('click', function(e) {
        e.preventDefault();
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be after end date');
            return;
        }

        currentStartDate = startDate;
        currentEndDate = endDate;

        // Update selected range text
        const formattedStart = new Date(startDate).toLocaleDateString();
        const formattedEnd = new Date(endDate).toLocaleDateString();
        $('#selectedRangeText').text(`${formattedStart} - ${formattedEnd}`);

        // Reload data
        loadDashboardData();
        loadRecentTransactions();
    });
}

function selectDateRange(range) {
    currentDateRange = range;

    // Update button states
    $('#dateRangeButtons button').removeClass('active');
    $(`#dateRangeButtons button[data-range="${range}"]`).addClass('active');

    // Show/hide custom date inputs
    if (range === 'custom') {
        $('#customDateRange').removeClass('d-none');
        return; // Don't reload data yet, wait for custom date selection
    } else {
        $('#customDateRange').addClass('d-none');
    }

    // Calculate date range
    const {
        startDate,
        endDate,
        displayText
    } = getDateRange(range);
    currentStartDate = startDate;
    currentEndDate = endDate;

    // Update labels based on selected range
    updateLabelsForRange(range);

    // Update selected range text
    $('#selectedRangeText').text(displayText);

    // Reload data
    loadDashboardData();
    loadRecentTransactions();
}

function getDateRange(range) {
    const now = new Date();
    let startDate, endDate, displayText;

    switch (range) {
        case 'today':
            startDate = now.toISOString().split('T')[0];
            endDate = startDate;
            displayText = 'Today';
            break;

        case 'week':
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - now.getDay());
            startDate = startOfWeek.toISOString().split('T')[0];
            endDate = now.toISOString().split('T')[0];
            displayText = 'This Week';
            break;

        case 'month':
            startDate = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            endDate = now.toISOString().split('T')[0];
            displayText = 'This Month';
            break;

        case 'year':
            startDate = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
            endDate = now.toISOString().split('T')[0];
            displayText = 'This Year';
            break;

        default:
            startDate = now.toISOString().split('T')[0];
            endDate = startDate;
            displayText = 'Today';
    }

    return {
        startDate,
        endDate,
        displayText
    };
}

function updateLabelsForRange(range) {
    const labels = {
        today: {
            sales: 'Total Sales Today',
            salesSubtext: 'Revenue generated today',
            transactions: 'Transactions Today',
            transactionsSubtext: 'Completed transactions'
        },
        week: {
            sales: 'Total Sales This Week',
            salesSubtext: 'Revenue generated this week',
            transactions: 'Transactions This Week',
            transactionsSubtext: 'Completed transactions'
        },
        month: {
            sales: 'Total Sales This Month',
            salesSubtext: 'Revenue generated this month',
            transactions: 'Transactions This Month',
            transactionsSubtext: 'Completed transactions'
        },
        year: {
            sales: 'Total Sales This Year',
            salesSubtext: 'Revenue generated this year',
            transactions: 'Transactions This Year',
            transactionsSubtext: 'Completed transactions'
        }
    };

    const label = labels[range] || labels.today;

    $('#totalSalesLabel').text(label.sales);
    $('#totalSalesSubtext').text(label.salesSubtext);
    $('#transactionsLabel').text(label.transactions);
    $('#transactionsSubtext').text(label.transactionsSubtext);
}

function loadDashboardData() {
    // Show loading state
    $('#totalSalesToday').text('Loading...');
    $('#transactionsToday').text('Loading...');
    $('#averageTransaction').text('Loading...');
    $('#cashPercentage').text('Loading...');
    $('#creditPercentage').text('Loading...');

    // Use existing controllers with better error handling
    let insightsUrl = '/transaction-insights';
    let topSellingUrl = '/top-selling-items';

    const params = new URLSearchParams();
    if (currentStartDate && currentEndDate) {
        params.append('start_date', currentStartDate);
        params.append('end_date', currentEndDate);
        params.append('range', currentDateRange);
    }

    const queryString = params.toString();
    if (queryString) {
        insightsUrl += `?${queryString}`;
        topSellingUrl += `?${queryString}`;
    }

    // Load transaction insights
    $.get(insightsUrl)
        .done(function(insights) {
            updateSummaryCards(insights);
            updateChartsWithRealData(insights);
        })
        .fail(function(xhr, status, error) {
            // Try dashboard summary endpoint as fallback
            $.get('/dashboard-summary?' + queryString)
                .done(function(summary) {
                    updateSummaryCards(summary);
                    updateChartsWithRealData(summary);
                })
                .fail(function() {
                    showNoDataState();
                });
        });

    // Load top selling items
    $.get(topSellingUrl)
        .done(function(topSellingData) {
            updateTopProductsChart(topSellingData);
        })
        .fail(function(xhr, status, error) {
            updateTopProductsChart({
                menu: {
                    top: []
                },
                products: {
                    top: []
                }
            });
        });

    // Load sales chart data
    $.get('/sales-chart-data?' + queryString)
        .done(function(chartData) {
            updateSalesTrendChart(chartData);
        })
        .fail(function(xhr, status, error) {
            // Show no data state for sales trend chart
            showSalesTrendNoData();
        });
}

function showNoDataState() {
    // Set default values when API fails
    $('#totalSalesToday').text('₱0.00');
    $('#transactionsToday').text('0');
    $('#averageTransaction').text('₱0.00');
    $('#cashPercentage').text('Cash: 0%');
    $('#creditPercentage').text('Credit: 0%');

    // Update charts with empty state
    showSalesTrendNoData();
    showPaymentMethodsNoData();
    showHourlySalesNoData();
    showTopProductsNoData();
}

function showSalesTrendNoData() {
    if (window.salesTrendChart) {
        window.salesTrendChart.data.labels = ['No Data Available'];
        window.salesTrendChart.data.datasets[0].data = [0];
        window.salesTrendChart.update();
    }
}

function showPaymentMethodsNoData() {
    if (window.paymentMethodsChart) {
        window.paymentMethodsChart.data.datasets[0].data = [0, 0];
        window.paymentMethodsChart.update();
    }
}

function showHourlySalesNoData() {
    if (window.hourlySalesChart) {
        window.hourlySalesChart.data.datasets[0].data = Array(12).fill(0);
        window.hourlySalesChart.update();
    }
}

function showTopProductsNoData() {
    if (window.topProductsChart) {
        window.topProductsChart.data.labels = ['No Data Available'];
        window.topProductsChart.data.datasets[0].data = [1];
        window.topProductsChart.data.datasets[0].backgroundColor = ['#e9ecef'];
        window.topProductsChart.update();
    }
}

function updateTopProductsChart(topSellingData) {
    if (!window.topProductsChart) return;

    // Combine menu items and products
    const menuItems = topSellingData.menu?.top || [];
    const products = topSellingData.products?.top || [];

    // Combine and sort all items by total sales/quantity
    const allItems = [];

    // Add menu items
    menuItems.forEach(item => {
        allItems.push({
            name: item.name || 'Menu Item',
            value: parseFloat(item.total_sales || item.quantity || 0),
            type: 'Menu'
        });
    });

    // Add products
    products.forEach(item => {
        allItems.push({
            name: item.name || item.product_name || 'Product',
            value: parseFloat(item.total_sales || item.quantity || 0),
            type: 'Product'
        });
    });

    // Sort by value and take top 5
    allItems.sort((a, b) => b.value - a.value);
    const topItems = allItems.slice(0, 5);

    if (topItems.length === 0) {
        showTopProductsNoData();
        return;
    }

    // Prepare chart data
    const labels = topItems.map(item => {
        const name = item.name;
        const displayName = name.length > 15 ? name.substring(0, 15) + '...' : name;
        return displayName;
    });
    const data = topItems.map(item => item.value);

    // Generate colors for the chart
    const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1'];

    window.topProductsChart.data.labels = labels;
    window.topProductsChart.data.datasets[0].data = data;
    window.topProductsChart.data.datasets[0].backgroundColor = colors.slice(0, topItems.length);

    window.topProductsChart.update();
}

function updateSummaryCards(data) {
    // Handle different response formats from your controllers
    const totalAmount = Number(data.total_revenue || data.total_amount || data.today_amount || 0);
    const totalTransactions = data.total_transactions || data.today_total || 0;
    const avgTransaction = data.average_transaction || (totalTransactions > 0 ? (totalAmount / totalTransactions) : 0);
    const cashAmount = Number(data.cash_revenue || data.cash_amount || data.cash_on_hand || 0);

    $('#totalSalesToday').text('₱' + totalAmount.toFixed(2));
    $('#transactionsToday').text(totalTransactions);
    $('#averageTransaction').text('₱' + Number(avgTransaction).toFixed(2));

    // Calculate payment method percentages
    const cashPercentage = totalAmount > 0 ? Math.round((cashAmount / totalAmount) * 100) : 0;
    const creditPercentage = 100 - cashPercentage;

    $('#cashPercentage').text('Cash: ' + cashPercentage + '%');
    $('#creditPercentage').text('Credit: ' + creditPercentage + '%');
}

function updateChartsWithRealData(data) {
    // Update Payment Methods Chart with real data
    const totalAmount = Number(data.total_revenue || data.total_amount || data.today_amount || 0);
    const cashAmount = Number(data.cash_revenue || data.cash_amount || data.cash_on_hand || 0);
    const creditAmount = totalAmount - cashAmount;

    if (window.paymentMethodsChart) {
        window.paymentMethodsChart.data.datasets[0].data = [cashAmount, creditAmount];
        window.paymentMethodsChart.update();
    }

    // Update hourly sales chart with real data
    if (data.hourly_sales && window.hourlySalesChart) {
        const hourlyLabels = ['6AM', '7AM', '8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM'];
        const hourlyData = hourlyLabels.map((label, index) => {
            const hour = index + 6;
            return Number(data.hourly_sales[hour] || 0);
        });

        window.hourlySalesChart.data.datasets[0].data = hourlyData;
        window.hourlySalesChart.update();
    } else {
        // Load hourly sales data separately
        loadHourlySalesData();
    }
}

function updateSalesTrendChart(chartData) {
    if (!window.salesTrendChart) return;

    // Check the response format - it could be wrapped in 'data' or direct
    let labels, salesData;

    if (chartData.status === 'success') {
        // Response from updated getSalesChartData method
        labels = chartData.labels || [];
        salesData = chartData.data || [];
    } else if (chartData.data && chartData.data.labels) {
        // Response with nested data
        labels = chartData.data.labels || [];
        salesData = chartData.data.sales || [];
    } else if (chartData.labels) {
        // Direct response
        labels = chartData.labels || [];
        salesData = chartData.data || chartData.sales || [];
    } else {
        showSalesTrendNoData();
        return;
    }

    // Check if we have valid chart data
    if (labels.length === 0 || salesData.length === 0) {
        showSalesTrendNoData();
        return;
    }

    // Convert to numbers
    const processedSalesData = salesData.map(value => Number(value) || 0);

    window.salesTrendChart.data.labels = labels;
    window.salesTrendChart.data.datasets[0].data = processedSalesData;
    window.salesTrendChart.update();
}

function loadHourlySalesData() {
    const params = new URLSearchParams();
    if (currentStartDate && currentEndDate) {
        params.append('start_date', currentStartDate);
        params.append('end_date', currentEndDate);
        params.append('range', currentDateRange);
    }

    $.get('/sales-chart-data?' + params.toString())
        .done(function(chartData) {
            if (window.hourlySalesChart && chartData.labels && chartData.data) {
                if (chartData.labels.length === 24) {
                    const businessHours = chartData.data.slice(6, 18); // 6AM to 6PM
                    window.hourlySalesChart.data.datasets[0].data = businessHours;
                    window.hourlySalesChart.update();
                }
            }
        })
        .fail(function() {
            showHourlySalesNoData();
        });
}

function loadRecentTransactions() {
    // Use existing TransactionController endpoint
    let url = '/recent-transactions';
    const params = new URLSearchParams();

    if (currentStartDate && currentEndDate) {
        params.append('start_date', currentStartDate);
        params.append('end_date', currentEndDate);
        params.append('range', currentDateRange);
        params.append('limit', '10');
    }

    if (params.toString()) {
        url += `?${params.toString()}`;
    }

    $.get(url)
        .done(function(response) {
            const tbody = $('#recentTransactionsBody');

            if (!response.transactions || response.transactions.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            No transactions found for selected period (${currentDateRange})
                        </td>
                    </tr>
                `);
                return;
            }

            const transactionsHtml = response.transactions.map(transaction => {
                const paymentBadge = transaction.payment_type === 'cash' ?
                    '<span class="badge bg-success">Cash</span>' :
                    '<span class="badge bg-info">Credit</span>';

                const statusBadge = transaction.status === 'completed' ?
                    '<span class="badge bg-success">Completed</span>' :
                    '<span class="badge bg-warning">Pending</span>';

                const customerInfo = transaction.charge_to || 'Walk-in Customer';

                // Format date based on range
                let dateDisplay = transaction.created_at;
                if (transaction.created_at) {
                    const date = new Date(transaction.created_at);
                    if (currentDateRange === 'today') {
                        dateDisplay = date.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } else {
                        dateDisplay = date.toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                }

                return `
                    <tr>
                        <td class="px-4">${transaction.transaction_number}</td>
                        <td>${customerInfo}</td>
                        <td>₱${parseFloat(transaction.total_amount || 0).toFixed(2)}</td>
                        <td>${paymentBadge}</td>
                        <td>${dateDisplay}</td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
            }).join('');

            tbody.html(transactionsHtml);
        })
        .fail(function(error) {
            $('#recentTransactionsBody').html(`
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
                        Error loading transactions for ${currentDateRange}
                    </td>
                </tr>
            `);
        });
}

// Report generation
$('#reportOptionsForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    const sections = formData.getAll('sections[]');
    const format = formData.get('format') || 'pdf';
    const includeCharts = formData.has('include_charts');
    
    // Validation
    if (!startDate || !endDate) {
        alert('Please select both start and end dates.');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be after end date.');
        return;
    }
    
    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Generating...');
    submitBtn.prop('disabled', true);
    
    // Prepare the request data
    const requestData = {
        start_date: startDate,
        end_date: endDate,
        sections: sections,
        format: format,
        include_charts: includeCharts,
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    if (format === 'preview') {
        // Open preview in new window
        const params = new URLSearchParams(requestData);
        window.open('/sales/reports/preview?' + params.toString(), '_blank', 'width=1200,height=800,scrollbars=yes');
        
        // Reset button
        submitBtn.html(originalText);
        submitBtn.prop('disabled', false);
        bootstrap.Modal.getInstance(document.getElementById('reportOptionsModal')).hide();
    } else {
        // Download report
        $.ajax({
            url: '/sales/export-report',
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
                let filename = 'sales-report.' + format;
                if (contentDisposition && contentDisposition.includes('filename=')) {
                    filename = contentDisposition.split('filename=')[1].replace(/"/g, '');
                }
                
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                // Reset button and close modal
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
                bootstrap.Modal.getInstance(document.getElementById('reportOptionsModal')).hide();
                
                // Show success message
                showNotification('Report generated successfully!', 'success');
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Failed to generate report. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                alert('Error: ' + errorMessage);
                
                // Reset button
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    }
});

function initializeCharts() {
    // Sales Trend Chart
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    window.salesTrendChart = new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: ['Loading...'],
            datasets: [{
                label: 'Daily Sales',
                data: [0],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toFixed(0);
                        }
                    }
                }
            }
        }
    });

    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    window.topProductsChart = new Chart(topProductsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Loading...'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e9ecef'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${Math.round(value)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    window.paymentMethodsChart = new Chart(paymentMethodsCtx, {
        type: 'pie',
        data: {
            labels: ['Cash', 'Credit'],
            datasets: [{
                data: [0, 0],
                backgroundColor: ['#198754', '#ffc107'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ₱' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Hourly Sales Chart
    const hourlySalesCtx = document.getElementById('hourlySalesChart').getContext('2d');
    window.hourlySalesChart = new Chart(hourlySalesCtx, {
        type: 'bar',
        data: {
            labels: ['6AM', '7AM', '8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM'],
            datasets: [{
                label: 'Sales by Hour',
                data: Array(12).fill(0),
                backgroundColor: '#ffc107',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toFixed(0);
                        }
                    }
                }
            }
        }
    });

    }

// Notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    // Append to body
    $('body').append(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}
</script>
@endpush