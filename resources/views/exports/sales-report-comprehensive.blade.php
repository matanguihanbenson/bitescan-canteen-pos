@extends('exports.pdf-master')

@section('content')

@php
    // Helper to check if a section should be displayed
    $shouldShow = function($sectionName) use ($selectedSections) {
        // If no sections specified, show all
        if (empty($selectedSections) || !is_array($selectedSections)) {
            return true;
        }
        return in_array($sectionName, $selectedSections);
    };
@endphp

<!-- EXECUTIVE SUMMARY (always shown) -->
<div class="section-header">📊 Executive Summary</div>
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-label">Total Revenue</div>
        <div class="summary-value">₱{{ number_format($reportData['summary']['total_revenue'] ?? 0, 2) }}</div>
        <div class="summary-subtext">{{ $reportData['summary']['revenue_growth'] ?? 0 }}% growth</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Transactions</div>
        <div class="summary-value">{{ $reportData['summary']['total_transactions'] ?? 0 }}</div>
        <div class="summary-subtext">Total orders</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Average Sale</div>
        <div class="summary-value">₱{{ number_format($reportData['summary']['average_transaction'] ?? 0, 2) }}</div>
        <div class="summary-subtext">Per transaction</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Cash Revenue</div>
        <div class="summary-value">₱{{ number_format($reportData['summary']['cash_revenue'] ?? 0, 2) }}</div>
        <div class="summary-subtext">Credit: ₱{{ number_format($reportData['summary']['credit_revenue'] ?? 0, 2) }}</div>
    </div>
</div>

