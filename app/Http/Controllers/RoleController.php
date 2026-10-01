<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\SystemLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $logService;

    public function __construct(SystemLogService $logService)
    {
        $this->logService = $logService;
    }
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Role::create([
                'name' => strtolower(str_replace(' ', '_', $request->name)),
                'display_name' => $request->display_name,
                'description' => $request->description
            ]);

            // Log role creation
            $this->logService->logSettings(
                'create',
                "Created new role: {$role->display_name}",
                null,
                [
                    'role_id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description
                ],
                'info'
            );

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $role
            ]);
        } catch (\Exception $e) {
            Log::error('Role creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            // Prevent updating protected roles
            if (in_array($role->name, ['admin', 'System Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit protected role'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldValues = [
                'display_name' => $role->display_name,
                'description' => $role->description
            ];

            $role->update([
                'display_name' => $request->display_name,
                'description' => $request->description
            ]);

            // Log role update
            $this->logService->logSettings(
                'update',
                "Updated role: {$role->display_name}",
                $oldValues,
                [
                    'display_name' => $role->display_name,
                    'description' => $role->description
                ],
                'info'
            );

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'role' => $role
            ]);
        } catch (\Exception $e) {
            Log::error('Role update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            // Prevent deleting protected roles
            if (in_array($role->name, ['admin', 'System Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete protected role'
                ], 403);
            }

            // Check if role is assigned to any users
            $userCount = DB::table('users')->where('role_id', $id)->count();
            if ($userCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete role. {$userCount} user(s) are assigned to this role."
                ], 400);
            }

            $roleName = $role->display_name;
            $roleData = [
                'role_id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name
            ];

            $role->delete();

            // Log role deletion
            $this->logService->logSettings(
                'delete',
                "Deleted role: {$roleName}",
                $roleData,
                null,
                'warning'
            );

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Role deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting role: ' . $e->getMessage()
            ], 500);
        }
    }
}