<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Scholar;
use App\Services\ScholarService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
class TransactionController extends Controller
{
    protected $scholarService;

    public function __construct(ScholarService $scholarService)
    {
        $this->scholarService = $scholarService;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $scholarId = null;
            if ($request->discount_type === 'scholar' && $request->scholar_id) {
                $scholar = Scholar::findOrFail($request->scholar_id);
                $discountAmount = $request->discount ?? 0;
                
                if ($discountAmount > 0) {
                    $this->scholarService->deductBalance(
                        $scholar, 
                        $discountAmount, 
                        $request->transaction_number,
                        'Purchase at canteen'
                    );
                }
                $scholarId = $scholar->id;
            }

            $transaction = Transaction::create([
                'transaction_number' => $request->transaction_number,
                'subtotal' => $request->subtotal ?? 0,
                'discount' => $request->discount ?? 0,
                'total_amount' => $request->total_amount ?? $request->total ?? 0,
                'amount_paid' => $request->amount_paid ?? $request->amountPaid ?? 0,
                'change_amount' => $request->change_amount ?? $request->change ?? 0,
                'payment_type' => $request->payment_type ?? 'cash',
                'charge_type' => $request->payment_type === 'credit' ? $request->charge_type : null,
                'charge_to' => $request->payment_type === 'credit' ? $request->charge_to : null,
                'scholar_id' => $scholarId,
                'discount_type' => $request->discount_type,
                'is_scholar_transaction' => $request->discount_type === 'scholar',
                'user_id' => auth()->id(),
                'status' => $request->payment_type === 'credit' ? 'pending' : ($request->status ?? 'completed')
            ]);

            $items = json_decode($request->items, true);
            foreach ($items as $item) {
                $isProduct = $item['type'] !== 'menu';

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? $item['price'],
                    'subtotal' => ($item['unit_price'] ?? $item['price']) * $item['quantity'],
                    'unit' => $item['unit'] ?? 'piece',
                    'type' => $item['type'],
                    'order_type' => $isProduct ? 'product' : 'menu',
                    'product_code' => $isProduct ? $item['code'] : null,
                    'product_name' => $isProduct ? $item['name'] : null,
                    'menu_code' => !$isProduct ? $item['code'] : null,
                    'menu_name' => !$isProduct ? $item['name'] : null,
                    'menu_id' => !$isProduct ? ($item['id'] ?? null) : null
                ]);

