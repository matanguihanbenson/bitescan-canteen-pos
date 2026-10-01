@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Greeting Section -->
    <div class="mb-4"
        style="background-image: url('/images/dwcc.png');background-size:cover;height:160px;display: flex; flex-direction:column;justify-content:center;padding-left:20px;">
        <h1 class="fw-bold h3 mb-2">Welcome back,
            {{ auth()->user()->firstname . ' ' . auth()->user()->lastname ?? 'User' }}!</h1>
        <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->format('l, F j, Y') }}</p>
    </div>

    @if(auth()->user()->role->name !== 'System Admin')
    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        @if(Auth::user()->hasPermission('access_pos'))
        <!-- POS -->
        <div class="col-md-3">
            <a href="{{ route('pos.index') }}" class="card shadow-sm border-0 h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-dark mb-1">Point of Sale</h6>
                            <p class="text-muted small mb-0">Create new transaction</p>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cart-plus text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @if(Auth::user()->hasPermission('manage_inventory'))
        <!-- Inventory -->
        <div class="col-md-3">
            <a href="{{ route('inventory.stock') }}" class="card shadow-sm border-0 h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-dark mb-1">Manage Stock</h6>
                            <p class="text-muted small mb-0">Update inventory levels</p>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-box-seam text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @if(Auth::user()->hasPermission('manage_menu'))
        <!-- Menu -->
        <div class="col-md-3">
            <a href="{{ route('menu.index') }}" class="card shadow-sm border-0 h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-dark mb-1">Menu Items</h6>
                            <p class="text-muted small mb-0">Manage menu & products</p>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-journal-text text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @if(Auth::user()->hasPermission('view_sales'))

        <!-- Reports -->
        <div class="col-md-3">
            <a href="{{ route('sales.index') }}" class="card shadow-sm border-0 h-100 text-decoration-none">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-dark mb-1">Sales Report</h6>
                            <p class="text-muted small mb-0">View transactions & reports</p>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-graph-up text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- Sales Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-currency-dollar fs-4 text-primary me-2"></i>
                        <h6 class="mb-0">Today's Revenue</h6>
                    </div>
                    <h3 id="today-revenue" class="mb-0">Loading...</h3>
                    <small id="avg-order" class="text-muted">Avg. Order: ₱0.00</small>
                </div>
            </div>
        </div>

        <!-- Orders Count -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-receipt fs-4 text-success me-2"></i>
                        <h6 class="mb-0">Total Orders</h6>
                    </div>
                    <h3 id="order-count" class="mb-0">Loading...</h3>
                    <small id="pending-orders" class="text-warning">Pending: 0</small>
                </div>
            </div>
        </div>

        <!-- Cash on Hand -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-info bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-cash-stack fs-4 text-info me-2"></i>
                        <h6 class="mb-0">Cash on Hand</h6>
                    </div>
                    <h3 id="cash-amount" class="mb-0">Loading...</h3>
                    <small id="payment-split" class="text-muted">Cash Payments Today</small>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-triangle fs-4 text-warning me-2"></i>
                        <h6 class="mb-0">Stock Alerts</h6>
                    </div>
                    <h3 id="low-stock" class="mb-0">Loading...</h3>
                    <small id="out-of-stock" class="text-danger">0 out of stock</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Sales Trend -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title">Sales Trend</h5>
                        <select id="trend-period" class="form-select form-select-sm w-auto">
                            <option value="daily">Today</option>
                            <option value="weekly" selected>This Week</option>
                            <option value="monthly">This Month</option>
                        </select>
                    </div>
                    <div style="height: 300px; position: relative;">
                        <!-- Add fixed height container -->
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Items -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="menu-tab" data-bs-toggle="tab"
                                data-bs-target="#menu-content" type="button" role="tab">Menu Items</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="products-tab" data-bs-toggle="tab"
                                data-bs-target="#products-content" type="button" role="tab">Products</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Menu Items Tab -->
                        <div class="tab-pane fade show active" id="menu-content" role="tabpanel">
                            <ul class="nav nav-pills nav-fill mb-3">
                                <li class="nav-item">
                                    <button class="nav-link active btn-sm" data-bs-toggle="pill"
                                        data-bs-target="#menu-top">Top Selling</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link btn-sm" data-bs-toggle="pill"
                                        data-bs-target="#menu-least">Least Selling</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="menu-top">
                                    <div id="top-menu-list" class="list-group">
                                        <div class="text-center py-3">Loading...</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="menu-least">
                                    <div id="least-menu-list" class="list-group">
                                        <div class="text-center py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Tab -->
                        <div class="tab-pane fade" id="products-content" role="tabpanel">
                            <ul class="nav nav-pills nav-fill mb-3">
                                <li class="nav-item">
                                    <button class="nav-link active btn-sm" data-bs-toggle="pill"
                                        data-bs-target="#products-top">Top Selling</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link btn-sm" data-bs-toggle="pill"
                                        data-bs-target="#products-least">Least Selling</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="products-top">
                                    <div id="top-products-list" class="list-group">
                                        <div class="text-center">Loading...</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="products-least">
                                    <div id="least-products-list" class="list-group">
                                        <div class="text-center py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(Auth::user()->hasPermission('view_sales'))

    <!-- Recent Transactions -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title">Recent Transactions</h5>
                <a href="/sales/transactions" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table id="recent-transactions" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Transaction #</th>
                            <th>Amount</th>
                            <th>Items</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
let salesChart, paymentsChart;

document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    setInterval(loadDashboardData, 300000);

    document.getElementById('trend-period').addEventListener('change', function() {
        loadSalesAnalytics(this.value);
    });
});

