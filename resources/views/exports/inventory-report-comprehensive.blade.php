@extends('exports.pdf-master')

@section('content')

<!-- EXECUTIVE SUMMARY -->
<div class="section-header">Executive Summary</div>
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-label">Total Items</div>
        <div class="summary-value">{{ $reportData['summary']['total_items'] ?? 0 }}</div>
        <div class="summary-subtext">{{ $reportData['summary']['categories_count'] ?? 0 }} categories</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Total Value</div>
        <div class="summary-value">₱{{ number_format($reportData['summary']['total_value'] ?? 0, 2) }}</div>
        <div class="summary-subtext">Inventory worth</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Low Stock</div>
        <div class="summary-value text-warning">{{ $reportData['summary']['low_stock_count'] ?? 0 }}</div>
        <div class="summary-subtext">Needs restock</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Out of Stock</div>
        <div class="summary-value text-danger">{{ $reportData['summary']['out_of_stock_count'] ?? 0 }}</div>
        <div class="summary-subtext">Critical items</div>
    </div>
</div>

<!-- LOW STOCK ALERTS -->
@if((empty($selectedSections) || in_array('low_stock', $selectedSections)) && !empty($reportData['low_stock_alerts']) && count($reportData['low_stock_alerts']) > 0)
<div class="page-break"></div>
<div class="section-header">⚠️ Low Stock Alerts</div>
<div class="subsection-header">Items below minimum stock level</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="35%">Product</th>
            <th width="15%">Current Stock</th>
            <th width="15%">Min Stock</th>
            <th width="20%">Category</th>
            <th width="15%" class="text-right">Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData['low_stock_alerts'] as $item)
        <tr>
            <td>
                <strong>{{ $item['product_name'] }}</strong><br>
                <span class="text-small text-muted">Code: {{ $item['product_code'] }}</span>
            </td>
            <td>
                <strong>{{ $item['current_stock'] }}</strong> {{ $item['unit'] }}<br>
                <span class="badge badge-warning">Low</span>
            </td>
            <td>{{ $item['min_stock'] }} {{ $item['unit'] }}</td>
            <td>{{ $item['category'] }}</td>
            <td class="text-right">₱{{ number_format($item['selling_price'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@elseif(empty($selectedSections) || in_array('low_stock', $selectedSections))
<div class="section-header">⚠️ Low Stock Alerts</div>
<div class="empty-state">✅ No low stock items found - All inventory levels are healthy!</div>
@endif

<!-- OUT OF STOCK ITEMS -->
@if((empty($selectedSections) || in_array('out_of_stock', $selectedSections)) && !empty($reportData['out_of_stock']) && count($reportData['out_of_stock']) > 0)
<div class="page-break"></div>
<div class="section-header">🚫 Out of Stock Items</div>
<div class="subsection-header">Items with zero stock</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="40%">Product</th>
            <th width="25%">Category</th>
            <th width="20%">Last Updated</th>
            <th width="15%" class="text-right">Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData['out_of_stock'] as $item)
        <tr>
            <td>
                <strong>{{ $item['product_name'] }}</strong><br>
                <span class="text-small text-muted">Code: {{ $item['product_code'] }}</span>
            </td>
            <td>{{ $item['category'] }}</td>
            <td>{{ $item['last_updated'] }}</td>
            <td class="text-right">₱{{ number_format($item['selling_price'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@elseif(empty($selectedSections) || in_array('out_of_stock', $selectedSections))
<div class="section-header">🚫 Out of Stock Items</div>
<div class="empty-state">✅ No out of stock items - All products are available!</div>
@endif

<!-- EXPIRY ALERTS -->
@if((empty($selectedSections) || in_array('expiry_alerts', $selectedSections)) && !empty($reportData['expiry_alerts']) && count($reportData['expiry_alerts']) > 0)
<div class="page-break"></div>
<div class="section-header">📅 Expiry Alerts</div>
<div class="subsection-header">Items expiring soon or already expired</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="35%">Product</th>
            <th width="15%">Current Stock</th>
            <th width="20%">Expiry Date</th>
            <th width="15%">Days Until Expiry</th>
            <th width="15%">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData['expiry_alerts'] as $item)
        <tr>
            <td>
                <strong>{{ $item['product_name'] }}</strong><br>
                <span class="text-small text-muted">{{ $item['category'] }}</span>
            </td>
            <td>{{ $item['current_stock'] }} units</td>
            <td>{{ $item['exp_date'] }}</td>
            <td class="text-center">{{ $item['days_until_expiry'] }} days</td>
            <td>
                @if($item['status'] === 'expired')
                    <span class="badge badge-danger">Expired</span>
                @elseif($item['status'] === 'critical')
                    <span class="badge badge-warning">Critical</span>
                @else
                    <span class="badge badge-info">Warning</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- TOP VALUE ITEMS -->
@if((empty($selectedSections) || in_array('valuation_summary', $selectedSections)) && !empty($reportData['top_value_items']) && count($reportData['top_value_items']) > 0)
<div class="page-break"></div>
<div class="section-header">💰 Top 10 Most Valuable Items</div>
<div class="subsection-header">Items with highest total inventory value</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="35%">Product</th>
            <th width="15%">Current Stock</th>
            <th width="15%" class="text-right">Unit Price</th>
            <th width="15%" class="text-right">Total Value</th>
            <th width="15%">Category</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData['top_value_items'] as $index => $item)
        <tr>
            <td class="text-center"><strong>{{ $index + 1 }}</strong></td>
            <td>
                <strong>{{ $item['product_name'] }}</strong><br>
                <span class="text-small text-muted">{{ $item['product_code'] }}</span>
            </td>
            <td>{{ $item['current_stock'] }}</td>
            <td class="text-right">₱{{ number_format($item['selling_price'], 2) }}</td>
            <td class="text-right text-bold">₱{{ number_format($item['total_value'], 2) }}</td>
            <td>{{ $item['category'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- CATEGORY DISTRIBUTION -->
@if((empty($selectedSections) || in_array('category_distribution', $selectedSections)) && !empty($reportData['category_distribution']) && count($reportData['category_distribution']) > 0)
<div class="page-break"></div>
<div class="section-header">📦 Inventory by Category</div>
<div class="subsection-header">Stock and value distribution across categories</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="40%">Category</th>
            <th width="20%" class="text-center">Item Count</th>
            <th width="20%" class="text-center">Total Stock</th>
            <th width="20%" class="text-right">Total Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData['category_distribution'] as $category)
        <tr>
            <td><strong>{{ $category['category'] }}</strong></td>
            <td class="text-center">{{ $category['item_count'] }} items</td>
            <td class="text-center">{{ $category['total_stock'] }} units</td>
            <td class="text-right">₱{{ number_format($category['total_value'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- COMPLETE INVENTORY LISTING -->
@if((empty($selectedSections) || in_array('stock_movement', $selectedSections)) && !empty($inventoryItems) && count($inventoryItems) > 0)
<div class="page-break"></div>
<div class="section-header">📋 Complete Inventory Listing</div>
<div class="subsection-header">All inventory items with full details</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="30%">Product</th>
            <th width="15%">Stock Status</th>
            <th width="20%">Pricing</th>
            <th width="15%">Category</th>
            <th width="20%">Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inventoryItems as $item)
        <tr>
            <td>
                <strong>{{ $item->product->product_name }}</strong><br>
                <span class="text-small text-muted">{{ $item->product->product_code }}</span>
            </td>
            <td>
                <strong>{{ $item->current_stock }}</strong> {{ $item->unit }}<br>
                @php
                    $stockClass = 'badge-danger';
                    $stockText = 'Out of Stock';
                    
                    if ($item->current_stock > $item->min_stock) {
                        $stockClass = 'badge-success';
                        $stockText = 'Good Stock';
                    } elseif ($item->current_stock > 0) {
                        $stockClass = 'badge-warning';
                        $stockText = 'Low Stock';
                    }
                @endphp
                <span class="badge {{ $stockClass }}">{{ $stockText }}</span><br>
                <span class="text-small text-muted">Min: {{ $item->min_stock }}</span>
            </td>
            <td>
                <strong>₱{{ number_format($item->selling_price, 2) }}</strong><br>
                <span class="text-small text-muted">Base: ₱{{ number_format($item->base_price, 2) }}</span>
            </td>
            <td>{{ $item->product->category->name ?? 'N/A' }}</td>
            <td>{{ $item->exp_date ? $item->exp_date->format('M d, Y') : 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- REPORT SUMMARY -->
<div class="page-break"></div>
<div class="section-header">📈 Report Summary</div>
<div class="subsection-header">Key metrics and recommendations</div>

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
            <td><strong>Total Inventory Items</strong></td>
            <td class="text-center">{{ $reportData['summary']['total_items'] ?? 0 }}</td>
            <td><span class="badge badge-info">Tracked</span></td>
        </tr>
        <tr>
            <td><strong>Total Inventory Value</strong></td>
            <td class="text-center">₱{{ number_format($reportData['summary']['total_value'] ?? 0, 2) }}</td>
            <td><span class="badge badge-success">Calculated</span></td>
        </tr>
        <tr>
            <td><strong>Low Stock Items</strong></td>
            <td class="text-center">{{ $reportData['summary']['low_stock_count'] ?? 0 }}</td>
            <td>
                @if(($reportData['summary']['low_stock_count'] ?? 0) > 0)
                    <span class="badge badge-warning">Needs Attention</span>
                @else
                    <span class="badge badge-success">Good</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Out of Stock Items</strong></td>
            <td class="text-center">{{ $reportData['summary']['out_of_stock_count'] ?? 0 }}</td>
            <td>
                @if(($reportData['summary']['out_of_stock_count'] ?? 0) > 0)
                    <span class="badge badge-danger">Critical</span>
                @else
                    <span class="badge badge-success">Good</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Average Stock Level</strong></td>
            <td class="text-center">{{ number_format($reportData['summary']['average_stock_level'] ?? 0, 2) }} units</td>
            <td><span class="badge badge-info">Monitored</span></td>
        </tr>
    </tbody>
</table>

<div style="margin-top: 20px; padding: 15px; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 4px;">
    <strong style="color: #92400E;">📌 Recommendations:</strong><br>
    <ul style="margin: 10px 0 0 20px; color: #78350F;">
        @if(($reportData['summary']['low_stock_count'] ?? 0) > 0)
        <li>Restock {{ $reportData['summary']['low_stock_count'] }} low stock items to prevent stockouts</li>
        @endif
        @if(($reportData['summary']['out_of_stock_count'] ?? 0) > 0)
        <li><strong>URGENT:</strong> {{ $reportData['summary']['out_of_stock_count'] }} items are completely out of stock</li>
        @endif
        @if(!empty($reportData['expiry_alerts']) && count($reportData['expiry_alerts']) > 0)
        <li>Review {{ count($reportData['expiry_alerts']) }} items with upcoming expiry dates</li>
        @endif
        @if(($reportData['summary']['low_stock_count'] ?? 0) == 0 && ($reportData['summary']['out_of_stock_count'] ?? 0) == 0)
        <li>✅ Inventory levels are healthy - Continue monitoring regularly</li>
        @endif
    </ul>
</div>

@endsection