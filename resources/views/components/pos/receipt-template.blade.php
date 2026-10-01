<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt</title>
    <style>
        @page {
            size: 80mm 297mm; /* Standard thermal receipt width */
            margin: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 14px; 
            line-height: 1.5;
            width: 90%;
            max-width: 300px; 
            margin: 0 auto;
            padding: 10px;
            box-sizing: border-box;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
            line-height: 1.2;
            width: 100%;
        }
        .receipt-title {
            font-size: 16px; 
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .info-text {
            font-size: 14px;
            margin: 3px 0;
        }
        .items-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            padding: 5px 3px;
            border-bottom: 1.5px solid #000;
            text-transform: uppercase;
            font-size: 14px;
        }
        .items-table td {
            padding: 5px 3px;
            font-size: 13px;
        }
        .totals-table {
            width: 100%;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 5px 3px;
            font-size: 14px;
        }
        .right {
            text-align: right;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
        }
        .total-amount {
            font-size: 18px; 
            font-weight: bold;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 8px 0;
        }
        .receipt-info {
            width: 100%;
            margin: 8px 0;
            padding: 4px 0;
            border: 1px solid #000;
            text-align: center;
        }
        /* Column widths for better layout */
        .item-column { width: 45%; }
        .qty-column { width: 15%; }
        .price-column { width: 20%; }
        .total-column { width: 20%; }
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            /* Hide the print dialog cancel button */
            @page {
                margin: 0;
                size: 80mm 297mm;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div class="receipt-title">BiteScan POS</div>
        <div class="info-text">Divine Word College of Calapan</div>
        <div class="info-text">Gov. Infantado, Calapan City</div>
    </div>

    <div class="receipt-info">
        <div class="info-text">Receipt #: {{ $receiptNumber }}</div>
        <div class="info-text">Date: {{ $date }}</div>
        <div class="info-text">Cashier: {{ $cashier }}</div>
        <div class="info-text">Payment Type: {{ ucfirst($paymentType) }}</div>
        @if($paymentType === 'credit')
            <div class="info-text">Charge Type: {{ ucfirst($chargeType ?? '') }}</div>
            <div class="info-text">Charge To: {{ $chargeTo ?? '' }}</div>
        @endif
    </div>

    <table class="items-table">
        <tr>
            <th class="item-column">Item</th>
            <th class="qty-column">Qty</th>
            <th class="price-column right">Price</th>
            <th class="total-column right">Total</th>
        </tr>
        @foreach($items as $item)
        <tr>
            <td class="item-column" style="word-break: break-word;">{{ $item['name'] }}</td>
            <td class="qty-column">{{ $item['quantity'] }} {{ $item['unit'] ?? 'pcs' }}</td>
            <td class="price-column right">P{{ number_format($item['unit_price'] ?? $item['price'], 2) }}</td>
            <td class="total-column right">P{{ number_format(($item['unit_price'] ?? $item['price']) * $item['quantity'], 2) }}</td>
        </tr>
        @endforeach
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="right">P{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Discount:</td>
            <td class="right">P{{ number_format($discount, 2) }}</td>
        </tr>
        <tr class="total-amount">
            <td><strong>TOTAL AMOUNT:</strong></td>
            <td class="right"><strong>P{{ number_format($total, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Amount Paid:</td>
            <td class="right">P{{ number_format($amountPaid, 2) }}</td>
        </tr>
        <tr>
            <td>Change:</td>
            <td class="right">P{{ number_format($change, 2) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer">
        <p><strong>Thank you for your purchase!</strong></p>
    </div>
</body>
</html>
