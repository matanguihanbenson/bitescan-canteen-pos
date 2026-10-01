<?php

namespace App\Http\Controllers;

use App\DataTables\MenuDataTable;
use App\Models\MenuSubcategory;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuOrder;
use App\Models\MenuGroup;
use App\Models\MenuGroupItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use TCPDF;
use App\Services\MenuBarcodeService;

class MenuController extends Controller 
{

    public function getDashboardStats()
    {
        try {
            $totalMenuItems = Menu::where('is_archived', '!=', 'true')
                ->count();
            Log::info('Total non-archived menu items:', ['count' => $totalMenuItems]);
            
            $activeItems = Menu::where('status', 'active')
                ->where('is_archived', '!=', 'true')
                ->count();
            Log::info('Active non-archived menu items:', ['count' => $activeItems]);
            
            $categoriesCount = MenuCategory::where('status', 'active')->count();
            Log::info('Active categories:', ['count' => $categoriesCount]);
            
            Log::info('Fetching most ordered items');
            $mostOrdered = DB::table('transaction_items')
                ->select('menu_name', DB::raw('COUNT(*) as count'))
                ->where('order_type', '=', 'menu')
                ->whereNotNull('menu_name')
                ->groupBy('menu_name')
                ->orderByDesc('count')
                ->first();

            Log::info('Most ordered item raw data:', [
                'data' => $mostOrdered ? [
                    'menu_name' => $mostOrdered->menu_name,
                    'count' => $mostOrdered->count
                ] : 'No orders found'
            ]);

            $response = [
                'success' => true,
                'stats' => [
                    'totalMenuItems' => $totalMenuItems ?? 0,
                    'activeItems' => $activeItems ?? 0,
                    'categoriesCount' => $categoriesCount ?? 0,
                    'mostOrdered' => $mostOrdered ? [
                        'name' => $mostOrdered->menu_name,
                        'count' => $mostOrdered->count
                    ] : null
                ]
            ];

            Log::info('Final response data:', $response);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => true,
                'stats' => [
                    'totalMenuItems' => 0,
                    'activeItems' => 0,
                    'categoriesCount' => 0,
                    'mostOrdered' => null
                ]
            ]);
        }
    }

    public function deleteCategory($id)
    {
        try {
            $category = MenuCategory::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'menu_code' => 'required|string|max:50|unique:menus,menu_code',
                'price' => 'required|numeric|min:0',
                'menucategory_id' => 'nullable|exists:menu_categories,id',
                'menusubcategory_id' => 'nullable|exists:menu_subcategories,id',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $menuItem = Menu::create($validated);

            if ($request->hasFile('image')) {
                if ($menuItem->image_path) {
                    Storage::disk('public')->delete($menuItem->image_path);
                }

                $menuDir = 'menu/' . $menuItem->id;
                Storage::disk('public')->makeDirectory($menuDir);
                
                $extension = $request->file('image')->getClientOriginalExtension();
                $imagePath = $menuDir . '/image.' . $extension;
                
                Storage::disk('public')->putFileAs(
                    $menuDir,
                    $request->file('image'),
                    'image.' . $extension
                );
                
                $menuItem->update(['image_path' => $imagePath]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Menu item added successfully',
                'data' => $menuItem->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'menu_code' => 'required|string|max:50|unique:menus,menu_code,'.$id,
                'price' => 'required|numeric|min:0',
                'menucategory_id' => 'nullable|exists:menu_categories,id',
                'menusubcategory_id' => 'nullable|exists:menu_subcategories,id',
                'description' => 'nullable|string',
                'status' => 'required|in:active,deactivated',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $menu = Menu::findOrFail($id);
            $validated = $validator->validated();

            if ($request->hasFile('image')) {
                if ($menu->image_path) {
                    Storage::disk('public')->delete($menu->image_path);
                }

                $menuDir = 'menu/' . $menu->id;
                Storage::disk('public')->makeDirectory($menuDir);
                
                $extension = $request->file('image')->getClientOriginalExtension();
                $imagePath = $menuDir . '/image.' . $extension;
                
                Storage::disk('public')->putFileAs(
                    $menuDir,
                    $request->file('image'),
                    'image.' . $extension
                );
                
                $validated['image_path'] = $imagePath;
            }

            $menu->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Menu item updated successfully',
                'data' => $menu->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Menu update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating menu item'
            ], 500);
        }
    }

    public function index(MenuDataTable $dataTable)
    {
        $categories = MenuCategory::where('status', 'active')->get();
        $subcategories = MenuSubcategory::where('status', 'active')->get();
        
        return $dataTable->render('menu.index', compact('categories', 'subcategories'));
    }

    public function getSubcategories($categoryId)
    {
        $subcategories = MenuSubcategory::where('menucategory_id', $categoryId)
            ->where('status', 'active')
            ->get();

        return response()->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }

    public function show($id)
    {
        try {
            Log::info('Attempting to find menu item with ID: ' . $id);
            
            $exists = Menu::where('id', $id)->exists();
            if (!$exists) {
                Log::warning('Menu item not found with ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found'
                ], 404);
            }

            $menu = Menu::with(['category', 'subcategory'])
                ->findOrFail($id);
            
            Log::info('Found menu item:', $menu->toArray());
            
            $data = [
                'id' => $menu->id,
                'name' => $menu->name,
                'menu_code' => $menu->menu_code,
                'price' => $menu->price,
                'description' => $menu->description,
                'status' => $menu->status,
                'category_id' => optional($menu->category)->id,
                'subcategory_id' => optional($menu->subcategory)->id,
                'image_url' => $menu->image_path ? asset('storage/' . $menu->image_path) : null
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Menu show error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving menu item: ' . $e->getMessage()
            ], 404);
        }
    }


    public function getArchivedMenus()
    {
        try {
            Log::info('Starting getArchivedMenus');
            
            DB::enableQueryLog();
            
            $query = Menu::with(['category', 'subcategory'])
                ->where('is_archived', '=', 'true')
                ->orderBy('updated_at', 'desc');
                
            $items = $query->get();
            
            Log::info('Archive query:', [
                'sql' => DB::getQueryLog(),
                'count' => $items->count(),
                'sample_item' => $items->first()?->toArray()
            ]);

            Log::info('Items query result:', [
                'raw_count' => $items->count(),
                'raw_data' => $items->toArray(),
                'is_archived_values' => $items->pluck('is_archived')->toArray()
            ]);
            
            Log::info('Raw archived items:', [
                'count' => $items->count(),
                'first_item' => $items->first()?->toArray()
            ]);

            Log::info('Found archived items:', [
                'count' => $items->count(),
                'items' => $items->toArray()
            ]);

            if ($items->isEmpty()) {
                Log::info('No archived items found');
                return response()->json([
                    'success' => true,
                    'items' => []
                ]);
            }

            $formattedItems = $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'description' => $item->description,
                    'image_path' => $item->image_path,
                    'category' => $item->category,
                    'subcategory' => $item->subcategory,
                    'status' => $item->status,
                    'archived_at' => $item->updated_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'items' => $formattedItems
            ]);
        } catch (\Exception $e) {
            Log::error('Archive fetch error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error loading archived items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restoreMenu($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->update(['is_archived' => 'false']);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring menu item'
            ], 500);
        }
    }

    public function archiveMenu($id)
    {
        try {
            Log::info('Archive request received for menu:', ['id' => $id]);
            
            $menu = Menu::findOrFail($id);
            Log::debug('Menu found:', $menu->toArray());
            
            $result = $menu->update(['is_archived' => 'true']);
            Log::info('Archive update result:', ['success' => $result]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item archived successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Archive error:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error archiving menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loadMenu()
    {
        $availableMenuItems = Menu::with('category')
            ->whereHas('category')
            ->get()
            ->groupBy(function($item) {
                return optional($item->category)->name ?? 'Uncategorized';
            });
        
        $categories = MenuCategory::where('status', 'active')->get();
        
        $menuGroups = MenuGroup::with(['category', 'items.menu'])
            ->where('status', 'active')
            ->where('is_archived', '!=', 'true')
            ->get()
            ->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'category' => optional($group->category)->name ?? 'Uncategorized',
                    'itemCount' => $group->items->count(),
                    'lastUpdated' => $group->updated_at->diffForHumans(),
                    'is_displayed' => $group->is_displayed,
                    'items' => $group->items->map(function($item) {
                        $imagePath = $item->menu->image_path 
                            ? asset('storage/' . $item->menu->image_path)
                            : asset('images/menu.png');
                            
                        return [
                            'id' => $item->menu->id,
                            'name' => $item->menu->name,
                            'price' => number_format($item->menu->price, 2),
                            'category' => optional($item->menu->category)->name ?? 'Uncategorized',
                            'image' => $imagePath,
                            'isActive' => $item->menu->status === 'active'
                        ];
                    })
                ];
            });

        $archivedMenuGroups = MenuGroup::with(['category', 'items.menu'])
            ->where('status', 'active')
            ->where('is_archived', 'true')
            ->get()
            ->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'category' => $group->category->name,
                    'itemCount' => $group->items->count(),
                    'archivedDate' => $group->updated_at->format('Y-m-d'),
                    'archivedBy' => $group->user ? $group->user->firstname . ' ' . $group->user->lastname : 'System',
                    'archiveReason' => $group->archive_reason ?? 'No reason provided',
                    'lastUpdated' => $group->updated_at->diffForHumans(),
                    'items' => $group->items->map(function($item) {
                        $imagePath = $item->menu->image_path 
                            ? asset('storage/' . $item->menu->image_path)
                            : asset('images/menu.png');
                            
                        return [
                            'id' => $item->menu->id,
                            'name' => $item->name,
                            'price' => number_format($item->price, 2),
                            'category' => $item->category->name,
                            'image' => $imagePath,
                            'isActive' => $item->status === 'active'
                        ];
                    })
                ];
            });

        return view('menu.load-menu', compact('menuGroups', 'categories', 'availableMenuItems', 'archivedMenuGroups'));
    }

    public function storeMenuGroup(Request $request)
    {
        try {
            Log::info('storeMenuGroup called');
            Log::info('Request data:', $request->all());

            $menuItems = Menu::whereIn('id', $request->menu_items)->get();
            if ($menuItems->count() !== count($request->menu_items)) {
                Log::error('Some menu items do not exist');
                return response()->json([
                    'success' => false,
                    'message' => 'One or more selected menu items do not exist'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:menu_categories,id',
                'description' => 'nullable|string',
                'menu_items' => 'required|array|min:1',
                'menu_items.*' => 'exists:menus,id'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            DB::beginTransaction();

            Log::info('Creating menu group with data:', [
                'name' => $request->name,
                'menucategory_id' => $request->category_id,
                'description' => $request->description
            ]);

            $menuGroup = MenuGroup::create([
                'name' => $request->name,
                'menucategory_id' => $request->category_id,
                'description' => $request->description,
                'status' => 'active',
                'created_by' => auth()->id() ?? 1
            ]);

            Log::info('Created menu group:', $menuGroup->toArray());

            foreach ($request->menu_items as $menuId) {
                Log::info('Creating menu group item for menu ID: ' . $menuId);
                MenuGroupItem::create([
                    'menugroup_id' => $menuGroup->id,
                    'menu_id' => $menuId
                ]);            }

            DB::commit();
            Log::info('Menu group creation successful');

            return response()->json([
                'success' => true,
                'message' => 'Menu group created successfully',
                'data' => $menuGroup->load('items.menu')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu group creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating menu group: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateGroupDisplayStatus($id)
    {
        try {
            $menuGroup = MenuGroup::findOrFail($id);
            $menuGroup->update(['is_displayed' => 'true']);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu group loaded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading menu group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading menu group'
            ], 500);
        }
    }

    public function updateGroupUndisplayStatus($id)
    {
        try {
            $menuGroup = MenuGroup::findOrFail($id);
            $menuGroup->update(['is_displayed' => 'false']);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu group unloaded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error unloading menu group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error unloading menu group'
            ], 500);
        }
    }

    public function archiveMenuGroup($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a reason for archiving'
                ], 422);
            }

            $menuGroup = MenuGroup::findOrFail($id);
            $menuGroup->update([
                'is_archived' => 'true',
                'archive_reason' => $request->reason,
                'updated_by' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu group archived successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error archiving menu group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error archiving menu group'
            ], 500);
        }
    }

    public function restoreMenuGroup($id)
    {
        try {
            $menuGroup = MenuGroup::findOrFail($id);
            $menuGroup->update(['is_archived' => 'false']);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu group restored successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring menu group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error restoring menu group'
            ], 500);
        }
    }

    public function deleteMenuGroup($id)
    {
        try {
            DB::beginTransaction();
            
            $menuGroup = MenuGroup::findOrFail($id);
            
            MenuGroupItem::where('menugroup_id', $id)->delete();
            
            $menuGroup->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Menu group deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting menu group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting menu group'
            ], 500);
        }
    }

    public function getDisplayedMenuGroups()
    {
        try {
            Log::info('Starting getDisplayedMenuGroups');
            
            $categories = MenuCategory::where('status', 'active')->get();
            
            // Lightweight caching to speed up POS modal
            $cacheKey = 'pos_displayed_menu_items_v1';
            $menuItems = cache()->remember($cacheKey, now()->addMinutes(2), function () {
                return DB::table('menu_group_items')
                ->join('menu_groups', 'menu_group_items.menugroup_id', '=', 'menu_groups.id')
                ->join('menus', 'menu_group_items.menu_id', '=', 'menus.id')
                ->join('menu_categories', 'menus.menucategory_id', '=', 'menu_categories.id')
                ->where('menu_groups.is_displayed', '=', 'true')
                ->where('menu_groups.status', '=', 'active')
                ->where('menus.status', '=', 'active')
                ->select(
                    'menus.id',
                    'menus.menu_code as code',
                    'menus.name',
                    'menus.price',
                    'menus.description',
                    'menus.image_path',
                    'menus.status',
                    'menus.menucategory_id as category_id',
                    'menu_categories.name as category_name'
                )
                ->distinct()
                ->get();
            });

            Log::info('Found menu items:', [
                'count' => $menuItems->count(),
                'first_item' => $menuItems->first()
            ]);

            $formattedItems = $menuItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'price' => (float)$item->price,
                    'description' => $item->description,
                    'image' => $item->image_path ? url('storage/' . $item->image_path) : null,
                    'status' => $item->status,
                    'category_id' => $item->category_id,
                    'category_name' => $item->category_name
                ];
            });

            Log::info('Formatted items count: ' . $formattedItems->count());

            return response()->json([
                'success' => true,
                'items' => $formattedItems->values()->all(),
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDisplayedMenuGroups: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching menu items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMenuItems(Request $request)
    {
        try {
            $query = Menu::query()
                ->with('category')
                ->where('status', 'active')
                ->where('is_archived', '!=', 'true');

            if ($request->has('category_id')) {
                $query->where('menucategory_id', $request->category_id);
            }

            $items = $query->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->price,
                        'description' => $item->description,
                        'image' => $item->image_path ? Storage::disk('public')->url($item->image_path) : null,
                        'status' => $item->status,
                        'category_id' => $item->category->id,
                        'category_name' => $item->category->name
                    ];
                });

            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching menu items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching menu items'
            ], 500);
        }
    }

    public function getCategories()
    {
        try {
            Log::info('Fetching active categories');
            
            $categories = MenuCategory::where('status', 'active')->get();
            
            Log::info('Categories found:', [
                'count' => $categories->count(),
                'categories' => $categories->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Categories fetched successfully',
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching menu categories:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching menu categories: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMenuByCode($code)
    {
        try {
            Log::info('Looking up menu code: ' . $code);
            
            $menu = Menu::where('menu_code', $code)
                ->where('status', 'active')
                ->first();
            
            if (!$menu) {
                Log::info('Menu not found for code: ' . $code);
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found or inactive'
                ], 404);
            }

            Log::info('Found menu item: ', $menu->toArray());
            return response()->json([
                'success' => true,
                'menu' => [
                    'code' => $menu->menu_code,
                    'name' => $menu->name,
                    'price' => floatval($menu->price),
                    'type' => 'menu'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMenuByCode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching menu item'
            ], 500);
        }
    }

    public function printBarcode($code, $quantity, MenuBarcodeService $barcodeService)
{
    try {
        Log::info('Starting barcode generation', ['code' => $code, 'quantity' => $quantity]);
        return $barcodeService->generateBarcodePDF($code, $quantity);
    } catch (\Exception $e) {
        Log::error('Failed to generate barcode PDF', [
            'code' => $code,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        abort(500, 'Error generating barcode');
    }
}

public function removeItemsFromGroup($id, Request $request)
{
    try {
        $group = MenuGroup::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'required|integer|exists:menus,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'One or more menu items are invalid',
                'errors' => $validator->errors()
            ], 422);
        }

        $group->items()->whereIn('menu_id', $request->items)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Items removed from menu group successfully'
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to remove items from menu group: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to remove items: ' . $e->getMessage()
        ], 500);
    }
}

