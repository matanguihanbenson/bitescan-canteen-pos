<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report' }} - BiteScan POS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Arial', 'Helvetica', sans-serif;
            color: #2D3748;
            line-height: 1.5;
            font-size: 10pt;
        }
        
        /* Page setup for short bond paper (8.5" x 11") with proper margins */
        @page {
            size: letter;
            margin: 0.75in 0.75in 0.75in 0.75in;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
        
        /* Header - First Page Only */
        .report-header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #4e73df;
            margin-bottom: 20px;
            page-break-after: avoid;
        }
        
        .logo-section {
            margin-bottom: 15px;
        }
        
        .system-brand {
            font-size: 16pt;
            font-weight: bold;
            color: #4e73df;
            margin-bottom: 8px;
        }
        
        .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #2D3748;
            margin-bottom: 4px;
        }
        
        .school-address {
            font-size: 9pt;
            color: #6B7280;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1F2937;
            margin: 15px 0 8px;
            text-transform: uppercase;
        }
        
        .report-subtitle {
            font-size: 10pt;
            color: #6B7280;
            margin-bottom: 5px;
        }
        
        /* Filter Display */
        .filters-section {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 12px;
            margin: 15px 0 20px;
            page-break-inside: avoid;
        }
        
        .filters-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            font-size: 10pt;
        }
        
        .filter-item {
            display: inline-block;
            background: white;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            padding: 4px 10px;
            margin: 3px 5px 3px 0;
            font-size: 9pt;
            color: #4B5563;
        }
        
        .filter-label {
            font-weight: 600;
            color: #1F2937;
        }
        
        /* Section Headers */
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 18px;
            margin: 25px 0 15px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12pt;
            page-break-after: avoid;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }
        
        .subsection-header {
            background: linear-gradient(to right, #F0F9FF 0%, #E0F2FE 100%);
            color: #1E40AF;
            padding: 10px 15px;
            margin: 20px 0 12px;
            border-left: 5px solid #3B82F6;
            font-weight: 600;
            font-size: 10.5pt;
            page-break-after: avoid;
            border-radius: 4px;
        }
        
        /* Summary Cards */
        .summary-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
            page-break-inside: avoid;
        }
        
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-right: none;
        }
        
        .summary-card:first-child {
            border-radius: 6px 0 0 6px;
        }
        
        .summary-card:last-child {
            border-radius: 0 6px 6px 0;
            border-right: 1px solid #E5E7EB;
        }
        
        .summary-label {
            font-size: 8.5pt;
            color: #1E40AF;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: 0.8px;
        }
        
        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1F2937;
            margin: 4px 0;
        }
        
        .summary-subtext {
            font-size: 8.5pt;
            color: #6B7280;
            margin-top: 4px;
            font-weight: 500;
        }
        
        /* Tables */
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0;
            font-size: 9pt;
            page-break-inside: auto;
        }
        
        .data-table thead {
            background: #F3F4F6;
            page-break-after: avoid;
        }
        
        .data-table th { 
            background: linear-gradient(to bottom, #3B82F6 0%, #2563EB 100%);
            color: white;
            font-weight: 700;
            padding: 12px 10px;
            border: 1px solid #1E40AF;
            text-align: left;
            font-size: 9.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td { 
            padding: 10px;
            border: 1px solid #E5E7EB;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background: #F9FAFB;
        }
        
        .data-table tbody tr {
            page-break-inside: avoid;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge-success { 
            color: #047857; 
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%); 
            border: 1px solid #6EE7B7;
        }
        .badge-warning { 
            color: #B45309; 
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); 
            border: 1px solid #FCD34D;
        }
        .badge-danger { 
            color: #DC2626; 
            background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%); 
            border: 1px solid #FCA5A5;
        }
        .badge-info { 
            color: #1E40AF; 
            background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%); 
            border: 1px solid #93C5FD;
        }
        .badge-secondary { 
            color: #4B5563; 
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%); 
            border: 1px solid #D1D5DB;
        }
        
        /* Text Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-muted { color: #6B7280; }
        .text-small { font-size: 8pt; }
        
        /* Footer */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            font-size: 8pt;
            color: #6B7280;
            page-break-inside: avoid;
        }
        
        .footer-info {
            display: table;
            width: 100%;
        }
        
        .footer-left {
            display: table-cell;
            width: 50%;
        }
        
        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
        }
        
        /* Page Break Control */
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 30px;
            color: #6B7280;
            font-style: italic;
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            border-radius: 8px;
            border: 2px dashed #D1D5DB;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    <!-- Report Header (First Page) -->
    <div class="report-header">
        <div class="logo-section">
            <div class="system-brand">🛒 BiteScan POS System</div>
            <div class="school-name">Divine Word College of Calapan</div>
            <div class="school-address">Gov. Infantado, Calapan City, Oriental Mindoro, Philippines</div>
        </div>
        <div class="report-title">{{ $title ?? 'System Report' }}</div>
        <div class="report-subtitle">{{ $subtitle ?? '' }}</div>
        <div class="report-subtitle">Generated: {{ now()->timezone('Asia/Manila')->format('F d, Y • h:i A') }}</div>
    </div>

    <!-- Filters Section -->
    @if(!empty($filters) && count($filters) > 0)
    <div class="filters-section no-break">
        <div class="filters-title">📋 Report Filters & Parameters</div>
        @foreach($filters as $key => $value)
            @if($value)
            <span class="filter-item">
                <span class="filter-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $value }}
            </span>
            @endif
        @endforeach
    </div>
    @endif

    <!-- Main Content -->
    <div class="report-content">
        @yield('content')
    </div>

    <!-- Report Footer -->
    <div class="report-footer">
        <div class="footer-info">
            <div class="footer-left">
                <strong>BiteScan POS System</strong><br>
                Comprehensive Reporting Module
            </div>
            <div class="footer-right">
                Report ID: {{ strtoupper(substr(md5(now()), 0, 8)) }}<br>
                Page generated on {{ now()->timezone('Asia/Manila')->format('M d, Y h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>