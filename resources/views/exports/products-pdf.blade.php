<!DOCTYPE html>
<html>
<head>
    <title>Products Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .system-title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .school-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .page-title {
            font-size: 20px;
            color: #2c3e50;
            margin: 30px 0;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #f2f2f2;
            color: #2c3e50;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="system-title">BiteScan POS System</div>
        <div class="school-name">Divine Word College of Calapan</div>
        <div class="school-address">Gov. Infantado, Calapan City, Oriental Mindoro, Philippines</div>
    </div>

    <div class="page-title">Products List</div>
    
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->product_code }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                <td>{{ $product->product_description }}</td>
                <td>{{ $product->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