public function getAvailableItems($groupId)
{
    try {
        Log::info('Getting available items for group: ' . $groupId);

        $group = MenuGroup::find($groupId);
        if (!$group) {
            Log::warning('Menu group not found: ' . $groupId);
            return response()->json([
                'success' => false,
                'message' => 'Menu group not found'
            ], 404);
        }

        $existingItemIds = MenuGroupItem::where('menugroup_id', $groupId)
            ->pluck('menu_id');
        Log::info('Existing items:', ['count' => $existingItemIds->count()]);

        $query = Menu::with('category')
            ->where('status', 'active')
            ->where('is_archived', '!=', 'true')
            ->whereNotIn('id', $existingItemIds);
        
        Log::info('Executing query: ' . $query->toSql());
        Log::info('Query bindings:', $query->getBindings());

        $availableItems = $query->get();
        Log::info('Found available items:', ['count' => $availableItems->count()]);

        $mappedItems = $availableItems->map(function($item) {
            try {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'category_name' => optional($item->category)->name ?? 'Uncategorized'
                ];
            } catch (\Exception $e) {
                Log::error('Error mapping item:', [
                    'item_id' => $item->id ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        })->filter();

        return response()->json([
            'success' => true,
            'items' => $mappedItems
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to get available items: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to get available items',
            'debug_message' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function getItemsDetails(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'integer|exists:menus,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid items'
            ], 422);
        }

        $items = Menu::with('category')
            ->whereIn('id', $request->items)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => number_format($item->price, 2),
                    'category' => $item->category->name,
                    'image' => $item->image_path ? asset('storage/' . $item->image_path) : asset('images/menu.png'),
                    'isActive' => $item->status === 'active'
                ];
            });

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to get items details: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to get items details'
        ], 500);
    }
}

public function updateMenuGroup($id, Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'deletedItems' => 'array',
            'deletedItems.*' => 'integer|exists:menus,id',
            'addedItems' => 'array',
            'addedItems.*' => 'integer|exists:menus,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        $group = MenuGroup::findOrFail($id);
        $group->update(['name' => $request->name]);

        if (!empty($request->deletedItems)) {
            $group->items()->whereIn('menu_id', $request->deletedItems)->delete();
        }

        if (!empty($request->addedItems)) {
            foreach ($request->addedItems as $menuId) {
                MenuGroupItem::create([
                    'menugroup_id' => $group->id,
                    'menu_id' => $menuId
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Menu group updated successfully'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to update menu group: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update menu group: ' . $e->getMessage()
        ], 500);
    }
}

}