function loadDashboardData() {
    const safeJson = (p) => p.then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)));
    Promise.allSettled([
        safeJson(fetch('/transaction-insights')),
        safeJson(fetch('/inventory-summary')),
        safeJson(fetch('/recent-transactions')),
        safeJson(fetch('/top-selling-items'))
    ]).then(results => {
        const [insightsRes, inventoryRes, transactionsRes, topItemsRes] = results;
        const insights = insightsRes.status === 'fulfilled' ? insightsRes.value : {};
        const inventory = inventoryRes.status === 'fulfilled' ? inventoryRes.value : {};
        const transactions = transactionsRes.status === 'fulfilled' ? transactionsRes.value : { transactions: [] };
        const topItems = topItemsRes.status === 'fulfilled' ? topItemsRes.value : {};
        updateMetrics(insights);
        updateInventoryStatus(inventory);
        updateTransactionsList(transactions);
        updateTopSellingLists(topItems);
        loadSalesAnalytics('weekly');
    }).catch(err => {
        });
}

function updateMetrics(data) {
    document.getElementById('today-revenue').textContent = '₱' + formatNumber(data.today_amount || 0);
    document.getElementById('avg-order').textContent = 'Avg. Order: ₱' +
        formatNumber((data.today_amount || 0) / (data.today_total || 1));

    document.getElementById('order-count').textContent = data.today_total || 0;
    document.getElementById('pending-orders').textContent =
        'Pending: ' + (data.pending_count || 0);

    document.getElementById('cash-amount').textContent = '₱' + formatNumber(data.cash_on_hand || 0);
    const cashPercent = (data.cash_on_hand / data.today_amount * 100) || 0;
    document.getElementById('payment-split').textContent =
        `${formatNumber(cashPercent, 1)}% of today's sales`;

    if (data.cash_on_hand) {
        document.getElementById('cash-amount').textContent = '₱' + formatNumber(data.cash_on_hand);
    }
}

function updateInventoryStatus(data) {
    document.getElementById('low-stock').textContent = (data.low_stock || 0) + ' low stock';
    document.getElementById('out-of-stock').textContent = data.out_of_stock + ' out of stock';

    if (data.value_change) {
        }
}

function updateTransactionsList(data) {
    const tbody = document.querySelector('#recent-transactions tbody');

    if (!data.transactions || data.transactions.length === 0) {
        tbody.innerHTML =
            '<tr><td colspan="6" class="text-center py-4 text-muted">No recent transactions available</td></tr>';
        return;
    }

    tbody.innerHTML = data.transactions.map(tx => {
        return `
        <tr>
            <td>${tx.transaction_number}</td>
            <td>₱${formatNumber(tx.total_amount)}</td>
            <td>${tx.items_count || '-'}</td>
            <td><span class="badge bg-${tx.payment_type === 'cash' ? 'success' : 'info'}-subtle text-${tx.payment_type === 'cash' ? 'success' : 'info'}">${tx.payment_type}</span></td>
            <td><span class="badge bg-${tx.status === 'completed' ? 'success' : 'warning'}-subtle text-${tx.status === 'completed' ? 'success' : 'warning'}">${tx.status}</span></td>
            <td>${tx.created_at}</td>
        </tr>
    `
    }).join('');
}

function updateTopSellingLists(data) {
    if (!data) {
        return;
    }

    if (data.menu) {
        updateItemsList('top-menu-list', data.menu.top);
        updateItemsList('least-menu-list', data.menu.least);
    }

    if (data.products) {
        updateItemsList('top-products-list', data.products.top);
        updateItemsList('least-products-list', data.products.least);
    }
}

function updateItemsList(elementId, items) {
    const container = document.getElementById(elementId);
    if (!container) {
        return;
    }

    if (!items || items.length === 0) {
        container.innerHTML = '<div class="text-center py-3">No data available</div>';
        return;
    }

    container.innerHTML = items.map((item, index) => `
        <div class="list-group-item px-0">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3 p-3">
                    <span class="${index < 3 ? 'text-success fw-bold' : 'text-secondary'}">#${index + 1}</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0">${item.name || 'Unnamed Item'}</h6>
                    <small class="text-muted">
                        ${item.quantity || 0} sold - ₱${formatNumber(item.total_sales || 0)}
                    </small>
                </div>
            </div>
        </div>
    `).join('');
}

function loadSalesAnalytics(period) {
    fetch(`/sales-analytics?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                updateSalesChart(data.data);
            } else {
                }
        })
        .catch(error => {
            });
}

function updateSalesChart(data) {
    const ctx = document.getElementById('salesTrendChart').getContext('2d');

    if (salesChart) {
        salesChart.destroy();
    }

    const formattedLabels = data.peak_hours.labels.map(hour =>
        moment(hour, 'H').format('ha')
    );

    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: formattedLabels,
            datasets: [{
                label: 'Orders',
                data: data.peak_hours.orders,
                borderColor: '#4e73df',
                backgroundColor: '#4e73df20',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback: function(value) {
                            return value + ' orders';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            }
        }
    });
}

function updatePaymentMethodsChart(data) {
    const ctx = document.getElementById('paymentsChart').getContext('2d');

    if (paymentsChart) {
        paymentsChart.destroy();
    }

    paymentsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Cash', 'Credit'],
            datasets: [{
                data: [data.cash, data.credit],
                backgroundColor: ['#1cc88a', '#36b9cc']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
}

function formatNumber(num) {
    return new Intl.NumberFormat('en-PH').format(num);
}
</script>
@endpush
@endsection