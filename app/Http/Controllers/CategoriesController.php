<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::with(['subcategories' => function($query) {
            $query->withCount('products');
        }])->get();

        if (request()->ajax()) {
            return response()->json([
                'categories' => $categories
            ]);
        }

        $totalCategories = $categories->count();
        $totalSubcategories = $categories->pluck('subcategories')->flatten()->count();
        $totalProducts = $categories->pluck('subcategories')->flatten()->sum('products_count');

        return view('inventory.categories', compact('categories', 'totalCategories', 'totalSubcategories', 'totalProducts'));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string',
                'status' => 'required|in:active,deactivated'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = Category::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Category added successfully',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding category: ' . $e->getMessage()
            ], 422);
        }
    }

    public function storeSubcategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:active,deactivated'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subcategory = Subcategory::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Subcategory added successfully',
                'subcategory' => $subcategory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding subcategory: ' . $e->getMessage()
            ], 422);
        }
    }
    public function show($id)
{
    try {
        $category = Category::with(['subcategories' => function($query) {
            $query->orderBy('name', 'asc');
        }])
        ->findOrFail($id);

        $activeSubcategories = $category->subcategories->where('status', 'active')->count();
        $inactiveSubcategories = $category->subcategories->where('status', 'deactivated')->count();

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'status' => $category->status,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
                'stats' => [
                    'total_subcategories' => $category->subcategories->count(),
                    'active_subcategories' => $activeSubcategories,
                    'inactive_subcategories' => $inactiveSubcategories
                ],
                'subcategories' => $category->subcategories->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'description' => $sub->description,
                        'status' => $sub->status,
                        'created_at' => $sub->created_at->format('M d, Y h:ia')
                    ];
                })
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Category not found'
        ], 404);
    }
}
    public function list()
    {
        try {
            $categories = Category::select('id', 'name', 'status')
                ->where('status', 'active')
                ->get();

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteSubcategories(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $subcategoryIds = $request->input('subcategories', []);
            
            DB::table('product_subcategories')
                ->whereIn('subcategory_id', $subcategoryIds)
                ->delete();
                
            Subcategory::whereIn('id', $subcategoryIds)->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Subcategories deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting subcategories: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCategoryDistribution()
    {
        $categories = Category::withCount('products')
            ->where('status', 'active')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->products_count
                ];
            })
            ->sortByDesc('count')
            ->values();
    
        $topCategories = $categories->take(5);
        $othersCount = $categories->slice(5)->sum('count');
    
        $result = $topCategories->toArray();
        if ($othersCount > 0) {
            $result[] = [
                'name' => 'Others',
                'count' => $othersCount
            ];
        }
    
        return response()->json($result);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $category = Category::findOrFail($id);
            
            DB::table('products')
                ->where('category_id', $id)
                ->update(['category_id' => null]);
            
            $subcategoryIds = $category->subcategories->pluck('id')->toArray();
            
            if (!empty($subcategoryIds)) {
                DB::table('product_subcategories')
                    ->whereIn('subcategory_id', $subcategoryIds)
                    ->delete();
                
                Subcategory::whereIn('id', $subcategoryIds)->delete();
            }
            
            $deleted = $category->delete();
            
            if (!$deleted) {
                throw new \Exception('Failed to delete category');
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSubcategory($id)
    {
        try {
            $subcategory = Subcategory::findOrFail($id);
            return response()->json([
                'success' => true,
                'subcategory' => $subcategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategory not found'
            ], 404);
        }
    }
    public function getWithSubcategories()
{
    $categories = Category::with('subcategories')->get();
    
    return response()->json([
        'success' => true,
        'categories' => $categories
    ]);
}

    public function updateSubcategory(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:active,deactivated'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $subcategory = Subcategory::findOrFail($id);
            $subcategory->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Subcategory updated successfully',
                'subcategory' => $subcategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating subcategory: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
{
    try {
        $category = Category::with('subcategories')->findOrFail($id);
        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Category not found'
        ], 404);
    }
}
public function update(Request $request, $id)
{
    try {
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating category'
        ], 500);
    }
}

}
