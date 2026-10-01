<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
        body { 
            font-family: 'Inter', sans-serif;
            color: #2D3748;
            line-height: 1.6;
        }
        .header { 
            margin-bottom: 20px;
            text-align: center;
            padding: 10px;
        }
        .title { 
            font-size: 18px; 
            color: #4e73df; 
            margin-bottom: 5px;
            font-weight: bold;
        }
        .subtitle { 
            font-size: 12px; 
            color: #858796;
            margin-top: 10px;
        }
        .system-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .school-address {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        .divider {
            border-top: 2px solid #4e73df;
            width: 100%;
            max-width: 200px;
            margin: 15px auto;
        }
        .report-title {
            font-size: 18px;
            font-weight: 600;
            color: #2D3748;
            margin: 12px 0 4px;
        }
        .generated-date {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 25px 0;
            font-size: 13px;
        }
        .table th { 
            background-color: #F3F4F6;
            color: #4B5563;
            font-weight: 600;
            padding: 12px;
            border: 1px solid #E5E7EB;
        }
        .table td { 
            padding: 12px;
            border: 1px solid #E5E7EB;
            vertical-align: top;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .text-danger { color: #DC2626; background-color: #FEE2E2; }
        .text-warning { color: #B45309; background-color: #FEF3C7; }
        .text-success { color: #047857; background-color: #D1FAE5; }
        .text-muted { color: #6B7280; }
        .price-container {
            margin-bottom: 4px;
        }
        .price-label {
            color: #4B5563;
            font-size: 12px;
            margin-bottom: 2px;
        }
        .price-value {
            font-weight: 600;
            color: #1F2937;
            font-size: 14px;
        }
        .footer {
            margin-top: 32px;
            font-size: 12px;
            color: #6B7280;
            text-align: right;
        }
        .product-code {
            color: #6B7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="system-title">BiteScan POS System</div>
        <div class="school-name">Divine Word College of Calapan</div>
        <div class="school-address">Gov. Infantado, Calapan City, Oriental Mindoro, Philippines</div>
        <div class="divider"></div>
        <div class="title">Inventory Status Report</div>
        <div class="generated-date">Generated on: {{ now()->timezone('Asia/Manila')->format('F d, Y h:i A') }}</div>
        <div class="filters">
            @if(!empty($filters['date_range']))
                <small>Date Range: {{ $filters['date_range'] }}</small><br>
            @endif
            @if(!empty($filters['stock_status']))
                <small>Status: {{ ucfirst(str_replace('_', ' ', $filters['stock_status'])) }}</small><br>
            @endif
            @if(!empty($filters['sort_by']))
                <small>Sorted by: {{ ucfirst(str_replace('_', ' ', $filters['sort_by'])) }}</small>
            @endif
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="30%">Product Details</th>
                <th width="25%">Stock Status</th>
                <th width="25%">Pricing Details</th>
                <th width="20%">Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>
                    <strong>{{ $item->product->product_name }}</strong><br>
                    <span class="product-code">Code: {{ $item->product->product_code }}</span>
                </td>
                <td>
                    <strong>{{ $item->current_stock }}</strong> units
                    @php
                        $stockClass = 'text-danger';
                        $stockText = 'Out of Stock';
                        
                        if ($item->current_stock > $item->min_stock) {
                            $stockClass = 'text-success';
                            $stockText = 'High Stock';
                        } elseif ($item->current_stock > 0) {
                            $stockClass = 'text-warning';
                            $stockText = 'Low Stock';
                        }
                    @endphp
                    <br>
                    <span class="badge {{ $stockClass }}">{{ $stockText }}</span>
                    <br>
                    <small class="text-muted">Minimum: {{ $item->min_stock }} units</small>
                </td>
                <td>
                    <div class="price-container">
                        <div class="price-label">Selling Price</div>
                        <div class="price-value">PHP {{ number_format($item->selling_price, 2) }}</div>
                    </div>
                    <div class="price-container">
                        <div class="price-label">Base Price</div>
                        <div class="price-value">PHP {{ number_format($item->base_price, 2) }}</div>
                    </div>
                </td>
                <td>{{ $item->exp_date ? $item->exp_date->format('M d, Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="divider"></div>
        <p>Total Items: {{ count($data) }}</p>
    </div>
</body>
</html>
