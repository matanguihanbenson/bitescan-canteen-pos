<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\DataTables\InventoryDataTable;
use Illuminate\Support\Facades\DB;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index(InventoryDataTable $dataTable)
    {
        return $dataTable->render('inventory.stock');
    }

    public function exportPDF(Request $request)
    {
        try {
            $selectedFields = $request->export_fields ?? ['product_name', 'product_code', 'current_stock', 'selling_price', 'exp_date'];
            
            $query = Inventory::with('product')->select('inventories.*');

            if ($request->filled('stock_status')) {
                switch ($request->stock_status) {
                    case 'out_of_stock':
                        $query->where('inventories.current_stock', 0);
                        break;
                    case 'low_stock':
                        $query->whereColumn('inventories.current_stock', '<=', 'inventories.min_stock')
                              ->where('inventories.current_stock', '>', 0);
                        break;
                    case 'high_stock':
                        $query->whereColumn('inventories.current_stock', '>', 'inventories.min_stock');
                        break;
                }
            }

            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('inventories.created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            switch ($request->get('sort_by', 'product_name')) {
                case 'product_name':
                    $query->join('products', 'inventories.product_code', '=', 'products.product_code')
                          ->orderBy('products.product_name');
                    break;
                case 'current_stock':
                    $query->orderBy('inventories.current_stock');
                    break;
                case 'exp_date':
                    $query->orderBy('inventories.exp_date');
                    break;
            }

            $data = $query->get();

            $filteredData = $data->map(function ($item) use ($selectedFields) {
                $filtered = collect();
                foreach ($selectedFields as $field) {
                    switch ($field) {
                        case 'product_name':
                            $filtered['product_name'] = $item->product->product_name;
                            break;
                        case 'product_code':
                            $filtered['product_code'] = $item->product_code;
                            break;
                        case 'current_stock':
                            $filtered['current_stock'] = $item->current_stock;
                            break;
                        case 'base_price':
                            $filtered['base_price'] = $item->base_price;
                            break;
                        case 'selling_price':
                            $filtered['selling_price'] = $item->selling_price;
                            break;
                        case 'exp_date':
                            $filtered['exp_date'] = $item->exp_date;
                            break;
                        case 'min_stock':
                            $filtered['min_stock'] = $item->min_stock;
                            break;
                        case 'unit':
                            $filtered['unit'] = $item->unit;
                            break;
                    }
                }
                return $filtered;
            });
            
            $viewData = [
                'data' => $data,
                'filters' => [
                    'date_range' => $request->filled(['start_date', 'end_date']) 
                        ? Carbon::parse($request->start_date)->format('M d, Y') . ' - ' . 
                          Carbon::parse($request->end_date)->format('M d, Y')
                        : null,
                    'stock_status' => $request->stock_status,
                    'sort_by' => $request->sort_by,
                    'export_fields' => $selectedFields
                ]
            ];

            return view('exports.inventory-preview', $viewData);
            
        } catch (\Exception $e) {
            Log::error('PDF Export Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to generate report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductData($id)
    {
        $stock = Inventory::findOrFail($id);
        $lastMovement = StockMovement::where('inventory_id', $id)
            ->latest()
            ->first();

        $movementInfo = null;
        if ($lastMovement) {
            $movementInfo = [
                'type' => $lastMovement->type,
                'quantity' => $lastMovement->quantity,
                'date' => $lastMovement->created_at->diffForHumans(),
                'adjustment_type' => $lastMovement->adjustment_type,
                'unit' => $lastMovement->unit 
            ];
        }

        return response()->json([
            'stock' => $stock,
            'product_name' => $stock->product->product_name,
            'last_movement' => $movementInfo
        ]);
    }

    public function stockIn(Request $request)
    {
        Log::info('Stock In Request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'stock_in_product_id' => 'required|exists:inventories,id',
            'stock_in_quantity' => 'required|numeric|min:1',
            'stock_in_type' => 'required|string|in:current_stock,base_quantity',
            'stock_in_unit' => 'required|string',
            'stock_in_reference' => 'nullable|string',
            'stock_in_remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $inventory = Inventory::findOrFail($request->stock_in_product_id);
            
            $quantity = $request->stock_in_quantity;
            $type = $request->stock_in_type;
            
            if ($type === 'base_quantity') {
                $inventory->base_quantity += $quantity;
                $inventory->current_stock = floor($inventory->base_quantity / $inventory->units_per_group);
                
                $movement = StockMovement::create([
                    'inventory_id' => $request->stock_in_product_id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'adjustment_type' => 'base_quantity',
                    'unit' => 'pcs',
                    'date' => now(),
                    'reference' => $request->stock_in_reference ?? 'STOCK-IN-' . date('YmdHis'),
                    'remarks' => $request->stock_in_remarks ?? '',
                    'created_by' => auth()->id() ?? 1
                ]);
            } else {
                $inventory->current_stock += $quantity;
                $inventory->base_quantity = $inventory->current_stock * $inventory->units_per_group;
                
                $movement = StockMovement::create([
                    'inventory_id' => $request->stock_in_product_id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'adjustment_type' => 'current_stock',
                    'unit' => $inventory->unit,
                    'date' => now(),
                    'reference' => $request->stock_in_reference ?? 'STOCK-IN-' . date('YmdHis'),
                    'remarks' => $request->stock_in_remarks ?? '',
                    'created_by' => auth()->id() ?? 1
                ]);
            }

            $inventory->save();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'movement' => [
                    'type' => $movement->type,
                    'quantity' => $movement->quantity,
                    'unit' => $movement->unit,
                    'adjustment_type' => $movement->adjustment_type
                ]
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Stock In Error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function stockOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:inventories,id',
            'quantity' => 'required|numeric|min:1',
            'type' => 'required|string|in:current_stock,base_quantity',
            'reference' => 'nullable|string',
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $inventory = Inventory::findOrFail($request->product_id);
            
            if ($request->type === 'base_quantity') {
                if ($inventory->base_quantity < $request->quantity) {
                    throw new \Exception('Insufficient base quantity available');
                }
                $inventory->base_quantity -= $request->quantity;
                $inventory->current_stock = floor($inventory->base_quantity / $inventory->units_per_group);
                
                $unit = 'pcs';
            } else {
                if ($inventory->current_stock < $request->quantity) {
                    throw new \Exception('Insufficient stock available');
                }
                $inventory->current_stock -= $request->quantity;
                $inventory->base_quantity = $inventory->current_stock * $inventory->units_per_group;
                
                $unit = $inventory->unit;
            }

            $inventory->save();

            StockMovement::create([
                'inventory_id' => $request->product_id,
                'type' => 'out',
                'quantity' => $request->quantity,
                'adjustment_type' => $request->type,
                'unit' => $unit,
                'date' => now(),
                'reference' => $request->reference ?? 'STOCK-OUT-' . date('YmdHis'),
                'remarks' => $request->reason,
                'created_by' => auth()->id() ?? 1
            ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getStockHistory($id)
    {
        try {
            $inventory = Inventory::with(['product', 'stockMovements.user'])
                ->findOrFail($id);
            
            $history = $inventory->stockMovements()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($movement) {
                    $quantity = match ($movement->type) {
                        'in' => '+' . $movement->quantity,
                        'out' => '-' . $movement->quantity,
                        'adjustment' => $this->formatAdjustmentQuantity($movement),
                        default => $movement->quantity
                    };

                    return [
                        'type' => $movement->type,
                        'quantity' => $quantity,
                        'unit' => $movement->unit,
                        'reference' => $movement->reference,
                        'remarks' => $this->formatRemarks($movement),
                        'user_name' => optional($movement->user)->firstname . ' ' . optional($movement->user)->lastname ?? 'System',
                        'formatted_date' => $movement->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                    ];
                });

            $counts = $inventory->stockMovements()
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray();

            $totalCount = array_sum($counts);
                
            return response()->json([
                'product' => [
                    'product_code' => $inventory->product_code,
                    'product_name' => $inventory->product->product_name,
                    'current_stock' => $inventory->current_stock,
                    'updated_at' => $inventory->updated_at->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                ],
                'history' => $history,
                'counts' => [
                    'total' => $totalCount,
                    'in' => $counts['in'] ?? 0,
                    'out' => $counts['out'] ?? 0,
                    'adjustment' => $counts['adjustment'] ?? 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Stock History Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching stock history',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function formatAdjustmentQuantity($movement)
    {
        if (preg_match('/\((Current Stock|Base Quantity):\s*([+-]\d+)\)/', $movement->remarks, $matches)) {
            return $matches[2];
        }
        return $movement->quantity >= 0 ? "+{$movement->quantity}" : "{$movement->quantity}";
    }

    private function formatRemarks($movement)
    {
        if ($movement->type === 'adjustment') {
            return preg_replace('/\s*\((Current Stock|Base Quantity):\s*[+-]\d+\)/', '', $movement->remarks);
        }
        return $movement->remarks ?? '-';
    }

    public function stockAdjust(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:inventories,id',
            'current_stock' => 'required|numeric|min:0',
            'base_quantity' => 'required|numeric|min:0',
            'type' => 'required|string',
            'reference' => 'required|string',
            'remarks' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $inventory = Inventory::findOrFail($request->product_id);
            
            $stockDiff = $request->current_stock - $inventory->current_stock;
            $baseDiff = $request->base_quantity - $inventory->base_quantity;
            
            $inventory->current_stock = $request->current_stock;
            $inventory->base_quantity = $request->base_quantity;
            $inventory->save();

            if ($stockDiff !== 0) {
                StockMovement::create([
                    'inventory_id' => $request->product_id,
                    'type' => 'adjustment',
                    'quantity' => abs($stockDiff),
                    'unit' => $inventory->unit,
                    'adjustment_type' => 'current_stock',
                    'date' => now(),
                    'reference' => $request->reference,
                    'remarks' => $request->remarks . ' (Current Stock: ' . ($stockDiff > 0 ? '+' : '-') . abs($stockDiff) . ')',
                    'created_by' => auth()->id() ?? 1
                ]);
            }

            if ($baseDiff !== 0) {
                StockMovement::create([
                    'inventory_id' => $request->product_id,
                    'type' => 'adjustment',
                    'quantity' => abs($baseDiff),
                    'unit' => 'pcs',
                    'adjustment_type' => 'base_quantity',
                    'date' => now(),
                    'reference' => $request->reference,
                    'remarks' => $request->remarks . ' (Base Quantity: ' . ($baseDiff > 0 ? '+' : '-') . abs($baseDiff) . ')',
                    'created_by' => auth()->id() ?? 1
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Stock Adjustment Error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $query = DB::table('products')
                ->join('inventories', 'products.product_code', '=', 'inventories.product_code')
                ->select(
                    'products.product_code',
                    'products.product_name',
                    'products.category_id',
                    'products.status',
                    'inventories.selling_price',
                    'inventories.current_stock',
                    'products.product_image',
                    'inventories.unit',
                    'inventories.units_per_group',
                    'inventories.base_quantity'
                )
                ->where('products.status', 'active')
                ->where(function($query) {
                    $query->where('inventories.current_stock', '>', 0)
                          ->orWhere('inventories.base_quantity', '>', 0);
                });

            if ($request->has('category_id') && $request->category_id != 'all') {
                $query->where('products.category_id', $request->category_id);
            }

            $products = $query->get();

            Log::info('Products query', [
                'category_id' => $request->category_id,
                'count' => $products->count(),
                'active_products' => $products->where('status', 'active')->count()
            ]);

            return response()->json($products);
        } catch (Exception $e) {
            Log::error('Product fetch error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Database error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductByCode($code)
    {
        try {
            $product = Product::select('products.*', 'inventories.selling_price', 'inventories.units_per_group', 'inventories.unit')
                ->join('inventories', 'products.product_code', '=', 'inventories.product_code')
                ->where('products.product_code', $code)
                ->first();
            
            if ($product) {
                return response()->json([
                    'success' => true,
                    'product' => [
                        'product_code' => $product->product_code,
                        'product_name' => $product->product_name,
                        'selling_price' => floatval($product->selling_price),
                        'units_per_group' => $product->units_per_group,
                        'unit' => $product->unit
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product'
            ], 500);
        }
    }

    public function getStockTrends(Request $request)
    {
        try {
            $period = $request->get('period', 'weekly');
            
            switch ($period) {
                case 'daily':
                    $startDate = now()->startOfDay();
                    $groupBy = 'HOUR(created_at)';
                    $dateFormat = 'h:i A';
                    $labels = collect(range(0, 23))->map(function($hour) {
                        return Carbon::createFromTime($hour)->format('h:i A');
                    });
                    break;
                    
                case 'weekly':
                    $startDate = now()->startOfWeek();
                    $groupBy = 'WEEKDAY(created_at)';
                    $dateFormat = 'D';
                    $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    break;
                    
                case 'monthly':
                    $startDate = now()->startOfYear();
                    $groupBy = 'MONTH(created_at)';
                    $dateFormat = 'M';
                    $labels = collect(range(1, 12))->map(function($month) {
                        return Carbon::create(null, $month, 1)->format('M');
                    });
                    break;
            }

            $query = StockMovement::select(
                DB::raw($groupBy . ' as period'),
                'type',
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->where('created_at', '>=', $startDate);

            if ($period === 'monthly') {
                $query->whereYear('created_at', now()->year);
            }

            $stockTrends = $query->groupBy(DB::raw($groupBy), 'type')
                ->orderBy('period')
                ->get();

            $formattedData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Stock In',
                        'data' => array_fill(0, count($labels), 0),
                        'backgroundColor' => '#1cc88a',
                        'borderColor' => '#1cc88a',
                        'type' => 'bar'
                    ],
                    [
                        'label' => 'Stock Out',
                        'data' => array_fill(0, count($labels), 0),
                        'backgroundColor' => '#e74a3b',
                        'borderColor' => '#e74a3b',
                        'type' => 'bar'
                    ]
                ]
            ];

            foreach ($stockTrends as $trend) {
                $index = match ($period) {
                    'monthly' => ((int)$trend->period - 1),
                    'weekly' => (int)$trend->period,
                    default => (int)$trend->period
                };
                
                if ($trend->type === 'in') {
                    $formattedData['datasets'][0]['data'][$index] = (int)$trend->total_quantity;
                } else if ($trend->type === 'out') {
                    $formattedData['datasets'][1]['data'][$index] = (int)$trend->total_quantity;
                }
            }

            return response()->json($formattedData);

        } catch (\Exception $e) {
            Log::error('Stock Trends Error: ' . $e->getMessage(), [
                'period' => $period ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error fetching stock trends',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getSummaryData()
    {
        try {
            $groupUnits = ['pack', 'box', 'carton', 'bag', 'bundle', 'sack', 'dozen', 'half-dozen', 'pair',
                          'bottle', 'can', 'case', 'set'];
            
            $valueSql = 'CASE WHEN unit IN ("' . implode('","', $groupUnits) . '") 
                         THEN base_quantity * selling_price
                         ELSE current_stock * selling_price END';

            $currentValue = DB::table('inventories')
                ->selectRaw("SUM({$valueSql}) as total_value")
                ->first()->total_value ?? 0;

            $lastMonthValue = DB::table('stock_movements')
                ->join('inventories', 'stock_movements.inventory_id', '=', 'inventories.id')
                ->where('stock_movements.created_at', '>=', now()->subMonth()->startOfMonth())
                ->where('stock_movements.created_at', '<=', now()->subMonth()->endOfMonth())
                ->selectRaw("SUM(
                    CASE 
                        WHEN type = 'in' THEN quantity * selling_price
                        WHEN type = 'out' THEN -quantity * selling_price
                        ELSE 0
                    END
                ) as monthly_value")
                ->first()->monthly_value ?? 0;

            $percentageChange = $lastMonthValue != 0 
                ? (($currentValue - $lastMonthValue) / abs($lastMonthValue)) * 100 
                : 0;

            $totalProducts = DB::table('inventories')->count();
            $lowStockCount = DB::table('inventories')
                ->whereColumn('current_stock', '<=', 'min_stock')
                ->where('current_stock', '>', 0)
                ->count();
            $outOfStockCount = DB::table('inventories')
                ->where('current_stock', '=', 0)
                ->count();

            return response()->json([
                'total_value' => number_format($currentValue, 2),
                'total_products' => $totalProducts,
                'low_stock' => $lowStockCount,
                'out_of_stock' => $outOfStockCount,
                'value_change' => round($percentageChange, 1)
            ]);
        } catch (\Exception $e) {
            Log::error('Summary Data Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching summary data'
            ], 500);
        }
    }

    public function updateStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_code' => 'required',
                'current_stock' => 'required|integer|min:0',
                'base_quantity' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false, 
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $inventory = DB::table('inventories')
                ->where('product_code', $request->product_code)
                ->update([
                    'current_stock' => $request->current_stock,
                    'base_quantity' => $request->base_quantity,
                    'updated_at' => now()
                ]);

            if (!$inventory) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Inventory record not found'
                ], 404);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Stock Update Error:', [
                'message' => $e->getMessage(),
                'product_code' => $request->product_code
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Error updating stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showLogs()
    {
        return view('inventory.logs');
    }

    public function getLogsData(Request $request)
    {
        try {
            $query = StockMovement::with(['inventory.product', 'user'])
                ->select('stock_movements.*');

            $dateFrom = $request->filled('dateFrom') ? 
                Carbon::parse($request->dateFrom)->startOfDay() : 
                Carbon::today()->startOfDay();
            
            $dateTo = $request->filled('dateTo') ? 
                Carbon::parse($request->dateTo)->endOfDay() : 
                Carbon::today()->endOfDay();

            $query->whereDate('date', '>=', $dateFrom)
                  ->whereDate('date', '<=', $dateTo);

            if ($request->filled('movementType')) {
                $query->where('type', $request->movementType);
            }

            if ($request->filled('productSearch')) {
                $query->whereHas('inventory.product', function($q) use ($request) {
                    $q->where('product_name', 'like', '%' . $request->productSearch . '%');
                });
            }

            $movements = $query->orderBy('date', 'desc')
                ->get()
                ->map(function ($movement) {
                    return [
                        'date' => $movement->date ? $movement->date->toDateTimeString() : '',
                        'reference' => $movement->reference,
                        'product_name' => $movement->inventory->product->product_name,
                        'product_code' => $movement->inventory->product->product_code,
                        'type' => $movement->type,
                        'quantity' => $movement->quantity,
                        'unit' => $movement->unit,
                        'remarks' => $movement->remarks,
                        'user_name' => $movement->user ? ($movement->user->firstname . ' ' . $movement->user->lastname) : 'System'
                    ];
                });

            return response()->json([
                'data' => $movements,
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $movements->count(),
                'recordsFiltered' => $movements->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Logs Data Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching logs',
                'message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function getFilteredMovements(Request $request)
    {
        try {
            $query = StockMovement::with(['inventory.product', 'user'])
                ->orderBy('date', 'desc');

            try {
                $filterDate = $request->filled('filterDate') ? 
                    Carbon::parse($request->filterDate) : 
                    Carbon::today();
            } catch (\Exception $e) {
                $filterDate = Carbon::today();
            }

            Log::info('Filter Parameters:', [
                'filterDate' => $request->filterDate,
                'parsed_date' => $filterDate->format('Y-m-d'),
                'movementType' => $request->movementType,
                'fields' => $request->fields
            ]);

            $query->where(function($q) use ($filterDate) {
                $q->whereDate('date', $filterDate)
                  ->orWhereDate('created_at', $filterDate);
            });

            if ($request->filled('movementType')) {
                $query->where('type', $request->movementType);
            }

            $movements = $query->get();
            
            Log::info('Query Results:', [
                'count' => $movements->count(),
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            if ($movements->isEmpty()) {
                Log::warning('No movements found for date: ' . $filterDate->format('Y-m-d'));
                return collect([[
                    'Date' => 'No data',
                    'Reference' => 'No data',
                    'Product' => 'No data',
                    'Type' => 'No data',
                    'Quantity' => 'No data',
                    'Updated By' => 'No data',
                    'Remarks' => 'No data'
                ]]);
            }

            $selectedFields = is_array($request->fields) ? $request->fields : explode(',', $request->fields ?? '');
            if (empty($selectedFields)) {
                $selectedFields = ['date', 'reference', 'product', 'type', 'quantity', 'user', 'remarks'];
            }

            return $movements->map(function ($movement) use ($selectedFields) {
                $data = [];
                
                if (in_array('date', $selectedFields)) {
                    $data['Date'] = $movement->date->format('M d, Y h:i A');
                }
                if (in_array('reference', $selectedFields)) {
                    $data['Reference'] = $movement->reference;
                }
                if (in_array('product', $selectedFields)) {
                    $data['Product'] = $movement->inventory->product->product_name;
                }
                if (in_array('type', $selectedFields)) {
                    $data['Type'] = strtoupper($movement->type);
                }
                if (in_array('quantity', $selectedFields)) {
                    $data['Quantity'] = $movement->quantity . ' ' . $movement->unit;
                }
                if (in_array('user', $selectedFields)) {
                    $data['Updated By'] = $movement->user ? 
                        ($movement->user->firstname . ' ' . $movement->user->lastname) : 
                        'System';
                }
                if (in_array('remarks', $selectedFields)) {
                    $data['Remarks'] = $movement->remarks ?? 'No remarks provided';
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('Filtered Movements Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function exportLogsPDF(Request $request)
    {
        try {
            Log::info('Starting PDF Export', ['request' => $request->all()]);

            $movements = $this->getFilteredMovements($request);
            
            if ($movements->isEmpty() || isset($movements->first()['Date']) && $movements->first()['Date'] === 'No data') {
                return response()->json([
                    'success' => false,
                    'message' => 'No data available for the selected date'
                ], 422);
            }

            $data = [
                'movements' => $movements->toArray(),
                'date_generated' => now()->format('F d, Y h:i A')
            ];

            $pdf = PDF::loadView('exports.logs-pdf', $data);
            
            $pdf->setPaper([0, 0, 612, 792], 'portrait');
            
            return $pdf->download('stock-movements-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportLogsExcel(Request $request)
    {
        try {
            $movements = $this->getFilteredMovements($request);

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = array_keys($movements->first());
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            $row = 2;
            foreach ($movements as $movement) {
                $col = 'A';
                foreach ($movement as $value) {
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            $fileName = 'stock-movements-' . now()->format('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Excel Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error generating Excel file');
        }
    }

    public function exportLogsCSV(Request $request)
    {
        try {
            $movements = $this->getFilteredMovements($request);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=stock-movements-' . now()->format('Y-m-d') . '.csv'
            ];

            $handle = fopen('php://temp', 'w+');
            
            fputcsv($handle, array_keys($movements->first()));
            
            foreach ($movements as $movement) {
                fputcsv($handle, $movement);
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content, 200, $headers);
        } catch (\Exception $e) {
            Log::error('CSV Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error generating CSV file');
        }
    }

    public function getLogsSummary()
    {
        try {
            $summary = StockMovement::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN type = "in" THEN 1 ELSE 0 END) as stock_ins,
                SUM(CASE WHEN type = "out" THEN 1 ELSE 0 END) as stock_outs,
                SUM(CASE WHEN type = "adjustment" THEN 1 ELSE 0 END) as adjustments
            ')->first();

            return response()->json([
                'total' => number_format($summary->total),
                'stock_ins' => number_format($summary->stock_ins),
                'stock_outs' => number_format($summary->stock_outs),
                'adjustments' => number_format($summary->adjustments)
            ]);
        } catch (\Exception $e) {
            Log::error('Logs Summary Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching summary'
            ], 500);
        }
    }

    public function exportAnalyticsReport(Request $request)
    {
        try {
            $sections = $request->sections ?? ['summary', 'trends', 'categories'];
            
            $startDate = $request->filled('start_date') 
                ? Carbon::parse($request->start_date) 
                : now()->subDays(30);
            $endDate = $request->filled('end_date')
                ? Carbon::parse($request->end_date)
                : now();

            $summary = DB::table('inventories')
                ->selectRaw('
                    COUNT(*) as total_products,
                    SUM(current_stock * selling_price) as total_value,
                    COUNT(CASE WHEN current_stock <= min_stock AND current_stock > 0 THEN 1 END) as low_stock
                ')
                ->first();

            $trends = StockMovement::selectRaw('
                    DATE(created_at) as date,
                    type,
                    SUM(quantity) as total
                ')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(created_at)'), 'type')
                ->orderBy(DB::raw('DATE(created_at)'))
                ->get();

            $dates = $trends->pluck('date')->unique()->values();
            $stockTrends = [
                'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('M d')),
                'datasets' => [
                    [
                        'label' => 'Stock In',
                        'data' => $dates->map(function($date) use ($trends) {
                            return $trends->where('date', $date)
                                        ->where('type', 'in')
                                        ->first()?->total ?? 0;
                        })->values(),
                        'backgroundColor' => '#1cc88a'
                    ],
                    [
                        'label' => 'Stock Out',
                        'data' => $dates->map(function($date) use ($trends) {
                            return $trends->where('date', $date)
                                        ->where('type', 'out')
                                        ->first()?->total ?? 0;
                        })->values(),
                        'backgroundColor' => '#e74a3b'
                    ]
                ]
            ];

            $categories = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->selectRaw('
                    categories.name,
                    COUNT(*) as count
                ')
                ->groupBy('categories.id', 'categories.name')
                ->get();

            $categoryDistribution = [
                'labels' => $categories->pluck('name'),
                'datasets' => [[
                    'data' => $categories->pluck('count'),
                    'backgroundColor' => [
                        '#4e73df', '#1cc88a', '#36b9cc', 
                        '#f6c23e', '#e74a3b', '#858796'
                    ]
                ]]
            ];

            $dateRange = $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');

            return view('exports.analytics-report-preview', [
                'summary' => [
                    'total_value' => number_format($summary->total_value ?? 0, 2),
                    'total_products' => $summary->total_products ?? 0,
                    'low_stock' => $summary->low_stock ?? 0
                ],
                'stockTrends' => $stockTrends,
                'categoryDistribution' => $categoryDistribution,
                'filters' => [
                    'date_range' => $dateRange,
                    'sections' => $sections
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Analytics Report Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to generate analytics report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStockLevels($product_code)
    {
        try {
            $inventory = Inventory::where('product_code', $product_code)->first();
            
            if ($inventory) {
                return response()->json([
                    'success' => true,
                    'stock_levels' => [
                        'current_stock' => $inventory->current_stock,
                        'base_quantity' => $inventory->base_quantity,
                        'units_per_group' => $inventory->units_per_group,
                        'unit' => $inventory->unit,
                        'min_stock' => $inventory->min_stock
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stock levels: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportInventoryReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'stock_status' => 'nullable|in:all,low_stock,out_of_stock,high_stock',
                'category' => 'nullable|string',
                'sort_by' => 'nullable|in:product_name,current_stock,exp_date,selling_price',
                'format' => 'required|in:pdf,excel,csv',
                'export_fields' => 'array',
                'include_analytics' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $reportData = $this->generateInventoryReportData($request);

            switch ($request->format) {
                case 'pdf':
                    return $this->exportInventoryReportPDF($reportData, $request);
                case 'excel':
                    return $this->exportInventoryReportExcel($reportData, $request);
                case 'csv':
                    return $this->exportInventoryReportCSV($reportData, $request);
                default:
                    return response()->json(['error' => 'Invalid format'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Inventory report export error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate inventory report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewInventoryReport(Request $request)
    {
        try {
            $reportData = $this->generateInventoryReportData($request);

            return view('exports.inventory-report-preview', [
                'reportData' => $reportData,
                'filters' => $request->all(),
                'generatedAt' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Inventory report preview error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate report preview');
        }
    }

    private function generateInventoryReportData(Request $request)
    {
        $query = Inventory::with(['product', 'product.category']);

        // Apply filters
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'out_of_stock':
                    $query->where('current_stock', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('current_stock', '<=', 'min_stock')
                          ->where('current_stock', '>', 0);
                    break;
                case 'high_stock':
                    $query->whereColumn('current_stock', '>', 'min_stock');
                    break;
            }
        }

        if ($request->filled('category')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('updated_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Apply sorting
        switch ($request->get('sort_by', 'product_name')) {
            case 'product_name':
                $query->join('products', 'inventories.product_code', '=', 'products.product_code')
                      ->orderBy('products.product_name');
                break;
            case 'current_stock':
                $query->orderBy('current_stock', 'asc');
                break;
            case 'exp_date':
                $query->orderBy('exp_date', 'asc');
                break;
            case 'selling_price':
                $query->orderBy('selling_price', 'desc');
                break;
        }

        $inventory = $query->get();

        // Generate analytics if requested
        $analytics = [];
        if ($request->boolean('include_analytics', true)) {
            $analytics = $this->generateInventoryAnalytics($inventory);
        }

        return [
            'inventory' => $inventory,
            'analytics' => $analytics,
            'summary' => [
                'total_items' => $inventory->count(),
                'total_value' => $inventory->sum(function($item) {
                    return $item->current_stock * $item->selling_price;
                }),
                'low_stock_count' => $inventory->where('current_stock', '<=', function($item) {
                    return $item->min_stock;
                })->where('current_stock', '>', 0)->count(),
                'out_of_stock_count' => $inventory->where('current_stock', 0)->count(),
                'categories' => $inventory->groupBy('product.category.name')->map->count()
            ]
        ];
    }

    private function generateInventoryAnalytics($inventory)
    {
        return [
            'stock_distribution' => [
                'high_stock' => $inventory->filter(function($item) {
                    return $item->current_stock > $item->min_stock;
                })->count(),
                'low_stock' => $inventory->filter(function($item) {
                    return $item->current_stock <= $item->min_stock && $item->current_stock > 0;
                })->count(),
                'out_of_stock' => $inventory->where('current_stock', 0)->count()
            ],
            'value_analysis' => [
                'highest_value_items' => $inventory->sortByDesc(function($item) {
                    return $item->current_stock * $item->selling_price;
                })->take(5),
                'lowest_stock_items' => $inventory->where('current_stock', '>', 0)->sortBy('current_stock')->take(5)
            ],
            'expiry_analysis' => [
                'expiring_soon' => $inventory->filter(function($item) {
                    return $item->exp_date && $item->exp_date <= now()->addDays(30);
                })->count(),
                'expired' => $inventory->filter(function($item) {
                    return $item->exp_date && $item->exp_date < now();
                })->count()
            ]
        ];
    }

    private function exportInventoryReportPDF($reportData, $request)
    {
        $pdf = PDF::loadView('exports.inventory-report-pdf', [
            'reportData' => $reportData,
            'filters' => $request->all(),
            'generatedAt' => now()
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'inventory-report-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    private function exportInventoryReportExcel($reportData, $request)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', 'Inventory Report');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('F d, Y h:i A'));
        
        $row = 4;
        
        // Summary
        $sheet->setCellValue('A' . $row, 'SUMMARY');
        $row += 2;
        
        $sheet->setCellValue('A' . $row, 'Total Items:');
        $sheet->setCellValue('B' . $row, $reportData['summary']['total_items']);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Value:');
        $sheet->setCellValue('B' . $row, '₱' . number_format($reportData['summary']['total_value'], 2));
        $row += 3;
        
        // Inventory data
        $sheet->setCellValue('A' . $row, 'INVENTORY DETAILS');
        $row += 2;
        
        // Headers
        $headers = ['Product Name', 'Code', 'Current Stock', 'Min Stock', 'Selling Price', 'Base Price', 'Expiry Date'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        $row++;
        
        // Data
        foreach ($reportData['inventory'] as $item) {
            $sheet->setCellValue('A' . $row, $item->product->product_name);
            $sheet->setCellValue('B' . $row, $item->product_code);
            $sheet->setCellValue('C' . $row, $item->current_stock . ' ' . $item->unit);
            $sheet->setCellValue('D' . $row, $item->min_stock);
            $sheet->setCellValue('E' . $row, '₱' . number_format($item->selling_price, 2));
            $sheet->setCellValue('F' . $row, '₱' . number_format($item->base_price, 2));
            $sheet->setCellValue('G' . $row, $item->exp_date ? $item->exp_date->format('M d, Y') : 'N/A');
            $row++;
        }
        
        $filename = 'inventory-report-' . now()->format('Y-m-d-His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function exportInventoryReportCSV($reportData, $request)
    {
        $filename = 'inventory-report-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename
        ];
        
        $handle = fopen('php://temp', 'w+');
        
        // Summary data
        fputcsv($handle, ['INVENTORY REPORT SUMMARY']);
        fputcsv($handle, ['Generated', now()->format('F d, Y h:i A')]);
        fputcsv($handle, []);
        
        fputcsv($handle, ['Total Items', $reportData['summary']['total_items']]);
        fputcsv($handle, ['Total Value', '₱' . number_format($reportData['summary']['total_value'], 2)]);
        fputcsv($handle, ['Low Stock Items', $reportData['summary']['low_stock_count']]);
        fputcsv($handle, ['Out of Stock Items', $reportData['summary']['out_of_stock_count']]);
        fputcsv($handle, []);
        
        // Inventory details
        fputcsv($handle, ['INVENTORY DETAILS']);
        fputcsv($handle, ['Product Name', 'Code', 'Current Stock', 'Min Stock', 'Selling Price', 'Base Price', 'Expiry Date', 'Category']);
        
        foreach ($reportData['inventory'] as $item) {
            fputcsv($handle, [
                $item->product->product_name,
                $item->product_code,
                $item->current_stock . ' ' . $item->unit,
                $item->min_stock,
                '₱' . number_format($item->selling_price, 2),
                '₱' . number_format($item->base_price, 2),
                $item->exp_date ? $item->exp_date->format('M d, Y') : 'N/A',
                $item->product->category->name ?? 'N/A'
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content, 200, $headers);
    }

}
