<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif;
            color: #2D3748;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        
        .header { 
            margin-bottom: 30px;
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #4e73df;
        }
        
        .system-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #4e73df;
        }
        
        .school-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: #2D3748;
            margin: 15px 0 8px;
        }
        
        .report-period {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 5px;
        }
        
        .generated-date {
            font-size: 12px;
            color: #9CA3AF;
        }
        
        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metrics-row {
            display: table-row;
        }
        
        .metric-card {
            display: table-cell;
            background: #f9fafb;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-right: 10px;
            vertical-align: top;
            width: 32%;
        }
        
        .metric-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .metric-value {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }
        
        .metric-change {
            font-size: 10px;
            color: #6b7280;
        }
        
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            font-size: 11px;
        }
        
        .table th { 
            background-color: #F3F4F6;
            color: #374151;
            font-weight: 600;
            padding: 10px;
            border: 1px solid #E5E7EB;
            text-align: left;
        }
        
        .table td { 
            padding: 8px 10px;
            border: 1px solid #E5E7EB;
            vertical-align: top;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status-badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 500;
        }
        
        .status-completed {
            background-color: #D1FAE5;
            color: #047857;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400e;
        }
        
        .payment-cash {
            background-color: #D1FAE5;
            color: #047857;
        }
        
        .payment-credit {
            background-color: #DBEAFE;
            color: #1e40af;
        }
        
        .summary-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .summary-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #6B7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @page {
            size: A4;
            margin: 20mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="system-title">BiteScan POS System</div>
        <div class="school-name">Divine Word College of Calapan</div>
        <div class="school-address">Gov. Infantado, Calapan City, Oriental Mindoro, Philippines</div>
        <div class="report-title">Sales Analytics Report</div>
        <div class="report-period">
            Period: {{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}
        </div>
        <div class="generated-date">
            Generated on {{ $generatedAt->format('F d, Y h:i A') }}
        </div>
    </div>
    
    @if(isset($reportData['summary']))
    <div class="section">
        <h2 class="section-title">Executive Summary</h2>
        
        <div class="summary-box">
            <div class="summary-title">Financial Overview</div>
            <div class="summary-item">
                <span>Total Revenue:</span>
                <strong>₱{{ number_format($reportData['summary']['total_revenue'], 2) }}</strong>
            </div>
            <div class="summary-item">
                <span>Total Transactions:</span>
                <strong>{{ number_format($reportData['summary']['total_transactions']) }}</strong>
            </div>
            <div class="summary-item">
                <span>Average Transaction Value:</span>
                <strong>₱{{ number_format($reportData['summary']['average_transaction'], 2) }}</strong>
            </div>
            @if($reportData['summary']['revenue_growth'] != 0)
            <div class="summary-item">
                <span>Revenue Growth:</span>
                <strong style="color: {{ $reportData['summary']['revenue_growth'] > 0 ? '#059669' : '#dc2626' }}">
                    {{ number_format($reportData['summary']['revenue_growth'], 1) }}% vs previous period
                </strong>
            </div>
            @endif
        </div>
        
        <div class="summary-box">
            <div class="summary-title">Payment Methods Breakdown</div>
            <div class="summary-item">
                <span>Cash Sales:</span>
                <strong>₱{{ number_format($reportData['summary']['cash_revenue'], 2) }} ({{ number_format($reportData['summary']['cash_percentage'], 1) }}%)</strong>
            </div>
            <div class="summary-item">
                <span>Credit Sales:</span>
                <strong>₱{{ number_format($reportData['summary']['credit_revenue'], 2) }} ({{ number_format($reportData['summary']['credit_percentage'], 1) }}%)</strong>
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($reportData['analytics']))
    <div class="section">
        <h2 class="section-title">Performance Analytics</h2>
        
        @if($reportData['analytics']['peak_hour'] || $reportData['analytics']['peak_day'])
        <div class="summary-box">
            <div class="summary-title">Peak Performance</div>
            @if($reportData['analytics']['peak_hour'])
            <div class="summary-item">
                <span>Peak Sales Hour:</span>
                <strong>{{ $reportData['analytics']['peak_hour']->hour }}:00 (₱{{ number_format($reportData['analytics']['peak_hour']->total_sales, 2) }})</strong>
            </div>
            @endif
            @if($reportData['analytics']['peak_day'])
            <div class="summary-item">
                <span>Peak Sales Day:</span>
                <strong>{{ \Carbon\Carbon::parse($reportData['analytics']['peak_day']->date)->format('F d, Y') }} (₱{{ number_format($reportData['analytics']['peak_day']->total_sales, 2) }})</strong>
            </div>
            @endif
        </div>
        @endif
        
        @if(count($reportData['analytics']['payment_methods']) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th class="text-right">Transactions</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Average Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['analytics']['payment_methods'] as $method)
                <tr>
                    <td>{{ ucfirst($method->payment_type) }}</td>
                    <td class="text-right">{{ number_format($method->transaction_count) }}</td>
                    <td class="text-right">₱{{ number_format($method->total_amount, 2) }}</td>
                    <td class="text-right">₱{{ number_format($method->total_amount / $method->transaction_count, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif
    
    @if(isset($reportData['products']) && count($reportData['products']['top_products']) > 0)
    <div class="section page-break">
        <h2 class="section-title">Top Performing Products</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th width="35%">Product Name</th>
                    <th width="15%">Type</th>
                    <th width="15%" class="text-right">Qty Sold</th>
                    <th width="20%" class="text-right">Total Sales</th>
                    <th width="15%" class="text-right">Avg Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['products']['top_products']->take(20) as $product)
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
    @endif
    
    @if(isset($reportData['products']['category_performance']) && count($reportData['products']['category_performance']) > 0)
    <div class="section">
        <h2 class="section-title">Category Performance</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th width="40%">Category</th>
                    <th width="30%" class="text-right">Total Quantity</th>
                    <th width="30%" class="text-right">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['products']['category_performance'] as $category)
                <tr>
                    <td style="font-weight: 500;">{{ $category->category }}</td>
                    <td class="text-right">{{ number_format($category->total_quantity) }}</td>
                    <td class="text-right">₱{{ number_format($category->total_sales, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    @if(isset($reportData['transactions']) && count($reportData['transactions']) > 0)
    <div class="section page-break">
        <h2 class="section-title">Transaction Details</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th width="18%">Transaction #</th>
                    <th width="20%">Date & Time</th>
                    <th width="15%" class="text-right">Amount</th>
                    <th width="12%">Payment</th>
                    <th width="10%">Status</th>
                    <th width="8%" class="text-center">Items</th>
                    <th width="17%">Cashier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['transactions']->take(100) as $transaction)
                <tr>
                    <td style="font-size: 10px;">{{ $transaction['transaction_number'] }}</td>
                    <td style="font-size: 10px;">{{ \Carbon\Carbon::parse($transaction['date'])->format('M d, h:i A') }}</td>
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
                    <td style="font-size: 10px;">{{ $transaction['cashier'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if(count($reportData['transactions']) > 100)
        <div style="text-align: center; margin-top: 15px; font-style: italic; color: #6b7280;">
            Showing first 100 transactions. Total transactions: {{ number_format(count($reportData['transactions'])) }}
        </div>
        @endif
    </div>
    @endif
    
    <div class="footer">
        <div>
            <strong>BiteScan POS System</strong> - Sales Report Generated on {{ $generatedAt->format('F d, Y h:i A') }}
        </div>
        <div style="margin-top: 5px;">
            This report contains {{ isset($reportData['summary']) ? number_format($reportData['summary']['total_transactions']) : '0' }} transactions 
            with total revenue of {{ isset($reportData['summary']) ? '₱' . number_format($reportData['summary']['total_revenue'], 2) : '₱0.00' }}
        </div>
    </div>
</body>
</html>