<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the main reports dashboard
     */
    public function index()
    {
        return view('reports.dashboard');
    }

    /**
     * Show inventory reports page
     */
    public function inventoryReports()
    {
        return view('reports.inventory');
    }

    /**
     * Show sales reports page
     */
    public function salesReports()
    {
        return view('reports.sales');
    }

    /**
     * Generate comprehensive inventory analytics data
     */
    public function getInventoryAnalytics(Request $request)
    {
        try {
            $dateFilter = $this->getDateFilter($request);
            $categoryFilter = $request->input('category');

            // Inventory represents current state, not historical data
            $query = Inventory::with(['product.category']);
            
            if ($categoryFilter && $categoryFilter !== 'all') {
                $query->whereHas('product', function($q) use ($categoryFilter) {
                    $q->where('category_id', $categoryFilter);
                });
            }

            $inventory = $query->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $this->getInventorySummary($inventory),
                    'low_stock_alerts' => $this->getLowStockAlerts($inventory),
                    'out_of_stock' => $this->getOutOfStockItems($inventory),
                    'top_value_items' => $this->getTopValueItems($inventory),
                    'expiry_alerts' => $this->getExpiryAlerts($inventory),
                    'category_distribution' => $this->getCategoryDistribution($inventory),
                    'stock_movement_trends' => $this->getStockMovementTrends($request),
                    'top_10_insights' => $this->getTop10InventoryInsights($inventory, $request),
                    'performance_metrics' => $this->getInventoryPerformanceMetrics($inventory)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Inventory analytics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating inventory analytics'
            ], 500);
        }
    }

    /**
     * Generate comprehensive sales analytics data
     */
    public function getSalesAnalytics(Request $request)
    {
        try {
            $dateFilter = $this->getDateFilter($request);
            $paymentTypeFilter = $request->input('payment_type');

            $query = Transaction::with(['items', 'user'])->where('status', 'completed');
            
            if ($dateFilter) {
                $query->whereBetween('created_at', $dateFilter);
            }
            
            if ($paymentTypeFilter && $paymentTypeFilter !== 'all') {
                $query->where('payment_type', $paymentTypeFilter);
            }

            $transactions = $query->get();

            // Build components
            $summary = $this->getSalesSummary($transactions, $dateFilter);
            $revenueTrends = $this->getRevenueTrends($request);
            $hourlyPerformance = $this->getHourlyPerformance($request);

            // Calculate analytics summary used by UI performance summary cards
            $analytics = $this->calculateSalesAnalyticsSummary($revenueTrends, $hourlyPerformance);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'revenue_trends' => $revenueTrends,
                    'top_products' => $this->getTopSellingProducts($request),
                    'payment_breakdown' => $this->getPaymentMethodBreakdown($transactions),
                    'hourly_performance' => $hourlyPerformance,
                    'customer_insights' => $this->getCustomerInsights($transactions),
                    'top_10_insights' => $this->getTop10SalesInsights($transactions, $request),
                    'analytics' => $analytics
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Sales analytics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating sales analytics'
            ], 500);
        }
    }

    /**
     * Export comprehensive inventory report
     */
    public function exportInventoryReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'format' => 'required|in:pdf,excel,csv',
                'sections' => 'array',
                'date_filter' => 'nullable|in:today,this_week,this_month,this_year,custom',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'category' => 'nullable|exists:categories,id'
            ]);

            if ($validator->fails()) {
                Log::error('Inventory export validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $reportData = $this->generateInventoryReportData($request);
            
            switch ($request->format) {
                case 'pdf':
                    return $this->exportInventoryPDF($reportData, $request);
                case 'excel':
                    return $this->exportInventoryExcel($reportData, $request);
                case 'csv':
                    return $this->exportInventoryCSV($reportData, $request);
            }

        } catch (\Exception $e) {
            Log::error('Inventory report export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting inventory report'
            ], 500);
        }
    }

    /**
     * Export comprehensive sales report
     */
    public function exportSalesReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'format' => 'required|in:pdf,excel,csv',
                'sections' => 'array',
                'date_filter' => 'nullable|in:today,this_week,this_month,this_year,custom',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'payment_type' => 'nullable|in:cash,credit'
            ]);

            if ($validator->fails()) {
                Log::error('Sales export validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $reportData = $this->generateSalesReportData($request);
            
            switch ($request->format) {
                case 'pdf':
                    return $this->exportSalesPDF($reportData, $request);
                case 'excel':
                    return $this->exportSalesExcel($reportData, $request);
                case 'csv':
                    return $this->exportSalesCSV($reportData, $request);
            }

        } catch (\Exception $e) {
            Log::error('Sales report export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting sales report'
            ], 500);
        }
    }

    /**
     * Get date filter based on request parameters
     */
    private function getDateFilter(Request $request)
    {
        $dateFilter = $request->input('date_filter');
        
        if ($dateFilter === 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            if ($startDate && $endDate) {
                return [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ];
            }
        }

        switch ($dateFilter) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case 'this_week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'this_month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'this_year':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return null;
        }
    }

    /**
     * Generate inventory summary data
     */
    private function getInventorySummary($inventory)
    {
        $totalItems = $inventory->count();
        $totalValue = $inventory->sum(function($item) {
            return $item->current_stock * $item->selling_price;
        });
        
        // Use correct field name: min_stock (not min_stock_level)
        $lowStockCount = $inventory->filter(function($item) {
            return $item->current_stock > 0 && $item->current_stock <= ($item->min_stock ?? 0);
        })->count();
        
        $outOfStockCount = $inventory->filter(function($item) {
            return $item->current_stock <= 0;
        })->count();

        $averageStockLevel = $totalItems > 0 ? $inventory->avg('current_stock') : 0;

        return [
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'average_stock_level' => round($averageStockLevel, 2),
            'categories_count' => $inventory->pluck('product.category_id')->unique()->count()
        ];
    }

    /**
     * Generate sales summary data
     */
    private function getSalesSummary($transactions, $dateFilter)
    {
        $totalRevenue = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Get previous period for comparison
        $previousPeriodRevenue = 0;
        if ($dateFilter && count($dateFilter) >= 2) {
            $daysDiff = $dateFilter[0]->diffInDays($dateFilter[1]) + 1;
            $previousStart = $dateFilter[0]->copy()->subDays($daysDiff);
            $previousEnd = $dateFilter[0]->copy()->subDay();
            
            $previousPeriodRevenue = Transaction::whereBetween('created_at', [$previousStart, $previousEnd])
                ->where('status', 'completed')
                ->sum('total_amount');
        }

        $revenueGrowth = $previousPeriodRevenue > 0 
            ? (($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 
            : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $averageTransaction,
            'revenue_growth' => round($revenueGrowth, 2),
            'cash_revenue' => $transactions->where('payment_type', 'cash')->sum('total_amount'),
            'credit_revenue' => $transactions->where('payment_type', 'credit')->sum('total_amount')
        ];
    }

    /**
     * Get low stock alerts
     */
    private function getLowStockAlerts($inventory)
    {
        return $inventory->filter(function($item) {
            return $item->current_stock > 0 && $item->current_stock <= ($item->min_stock ?? 0);
        })->sortBy('current_stock')->take(20)->map(function($item) {
            return [
                'product_name' => $item->product->product_name,
                'product_code' => $item->product_code,
                'current_stock' => $item->current_stock,
                'min_stock' => $item->min_stock,
                'category' => $item->product->category->name ?? 'N/A',
                'unit' => $item->unit,
                'selling_price' => $item->selling_price
            ];
        })->values();
    }

    /**
     * Get out of stock items
     */
    private function getOutOfStockItems($inventory)
    {
        return $inventory->filter(function($item) {
            return $item->current_stock <= 0;
        })->take(20)->map(function($item) {
            return [
                'product_name' => $item->product->product_name,
                'product_code' => $item->product_code,
                'category' => $item->product->category->name ?? 'N/A',
                'last_updated' => $item->updated_at->format('M d, Y'),
                'selling_price' => $item->selling_price
            ];
        })->values();
    }

    /**
     * Get top value items
     */
    private function getTopValueItems($inventory)
    {
        return $inventory->sortByDesc(function($item) {
            return $item->current_stock * $item->selling_price;
        })->take(10)->map(function($item) {
            $totalValue = $item->current_stock * $item->selling_price;
            return [
                'product_name' => $item->product->product_name,
                'product_code' => $item->product_code,
                'current_stock' => $item->current_stock,
                'selling_price' => $item->selling_price,
                'total_value' => $totalValue,
                'category' => $item->product->category->name ?? 'N/A'
            ];
        })->values();
    }

    /**
     * Get expiry alerts
     */
    private function getExpiryAlerts($inventory)
    {
        $now = now();
        
        return $inventory->filter(function($item) use ($now) {
            return $item->exp_date && $item->exp_date <= $now->addDays(30);
        })->sortBy('exp_date')->take(20)->map(function($item) {
            $daysUntilExpiry = now()->diffInDays($item->exp_date, false);
            return [
                'product_name' => $item->product->product_name,
                'product_code' => $item->product_code,
                'exp_date' => $item->exp_date->format('M d, Y'),
                'days_until_expiry' => $daysUntilExpiry,
                'current_stock' => $item->current_stock,
                'category' => $item->product->category->name ?? 'N/A',
                'status' => $daysUntilExpiry < 0 ? 'expired' : ($daysUntilExpiry <= 7 ? 'critical' : 'warning')
            ];
        })->values();
    }

    /**
     * Get category distribution
     */
    private function getCategoryDistribution($inventory)
    {
        return $inventory->groupBy('product.category.name')->map(function($items, $categoryName) {
            $totalValue = $items->sum(function($item) {
                return $item->current_stock * $item->selling_price;
            });
            return [
                'category' => $categoryName ?? 'Uncategorized',
                'item_count' => $items->count(),
                'total_value' => $totalValue,
                'total_stock' => $items->sum('current_stock')
            ];
        })->values();
    }

    /**
     * Get stock movement trends
     */
    private function getStockMovementTrends(Request $request)
    {
        $dateFilter = $this->getDateFilter($request);
        $query = StockMovement::with('inventory.product');
        
        if ($dateFilter) {
            $query->whereBetween('created_at', $dateFilter);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();
        
        $trends = $movements->groupBy(function($movement) {
            return $movement->created_at->format('Y-m-d');
        })->map(function($dayMovements, $date) {
            return [
                'date' => $date,
                'stock_in' => $dayMovements->where('type', 'in')->sum('quantity'),
                'stock_out' => $dayMovements->where('type', 'out')->sum('quantity'),
                'adjustments' => $dayMovements->where('type', 'adjustment')->count()
            ];
        })->values();

        return $trends->sortBy('date')->values();
    }

    /**
     * Get Top 10 inventory insights
     */
    private function getTop10InventoryInsights($inventory, Request $request)
    {
        $dateFilter = $this->getDateFilter($request);
        
        return [
            'most_valuable' => $this->getTopValueItems($inventory)->take(10),
            'fastest_moving' => $this->getFastestMovingItems($dateFilter),
            'slowest_moving' => $this->getSlowestMovingItems($dateFilter),
            'highest_stock' => $inventory->sortByDesc('current_stock')->take(10)->map(function($item) {
                return [
                    'product_name' => $item->product->product_name,
                    'current_stock' => $item->current_stock,
                    'category' => $item->product->category->name ?? 'N/A'
                ];
            })->values(),
            'lowest_stock' => $inventory->where('current_stock', '>', 0)->sortBy('current_stock')->take(10)->map(function($item) {
                return [
                    'product_name' => $item->product->product_name,
                    'current_stock' => $item->current_stock,
                    'min_stock' => $item->min_stock,
                    'category' => $item->product->category->name ?? 'N/A'
                ];
            })->values()
        ];
    }

    /**
     * Get fastest moving items
     */
    private function getFastestMovingItems($dateFilter)
    {
        $query = StockMovement::with('inventory.product')
            ->where('type', 'out');

        if ($dateFilter) {
            $query->whereBetween('created_at', $dateFilter);
        }

        return $query->select('inventory_id', DB::raw('SUM(quantity) as total_moved'))
            ->groupBy('inventory_id')
            ->orderBy('total_moved', 'desc')
            ->take(10)
            ->get()
            ->map(function($movement) {
                return [
                    'product_name' => $movement->inventory->product->product_name ?? 'Unknown',
                    'total_moved' => $movement->total_moved,
                    'category' => $movement->inventory->product->category->name ?? 'N/A'
                ];
            });
    }

    /**
     * Get slowest moving items
     */
    private function getSlowestMovingItems($dateFilter)
    {
        $query = StockMovement::with('inventory.product')
            ->where('type', 'out');

        if ($dateFilter) {
            $query->whereBetween('created_at', $dateFilter);
        }

        return $query->select('inventory_id', DB::raw('SUM(quantity) as total_moved'))
            ->groupBy('inventory_id')
            ->orderBy('total_moved', 'asc')
            ->take(10)
            ->get()
            ->map(function($movement) {
                return [
                    'product_name' => $movement->inventory->product->product_name ?? 'Unknown',
                    'total_moved' => $movement->total_moved,
                    'category' => $movement->inventory->product->category->name ?? 'N/A'
                ];
            });
    }

    /**
     * Get revenue trends
     */
    private function getRevenueTrends(Request $request)
    {
        $dateFilter = $this->getDateFilter($request);
        $filterType = $request->input('date_filter');

        $query = Transaction::where('status', 'completed');
        if ($dateFilter) {
            $query->whereBetween('created_at', $dateFilter);
        }
        $transactions = $query->get();

        // Build fixed buckets based on filter type
        $buckets = [];
        switch ($filterType) {
            case 'today':
                // 00:00 - 23:00
                for ($h = 0; $h < 24; $h++) {
                    $label = Carbon::createFromTime($h)->format('ga'); // 12am, 1am, ...
                    $buckets[$h] = ['label' => $label, 'revenue' => 0, 'transactions' => 0, 'cash_revenue' => 0, 'credit_revenue' => 0];
                }
                foreach ($transactions as $t) {
                    $h = (int) $t->created_at->format('G');
                    $buckets[$h]['revenue'] += $t->total_amount;
                    $buckets[$h]['transactions'] += 1;
                    if ($t->payment_type === 'cash') $buckets[$h]['cash_revenue'] += $t->total_amount;
                    if ($t->payment_type === 'credit') $buckets[$h]['credit_revenue'] += $t->total_amount;
                }
                return collect($buckets)->values();

            case 'this_week':
                // Mon-Sun
                $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                foreach ($days as $i => $d) {
                    $buckets[$i] = ['label' => $d, 'revenue' => 0, 'transactions' => 0, 'cash_revenue' => 0, 'credit_revenue' => 0];
                }
                foreach ($transactions as $t) {
                    // ISO-8601: 1 (Mon) to 7 (Sun) -> index 0..6
                    $idx = (int)$t->created_at->isoWeekday() - 1;
                    $buckets[$idx]['revenue'] += $t->total_amount;
                    $buckets[$idx]['transactions'] += 1;
                    if ($t->payment_type === 'cash') $buckets[$idx]['cash_revenue'] += $t->total_amount;
                    if ($t->payment_type === 'credit') $buckets[$idx]['credit_revenue'] += $t->total_amount;
                }
                return collect($buckets)->values();

            case 'this_month':
                // Week 1..4; map any days beyond week 4 into week 4
                for ($w = 1; $w <= 4; $w++) {
                    $buckets[$w] = ['label' => 'Week ' . $w, 'revenue' => 0, 'transactions' => 0, 'cash_revenue' => 0, 'credit_revenue' => 0];
                }
                foreach ($transactions as $t) {
                    // Compute week of month (1..5); clamp to 4
                    $day = (int)$t->created_at->format('j');
                    $weekOfMonth = (int) ceil($day / 7);
                    $week = min($weekOfMonth, 4);
                    $buckets[$week]['revenue'] += $t->total_amount;
                    $buckets[$week]['transactions'] += 1;
                    if ($t->payment_type === 'cash') $buckets[$week]['cash_revenue'] += $t->total_amount;
                    if ($t->payment_type === 'credit') $buckets[$week]['credit_revenue'] += $t->total_amount;
                }
                // Ensure ordered 1..4
                return collect([1,2,3,4])->map(fn($w) => $buckets[$w])->values();

            case 'this_year':
                // Jan-Dec
                for ($m = 1; $m <= 12; $m++) {
                    $buckets[$m] = ['label' => Carbon::create()->month($m)->format('M'), 'revenue' => 0, 'transactions' => 0, 'cash_revenue' => 0, 'credit_revenue' => 0];
                }
                foreach ($transactions as $t) {
                    $m = (int)$t->created_at->format('n');
                    $buckets[$m]['revenue'] += $t->total_amount;
                    $buckets[$m]['transactions'] += 1;
                    if ($t->payment_type === 'cash') $buckets[$m]['cash_revenue'] += $t->total_amount;
                    if ($t->payment_type === 'credit') $buckets[$m]['credit_revenue'] += $t->total_amount;
                }
                // Ensure ordered Jan..Dec
                return collect(range(1,12))->map(fn($m) => $buckets[$m])->values();

            default:
                // Fallback: group by day within range
                return $transactions->groupBy(function($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function($dayTransactions, $date) {
            return [
                'date' => $date,
                'revenue' => $dayTransactions->sum('total_amount'),
                'transactions' => $dayTransactions->count(),
                'cash_revenue' => $dayTransactions->where('payment_type', 'cash')->sum('total_amount'),
                'credit_revenue' => $dayTransactions->where('payment_type', 'credit')->sum('total_amount')
            ];
        })->sortBy('date')->values();
        }
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts(Request $request)
    {
        $dateFilter = $this->getDateFilter($request);
        $query = TransactionItem::with('transaction');

        if ($dateFilter) {
            $query->whereHas('transaction', function($q) use ($dateFilter) {
                $q->whereBetween('created_at', $dateFilter)
                  ->where('status', 'completed');
            });
        }

        return $query->select(
                DB::raw('COALESCE(product_name, menu_name) as name'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('AVG(unit_price) as average_price'),
                'order_type'
            )
            ->groupBy('name', 'order_type')
            ->orderBy('total_sales', 'desc')
            ->take(20)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'total_quantity' => $item->total_quantity,
                    'total_sales' => $item->total_sales,
                    'average_price' => round($item->average_price, 2),
                    'type' => $item->order_type
                ];
            });
    }

    /**
     * Get payment method breakdown
     */
    private function getPaymentMethodBreakdown($transactions)
    {
        return $transactions->groupBy('payment_type')->map(function($typeTransactions, $type) {
            return [
                'payment_type' => $type,
                'count' => $typeTransactions->count(),
                'total_amount' => $typeTransactions->sum('total_amount'),
                'percentage' => 0 // Will be calculated on frontend
            ];
        })->values();
    }

    /**
     * Get hourly performance
     */
    private function getHourlyPerformance(Request $request)
    {
        $dateFilter = $this->getDateFilter($request);
        $query = Transaction::where('status', 'completed');

        if ($dateFilter) {
            $query->whereBetween('created_at', $dateFilter);
        }

        $raw = $query->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Zero-fill all hours 0..23 and build labels 00:00..23:00
        $hours = [];
        for ($h = 0; $h < 24; $h++) {
            $hours[$h] = [
                'hour' => sprintf('%02d:00', $h),
                'transaction_count' => 0,
                'total_revenue' => 0
            ];
        }
        foreach ($raw as $row) {
            $idx = (int) $row->hour;
            $hours[$idx]['transaction_count'] = (int) $row->transaction_count;
            $hours[$idx]['total_revenue'] = (float) $row->total_revenue;
        }
        return collect($hours)->values();
    }

    /**
     * Get customer insights (based on transaction patterns)
     */
    private function getCustomerInsights($transactions)
    {
        $insights = [
            'peak_hours' => $transactions->groupBy(function($t) {
                return $t->created_at->format('H');
            })->map(function($hourTransactions) {
                return $hourTransactions->count();
            })->sortDesc()->take(3),
            
            'average_items_per_transaction' => $transactions->flatMap(function($t) {
                return $t->items;
            })->count() / max($transactions->count(), 1),
            
            'repeat_customer_indicators' => [
                'transactions_per_day' => $transactions->count() / max($transactions->groupBy(function($t) {
                    return $t->created_at->format('Y-m-d');
                })->count(), 1)
            ]
        ];

        return $insights;
    }

    /**
     * Get Top 10 sales insights
     */
    private function getTop10SalesInsights($transactions, Request $request)
    {
        return [
            'highest_transactions' => $transactions->sortByDesc('total_amount')->take(10)->map(function($t) {
                return [
                    'transaction_number' => $t->transaction_number,
                    'total_amount' => $t->total_amount,
                    'created_at' => $t->created_at->format('M d, Y H:i'),
                    'payment_type' => $t->payment_type
                ];
            })->values(),
            
            'most_items_sold' => $transactions->sortByDesc(function($t) {
                return $t->items->sum('quantity');
            })->take(10)->map(function($t) {
                return [
                    'transaction_number' => $t->transaction_number,
                    'total_items' => $t->items->sum('quantity'),
                    'total_amount' => $t->total_amount,
                    'created_at' => $t->created_at->format('M d, Y H:i')
                ];
            })->values(),
            
            'top_cashiers' => $transactions->groupBy('user.firstname')->map(function($userTransactions, $user) {
                return [
                    'cashier' => $user ?? 'Unknown',
                    'transaction_count' => $userTransactions->count(),
                    'total_sales' => $userTransactions->sum('total_amount')
                ];
            })->sortByDesc('total_sales')->take(10)->values()
        ];
    }

    // Export methods would continue here...
    // I'll implement the key export methods

private function exportInventoryPDF($reportData, Request $request)
    {
        // Get actual Inventory models with relationships for the PDF view
        $dateFilter = $this->getDateFilter($request);
        $categoryFilter = $request->input('category');

        $query = Inventory::with(['product.category']);
        
        if ($categoryFilter && $categoryFilter !== 'all') {
            $query->whereHas('product', function($q) use ($categoryFilter) {
                $q->where('category_id', $categoryFilter);
            });
        }

        $inventoryItems = $query->get();
        
        // Build comprehensive filter array
        $filters = [];
        
        // Date Filter
        $dateFilterType = $request->input('date_filter', 'this_week');
        if ($dateFilterType === 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $filters['Date Range'] = ($startDate && $endDate) 
                ? Carbon::parse($startDate)->format('M d, Y') . ' to ' . Carbon::parse($endDate)->format('M d, Y')
                : 'Custom Range';
        } else {
            $filters['Period'] = ucfirst(str_replace('_', ' ', $dateFilterType));
        }
        
        // Category Filter
        if ($categoryFilter && $categoryFilter !== 'all') {
            $category = \App\Models\Category::find($categoryFilter);
            if ($category) {
                $filters['Category'] = $category->name;
            }
        } else {
            $filters['Category'] = 'All Categories';
        }
        
        // Report Type
        $filters['Report Type'] = 'Comprehensive Inventory Analysis';
        $filters['Total Items'] = count($inventoryItems) . ' products';
        
        // Get selected sections
        $selectedSections = $request->input('sections', []);
        if (!is_array($selectedSections)) {
            $selectedSections = [];
        }
        
        // Add selected sections to filters display
        if (!empty($selectedSections)) {
            $filters['Sections Included'] = implode(', ', array_map(function($section) {
                return ucfirst(str_replace('_', ' ', $section));
            }, $selectedSections));
        }
        
        $pdf = PDF::loadView('exports.inventory-report-comprehensive', [
            'title' => 'Inventory Report',
            'subtitle' => 'Complete inventory status and analytics',
            'filters' => $filters,
            'inventoryItems' => $inventoryItems,
            'reportData' => $reportData,
            'selectedSections' => $selectedSections
        ]);
        
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        
        $filename = 'inventory-report-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    private function exportSalesPDF($reportData, Request $request)
    {
        // Build comprehensive filter array
        $filters = [];
        
        // Date Filter
        $dateFilterType = $request->input('date_filter', 'this_week');
        if ($dateFilterType === 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $filters['Date Range'] = ($startDate && $endDate) 
                ? Carbon::parse($startDate)->format('M d, Y') . ' to ' . Carbon::parse($endDate)->format('M d, Y')
                : 'Custom Range';
        } else {
            $filters['Period'] = ucfirst(str_replace('_', ' ', $dateFilterType));
        }
        
        // Payment Type Filter
        $paymentType = $request->input('payment_type');
        if ($paymentType && $paymentType !== 'all') {
            $filters['Payment Type'] = ucfirst($paymentType);
        } else {
            $filters['Payment Type'] = 'All Payment Methods';
        }
        
        // Report Type
        $filters['Report Type'] = 'Comprehensive Sales Analysis';
        $filters['Total Transactions'] = ($reportData['summary']['total_transactions'] ?? 0) . ' orders';
        
        // Get selected sections
        $selectedSections = $request->input('sections', []);
        if (!is_array($selectedSections)) {
            $selectedSections = [];
        }
        
        // Add selected sections to filters display
        if (!empty($selectedSections)) {
            $filters['Sections Included'] = implode(', ', array_map(function($section) {
                return ucfirst(str_replace('_', ' ', $section));
            }, $selectedSections));
        }
        
        $pdf = PDF::loadView('exports.sales-report-comprehensive', [
            'title' => 'Sales Report',
            'subtitle' => 'Complete sales performance and analytics',
            'filters' => $filters,
            'reportData' => $reportData,
            'selectedSections' => $selectedSections
        ]);
        
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        
        $filename = 'sales-report-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export inventory report as Excel
     */
    private function exportInventoryExcel($reportData, Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Inventory Report');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('M d, Y H:i:s'));
        
        // Add summary section
        $row = 4;
        $sheet->setCellValue('A' . $row, 'Summary');
        $row++;
        $summary = $reportData['summary'] ?? [];
        $sheet->setCellValue('A' . $row, 'Total Items:');
        $sheet->setCellValue('B' . $row, $summary['total_items'] ?? 0);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Value:');
        $sheet->setCellValue('B' . $row, '₱' . number_format($summary['total_value'] ?? 0, 2));
        $row++;
        $sheet->setCellValue('A' . $row, 'Low Stock Items:');
        $sheet->setCellValue('B' . $row, $summary['low_stock_count'] ?? 0);
        $row++;
        $sheet->setCellValue('A' . $row, 'Out of Stock Items:');
        $sheet->setCellValue('B' . $row, $summary['out_of_stock_count'] ?? 0);
        
        // Low Stock Alerts section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Low Stock Alerts');
        $row++;
        $sheet->setCellValue('A' . $row, 'Product Name');
        $sheet->setCellValue('B' . $row, 'Current Stock');
        $sheet->setCellValue('C' . $row, 'Min Stock');
        $sheet->setCellValue('D' . $row, 'Category');
        $sheet->setCellValue('E' . $row, 'Price');
        $row++;
        
        $lowStock = $reportData['low_stock_alerts'] ?? [];
        foreach ($lowStock as $item) {
            $sheet->setCellValue('A' . $row, $item['product_name']);
            $sheet->setCellValue('B' . $row, $item['current_stock']);
            $sheet->setCellValue('C' . $row, $item['min_stock']);
            $sheet->setCellValue('D' . $row, $item['category']);
            $sheet->setCellValue('E' . $row, '₱' . number_format($item['selling_price'], 2));
            $row++;
        }
        
        // Out of Stock section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Out of Stock Items');
        $row++;
        $sheet->setCellValue('A' . $row, 'Product Name');
        $sheet->setCellValue('B' . $row, 'Category');
        $sheet->setCellValue('C' . $row, 'Last Updated');
        $sheet->setCellValue('D' . $row, 'Price');
        $row++;
        
        $outOfStock = $reportData['out_of_stock'] ?? [];
        foreach ($outOfStock as $item) {
            $sheet->setCellValue('A' . $row, $item['product_name']);
            $sheet->setCellValue('B' . $row, $item['category']);
            $sheet->setCellValue('C' . $row, $item['last_updated']);
            $sheet->setCellValue('D' . $row, '₱' . number_format($item['selling_price'], 2));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'inventory-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export inventory report as CSV
     */
    private function exportInventoryCSV($reportData, Request $request)
    {
        $filename = 'inventory-report-' . now()->format('Y-m-d-His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['Inventory Report - Generated: ' . now()->format('M d, Y H:i:s')]);
        fputcsv($output, []);
        
        // Summary
        $summary = $reportData['summary'] ?? [];
        fputcsv($output, ['Summary']);
        fputcsv($output, ['Total Items', $summary['total_items'] ?? 0]);
        fputcsv($output, ['Total Value', '₱' . number_format($summary['total_value'] ?? 0, 2)]);
        fputcsv($output, ['Low Stock Items', $summary['low_stock_count'] ?? 0]);
        fputcsv($output, ['Out of Stock Items', $summary['out_of_stock_count'] ?? 0]);
        fputcsv($output, []);
        
        // Low Stock Alerts
        fputcsv($output, ['Low Stock Alerts']);
        fputcsv($output, ['Product Name', 'Current Stock', 'Min Stock', 'Category', 'Price']);
        
        $lowStock = $reportData['low_stock_alerts'] ?? [];
        foreach ($lowStock as $item) {
            fputcsv($output, [
                $item['product_name'],
                $item['current_stock'],
                $item['min_stock'],
                $item['category'],
                '₱' . number_format($item['selling_price'], 2)
            ]);
        }
        
        fputcsv($output, []);
        
        // Out of Stock
        fputcsv($output, ['Out of Stock Items']);
        fputcsv($output, ['Product Name', 'Category', 'Last Updated', 'Price']);
        
        $outOfStock = $reportData['out_of_stock'] ?? [];
        foreach ($outOfStock as $item) {
            fputcsv($output, [
                $item['product_name'],
                $item['category'],
                $item['last_updated'],
                '₱' . number_format($item['selling_price'], 2)
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export sales report as Excel
     */
    private function exportSalesExcel($reportData, Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Sales Report');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('M d, Y H:i:s'));
        
        // Add summary section
        $row = 4;
        $sheet->setCellValue('A' . $row, 'Summary');
        $row++;
        $summary = $reportData['summary'] ?? [];
        $sheet->setCellValue('A' . $row, 'Total Revenue:');
        $sheet->setCellValue('B' . $row, '₱' . number_format($summary['total_revenue'] ?? 0, 2));
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Transactions:');
        $sheet->setCellValue('B' . $row, $summary['total_transactions'] ?? 0);
        $row++;
        $sheet->setCellValue('A' . $row, 'Average Transaction:');
        $sheet->setCellValue('B' . $row, '₱' . number_format($summary['average_transaction'] ?? 0, 2));
        $row++;
        $sheet->setCellValue('A' . $row, 'Revenue Growth:');
        $sheet->setCellValue('B' . $row, number_format($summary['revenue_growth'] ?? 0, 2) . '%');
        
        // Top Products section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Top Selling Products');
        $row++;
        $sheet->setCellValue('A' . $row, 'Product Name');
        $sheet->setCellValue('B' . $row, 'Quantity Sold');
        $sheet->setCellValue('C' . $row, 'Total Sales');
        $sheet->setCellValue('D' . $row, 'Average Price');
        $row++;
        
        $topProducts = $reportData['top_products'] ?? [];
        foreach ($topProducts as $product) {
            $sheet->setCellValue('A' . $row, $product['name']);
            $sheet->setCellValue('B' . $row, $product['total_quantity']);
            $sheet->setCellValue('C' . $row, '₱' . number_format($product['total_sales'], 2));
            $sheet->setCellValue('D' . $row, '₱' . number_format($product['average_price'], 2));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'sales-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export sales report as CSV
     */
    private function exportSalesCSV($reportData, Request $request)
    {
        $filename = 'sales-report-' . now()->format('Y-m-d-His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['Sales Report - Generated: ' . now()->format('M d, Y H:i:s')]);
        fputcsv($output, []);
        
        // Summary
        $summary = $reportData['summary'] ?? [];
        fputcsv($output, ['Summary']);
        fputcsv($output, ['Total Revenue', '₱' . number_format($summary['total_revenue'] ?? 0, 2)]);
        fputcsv($output, ['Total Transactions', $summary['total_transactions'] ?? 0]);
        fputcsv($output, ['Average Transaction', '₱' . number_format($summary['average_transaction'] ?? 0, 2)]);
        fputcsv($output, ['Revenue Growth', number_format($summary['revenue_growth'] ?? 0, 2) . '%']);
        fputcsv($output, []);
        
        // Top Products
        fputcsv($output, ['Top Selling Products']);
        fputcsv($output, ['Product Name', 'Quantity Sold', 'Total Sales', 'Average Price']);
        
        $topProducts = $reportData['top_products'] ?? [];
        foreach ($topProducts as $product) {
            fputcsv($output, [
                $product['name'],
                $product['total_quantity'],
                '₱' . number_format($product['total_sales'], 2),
                '₱' . number_format($product['average_price'], 2)
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Calculate sales analytics summary for performance cards
     */
    private function calculateSalesAnalyticsSummary($revenueTrends, $hourlyPerformance)
    {
        $peakHour = null;
        $peakDay = null;

        if (is_iterable($hourlyPerformance) && count($hourlyPerformance) > 0) {
            $maxHour = null;
            foreach ($hourlyPerformance as $h) {
                if ($maxHour === null || ($h['total_revenue'] ?? 0) > ($maxHour['total_revenue'] ?? 0)) {
                    $maxHour = $h;
                }
            }
            if ($maxHour) {
                // Parse "HH:00" to integer hour if needed
                $hourStr = $maxHour['hour'] ?? '0:00';
                $hourInt = (int) explode(':', (string) $hourStr)[0];
                $peakHour = [
                    'hour' => $hourInt,
                    'total_revenue' => $maxHour['total_revenue'] ?? 0,
                    'transaction_count' => $maxHour['transaction_count'] ?? 0,
                ];
            }
        }

        if (is_iterable($revenueTrends) && count($revenueTrends) > 0) {
            $maxDay = null;
            foreach ($revenueTrends as $d) {
                if ($maxDay === null || ($d['revenue'] ?? 0) > ($maxDay['revenue'] ?? 0)) {
                    $maxDay = $d;
                }
            }
            if ($maxDay) {
                $peakDay = [
                    'date' => $maxDay['date'] ?? now()->toDateString(),
                    'revenue' => $maxDay['revenue'] ?? 0,
                    'transactions' => $maxDay['transactions'] ?? 0,
                ];
            }
        }

        return [
            'peak_hour' => $peakHour,
            'peak_day' => $peakDay,
        ];
    }

    /**
     * Generate complete inventory report data
     */
    private function generateInventoryReportData(Request $request)
    {
        $analytics = $this->getInventoryAnalytics($request);
        return $analytics->getData(true)['data'] ?? [];
    }

    /**
     * Generate complete sales report data
     */
    private function generateSalesReportData(Request $request)
    {
        $analytics = $this->getSalesAnalytics($request);
        return $analytics->getData(true)['data'] ?? [];
    }

    /**
     * Generate inventory performance metrics
     */
    private function getInventoryPerformanceMetrics($inventory)
    {
        $totalItems = $inventory->count();
        $totalValue = $inventory->sum(function($item) {
            return $item->current_stock * $item->selling_price;
        });
        
        // Use correct field name: min_stock (not min_stock_level)
        $lowStockCount = $inventory->filter(function($item) {
            return $item->current_stock > 0 && $item->current_stock <= ($item->min_stock ?? 0);
        })->count();
        
        $outOfStockCount = $inventory->filter(function($item) {
            return $item->current_stock <= 0;
        })->count();
        
        $averageStockLevel = $totalItems > 0 ? $inventory->avg('current_stock') : 0;
        
        // Calculate approximate stock turnover (simplified)
        $stockTurnoverRate = $totalItems > 0 ? ($totalValue / max($totalItems, 1)) / 365 : 0;
        
        // Calculate average days to sell (simplified)
        $averageDaysToSell = $averageStockLevel > 0 ? 365 / max($averageStockLevel, 1) : 0;
        
        return [
            'stock_turnover_rate' => $stockTurnoverRate,
            'average_days_to_sell' => $averageDaysToSell,
            'restock_needed_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'average_stock_level' => $averageStockLevel
        ];
    }
}
