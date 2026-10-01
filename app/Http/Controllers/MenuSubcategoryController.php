<?php

namespace App\Http\Controllers;

use App\Models\MenuSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuSubcategoryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'menucategory_id' => 'required|exists:menu_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subcategory = MenuSubcategory::create([
                'name' => $request->name,
                'description' => $request->description,
                'menucategory_id' => $request->menucategory_id,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory created successfully',
                'subcategory' => $subcategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating subcategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $subcategory = MenuSubcategory::findOrFail($id);
            $subcategory->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Subcategory deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting subcategory'
            ], 500);
        }
    }
    public function getByCategory($categoryId)
    {
        try {
            $subcategories = MenuSubcategory::where('menucategory_id', $categoryId)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'subcategories' => $subcategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subcategories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'menucategory_id' => 'required|exists:menu_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subcategory = MenuSubcategory::findOrFail($id);
            $subcategory->update([
                'name' => $request->name,
                'description' => $request->description,
                'menucategory_id' => $request->menucategory_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory updated successfully',
                'subcategory' => $subcategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating subcategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
