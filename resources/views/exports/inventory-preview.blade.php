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
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        #report-container {
            width: 215.9mm; /* Short Bond Paper width */
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 0 auto;
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
        @media print {
            body {
                padding: 0;
                background: none;
            }
            #report-container {
                box-shadow: none;
                width: 100%;
                padding: 0;
            }
            #print-button {
                display: none;
            }
            @page {
                size: A4;
                margin: 20mm;
            }
        }
        #print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #print-button:hover {
            background: #2e59d9;
        }
    </style>
</head>
<body>
    <div id="report-container">
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
                    @foreach($filters['export_fields'] as $field)
                        <th>{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                    <tr>
                        @foreach($filters['export_fields'] as $field)
                            <td>
                                @switch($field)
                                    @case('product_name')
                                        {{ $item->product->product_name }}
                                        @break
                                    @case('product_code')
                                        {{ $item->product_code }}
                                        @break
                                    @case('current_stock')
                                        {{ $item->current_stock }} {{ $item->unit }}
                                        @break
                                    @case('base_price')
                                        ₱{{ number_format($item->base_price, 2) }}
                                        @break
                                    @case('selling_price')
                                        ₱{{ number_format($item->selling_price, 2) }}
                                        @break
                                    @case('exp_date')
                                        {{ $item->exp_date ? $item->exp_date->format('M d, Y') : 'N/A' }}
                                        @break
                                    @case('min_stock')
                                        {{ $item->min_stock }}
                                        @break
                                    @case('unit')
                                        {{ $item->unit }}
                                        @break
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div class="divider"></div>
            <p>Total Items: {{ count($data) }}</p>
        </div>
    </div>

    <button id="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print Report
    </button>

    <script>
        window.resizeTo(900, 1100);
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