                if ($isProduct && $transaction->status === 'completed') {
                    $this->updateProductInventory($item);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'transaction_id' => $transaction->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction error:', [
                'message' => $e->getMessage(),
                'items' => $request->items
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Error processing transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function updateProductInventory($item)
    {
        $inventory = DB::table('inventories')
            ->where('product_code', $item['code'])
            ->first();

        if (!$inventory) {
            throw new \Exception("Inventory not found for product: {$item['code']}");
        }

        $currentStock = $inventory->current_stock;
        $baseQuantity = $inventory->base_quantity;
        $unitsPerGroup = $inventory->units_per_group;
        $quantity = intval($item['quantity']);
        $itemType = $item['type'] ?? 'piece';

        if ($unitsPerGroup > 1) {
            if ($itemType === 'group') {
                if ($quantity > $currentStock) {
                    throw new \Exception("Insufficient stock for {$item['name']}");
                }
                $newCurrentStock = $currentStock - $quantity;
                $newBaseQuantity = $baseQuantity - ($quantity * $unitsPerGroup);
            } else {
                if ($quantity > $baseQuantity) {
                    throw new \Exception("Insufficient pieces for {$item['name']}");
                }
                $newBaseQuantity = $baseQuantity - $quantity;
                $newCurrentStock = floor($newBaseQuantity / $unitsPerGroup);
            }
        } else {
            if ($quantity > $currentStock) {
                throw new \Exception("Insufficient stock for {$item['name']}");
            }
            $newCurrentStock = $currentStock - $quantity;
            $newBaseQuantity = $newCurrentStock;
        }

        DB::table('inventories')
            ->where('product_code', $item['code'])
            ->update([
                'current_stock' => max(0, $newCurrentStock),
                'base_quantity' => max(0, $newBaseQuantity),
                'updated_at' => now()
            ]);
    }

    public function getTransactions(Request $request)
    {
        $transactions = Transaction::with('user');
        
        if ($request->filled('daterange')) {
            $dates = explode(' - ', $request->daterange);
            $start = date('Y-m-d', strtotime($dates[0]));
            $end = date('Y-m-d', strtotime($dates[1]));
            $transactions->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59']);
        }

        if ($request->filled('status')) {
            $transactions->where('status', $request->status);
        }

        if ($request->filled('payment_type')) {
            $transactions->where('payment_type', $request->payment_type);
        }

        if ($request->filled('amount_min')) {
            $transactions->where('total_amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $transactions->where('total_amount', '<=', $request->amount_max);
        }
        
        return DataTables::of($transactions)
            ->addIndexColumn()
            ->addColumn('processed_by', function($row) {
                if ($row->user) {
                    return $row->user->firstname . ' ' . $row->user->lastname;
                }
                return 'System';
            })
            ->editColumn('transaction_number', function($row) {
                return $row->transaction_number ?? 'TRX-' . str_pad($row->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('status', function($row) {
                $statusClasses = [
                    'completed' => 'bg-success',
                    'pending' => 'bg-warning',
                    'declined' => 'bg-danger'
                ];
                $class = $statusClasses[$row->status] ?? 'bg-secondary';
                return '<span class="badge ' . $class . '">' . $row->status . '</span>';
            })
            ->addColumn('actions', function($row) {
                return '<div class="d-flex justify-content-center gap-1">
                    <button type="button" class="btn btn-sm btn-primary view-transaction" data-id="'.$row->id.'">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>';
            })
            ->filterColumn('total_amount', function($query, $keyword) {
                $query->whereRaw('total_amount like ?', ["%{$keyword}%"]);
            })
            ->filterColumn('created_at', function($query, $keyword) {
                $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") like ?', ["%{$keyword}%"]);
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    public function getTransactionDetails($id)
    {
        try {
            $transaction = Transaction::with(['user', 'items'])
                ->select(
                    'transactions.id',
                    'transactions.transaction_number',
                    'transactions.created_at',
                    'transactions.total_amount',
                    'transactions.payment_type',
                    'transactions.status',
                    'transactions.subtotal',
                    'transactions.discount',
                    'transactions.user_id',
                    'transactions.charge_to',
                    'transactions.charge_type'
                )
                ->findOrFail($id);
            
            $items = $transaction->items->map(function ($item) {
                return [
                    'product_name' => $item->order_type === 'menu' ? ($item->menu_name ?? 'Unknown Item') : ($item->product_name ?? 'Unknown Item'),
                    'unit' => $item->unit ?? 'pcs',
                    'quantity' => $item->quantity ?? 1,
                    'unit_price' => $item->unit_price ?? 0,
                    'subtotal' => $item->subtotal ?? 0
                ];
            });

            $userName = $transaction->user ? 
                $transaction->user->firstname . ' ' . $transaction->user->lastname : 
                'System';

            return response()->json([
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'created_at' => $transaction->created_at,
                    'total_amount' => $transaction->total_amount,
                    'payment_type' => $transaction->payment_type,
                    'status' => $transaction->status,
                    'subtotal' => $transaction->subtotal,
                    'discount' => $transaction->discount,
                    'charge_to' => $transaction->charge_to,
                    'charge_type' => $transaction->charge_type,
                    'user' => [
                        'name' => $userName
                    ]
                ],
                'items' => $items
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch transaction details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

public function getTransactionInsights(Request $request)
{
    // Support filtering by range or explicit date range
    $range = $request->input('range');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if ($range === 'custom' && $startDate && $endDate) {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
    } else {
        $now = now();
        switch ($range) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
        }
    }

    $baseQuery = Transaction::whereBetween('created_at', [$start, $end])
        ->where('status', 'completed');

    $totalAmount = (clone $baseQuery)->sum('total_amount');
    $totalCount = (clone $baseQuery)->count();
    $cashAmount = (clone $baseQuery)->where('payment_type', 'cash')->sum('total_amount');

    return response()->json([
        'total_amount' => $totalAmount,
        'total_transactions' => $totalCount,
        'cash_amount' => $cashAmount,
        // Back-compat keys used by some views
        'today_amount' => $totalAmount,
        'today_total' => $totalCount,
        'cash_on_hand' => $cashAmount,
    ]);
}

    public function getDashboardSummary()
    {
        try {
            $summary = Transaction::getDashboardSummary();
            return response()->json([
                'status' => 'success',
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Error in dashboard summary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch summary data'
            ], 500);
        }
    }

    public function getSalesAnalytics()
    {
        try {
            $today = now()->startOfDay();
            
            $paymentMethods = Transaction::where(function($query) use ($today) {
                    $query->where(function($q) use ($today) {
                        $q->where('payment_type', 'cash')
                          ->where('created_at', '>=', $today);
                    })
                    ->orWhere(function($q) use ($today) {
                        $q->where('payment_type', 'credit')
                          ->where('status', 'completed')
                          ->whereDate('updated_at', today());
                    });
                })
                ->where('status', 'completed')
                ->select(
                    DB::raw('COUNT(CASE WHEN payment_type = "cash" THEN 1 END) as cash_count'),
                    DB::raw('COUNT(CASE WHEN payment_type = "credit" THEN 1 END) as credit_count'),
                    DB::raw('SUM(CASE WHEN payment_type = "cash" THEN total_amount ELSE 0 END) as cash_total'),
                    DB::raw('SUM(CASE WHEN payment_type = "credit" THEN total_amount ELSE 0 END) as credit_total')
                )
                ->first();

            $hourlyOrders = Transaction::where('created_at', '>=', $today)
                ->where('status', 'completed')
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            $hours = [];
            $orderCounts = [];
            $maxOrders = 0;
            $peakHour = '-';
            $totalOrders = 0;

            foreach ($hourlyOrders as $hour) {
                $hourLabel = date('ga', strtotime($hour->hour . ':00'));
                $hours[] = $hourLabel;
                $orderCounts[] = $hour->count;
                $totalOrders += $hour->count;

                if ($hour->count > $maxOrders) {
                    $maxOrders = $hour->count;
                    $peakHour = $hourLabel;
                }
            }

            $avgOrdersPerHour = $hourlyOrders->count() > 0 ? $totalOrders / $hourlyOrders->count() : 0;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'payment_methods' => [
                        'cash' => $paymentMethods->cash_count ?? 0,
                        'credit' => $paymentMethods->credit_count ?? 0,
                        'cash_total' => $paymentMethods->cash_total ?? 0,
                        'credit_total' => $paymentMethods->credit_total ?? 0
                    ],
                    'peak_hours' => [
                        'labels' => $hours,
                        'orders' => $orderCounts
                    ],
                    'peak_hour' => $peakHour,
                    'avg_orders_per_hour' => $avgOrdersPerHour
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics data'
            ], 500);
        }
    }


    public function printReceipt($id)
    {
        try {
            $transaction = Transaction::with(['user', 'items'])->findOrFail($id);
            
            $data = [
                'receiptNumber' => $transaction->transaction_number ?? 'N/A',
                'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                'cashier' => $transaction->user ? $transaction->user->firstname . ' ' . $transaction->user->lastname : 'System User',
                'items' => $transaction->items->map(function ($item) {
                    return [
                        'name' => $item->order_type === 'menu' ? ($item->menu_name ?? 'Unknown Item') : ($item->product_name ?? 'Unknown Item'),
                        'quantity' => $item->quantity ?? 1,
                        'unit' => $item->unit ?? 'pcs',
                        'unit_price' => $item->unit_price ?? 0,
                        'price' => $item->unit_price ?? 0,
                    ];
                })->toArray(),
                'subtotal' => $transaction->subtotal ?? 0,
                'discount' => $transaction->discount ?? 0,
                'total' => $transaction->total_amount ?? 0,
                'amountPaid' => $transaction->amount_paid ?? 0,
                'change' => $transaction->change_amount ?? 0,
                'paymentType' => $transaction->payment_type ?? 'cash',
                'chargeType' => $transaction->charge_type ?? '',
                'chargeTo' => $transaction->charge_to ?? ''
            ];

            return view('components.pos.receipt-preview', $data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate receipt',
                'message' => $e->getMessage()
            ], 500);
        }
    }

public function getRecentTransactions(Request $request)
{
    try {
        $limit = (int)($request->input('limit', 5));
        $range = $request->input('range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::with(['user', 'items'])->orderBy('created_at', 'desc');

        if ($range === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($range) {
            $now = now();
            switch ($range) {
                case 'week':
                    $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
                    break;
                default:
                    $query->whereDate('created_at', $now->toDateString());
            }
        }

        $transactions = $query
            ->take($limit)
            ->get()
                ->map(function($transaction) {
                    return [
                        'transaction_number' => $transaction->transaction_number,
                        'total_amount' => $transaction->total_amount,
                        'status' => $transaction->status,
                        'created_at' => $transaction->created_at->diffForHumans(),
                        'payment_type' => $transaction->payment_type,
                        'items_count' => $transaction->items->count(), 
                        'user' => optional($transaction->user)->firstname . ' ' . optional($transaction->user)->lastname
                    ];
                });

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            Log::error('Recent transactions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching recent transactions'
            ], 500);
        }
    }
    public function getCreditTransactionDetails($id)
    {
        $transaction = Transaction::with(['items', 'user', 'approvedBy', 'declinedBy'])
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $items = $transaction->items->map(function ($item) {
            return [
                'product_name' => $item->product_name ?? $item->menu_name ?? 'Unknown Item',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->quantity * $item->unit_price
            ];
        });

        return response()->json([
            'transaction_number' => $transaction->transaction_number,
            'created_at' => $transaction->created_at,
            'processed_by' => $transaction->user ? $transaction->user->firstname . ' ' . $transaction->user->lastname : 'System',
            'charge_to' => $transaction->charge_to,
            'charge_type' => $transaction->charge_type,
            'status' => $transaction->status,
'approved_by' => $transaction->approvedBy ? $transaction->approvedBy->firstname . ' ' . $transaction->approvedBy->lastname : null,
            'approved_at' => $transaction->approved_at,
            'declined_by' => $transaction->declinedBy ? $transaction->declinedBy->firstname . ' ' . $transaction->declinedBy->lastname : null,
            'declined_at' => $transaction->declined_at,
            'decline_reason' => $transaction->decline_reason,
            'total_amount' => $transaction->total_amount,
            'items' => $items
        ]);
    }
    public function getCreditSummary()
    {
        try {
            $summary = [
                'total' => Transaction::where('payment_type', 'credit')->count(),
                'pending' => Transaction::where('payment_type', 'credit')
                    ->where('status', 'pending')->count(),
                'approved' => Transaction::where('payment_type', 'credit')
                    ->where('status', 'completed')->count(), 
                'declined' => Transaction::where('payment_type', 'credit')
                    ->where('status', 'declined')->count(),
            ];

            $departmentSummary = Transaction::where('payment_type', 'credit')
                ->where('charge_type', 'department')
                ->select('charge_to', DB::raw('SUM(total_amount) as total_amount'))
                ->groupBy('charge_to')
                ->get();

            $facultySummary = Transaction::where('payment_type', 'credit')
                ->where('charge_type', 'faculty')
                ->select('charge_to', DB::raw('SUM(total_amount) as total_amount'))
                ->groupBy('charge_to')
                ->get();

            return response()->json([
                'summary' => $summary,
                'departments' => $departmentSummary,
                'faculty' => $facultySummary
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCreditTransactions(Request $request)
    {
        $query = Transaction::with('user')
            ->where('payment_type', 'credit');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->charge_type) {
            $query->where('charge_type', $request->charge_type);
        }

        if ($request->charge_to) {
            $query->where('charge_to', 'like', '%' . $request->charge_to . '%');
        }

        if ($request->processed_by) {
            $term = $request->processed_by;
            $query->whereHas('user', function($q) use ($term) {
                $q->where(function($qq) use ($term) {
                    $qq->where('firstname', 'like', "%{$term}%")
                       ->orWhere('lastname', 'like', "%{$term}%");
                });
            });
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', (float) $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', (float) $request->amount_max);
        }

        // Sort latest to oldest by default
        $query->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('processed_by', function($row) {
                return $row->user ? $row->user->firstname . ' ' . $row->user->lastname : 'System';
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('total_amount', function($row) {
                return '₱' . number_format($row->total_amount, 2);
            })
            // Keep raw status in payload; provide a separate badge field if needed by views
->addColumn('status_badge', function($row) {
                $statusBg = [
                    'completed' => 'bg-success',
                    'pending' => 'bg-warning',
                    'declined' => 'bg-danger'
                ][strtolower($row->status)] ?? 'bg-secondary';
                $textClass = $statusBg === 'bg-warning' ? 'text-dark' : 'text-white';
                return '<span class="badge ' . $statusBg . ' ' . $textClass . '\">' . ucfirst($row->status) . '</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function approveCreditTransaction($id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::with('items')->findOrFail($id);

            if ($transaction->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending transactions can be approved'
                ], 400);
            }

            $transaction->status = 'completed';
            $transaction->approved_by = auth()->id();
            $transaction->approved_at = now();
            // Clear any decline fields if previously set
            $transaction->declined_by = null;
            $transaction->declined_at = null;
            $transaction->decline_reason = null;
            $transaction->save();


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Credit transaction approval error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving transaction: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function declineCreditTransaction($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::findOrFail($id);

            if ($transaction->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending transactions can be declined'
                ], 400);
            }

            $transaction->status = 'declined';
            $transaction->declined_by = auth()->id();
            $transaction->declined_at = now();
            $transaction->decline_reason = $request->reason;
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction declined successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Credit transaction decline error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error declining transaction: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getCreditReports(Request $request)
    {
        try {
            $period = $request->get('period', 'month');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            
            // Calculate date range based on period
            switch ($period) {
                case 'today':
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'quarter':
                    $startDate = now()->startOfQuarter();
                    $endDate = now()->endOfQuarter();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                case 'custom':
                    $startDate = $dateFrom ? date('Y-m-d 00:00:00', strtotime($dateFrom)) : now()->startOfMonth();
                    $endDate = $dateTo ? date('Y-m-d 23:59:59', strtotime($dateTo)) : now()->endOfMonth();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
            }
            
            // Analytics
            $analytics = [
                'total_amount' => Transaction::where('payment_type', 'credit')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount'),
                'pending_amount' => Transaction::where('payment_type', 'credit')
                    ->where('status', 'pending')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount'),
                'approved_amount' => Transaction::where('payment_type', 'credit')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount'),
                'department_count' => Transaction::where('payment_type', 'credit')
                    ->where('charge_type', 'department')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->distinct('charge_to')
                    ->count('charge_to')
            ];
            
            // Department breakdown
            $departments = Transaction::where('payment_type', 'credit')
                ->where('charge_type', 'department')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    'charge_to',
                    DB::raw('SUM(total_amount) as total_amount'),
                    DB::raw('SUM(CASE WHEN status = "pending" THEN total_amount ELSE 0 END) as pending_amount'),
                    DB::raw('COUNT(*) as transaction_count')
                )
                ->groupBy('charge_to')
                ->orderBy('total_amount', 'desc')
                ->get();
            
            // Faculty/Staff breakdown
            $faculty = Transaction::where('payment_type', 'credit')
                ->whereIn('charge_type', ['faculty', 'staff'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    'charge_to',
                    'charge_type',
                    DB::raw('SUM(total_amount) as total_amount'),
                    DB::raw('COUNT(*) as transaction_count')
                )
                ->groupBy('charge_to', 'charge_type')
                ->orderBy('total_amount', 'desc')
                ->get();
            
            return response()->json([
                'analytics' => $analytics,
                'departments' => $departments,
                'faculty' => $faculty
            ]);
            
        } catch (\Exception $e) {
            Log::error('Credit reports error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function exportCreditReport(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Export functionality coming soon'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