<!-- TOP SELLING PRODUCTS -->
@if($shouldShow('top_products') && !empty($reportData['top_products']) && count($reportData['top_products']) > 0)
<div class="page-break"></div>
<div class="section-header">🏆 Top Selling Products</div>
<div class="subsection-header">Best performing items by sales revenue</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="40%">Product Name</th>
            <th width="15%" class="text-center">Quantity Sold</th>
            <th width="20%" class="text-right">Total Sales</th>
            <th width="20%" class="text-right">Avg Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData['top_products'] as $index => $product)
        <tr>
            <td class="text-center"><strong>{{ $index + 1 }}</strong></td>
            <td>
                <strong>{{ $product['name'] }}</strong><br>
                <span class="text-small text-muted">Type: {{ ucfirst($product['type']) }}</span>
            </td>
            <td class="text-center">{{ $product['total_quantity'] }}</td>
            <td class="text-right text-bold">₱{{ number_format($product['total_sales'], 2) }}</td>
            <td class="text-right">₱{{ number_format($product['average_price'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@elseif($shouldShow('top_products'))
<div class="section-header">🏆 Top Selling Products</div>
<div class="empty-state">No sales data available for the selected period</div>
@endif

<!-- PAYMENT METHOD BREAKDOWN -->
@if($shouldShow('payment_breakdown') && !empty($reportData['payment_breakdown']) && count($reportData['payment_breakdown']) > 0)
<div class="page-break"></div>
<div class="section-header">💳 Payment Method Breakdown</div>
<div class="subsection-header">Revenue distribution by payment type</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="30%">Payment Method</th>
            <th width="25%" class="text-center">Transaction Count</th>
            <th width="25%" class="text-right">Total Amount</th>
            <th width="20%" class="text-center">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalAmount = collect($reportData['payment_breakdown'])->sum('total_amount');
        @endphp
        @foreach($reportData['payment_breakdown'] as $payment)
        <tr>
            <td>
                <strong>{{ ucfirst($payment['payment_type']) }}</strong>
                @if($payment['payment_type'] === 'cash')
                    <span class="badge badge-success">Cash</span>
                @else
                    <span class="badge badge-info">Credit</span>
                @endif
            </td>
            <td class="text-center">{{ $payment['count'] }} transactions</td>
            <td class="text-right text-bold">₱{{ number_format($payment['total_amount'], 2) }}</td>
            <td class="text-center">
                <strong>{{ $totalAmount > 0 ? number_format(($payment['total_amount'] / $totalAmount) * 100, 1) : 0 }}%</strong>
            </td>
        </tr>
        @endforeach
        <tr style="background: #F3F4F6; font-weight: bold;">
            <td colspan="2" class="text-right">TOTAL:</td>
            <td class="text-right">₱{{ number_format($totalAmount, 2) }}</td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
@endif

<!-- HOURLY PERFORMANCE -->
@if($shouldShow('hourly_performance') && !empty($reportData['hourly_performance']) && count($reportData['hourly_performance']) > 0)
<div class="page-break"></div>
<div class="section-header">🕐 Hourly Sales Performance</div>
<div class="subsection-header">Sales activity throughout the day</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="20%">Time Period</th>
            <th width="25%" class="text-center">Transactions</th>
            <th width="30%" class="text-right">Revenue</th>
            <th width="25%">Activity Level</th>
        </tr>
    </thead>
    <tbody>
        @php
            $maxRevenue = collect($reportData['hourly_performance'])->max('total_revenue');
        @endphp
        @foreach($reportData['hourly_performance'] as $hour)
            @if($hour['transaction_count'] > 0)
            <tr>
                <td><strong>{{ $hour['hour'] }}</strong></td>
                <td class="text-center">{{ $hour['transaction_count'] }}</td>
                <td class="text-right">₱{{ number_format($hour['total_revenue'], 2) }}</td>
                <td>
                    @php
                        $percentage = $maxRevenue > 0 ? ($hour['total_revenue'] / $maxRevenue) * 100 : 0;
                    @endphp
                    @if($percentage >= 75)
                        <span class="badge badge-success">Peak</span>
                    @elseif($percentage >= 40)
                        <span class="badge badge-info">High</span>
                    @elseif($percentage >= 20)
                        <span class="badge badge-warning">Moderate</span>
                    @else
                        <span class="badge badge-secondary">Low</span>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
@endif

<!-- REVENUE TRENDS -->
@if($shouldShow('revenue_trends') && !empty($reportData['revenue_trends']) && count($reportData['revenue_trends']) > 0)
<div class="page-break"></div>
<div class="section-header">📈 Revenue Trends</div>
<div class="subsection-header">Sales performance over time</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="25%">Period</th>
            <th width="20%" class="text-center">Transactions</th>
            <th width="25%" class="text-right">Total Revenue</th>
            <th width="15%" class="text-right">Cash</th>
            <th width="15%" class="text-right">Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData['revenue_trends'] as $trend)
        <tr>
            <td><strong>{{ $trend['label'] ?? ($trend['date'] ?? 'N/A') }}</strong></td>
            <td class="text-center">{{ $trend['transactions'] ?? 0 }}</td>
            <td class="text-right text-bold">₱{{ number_format($trend['revenue'] ?? 0, 2) }}</td>
            <td class="text-right">₱{{ number_format($trend['cash_revenue'] ?? 0, 2) }}</td>
            <td class="text-right">₱{{ number_format($trend['credit_revenue'] ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- PERFORMANCE ANALYTICS -->
@if($shouldShow('performance_analytics'))
<div class="page-break"></div>
<div class="section-header">📊 Performance Analytics</div>
<div class="subsection-header">Key performance indicators and insights</div>

<table class="data-table">
    <thead>
        <tr>
            <th width="40%">Metric</th>
            <th width="30%" class="text-center">Value</th>
            <th width="30%">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Total Revenue</strong></td>
            <td class="text-center">₱{{ number_format($reportData['summary']['total_revenue'] ?? 0, 2) }}</td>
            <td><span class="badge badge-success">Tracked</span></td>
        </tr>
        <tr>
            <td><strong>Total Transactions</strong></td>
            <td class="text-center">{{ $reportData['summary']['total_transactions'] ?? 0 }}</td>
            <td><span class="badge badge-info">Processed</span></td>
        </tr>
        <tr>
            <td><strong>Average Transaction Value</strong></td>
            <td class="text-center">₱{{ number_format($reportData['summary']['average_transaction'] ?? 0, 2) }}</td>
            <td><span class="badge badge-info">Calculated</span></td>
        </tr>
        <tr>
            <td><strong>Revenue Growth</strong></td>
            <td class="text-center">{{ number_format($reportData['summary']['revenue_growth'] ?? 0, 2) }}%</td>
            <td>
                @if(($reportData['summary']['revenue_growth'] ?? 0) > 0)
                    <span class="badge badge-success">Positive</span>
                @elseif(($reportData['summary']['revenue_growth'] ?? 0) < 0)
                    <span class="badge badge-danger">Negative</span>
                @else
                    <span class="badge badge-secondary">Stable</span>
                @endif
            </td>
        </tr>
        @if(!empty($reportData['analytics']['peak_hour']))
        <tr>
            <td><strong>Peak Hour</strong></td>
            <td class="text-center">
                {{ sprintf('%02d:00', $reportData['analytics']['peak_hour']['hour'] ?? 0) }}<br>
                <span class="text-small text-muted">({{ $reportData['analytics']['peak_hour']['transaction_count'] ?? 0 }} transactions)</span>
            </td>
            <td><span class="badge badge-warning">Busiest</span></td>
        </tr>
        @endif
    </tbody>
</table>

<div style="margin-top: 20px; padding: 15px; background: #DBEAFE; border-left: 4px solid #3B82F6; border-radius: 4px;">
    <strong style="color: #1E40AF;">📌 Insights & Recommendations:</strong><br>
    <ul style="margin: 10px 0 0 20px; color: #1E3A8A;">
        @if(($reportData['summary']['revenue_growth'] ?? 0) > 0)
        <li>✅ Revenue growth is positive at {{ number_format($reportData['summary']['revenue_growth'] ?? 0, 2) }}% - Continue current strategies</li>
        @endif
        @if(($reportData['summary']['revenue_growth'] ?? 0) < 0)
        <li>⚠️ Revenue has declined by {{ abs($reportData['summary']['revenue_growth'] ?? 0) }}% - Review pricing and promotions</li>
        @endif
        @if(!empty($reportData['analytics']['peak_hour']))
        <li>Peak sales hour is {{ sprintf('%02d:00', $reportData['analytics']['peak_hour']['hour'] ?? 0) }} - Ensure adequate staffing during this time</li>
        @endif
        @if(!empty($reportData['top_products']) && count($reportData['top_products']) > 0)
        <li>Top product "{{ $reportData['top_products'][0]['name'] }}" generated ₱{{ number_format($reportData['top_products'][0]['total_sales'], 2) }} in sales</li>
        @endif
        <li>Average transaction value: ₱{{ number_format($reportData['summary']['average_transaction'] ?? 0, 2) }}</li>
    </ul>
</div>
@endif

@endsection
