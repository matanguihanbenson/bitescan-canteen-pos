<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        'transaction_number',
        'subtotal',
        'discount',
        'total_amount',
        'amount_paid',
        'change_amount',
        'payment_type',
        'charge_to',
        'charge_type',
        'status',
        'user_id',
        'scholar_id',
        'discount_type',
        'is_scholar_transaction',
        // Approval/Decline audit fields
        'approved_by',
        'approved_at',
        'declined_by',
        'declined_at',
        'decline_reason',
    ];

protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'is_scholar_transaction' => 'boolean',
        'approved_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function declinedBy()
    {
        return $this->belongsTo(User::class, 'declined_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scholar()
    {
        return $this->belongsTo(Scholar::class);
    }

    public static function getTopSellingItems($limit = 7)
    {
        try {
            return DB::table('transaction_items')
                ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.created_at', '>=', now()->startOfDay())
                ->select('products.product_name', DB::raw('SUM(transaction_items.quantity) as total_quantity'))
                ->groupBy('products.product_name')
                ->orderByDesc('total_quantity')
                ->limit($limit)
                ->get() ?: collect([]);
        } catch (\Exception $e) {
            Log::error('Error getting top selling items: ' . $e->getMessage());
            return collect([]);
        }
    }

    public static function getLeastSellingItems($limit = 7)
    {
        try {
            return DB::table('transaction_items')
                ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.created_at', '>=', now()->startOfDay())
                ->select('products.product_name', DB::raw('SUM(transaction_items.quantity) as total_quantity'))
                ->groupBy('products.product_name')
                ->orderBy('total_quantity')
                ->limit($limit)
                ->get() ?: collect([]);
        } catch (\Exception $e) {
            Log::error('Error getting least selling items: ' . $e->getMessage());
            return collect([]);
        }
    }

    public static function getSalesData($period)
    {
        try {
            $query = DB::table('transactions');
            
            switch ($period) {
                case 'today':
                case 'yesterday':
                    $date = $period === 'today' ? now()->today() : now()->yesterday();
                    $rawData = $query->whereDate('created_at', $date)
                          ->selectRaw('HOUR(created_at) as hour')
                          ->selectRaw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as sales')
                          ->selectRaw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credits')
                          ->groupBy('hour')
                          ->orderBy('hour')
                          ->get();

                    $salesByHour = array_fill(0, 24, ['sales' => 0, 'credits' => 0]);
                    
                    foreach ($rawData as $row) {
                        $salesByHour[$row->hour] = [
                            'sales' => $row->sales,
                            'credits' => $row->credits
                        ];
                    }

                    $labels = [];
                    $sales = [];
                    $credits = [];
                    for ($i = 0; $i < 24; $i++) {
                        $hour = $i % 12;
                        $hour = $hour === 0 ? 12 : $hour;
                        $ampm = $i < 12 ? 'AM' : 'PM';
                        $labels[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00 ' . $ampm;
                        $sales[] = $salesByHour[$i]['sales'];
                        $credits[] = $salesByHour[$i]['credits'];
                    }

                    return collect([
                        'labels' => $labels,
                        'sales' => $sales,
                        'credits' => $credits
                    ]);

                case 'weekly':
                    $salesData = $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                          ->selectRaw('DAYNAME(created_at) as label')
                          ->selectRaw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as sales')
                          ->selectRaw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credits')
                          ->groupBy('label')
                          ->orderBy(DB::raw('FIELD(label, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")'))
                          ->get()
                          ->keyBy('label');

                    $labels = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                    $sales = [];
                    $credits = [];
                    foreach ($labels as $label) {
                        $data = $salesData->get($label, (object)['sales' => 0, 'credits' => 0]);
                        $sales[] = $data->sales;
                        $credits[] = $data->credits;
                    }

                    return collect([
                        'labels' => $labels,
                        'sales' => $sales,
                        'credits' => $credits
                    ]);

                case 'monthly':
                    try {
                        $currentMonth = now()->startOfMonth();
                        $weeks = [];
                        $labels = [];
                        $sales = [];
                        $credits = [];

                        $monthData = $query->whereMonth('created_at', $currentMonth->month)
                            ->whereYear('created_at', $currentMonth->year)
                            ->where('status', 'completed')
                            ->select(
                                DB::raw('DATE(created_at) as date'),
                                DB::raw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as cash_sales'),
                                DB::raw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credit_sales')
                            )
                            ->groupBy('date')
                            ->get()
                            ->keyBy('date');

                        for ($week = 1; $week <= 5; $week++) {
                            $weekStart = $currentMonth->copy()->addDays(($week - 1) * 7);
                            $weekEnd = $weekStart->copy()->addDays(6);
                            
                            if ($weekStart->month !== $currentMonth->month) {
                                continue;
                            }

                            $weekSales = 0;
                            $weekCredits = 0;

                            for ($day = $weekStart->copy(); $day <= $weekEnd && $day->month === $currentMonth->month; $day->addDay()) {
                                $dayData = $monthData->get($day->format('Y-m-d'));
                                if ($dayData) {
                                    $weekSales += $dayData->cash_sales;
                                    $weekCredits += $dayData->credit_sales;
                                }
                            }

                            $labels[] = 'Week ' . $week;
                            $sales[] = round($weekSales, 2);
                            $credits[] = round($weekCredits, 2);
                        }

                        return collect([
                            'labels' => $labels,
                            'sales' => $sales,
                            'credits' => $credits
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error in monthly sales calculation: ' . $e->getMessage());
                        return collect([
                            'labels' => [],
                            'sales' => [],
                            'credits' => []
                        ]);
                    }

                case 'yearly':
                    $salesData = $query->whereYear('created_at', now()->year)
                          ->selectRaw('MONTHNAME(created_at) as label')
                          ->selectRaw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as sales')
                          ->selectRaw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credits')
                          ->groupBy('label')
                          ->orderBy(DB::raw('FIELD(label, "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December")'))
                          ->get()
                          ->keyBy('label');

                    $labels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    $sales = [];
                    $credits = [];
                    foreach ($labels as $label) {
                        $data = $salesData->get($label, (object)['sales' => 0, 'credits' => 0]);
                        $sales[] = $data->sales;
                        $credits[] = $data->credits;
                    }

                    return collect([
                        'labels' => $labels,
                        'sales' => $sales,
                        'credits' => $credits
                    ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error getting sales data: ' . $e->getMessage());
            return collect(['labels' => [], 'sales' => [], 'credits' => []]);
        }
    }

    public static function getDashboardSummary()
    {
        try {
            $today = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();

            $todayStats = self::select(
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->first();

            $yesterdayTotal = self::where('created_at', '>=', $yesterday)
                ->where('created_at', '<', $today)
                ->where('status', 'completed')
                ->sum('total_amount');

            $percentChange = 0;
            if ($yesterdayTotal > 0) {
                $percentChange = (($todayStats->total_sales - $yesterdayTotal) / $yesterdayTotal) * 100;
            }

            $topItem = DB::table('transaction_items as ti')
                ->join('transactions as t', 't.id', '=', 'ti.transaction_id')
                ->where('t.created_at', '>=', $today)
                ->where('t.status', 'completed')
                ->select('ti.product_name', DB::raw('SUM(ti.quantity) as total_quantity'))
                ->groupBy('ti.product_name')
                ->orderByDesc('total_quantity')
                ->first();

            return [
                'total_sales' => $todayStats->total_sales ?? 0,
                'total_orders' => $todayStats->total_orders ?? 0,
                'avg_order_value' => $todayStats->total_orders > 0 
                    ? $todayStats->total_sales / $todayStats->total_orders 
                    : 0,
                'percent_change' => round($percentChange, 1),
                'top_item' => $topItem ? $topItem->product_name : '-'
            ];
        } catch (\Exception $e) {
            Log::error('Error getting dashboard summary: ' . $e->getMessage());
            return [
                'total_sales' => 0,
                'total_orders' => 0,
                'avg_order_value' => 0,
                'percent_change' => 0,
                'top_item' => '-'
            ];
        }
    }
}
