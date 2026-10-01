<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report Preview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
            padding: 20px;
        }
        
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .report-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 4px;
        }
        
        .report-period {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .report-content {
            padding: 40px;
        }
        
        .section {
            margin-bottom: 50px;
        }
        
        .section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .metric-card {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        
        .metric-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }
        
        .metric-change {
            font-size: 12px;
            margin-top: 4px;
        }
        
        .metric-change.positive {
            color: #059669;
        }
        
        .metric-change.negative {
            color: #dc2626;
        }
        
        .chart-container {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            position: relative;
            height: 350px;
        }
        
        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        
        .table tr:hover {
            background: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-completed {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .payment-cash {
            background: #dcfce7;
            color: #166534;
        }
        
        .payment-credit {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .actions-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .actions-bar {
                display: none;
            }
            
            .report-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .chart-container {
                break-inside: avoid;
            }
            
            @page {
                size: A4;
                margin: 20mm;
            }
        }
        
        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
            
            .two-column {
                grid-template-columns: 1fr;
            }
            
            .report-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <div class="report-title">Sales Analytics Report</div>
            <div class="report-subtitle">BiteScan POS System - Divine Word College of Calapan</div>
            <div class="report-period">
                {{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}
            </div>
            <div class="report-period">
                Generated on {{ now()->format('F d, Y h:i A') }}
            </div>
        </div>
        
        <div class="report-content">
            @if(in_array('summary', $sections) && isset($reportData['summary']))
            <div class="section">
                <h2 class="section-title">📊 Executive Summary</h2>
                
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-label">Total Revenue</div>
                        <div class="metric-value">₱{{ number_format($reportData['summary']['total_revenue'], 2) }}</div>
                        @if($reportData['summary']['revenue_growth'] != 0)
                        <div class="metric-change {{ $reportData['summary']['revenue_growth'] > 0 ? 'positive' : 'negative' }}">
                            {{ $reportData['summary']['revenue_growth'] > 0 ? '↗' : '↘' }} {{ number_format(abs($reportData['summary']['revenue_growth']), 1) }}% vs previous period
                        </div>
                        @endif
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-label">Total Transactions</div>
                        <div class="metric-value">{{ number_format($reportData['summary']['total_transactions']) }}</div>
                        <div class="metric-change">{{ $reportData['summary']['period_days'] }} day{{ $reportData['summary']['period_days'] > 1 ? 's' : '' }}</div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-label">Average Transaction</div>
                        <div class="metric-value">₱{{ number_format($reportData['summary']['average_transaction'], 2) }}</div>
                        <div class="metric-change">Per transaction average</div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-label">Cash Sales</div>
                        <div class="metric-value">₱{{ number_format($reportData['summary']['cash_revenue'], 2) }}</div>
                        <div class="metric-change">{{ number_format($reportData['summary']['cash_percentage'], 1) }}% of total sales</div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-label">Credit Sales</div>
                        <div class="metric-value">₱{{ number_format($reportData['summary']['credit_revenue'], 2) }}</div>
                        <div class="metric-change">{{ number_format($reportData['summary']['credit_percentage'], 1) }}% of total sales</div>
                    </div>
                </div>
            </div>
            @endif
            
            @if($includeCharts && isset($reportData['charts']))
            <div class="section">
                <h2 class="section-title">📈 Sales Performance</h2>
                
                <div class="two-column">
                    <div>
                        <div class="chart-container">
                            <div class="chart-title">Sales Trend</div>
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>
                    <div>
                        <div class="chart-container">
                            <div class="chart-title">Top Products</div>
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            @if(in_array('products', $sections) && isset($reportData['products']))
            <div class="section">
                <h2 class="section-title">🏆 Top Performing Products</h2>
                
                @if(count($reportData['products']['top_products']) > 0)
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Type</th>
                                <th class="text-right">Quantity Sold</th>
                                <th class="text-right">Total Sales</th>
                                <th class="text-right">Avg. Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['products']['top_products']->take(15) as $product)
                            <tr>
                                <td style="font-weight: 500;">{{ $product->name }}</td>
                                <td>
                                    <span class="status-badge {{ $product->order_type === 'menu' ? 'status-completed' : 'payment-credit' }}">
                                        {{ ucfirst($product->order_type) }}
                                    </span>
                                </td>
                                <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                                <td class="text-right">₱{{ number_format($product->total_sales, 2) }}</td>
                                <td class="text-right">₱{{ number_format($product->average_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p style="text-align: center; color: #6b7280; padding: 40px;">No product data available for this period.</p>
                @endif
            </div>
            @endif
            
            @if(in_array('analytics', $sections) && isset($reportData['analytics']))
            <div class="section">
                <h2 class="section-title">📊 Analytics Insights</h2>
                
                <div class="metrics-grid">
                    @if($reportData['analytics']['peak_hour'])
                    <div class="metric-card">
                        <div class="metric-label">Peak Hour</div>
                        <div class="metric-value">{{ $reportData['analytics']['peak_hour']->hour }}:00</div>
                        <div class="metric-change">₱{{ number_format($reportData['analytics']['peak_hour']->total_sales, 2) }} in sales</div>
                    </div>
                    @endif
                    
                    @if($reportData['analytics']['peak_day'])
                    <div class="metric-card">
                        <div class="metric-label">Peak Day</div>
                        <div class="metric-value">{{ \Carbon\Carbon::parse($reportData['analytics']['peak_day']->date)->format('M d') }}</div>
                        <div class="metric-change">₱{{ number_format($reportData['analytics']['peak_day']->total_sales, 2) }} in sales</div>
                    </div>
                    @endif
                    
                    @foreach($reportData['analytics']['payment_methods'] as $method)
                    <div class="metric-card">
                        <div class="metric-label">{{ ucfirst($method->payment_type) }} Payments</div>
                        <div class="metric-value">{{ number_format($method->transaction_count) }}</div>
                        <div class="metric-change">₱{{ number_format($method->total_amount, 2) }} total</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            @if(in_array('transactions', $sections) && isset($reportData['transactions']))
            <div class="section">
                <h2 class="section-title">📋 Recent Transactions</h2>
                
                @if(count($reportData['transactions']) > 0)
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Transaction #</th>
                                <th>Date & Time</th>
                                <th class="text-right">Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="text-center">Items</th>
                                <th>Cashier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['transactions']->take(50) as $transaction)
                            <tr>
                                <td style="font-weight: 500;">{{ $transaction['transaction_number'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y h:i A') }}</td>
                                <td class="text-right">₱{{ number_format($transaction['total_amount'], 2) }}</td>
                                <td>
                                    <span class="status-badge {{ $transaction['payment_type'] === 'cash' ? 'payment-cash' : 'payment-credit' }}">
                                        {{ ucfirst($transaction['payment_type']) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $transaction['status'] }}">
                                        {{ ucfirst($transaction['status']) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $transaction['items_count'] }}</td>
                                <td>{{ $transaction['cashier'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p style="text-align: center; color: #6b7280; padding: 40px;">No transactions found for this period.</p>
                @endif
            </div>
            @endif
        </div>
    </div>
    
    <div class="actions-bar">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print Report
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            ✕ Close
        </button>
    </div>
    
    @if($includeCharts && isset($reportData['charts']))
    <script>
        // Sales Trend Chart
        @if(isset($reportData['charts']['sales_trend']['labels']))
        const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(salesTrendCtx, {
            type: 'line',
            data: {
                labels: @json($reportData['charts']['sales_trend']['labels']),
                datasets: [{
                    label: 'Sales',
                    data: @json($reportData['charts']['sales_trend']['sales']),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        @endif
        
        // Top Products Chart
        @if(isset($reportData['charts']['top_products']['labels']))
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topProductsCtx, {
            type: 'doughnut',
            data: {
                labels: @json($reportData['charts']['top_products']['labels']),
                datasets: [{
                    data: @json($reportData['charts']['top_products']['data']),
                    backgroundColor: @json($reportData['charts']['top_products']['backgroundColor'])
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        @endif
    </script>
    @endif
</body>
</html>