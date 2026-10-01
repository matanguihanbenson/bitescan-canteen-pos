<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('sales.index');
    }

    public function getTransactions()
    {
        try {
            $transactions = Transaction::with(['order'])
                ->select('id', 'created_at', 'total_amount', 'payment_status')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                        'transaction_id' => 'TX-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT),
                        'amount' => '₱' . number_format($transaction->total_amount, 2),
                        'status' => ucfirst($transaction->payment_status)
                    ];
                });

            return response()->json([
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getDailySummary()
    {
        try {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            $todayStats = Transaction::whereDate('created_at', $today)
                ->selectRaw('
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_sales,
                    AVG(total_amount) as avg_order_value
                ')
                ->first();

            $yesterdayTotal = Transaction::whereDate('created_at', $yesterday)
                ->sum('total_amount');

            $percentChange = $yesterdayTotal > 0
                ? (($todayStats->total_sales - $yesterdayTotal) / $yesterdayTotal) * 100
                : 0;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_sales' => round($todayStats->total_sales, 2),
                    'total_orders' => $todayStats->total_orders,
                    'avg_order_value' => round($todayStats->avg_order_value, 2),
                    'percent_change' => round($percentChange, 1)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics data'
            ], 500);
        }
    }

    public function exportSalesReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'sections' => 'array',
                'format' => 'required|in:pdf,excel,csv',
                'include_charts' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $sections = $request->sections ?? ['summary', 'transactions', 'products', 'analytics'];
            $includeCharts = $request->boolean('include_charts', true);

            // Generate comprehensive report data
            $reportData = $this->generateReportData($startDate, $endDate, $sections, $includeCharts);

            switch ($request->format) {
                case 'pdf':
                    return $this->exportSalesReportPDF($reportData, $startDate, $endDate);
                case 'excel':
                    return $this->exportSalesReportExcel($reportData, $startDate, $endDate);
                case 'csv':
                    return $this->exportSalesReportCSV($reportData, $startDate, $endDate);
                default:
                    return response()->json(['error' => 'Invalid format'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Sales report export error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate sales report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewSalesReport(Request $request)
    {
        try {
            $startDate = Carbon::parse($request->start_date ?? now()->subDays(30))->startOfDay();
            $endDate = Carbon::parse($request->end_date ?? now())->endOfDay();
            $sections = $request->sections ?? ['summary', 'transactions', 'products', 'analytics'];
            $includeCharts = $request->boolean('include_charts', true);

            $reportData = $this->generateReportData($startDate, $endDate, $sections, $includeCharts);

            return view('exports.sales-report-preview', [
                'reportData' => $reportData,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'sections' => $sections,
                'includeCharts' => $includeCharts
            ]);

        } catch (\Exception $e) {
            Log::error('Sales report preview error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate report preview');
        }
    }

    private function generateReportData($startDate, $endDate, $sections, $includeCharts = true)
    {
        $data = [];

        // Summary section
        if (in_array('summary', $sections)) {
            $data['summary'] = $this->generateSummaryData($startDate, $endDate);
        }

        // Transactions section
        if (in_array('transactions', $sections)) {
            $data['transactions'] = $this->generateTransactionsData($startDate, $endDate);
        }

        // Products section
        if (in_array('products', $sections)) {
            $data['products'] = $this->generateProductsData($startDate, $endDate);
        }

        // Analytics section
        if (in_array('analytics', $sections)) {
            $data['analytics'] = $this->generateAnalyticsData($startDate, $endDate);
        }

        // Chart data
        if ($includeCharts) {
            $data['charts'] = $this->generateChartData($startDate, $endDate);
        }

        return $data;
    }

    private function generateSummaryData($startDate, $endDate)
    {
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed');

        $totalRevenue = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        $averageTransaction = $totalTransactions > 0 ? ($totalRevenue / $totalTransactions) : 0;

        $cashTransactions = $transactions->clone()->where('payment_type', 'cash');
        $creditTransactions = $transactions->clone()->where('payment_type', 'credit');

        $cashRevenue = $cashTransactions->sum('total_amount');
        $creditRevenue = $creditTransactions->sum('total_amount');

        // Previous period comparison
        $periodDays = $startDate->diffInDays($endDate);
        $previousStart = $startDate->copy()->subDays($periodDays + 1);
        $previousEnd = $startDate->copy()->subDay();

        $previousRevenue = Transaction::whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('status', 'completed')
            ->sum('total_amount');

        $revenueGrowth = $previousRevenue > 0 ? 
            (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $averageTransaction,
            'cash_revenue' => $cashRevenue,
            'credit_revenue' => $creditRevenue,
            'cash_percentage' => $totalRevenue > 0 ? ($cashRevenue / $totalRevenue) * 100 : 0,
            'credit_percentage' => $totalRevenue > 0 ? ($creditRevenue / $totalRevenue) * 100 : 0,
            'revenue_growth' => $revenueGrowth,
            'period_days' => $periodDays + 1
        ];
    }

    private function generateTransactionsData($startDate, $endDate)
    {
        $transactions = Transaction::with(['user', 'items'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'transaction_number' => $transaction->transaction_number,
                    'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'total_amount' => $transaction->total_amount,
                    'payment_type' => $transaction->payment_type,
                    'status' => $transaction->status,
                    'items_count' => $transaction->items->count(),
                    'cashier' => $transaction->user ? 
                        $transaction->user->firstname . ' ' . $transaction->user->lastname : 
                        'System',
                    'charge_to' => $transaction->charge_to
                ];
            });

        return $transactions;
    }

    private function generateProductsData($startDate, $endDate)
    {
        // Top selling products
        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select(
                DB::raw('COALESCE(transaction_items.product_name, transaction_items.menu_name) as name'),
                DB::raw('SUM(transaction_items.quantity) as total_quantity'),
                DB::raw('SUM(transaction_items.subtotal) as total_sales'),
                DB::raw('AVG(transaction_items.unit_price) as average_price'),
                'transaction_items.order_type'
            )
            ->groupBy('name', 'transaction_items.order_type')
            ->orderBy('total_sales', 'desc')
            ->limit(20)
            ->get();

        // Category performance
        $categoryPerformance = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->leftJoin('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select(
                DB::raw('COALESCE(categories.name, "Menu Items") as category'),
                DB::raw('SUM(transaction_items.quantity) as total_quantity'),
                DB::raw('SUM(transaction_items.subtotal) as total_sales')
            )
            ->groupBy('category')
            ->orderBy('total_sales', 'desc')
            ->get();

        return [
            'top_products' => $topProducts,
            'category_performance' => $categoryPerformance
        ];
    }

    private function generateAnalyticsData($startDate, $endDate)
    {
        // Hourly sales pattern
        $hourlySales = DB::table('transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Daily sales trend
        $dailySales = DB::table('transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Payment method analysis
        $paymentMethods = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                'payment_type',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->groupBy('payment_type')
            ->get();

        return [
            'hourly_sales' => $hourlySales,
            'daily_sales' => $dailySales,
            'payment_methods' => $paymentMethods,
            'peak_hour' => $hourlySales->sortByDesc('total_sales')->first(),
            'peak_day' => $dailySales->sortByDesc('total_sales')->first()
        ];
    }

    private function generateChartData($startDate, $endDate)
    {
        // Sales trend chart data
        $salesTrend = $this->getCustomRangeSalesData($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        
        // Top products chart data
        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select(
                DB::raw('COALESCE(transaction_items.product_name, transaction_items.menu_name) as name'),
                DB::raw('SUM(transaction_items.subtotal) as total_sales')
            )
            ->groupBy('name')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        return [
            'sales_trend' => $salesTrend,
            'top_products' => [
                'labels' => $topProducts->pluck('name'),
                'data' => $topProducts->pluck('total_sales'),
                'backgroundColor' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
            ]
        ];
    }

    private function exportSalesReportPDF($reportData, $startDate, $endDate)
    {
        try {
            $pdf = PDF::loadView('exports.sales-report-pdf', [
                'reportData' => $reportData,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'generatedAt' => now()
            ]);
            
            $pdf->setPaper('a4', 'portrait');
            
            $filename = 'sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF export error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function exportSalesReportExcel($reportData, $startDate, $endDate)
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Title
            $sheet->setCellValue('A1', 'Sales Report');
            $sheet->setCellValue('A2', 'Period: ' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'));
            $sheet->setCellValue('A3', 'Generated: ' . now()->format('M d, Y h:i A'));
            
            $row = 5;
            
            // Summary section
            if (isset($reportData['summary'])) {
                $sheet->setCellValue('A' . $row, 'SUMMARY');
                $row += 2;
                
                $summary = $reportData['summary'];
                $sheet->setCellValue('A' . $row, 'Total Revenue:');
                $sheet->setCellValue('B' . $row, '₱' . number_format($summary['total_revenue'], 2));
                $row++;
                
                $sheet->setCellValue('A' . $row, 'Total Transactions:');
                $sheet->setCellValue('B' . $row, number_format($summary['total_transactions']));
                $row++;
                
                $sheet->setCellValue('A' . $row, 'Average Transaction:');
                $sheet->setCellValue('B' . $row, '₱' . number_format($summary['average_transaction'], 2));
                $row += 3;
            }
            
            // Transactions section
            if (isset($reportData['transactions'])) {
                $sheet->setCellValue('A' . $row, 'TRANSACTIONS');
                $row += 2;
                
                // Headers
                $headers = ['Transaction #', 'Date', 'Amount', 'Payment Type', 'Status', 'Items', 'Cashier'];
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . $row, $header);
                    $col++;
                }
                $row++;
                
                // Data
                foreach ($reportData['transactions'] as $transaction) {
                    $sheet->setCellValue('A' . $row, $transaction['transaction_number']);
                    $sheet->setCellValue('B' . $row, $transaction['date']);
                    $sheet->setCellValue('C' . $row, '₱' . number_format($transaction['total_amount'], 2));
                    $sheet->setCellValue('D' . $row, ucfirst($transaction['payment_type']));
                    $sheet->setCellValue('E' . $row, ucfirst($transaction['status']));
                    $sheet->setCellValue('F' . $row, $transaction['items_count']);
                    $sheet->setCellValue('G' . $row, $transaction['cashier']);
                    $row++;
                }
            }
            
            $filename = 'sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Excel export error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function exportSalesReportCSV($reportData, $startDate, $endDate)
    {
        try {
            $filename = 'sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $filename
            ];
            
            $handle = fopen('php://temp', 'w+');
            
            // Summary data
            if (isset($reportData['summary'])) {
                fputcsv($handle, ['SALES REPORT SUMMARY']);
                fputcsv($handle, ['Period', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')]);
                fputcsv($handle, ['Generated', now()->format('M d, Y h:i A')]);
                fputcsv($handle, []);
                
                $summary = $reportData['summary'];
                fputcsv($handle, ['Metric', 'Value']);
                fputcsv($handle, ['Total Revenue', '₱' . number_format($summary['total_revenue'], 2)]);
                fputcsv($handle, ['Total Transactions', number_format($summary['total_transactions'])]);
                fputcsv($handle, ['Average Transaction', '₱' . number_format($summary['average_transaction'], 2)]);
                fputcsv($handle, ['Cash Revenue', '₱' . number_format($summary['cash_revenue'], 2)]);
                fputcsv($handle, ['Credit Revenue', '₱' . number_format($summary['credit_revenue'], 2)]);
                fputcsv($handle, []);
            }
            
            // Transaction details
            if (isset($reportData['transactions'])) {
                fputcsv($handle, ['TRANSACTION DETAILS']);
                fputcsv($handle, ['Transaction #', 'Date', 'Amount', 'Payment Type', 'Status', 'Items Count', 'Cashier']);
                
                foreach ($reportData['transactions'] as $transaction) {
                    fputcsv($handle, [
                        $transaction['transaction_number'],
                        $transaction['date'],
                        '₱' . number_format($transaction['total_amount'], 2),
                        ucfirst($transaction['payment_type']),
                        ucfirst($transaction['status']),
                        $transaction['items_count'],
                        $transaction['cashier']
                    ]);
                }
            }
            
            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);
            
            return response($content, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('CSV export error: ' . $e->getMessage());
            throw $e;
        }
    }

public function getSellingItems(Request $request)
    {
        try {
            // Support flexible range inputs
            $range = $request->input('range');
            $startDateParam = $request->input('start_date');
            $endDateParam = $request->input('end_date');

            if ($range === 'custom' && $startDateParam && $endDateParam) {
                $startDate = \Carbon\Carbon::parse($startDateParam)->startOfDay();
                $endDate = \Carbon\Carbon::parse($endDateParam)->endOfDay();
            } else {
                $startDate = match ($range) {
                    'week' => now()->startOfWeek(),
                    'month' => now()->startOfMonth(),
                    'year' => now()->startOfYear(),
                    default => now()->startOfDay(),
                };
                $endDate = now()->endOfDay();
            }

            $menuItems = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transaction_items.order_type', 'menu')
->whereBetween('transactions.created_at', [$startDate, $endDate])
                ->where('transactions.status', 'completed')
                ->select(
                    'transaction_items.menu_name as name',
                    DB::raw('SUM(transaction_items.quantity) as quantity'),
                    DB::raw('SUM(transaction_items.subtotal) as total_sales')
                )
                ->groupBy('transaction_items.menu_name')
                ->orderBy(DB::raw('SUM(transaction_items.quantity)'), 'desc')
                ->get();

            $productItems = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transaction_items.order_type', 'product')
->whereBetween('transactions.created_at', [$startDate, $endDate])
                ->where('transactions.status', 'completed')
                ->select(
                    'transaction_items.product_name as name',
                    DB::raw('SUM(transaction_items.quantity) as quantity'),
                    DB::raw('SUM(transaction_items.subtotal) as total_sales')
                )
                ->groupBy('transaction_items.product_name')
                ->orderBy(DB::raw('SUM(transaction_items.quantity)'), 'desc')
                ->get();

            $menuCollection = collect($menuItems);
            $productCollection = collect($productItems);

            return response()->json([
                'menu' => [
                    'top' => $menuCollection->take(5)->values(),
                    'least' => $menuCollection->sortBy('quantity')->take(5)->values()
                ],
                'products' => [
                    'top' => $productCollection->take(5)->values(),
                    'least' => $productCollection->sortBy('quantity')->take(5)->values()

                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching selling items: ' . $e->getMessage());
            return response()->json([
                'menu' => ['top' => [], 'least' => []],
                'error' => 'Error fetching selling items data'
            ], 200);
        }
    }

    public function getSalesChartData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'range' => 'nullable|in:today,week,month,year,custom'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $range = $request->input('range', 'today');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            Log::info('Sales chart data requested', [
                'range' => $range,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            if ($range === 'custom' && $startDate && $endDate) {
                $salesData = $this->getCustomRangeSalesData($startDate, $endDate);
            } else {
                // Map range to period for Transaction::getSalesData
                $period = match ($range) {
                    'week' => 'weekly',
                    'month' => 'monthly',
                    'year' => 'yearly',
                    default => 'today'
                };

                $salesData = Transaction::getSalesData($period);
            }

            Log::info('Sales data retrieved', [
                'labels_count' => count($salesData['labels'] ?? []),
                'sales_count' => count($salesData['sales'] ?? []),
                'sample_labels' => array_slice($salesData['labels'] ?? [], 0, 3),
                'sample_sales' => array_slice($salesData['sales'] ?? [], 0, 3)
            ]);

            return response()->json([
                'status' => 'success',
                'labels' => $salesData['labels'] ?? [],
                'data' => array_map(function ($value) {
                    return round($value, 2);
                }, $salesData['sales'] ?? [])
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting chart data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch chart data',
                'labels' => [],
                'data' => []
            ], 500);
        }
    }

    private function getCustomRangeSalesData($startDate, $endDate)
    {
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $diffInDays = $start->diffInDays($end);

            Log::info('Custom range sales data', [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'diff_days' => $diffInDays
            ]);

            if ($diffInDays <= 1) {
                return $this->getHourlySalesData($start);
            }
            elseif ($diffInDays <= 31) {
                return $this->getDailySalesData($start, $end);
            }
            else {
                return $this->getMonthlySalesData($start, $end);
            }
        } catch (\Exception $e) {
            Log::error('Error in custom range sales data: ' . $e->getMessage());
            return ['labels' => [], 'sales' => [], 'credits' => []];
        }
    }

    private function getHourlySalesData($date)
    {
        $rawData = DB::table('transactions')
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->selectRaw('HOUR(created_at) as hour')
            ->selectRaw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as sales')
            ->selectRaw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credits')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $labels = [];
        $sales = [];
        $credits = [];

        // Generate 24 hours
        for ($i = 0; $i < 24; $i++) {
            $hour = $i % 12;
            $hour = $hour === 0 ? 12 : $hour;
            $ampm = $i < 12 ? 'AM' : 'PM';
            $labels[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00 ' . $ampm;

            $hourData = $rawData->get($i);
            $sales[] = $hourData ? (float)$hourData->sales : 0;
            $credits[] = $hourData ? (float)$hourData->credits : 0;
        }

        return compact('labels', 'sales', 'credits');
    }

    private function getDailySalesData($startDate, $endDate)
    {
        $rawData = DB::table('transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as sales')
            ->selectRaw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credits')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $sales = [];
        $credits = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('M j');

            $dayData = $rawData->get($dateStr);
            $sales[] = $dayData ? (float)$dayData->sales : 0;
            $credits[] = $dayData ? (float)$dayData->credits : 0;

            $current->addDay();
        }

        return compact('labels', 'sales', 'credits');
    }

    private function getMonthlySalesData($startDate, $endDate)
    {
        $rawData = DB::table('transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month')
            ->selectRaw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as sales')
            ->selectRaw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credits')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $sales = [];
        $credits = [];

        foreach ($rawData as $row) {
            $labels[] = Carbon::create($row->year, $row->month, 1)->format('M Y');
            $sales[] = (float)$row->sales;
            $credits[] = (float)$row->credits;
        }

        return compact('labels', 'sales', 'credits');
    }

    public function getSalesAnalytics()
    {
        try {
            $today = Carbon::today();

            $hourlyOrders = Transaction::where('created_at', '>=', $today)
                ->where('status', 'completed')
                ->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('hour')
                ->get();

            $filledHours = collect(range(0, 23))->map(function ($hour) use ($hourlyOrders) {
                $orderData = $hourlyOrders->firstWhere('hour', $hour);
                return [
                    'hour' => $hour,
                    'count' => $orderData ? $orderData->count : 0
                ];
            });

            $paymentMethods = Transaction::where('created_at', '>=', $today)
                ->where('status', 'completed')
                ->select('payment_type', DB::raw('SUM(total_amount) as total'))
                ->groupBy('payment_type')
                ->get()
                ->pluck('total', 'payment_type')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'payment_methods' => [
                        'cash' => $paymentMethods['cash'] ?? 0,
                        'credit' => $paymentMethods['credit'] ?? 0
                    ],
                    'peak_hours' => [
                        'labels' => $filledHours->pluck('hour')->values(),
                        'orders' => $filledHours->pluck('count')->values()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sales analytics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sales analytics'
            ], 500);
        }
    }
}
