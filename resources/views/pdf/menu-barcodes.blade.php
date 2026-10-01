<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .barcode-container {
            text-align: center;
            margin-bottom: 10mm;
            padding: 2mm;
        }
        .item-name {
            font-size: 10pt;
            margin-bottom: 2mm;
        }
        .price {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 2mm;
        }
        .barcode-image {
            margin: 2mm 0;
            width: 90%;
            height: auto;
        }
    </style>
</head>
<body>
    @foreach($barcodes as $barcode)
        <div class="barcode-container">
            <div class="item-name">{{ $barcode['name'] }}</div>
            <img class="barcode-image" src="data:image/png;base64,{!! DNS1D::getBarcodePNG($barcode['code'], 'C128', 2, 50, [0,0,0]) !!}" 
                 alt="{{ $barcode['code'] }}">
            <div class="price">₱{{ number_format($barcode['price'], 2) }}</div>
        </div>
        @if(!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>
