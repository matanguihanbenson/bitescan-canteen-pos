@extends('layouts.app')

@section('title', 'Reports Dashboard')

@push('styles')
<style>
    .reports-nav .nav-link {
        border-radius: 10px;
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    
    .reports-nav .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .filter-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: none;
        border-radius: 15px;
    }
    
    .summary-card {
        background: white;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .chart-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    }
    
    .insight-card {
        background: white;
        border-radius: 15px;
        border-left: 4px solid;
    }
    
    .insight-card.inventory { border-left-color: #10b981; }
    .insight-card.sales { border-left-color: #3b82f6; }
    .insight-card.warning { border-left-color: #f59e0b; }
    .insight-card.critical { border-left-color: #ef4444; }
    
    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    
.top-item {
        padding: 8px 10px;
        background: #ffffff;
        border-radius: 6px;
        margin-bottom: 6px;
        border: 1px solid #eef2f7;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-white p-4 rounded shadow-sm">
                <div>
                    <h4 class="mb-1 font-size-18 fw-semibold">📊 Reports Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive analytics and insights for your business</p>
                </div>
                <div class="page-title-right">
                    <div class="d-flex align-items-center">
                        <ol class="breadcrumb m-0 me-3">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bi bi-download me-1"></i>Export Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills reports-nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" id="overview-tab" data-bs-toggle="pill" href="#overview" role="tab">
                        <i class="bi bi-speedometer2 me-2"></i>Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="inventory-tab" data-bs-toggle="pill" href="#inventory" role="tab">
                        <i class="bi bi-boxes me-2"></i>Inventory Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="sales-tab" data-bs-toggle="pill" href="#sales" role="tab">
                        <i class="bi bi-graph-up me-2"></i>Sales Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="top10-tab" data-bs-toggle="pill" href="#top10" role="tab">
                        <i class="bi bi-trophy me-2"></i>Top 10 Insights
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 text-white fw-semibold">Filter Options</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date Filter</label>
                            <select class="form-select" id="dateFilter">
                                <option value="today">Today</option>
                                <option value="this_week" selected>This Week</option>
                                <option value="this_month">This Month</option>
                                <option value="this_year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="customDateRange" style="display: none;">
                            <label class="form-label fw-semibold">Custom Range</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control" id="startDate">
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select class="form-select" id="categoryFilter">
                                <option value="all">All Categories</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Payment Type</label>
                            <select class="form-select" id="paymentFilter">
                                <option value="all">All Payment Types</option>
                                <option value="cash">Cash</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button class="btn btn-primary" id="applyFilters">
                                <i class="bi bi-funnel me-2"></i>Apply Filters
                            </button>
                            <button class="btn btn-outline-secondary ms-2" id="resetFilters">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="reportsTabContent">
        
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <!-- Summary Cards -->
            <div class="row g-4 mb-4" id="overviewSummary">
                <div class="col-xl-3 col-md-6">
                    <div class="card summary-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted text-uppercase fw-bold mb-2 small">Total Revenue</h6>
                                    <h3 class="mb-0 fw-bold text-success" id="totalRevenue">₱0.00</h3>
                                    <small class="text-success" id="revenueGrowth">+0%</small>
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
                                    <h3 class="mb-0 fw-bold text-primary" id="totalTransactions">0</h3>
                                    <small class="text-muted" id="avgTransaction">Avg: ₱0.00</small>
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
                                    <h6 class="text-muted text-uppercase fw-bold mb-2 small">Inventory Value</h6>
                                    <h3 class="mb-0 fw-bold text-info" id="inventoryValue">₱0.00</h3>
                                    <small class="text-muted" id="totalItems">0 items</small>
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
                                    <h6 class="text-muted text-uppercase fw-bold mb-2 small">Alerts</h6>
                                    <h3 class="mb-0 fw-bold text-warning" id="alertsCount">0</h3>
                                    <small class="text-muted">Low stock & expired</small>
                                </div>
                                <div class="metric-icon bg-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card chart-container border-0">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="card-title mb-0">Revenue Trends</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueTrendChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card chart-container border-0">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="card-title mb-0">Payment Methods</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Insights -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card insight-card inventory border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0">Inventory Alerts</h6>
                        </div>
                        <div class="card-body" id="inventoryAlerts">
                            <p class="text-muted">Loading inventory alerts...</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card insight-card sales border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="mb-0">Top Selling Items</h6>
                        </div>
                        <div class="card-body" id="topSellingItems">
                            <p class="text-muted">Loading top selling items...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Tab -->
        <div class="tab-pane fade" id="inventory" role="tabpanel">
            @include('reports.partials.inventory-reports')
        </div>

        <!-- Sales Tab -->
        <div class="tab-pane fade" id="sales" role="tabpanel">
            @include('reports.partials.sales-reports')
        </div>

        <!-- Top 10 Insights Tab -->
        <div class="tab-pane fade" id="top10" role="tabpanel">
            @include('reports.partials.top-insights')
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📁 Export Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Report Type</label>
                            <select class="form-select" name="report_type" id="exportReportType" required>
                                <option value="inventory">Inventory Report (Valuation, Low/Out of Stock, Top Value)</option>
                                <option value="sales">Sales Report (Summary, Top Products, Cashiers, Peak Days)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="bi bi-file-earmark-arrow-down me-2"></i>Format</label>
                            <select class="form-select" name="format" required>
                                <option value="pdf">📄 PDF Document</option>
                                <option value="excel">📊 Excel Spreadsheet</option>
                                <option value="csv">📋 CSV File</option>
                            </select>
                        </div>
                        
                        <!-- Date Filter -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="bi bi-calendar-range me-2"></i>Date Range</label>
                            <select class="form-select" name="date_filter" id="exportDateFilter">
                                <option value="today">Today</option>
                                <option value="this_week" selected>This Week</option>
                                <option value="this_month">This Month</option>
                                <option value="this_year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        
                        <!-- Category Filter (for inventory) -->
                        <div class="col-md-6" id="exportCategoryFilter">
                            <label class="form-label fw-bold"><i class="bi bi-tag me-2"></i>Category</label>
                            <select class="form-select" name="category" id="exportCategory">
                                <option value="all">All Categories</option>
                                <!-- Categories will be loaded dynamically -->
                            </select>
                        </div>
                        
                        <!-- Payment Type Filter (for sales) -->
                        <div class="col-md-6 d-none" id="exportPaymentFilter">
                            <label class="form-label fw-bold"><i class="bi bi-credit-card me-2"></i>Payment Type</label>
                            <select class="form-select" name="payment_type">
                                <option value="all">All Payment Types</option>
                                <option value="cash">Cash Only</option>
                                <option value="credit">Credit Only</option>
                            </select>
                        </div>
                        
                        <!-- Custom Date Range -->
                        <div class="col-12 d-none" id="exportCustomDateRange">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="exportStartDate">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="exportEndDate">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="my-3">
                            <label class="form-label fw-bold"><i class="bi bi-list-check me-2"></i>Include Sections</label>
                            <div class="form-check-group" id="inventorySections">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="valuation_summary" checked>
                                    <label class="form-check-label">Valuation Summary</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="low_stock" checked>
                                    <label class="form-check-label">Low Stock Items</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="out_of_stock" checked>
                                    <label class="form-check-label">Out of Stock Items</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="expiry_alerts">
                                    <label class="form-check-label">Expiry Alerts</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="category_distribution">
                                    <label class="form-check-label">Category Distribution</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="stock_movement">
                                    <label class="form-check-label">Stock Movement Trends</label>
                                </div>
                            </div>
                            <div class="form-check-group d-none" id="salesSections">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="executive_summary" checked>
                                    <label class="form-check-label">Executive Summary</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="revenue_trends" checked>
                                    <label class="form-check-label">Revenue Trends</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="payment_breakdown">
                                    <label class="form-check-label">Payment Breakdown</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="top_products" checked>
                                    <label class="form-check-label">Top Products</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="cashiers">
                                    <label class="form-check-label">Top Cashiers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="peak_times">
                                    <label class="form-check-label">Peak Days/Hours</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="transactions">
                                    <label class="form-check-label">Recent Transactions</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="exportButton">
                    <i class="bi bi-download me-2"></i>Export Report
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart, paymentChart;
let currentData = {
    inventory: {},
    sales: {},
    overview: {}
};

$(document).ready(function() {
    // Initialize
    loadCategories();
    loadExportCategories();
    applyFilters();
    
    // Event handlers
    $('#dateFilter').change(function() {
        $('#customDateRange').toggle($(this).val() === 'custom');
    });
    
    // Export modal date filter handler
    $('#exportDateFilter').change(function() {
        $('#exportCustomDateRange').toggle($(this).val() === 'custom');
    });
    
    $('#applyFilters').click(applyFilters);
    $('#resetFilters').click(resetFilters);
    $('#exportButton').click(exportReport);

    // Toggle include sections based on report type
    $('#exportReportType').on('change', function() {
        const type = $(this).val();
        if (type === 'inventory') {
            $('#inventorySections').removeClass('d-none');
            $('#salesSections').addClass('d-none');
            $('#exportCategoryFilter').removeClass('d-none');
            $('#exportPaymentFilter').addClass('d-none');
        } else {
            $('#inventorySections').addClass('d-none');
            $('#salesSections').removeClass('d-none');
            $('#exportCategoryFilter').addClass('d-none');
            $('#exportPaymentFilter').removeClass('d-none');
        }
    }).trigger('change');
    
    // Tab change handler - use stored filters
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        const filters = currentFilters || getFilters();
        
        if (target === '#inventory') {
            loadInventoryReports(filters);
        } else if (target === '#sales') {
            loadSalesReports(filters);
        } else if (target === '#top10') {
            loadTop10Insights(filters);
        }
    });
});

function loadCategories() {
    $.get('/categories/get').done(function(categories) {
        const select = $('#categoryFilter');
        categories.forEach(category => {
            select.append(`<option value="${category.id}">${category.name}</option>`);
        });
    });
}

function loadExportCategories() {
    $.get('/categories/get').done(function(categories) {
        const select = $('#exportCategory');
        categories.forEach(category => {
            select.append(`<option value="${category.id}">${category.name}</option>`);
        });
    });
}

function getFilters() {
    const filters = {
        date_filter: $('#dateFilter').val(),
        category: $('#categoryFilter').val(),
        payment_type: $('#paymentFilter').val()
    };
    
    if (filters.date_filter === 'custom') {
        filters.start_date = $('#startDate').val();
        filters.end_date = $('#endDate').val();
    }
    
    return filters;
}

// Store current filters globally
let currentFilters = {};

function applyFilters() {
    // Preserve current active tab
    const activeTabId = $('#reportsTabContent .tab-pane.active').attr('id');

    const filters = getFilters();
    
    // Store filters globally for tab switching
    currentFilters = filters;
    
    // Load overview data
    loadOverviewData(filters);
    
    // Pre-load all tab data with filters for consistent filtering
    loadInventoryReports(filters);
    loadSalesReports(filters);
    loadTop10Insights(filters);

    // Restore active tab (Bootstrap 5)
    if (activeTabId) {
        const triggerEl = document.querySelector(`[data-bs-toggle="pill"][href="#${activeTabId}"]`);
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    }
}

function loadOverviewData(filters = {}) {
    // Load both inventory and sales data for overview
    Promise.all([
        $.get('/reports/inventory/analytics', filters),
        $.get('/reports/sales/analytics', filters)
    ]).then(([inventoryRes, salesRes]) => {
        if (inventoryRes.success && salesRes.success) {
            updateOverviewSummary(inventoryRes.data, salesRes.data);
            updateOverviewCharts(inventoryRes.data, salesRes.data);
            updateQuickInsights(inventoryRes.data, salesRes.data);
        }
    }).catch(error => {
        showNotification('Error loading overview data', 'error');
    });
}

function updateOverviewSummary(inventory, sales) {
    // Update summary cards
    $('#totalRevenue').text('₱' + Number(sales.summary.total_revenue || 0).toLocaleString());
    $('#revenueGrowth').text((sales.summary.revenue_growth > 0 ? '+' : '') + (sales.summary.revenue_growth || 0) + '%');
    $('#totalTransactions').text(Number(sales.summary.total_transactions || 0).toLocaleString());
    $('#avgTransaction').text('Avg: ₱' + Number(sales.summary.average_transaction || 0).toLocaleString());
    $('#inventoryValue').text('₱' + Number(inventory.summary.total_value || 0).toLocaleString());
    $('#totalItems').text((inventory.summary.total_items || 0) + ' items');
    
    const alertsCount = (inventory.summary.low_stock_count || 0) + (inventory.summary.out_of_stock_count || 0);
    $('#alertsCount').text(alertsCount);
}

function updateOverviewCharts(inventory, sales) {
    // Revenue trend chart
    const ctx1 = document.getElementById('revenueTrendChart');
    if (revenueChart) revenueChart.destroy();
    
    const trendData = sales.revenue_trends || [];
    
    // Get current date filter
    const dateFilter = $('#dateFilter').val() || 'this_week';
    
    let labels = [];
    let revenueData = [];
    
    // If no data or empty trends, generate default labels
    if (!trendData || trendData.length === 0) {
        // Generate default labels based on filter type
        switch(dateFilter) {
            case 'today':
                // Generate 24 hours (12am to 11pm)
                for (let i = 0; i < 24; i++) {
                    const period = i >= 12 ? 'pm' : 'am';
                    const displayHour = i === 0 ? 12 : (i > 12 ? i - 12 : i);
                    labels.push(`${displayHour}${period}`);
                    revenueData.push(0);
                }
                break;
            
            case 'this_week':
                // Generate 7 days (Mon to Sun)
                const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                labels = weekDays;
                revenueData = new Array(7).fill(0);
                break;
            
            case 'this_month':
                // Generate weeks (Week 1 to Week 4)
                for (let i = 1; i <= 4; i++) {
                    labels.push(`Week ${i}`);
                    revenueData.push(0);
                }
                break;
            
            case 'this_year':
                // Generate 12 months (Jan to Dec)
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                labels = months;
                revenueData = new Array(12).fill(0);
                break;
            
            default:
                // Default to 7 days
                labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                revenueData = new Array(7).fill(0);
        }
    } else {
        // Generate labels from data
        labels = trendData.map(d => {
            if (d.label) {
                // Backend provided label
                return d.label;
            } else if (d.hour !== undefined) {
                // Hourly data
                const hour = parseInt(d.hour);
                const period = hour >= 12 ? 'pm' : 'am';
                const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
                return `${displayHour}${period}`;
            } else {
                // Daily/weekly/monthly data
                return new Date(d.date).toLocaleDateString();
            }
        });
        
        revenueData = trendData.map(d => d.revenue || 0);
    }
    
    revenueChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: revenueData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
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
    
    // Payment methods chart
    const ctx2 = document.getElementById('paymentChart');
    if (paymentChart) paymentChart.destroy();
    
    const paymentData = sales.payment_breakdown || [];
    paymentChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(p => p.payment_type.charAt(0).toUpperCase() + p.payment_type.slice(1)),
            datasets: [{
                data: paymentData.map(p => p.total_amount),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function updateQuickInsights(inventory, sales) {
    // Inventory alerts
    const lowStock = inventory.low_stock_alerts || [];
    const outOfStock = inventory.out_of_stock || [];
    
    let alertsHtml = '';
    if (lowStock.length === 0 && outOfStock.length === 0) {
        alertsHtml = '<p class="text-success">✅ No inventory alerts!</p>';
    } else {
        lowStock.slice(0, 3).forEach(item => {
            alertsHtml += `<div class="alert alert-warning py-2 mb-2">
                <small><strong>${item.product_name}</strong> - Only ${item.current_stock} ${item.unit} left</small>
            </div>`;
        });
        outOfStock.slice(0, 2).forEach(item => {
            alertsHtml += `<div class="alert alert-danger py-2 mb-2">
                <small><strong>${item.product_name}</strong> - Out of stock</small>
            </div>`;
        });
    }
    $('#inventoryAlerts').html(alertsHtml);
    
    // Top selling items
    const topProducts = sales.top_products || [];
    let topHtml = '';
    if (topProducts.length === 0) {
        topHtml = '<p class="text-muted">No sales data available</p>';
    } else {
        topProducts.slice(0, 5).forEach((item, index) => {
            topHtml += `<div class="top-item">
                <div class="d-flex justify-content-between">
                    <span class="fw-medium">${index + 1}. ${item.name}</span>
                    <span class="text-success">₱${Number(item.total_sales).toLocaleString()}</span>
                </div>
                <small class="text-muted">${item.total_quantity} sold</small>
            </div>`;
        });
    }
    $('#topSellingItems').html(topHtml);
}

function loadInventoryReports(filters = {}) {
    $.get('/reports/inventory/analytics', filters)
        .done(function(response) {
            if (response.success) {
                currentData.inventory = response.data;
                renderInventoryReports(response.data);
            }
        })
        .fail(function() {
            showNotification('Error loading inventory reports', 'error');
        });
}

function loadSalesReports(filters = {}) {
    $.get('/reports/sales/analytics', filters)
        .done(function(response) {
            if (response.success) {
                currentData.sales = response.data;
                renderSalesReports(response.data);
            }
        })
        .fail(function() {
            showNotification('Error loading sales reports', 'error');
        });
}

function loadTop10Insights(filters = {}) {
    const inventoryPromise = $.get('/reports/inventory/analytics', filters)
        .then(res => {
            if (res && res.success) {
                return res.data;
            }
            return {};
        })
        .catch(err => {
            return {};
        });

    const salesPromise = $.get('/reports/sales/analytics', filters)
        .then(res => {
            if (res && res.success) {
                return res.data;
            }
            return {};
        })
        .catch(err => {
            return {};
        });

    // Wait for BOTH promises to complete before rendering
    Promise.all([inventoryPromise, salesPromise])
        .then(([inventoryData, salesData]) => {
            // Store in global for safety re-render
            window.currentData = window.currentData || {};
            window.currentData.inventory = inventoryData;
            window.currentData.sales = salesData;
            
            if (typeof window.renderTop10Insights === 'function') {
                window.renderTop10Insights(inventoryData, salesData);
                } else {
                }
            
            // Verify DOM was updated
            setTimeout(() => {
                const table = document.getElementById('top-selling-products-table');
                const content = table ? table.innerHTML.substring(0, 100) : 'TABLE NOT FOUND';
                }, 200);
        })
        .catch(err => {
            });
}

if (typeof renderInventoryReports === 'undefined') {
    function renderInventoryReports(data) {}
}

if (typeof renderSalesReports === 'undefined') {
    function renderSalesReports(data) {}
}

// renderTop10Insights is defined in top-insights.blade.php partial as window.renderTop10Insights

function resetFilters() {
    $('#dateFilter').val('this_week');
    $('#categoryFilter').val('all');
    $('#paymentFilter').val('all');
    $('#customDateRange').hide();
    applyFilters();
}

function exportReport() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    
    const reportType = formData.get('report_type');
    const format = formData.get('format');
    const url = reportType === 'inventory' ? '/reports/inventory/export' : '/reports/sales/export';
    
    // Collect checked sections
    const sections = [];
    form.querySelectorAll('input[name="sections[]"]:checked').forEach(cb => {
        sections.push(cb.value);
    });
    
    // Get export-specific filters from the modal
    const dateFilter = $('#exportDateFilter').val();
    const category = $('#exportCategory').val();
    const paymentType = $('[name="payment_type"]').val();
    const startDate = $('#exportStartDate').val();
    const endDate = $('#exportEndDate').val();
    
    // Build data object properly for Laravel
    const data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        report_type: reportType,
        format: format,
        sections: sections,
        date_filter: dateFilter || 'this_week'
    };
    
    // Add category for inventory reports (if not 'all')
    if (reportType === 'inventory' && category && category !== 'all') {
        data.category = category;
    }
    
    // Add payment type for sales reports (if not 'all')
    if (reportType === 'sales' && paymentType && paymentType !== 'all') {
        data.payment_type = paymentType;
    }
    
    // Add custom date range if applicable
    if (dateFilter === 'custom') {
        if (startDate) data.start_date = startDate;
        if (endDate) data.end_date = endDate;
    }
    
    console.log('Export data being sent:', data);
    
    // Show loading state
    const exportBtn = $('#exportButton');
    const originalText = exportBtn.html();
    exportBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Generating...');
    
    // Use POST method for export
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data, status, xhr) {
            // Get filename from Content-Disposition header or generate default
            let filename = 'report.' + format;
            const disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition) {
                const matches = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (matches && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            // Create blob and download
            const blob = new Blob([data], {
                type: format === 'pdf' ? 'application/pdf' : 
                      format === 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 
                      'text/csv'
            });
            
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
            
            // Hide modal properly
            const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
            if (modal) {
                modal.hide();
            }
            $('#exportModal').modal('hide');
            
            // Remove any leftover backdrops
            setTimeout(() => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            }, 300);
            
            SweetAlert.success('Export Successful', 'Your report has been downloaded successfully!');
        },
        error: function(xhr) {
            console.log('Export error:', xhr.responseJSON);
            const errorMsg = xhr.responseJSON?.message || 'Error generating report. Please try again.';
            const errors = xhr.responseJSON?.errors || {};
            
            let errorDetails = errorMsg;
            if (Object.keys(errors).length > 0) {
                errorDetails += '\n\nDetails:\n' + Object.entries(errors).map(([key, msgs]) => 
                    key + ': ' + (Array.isArray(msgs) ? msgs.join(', ') : msgs)
                ).join('\n');
            }
            
            SweetAlert.error('Export Failed', errorDetails);
        },
        complete: function() {
            // Restore button state
            exportBtn.prop('disabled', false).html(originalText);
        }
    });
}

function showNotification(message, type = 'info') {
    // Use SweetAlert for notifications
    if (type === 'error') {
        SweetAlert.error('Error', message);
    } else if (type === 'success') {
        SweetAlert.success('Success', message);
    } else {
        SweetAlert.info('Info', message);
    }
    }
</script>
@endsection