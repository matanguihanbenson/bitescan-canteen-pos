<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Movements Report</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
        }
        th, td { 
            padding: 5px; 
            border: 1px solid #ddd; 
            font-size: 10px;
            text-align: left;
        }
        th { 
            background: #f8f9fc;
            font-weight: bold;
        }
        .header { 
            margin-bottom: 15px;
            text-align: center;
            padding: 8px;
        }
        .title { 
            font-size: 16px; 
            color: #4e73df; 
            margin-bottom: 4px;
            font-weight: bold;
        }
        .subtitle { 
            font-size: 11px; 
            color: #858796;
            margin-top: 8px;
        }
        .system-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .school-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .school-address {
            font-size: 10px;
            color: #666;
            margin-bottom: 8px;
        }
        .divider {
            border-top: 2px solid #4e73df;
            width: 100%;
            max-width: 150px;
            margin: 12px auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="system-title">BiteScan POS System</div>
        <div class="school-name">Divine Word College of Calapan</div>
        <div class="school-address">Gov. Infantado St., Calapan City, Oriental Mindoro</div>
        <div class="divider"></div>
        <div class="title">Stock Movements Report</div>
        <div class="subtitle">Generated on: {{ $date_generated }}</div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach(array_keys($movements[0]) as $header)
                <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
            <tr>
                @foreach($movement as $value)
                <td>{{ $value }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
