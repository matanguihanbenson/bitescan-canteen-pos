<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Analytics Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');
        body { 
            font-family: 'Inter', sans-serif;
            color: #2D3748;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        #report-container {
            width: 215.9mm;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        .header { text-align: center; margin-bottom: 30px; }
        .chart-container {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            height: 300px; /* Fixed height */
            position: relative; /* Important for Chart.js */
        }
        .chart-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2D3748;
        }
        .summary-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .summary-title {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 10px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        @media print {
            body { background: none; padding: 0; }
            #report-container { box-shadow: none; width: 100%; }
            #print-button { display: none; }
            .chart-container { break-inside: avoid; }
            @page { size: A4; margin: 20mm; }
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
    </style>
</head>
<body>
    <div id="report-container">
        <div class="header">
            <div style="font-size: 14px; font-weight: bold;">BiteScan POS System</div>
            <div style="font-size: 20px; font-weight: bold; margin: 10px 0;">Inventory Analytics Report</div>
            <div style="font-size: 12px; color: #666;">Generated on: {{ now()->format('F d, Y h:i A') }}</div>
            <div class="filters mb-3">
                @if(!empty($filters['date_range']))
                    <small>Period: {{ $filters['date_range'] }}</small><br>
                @endif
                @if(!empty($filters['sections']))
                    <small>Sections: {{ implode(', ', $filters['sections']) }}</small>
                @endif
            </div>
        </div>

        @if(in_array('summary', $filters['sections'] ?? []))
        <div class="stats-grid">
            <div class="summary-card">
                <div class="summary-title">Total Value</div>
                <div class="summary-value">₱{{ $summary['total_value'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Total Products</div>
                <div class="summary-value">{{ $summary['total_products'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Low Stock Items</div>
                <div class="summary-value">{{ $summary['low_stock'] }}</div>
            </div>
        </div>
        @endif

        @if(in_array('trends', $filters['sections'] ?? []))
        <div class="chart-container">
            <div class="chart-title">Stock Movement Trends</div>
            <canvas id="stockTrendChart"></canvas>
        </div>
        @endif

        @if(in_array('categories', $filters['sections'] ?? []))
        <div class="chart-container">
            <div class="chart-title">Category Distribution</div>
            <canvas id="categoryChart"></canvas>
        </div>
        @endif
    </div>

    <button id="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print Report
    </button>

    <script>
        const stockTrendData = @json($stockTrends);
        const categoryData = @json($categoryDistribution);

        if (document.getElementById('stockTrendChart')) {
            new Chart(document.getElementById('stockTrendChart'), {
                type: 'bar',
                data: stockTrendData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        }

        if (document.getElementById('categoryChart')) {
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: categoryData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        }

        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>
