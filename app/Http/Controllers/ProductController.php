<?php

namespace App\Http\Controllers;

use App\DataTables\ProductsDataTable;
use App\Exports\ProductsExport;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Services\BarcodeService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; 
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(ProductsDataTable $dataTable)
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        
        $summaries = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'deactivated_products' => Product::where('status', 'deactivated')->count(),
            'categorized_products' => Product::whereNotNull('category_id')->count(),
            'uncategorized_products' => Product::whereNull('category_id')->count(),
            'with_images' => Product::whereNotNull('product_image')->count(),
            'without_images' => Product::whereNull('product_image')->count(),
            'total_categories' => Category::count()
        ];

        return $dataTable->render('inventory.products', compact('categories', 'subcategories', 'summaries'));
    }
    
    public function export(Request $request) 
    {
        return Excel::download(new ProductsExport, 'Products_' . date('YmdHis') . '.xlsx');
    }

    public function exportPDF()
    {
        try {
            $products = Product::with('category')->get();
            
            $pdf = PDF::loadView('exports.products-pdf', ['products' => $products])
                ->setPaper('a4', 'landscape');

            return $pdf->download('Products_' . date('YmdHis') . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF Export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF');
        }
    }

    public function exportCSV()
    {
        try {
            return Excel::download(
                new ProductsExport, 
                'Products_' . date('YmdHis') . '.csv',
                \Maatwebsite\Excel\Excel::CSV
            );
        } catch (\Exception $e) {
            \Log::error('CSV Export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate CSV');
        }
    }


    public function printBarcode(string $productCode, int $quantity, BarcodeService $barcodeService)
    {
        return $barcodeService->generateBarcodePDF($productCode, $quantity);
    }
    public function show($id)
    {
        $product = Product::with(['category', 'inventory'])->findOrFail($id);
        $categories = Category::where('status', 'active')->get();
        return response()->json([
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255',
                'product_description' => 'nullable|string',
                'status' => 'required|in:active,deactivated',
                'category_id' => 'required|exists:categories,id',
                'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'base_price' => 'nullable|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'expiration_date' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $product = Product::findOrFail($id);
            
            $product->product_name = $request->product_name;
            $product->product_description = $request->product_description;
            $product->status = $request->status;
            $product->category_id = $request->category_id;

            if ($request->hasFile('product_image')) {
                if ($product->product_image) {
                    Storage::disk('public')->delete($product->product_image);
                }

                $productDir = 'products/' . $product->product_code;
                Storage::disk('public')->makeDirectory($productDir);
                
                $extension = $request->file('product_image')->getClientOriginalExtension();
                $imagePath = $request->file('product_image')
                    ->storeAs($productDir, 'image.' . $extension, 'public');
                
                $product->product_image = $imagePath;
            }

            if ($product->inventory) {
                $product->inventory->update([
                    'base_price' => $request->base_price,
                    'selling_price' => $request->selling_price,
                    'exp_date' => $request->expiration_date
                ]);
            }

            $product->save();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $product->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'product_code' => 'required|integer|unique:products',
                'product_name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'base_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'current_stock' => 'required|numeric|min:0',
                'min_stock' => 'required|numeric|min:0',
                'unit' => 'required|string',
                'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $groupUnits = [
                'pack', 'box', 'carton', 'bag', 'bundle', 'sack',
                'dozen', 'half-dozen', 'pair', 'bottle', 'can', 'case', 'set'
            ];
            $isGroupUnit = in_array($request->unit, $groupUnits);

            if ($isGroupUnit) {
                $groupValidator = Validator::make($request->all(), [
                    'units_per_group' => 'required|numeric|min:1',
                    'base_quantity' => 'required|numeric|min:1'
                ]);

                if ($groupValidator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => $groupValidator->errors()
                    ], 422);
                }
            }
                
            $product = Product::create([
                'product_code' => $request->product_code,
                'product_name' => $request->product_name,
                'category_id' => $request->category_id,
                'product_description' => $request->product_description,
                'status' => $request->status ?? 'active',
            ]);

            $inventoryData = [
                'product_code' => $product->product_code,
                'base_price' => $request->base_price,
                'selling_price' => $request->selling_price,
                'current_stock' => $request->current_stock,
                'min_stock' => $request->min_stock,
                'exp_date' => $request->exp_date,
                'unit' => $request->unit,
                'status' => $request->status ?? 'active'
            ];

            if ($isGroupUnit) {
                $inventoryData['units_per_group'] = $request->units_per_group;
                $inventoryData['base_quantity'] = $request->base_quantity;
            } else {
                $inventoryData['units_per_group'] = 1;
                $inventoryData['base_quantity'] = $request->current_stock;
            }

            if ($request->has('subcategories')) {
                foreach ($request->subcategories as $subcategoryId) {
                    DB::table('product_subcategories')->insert([
                        'product_code' => $product->product_code,
                        'subcategory_id' => $subcategoryId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $inventory = $product->inventory()->create($inventoryData);

            StockMovement::create([
                'inventory_id' => $inventory->id,
                'type' => 'in',
                'quantity' => $request->current_stock,
                'unit' => $request->unit,
                'date' => now(),
                'reference' => $product->product_code,
                'remarks' => 'Initial Stock',
                'created_by' => Auth::id()
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product added successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adjustInventory($productCode, Request $request)
    {}

    public function toggleStatus(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $product->update(['status' => $request->status]);
            if ($product->inventory) {
                $product->inventory->update(['status' => $request->status]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStockLevels($productCode)
    {
        try {
            $inventory = DB::table('inventories')
                ->where('product_code', $productCode)
                ->select('current_stock', 'base_quantity')
                ->first();

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'stock_levels' => [
                    'current_stock' => $inventory->current_stock,
                    'base_quantity' => $inventory->base_quantity
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stock levels'
            ], 500);
        }
    }
}
