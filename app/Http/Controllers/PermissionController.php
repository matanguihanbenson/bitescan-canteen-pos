<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Services\SystemLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    protected $logService;

    public function __construct(SystemLogService $logService)
    {
        $this->logService = $logService;
    }
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('user-management.permissions', compact('roles', 'permissions'))
            ->with('active', 'permissions'); 
    }

    public function getData(Request $request)
    {
        try {
            Log::info('PermissionController::getData - Starting DataTables request');
            
            $users = User::with(['role'])->select([
                'users.id',
                'users.username',
                'users.firstname',
                'users.lastname',
                'users.middlename',
                'users.role_id',
                'users.image',
                'users.status'
            ]);

            if ($request->role_id) {
                $users->where('role_id', $request->role_id);
            }

            if ($request->search_value) {
                $searchTerm = $request->search_value;
                $users->where(function($query) use ($searchTerm) {
                    $query->where(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$searchTerm}%")
                        ->orWhere('username', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            }

            Log::info('PermissionController::getData - Query built, total users: ' . $users->count());

            return DataTables::of($users)
                ->addColumn('user_info', function ($user) {
                    $name = trim($user->firstname . ' ' . $user->lastname);
                    $statusClass = $user->status === 'active' ? 'success' : 'danger';
                    
                    return '<div class="d-flex align-items-center">
                        <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="bi bi-person text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-medium">' . e($name) . '</div>
                            <div class="text-muted small">' . e($user->username) . '</div>
                            <div><span class="badge bg-' . $statusClass . ' badge-sm">' . ucfirst($user->status) . '</span></div>
                        </div>
                    </div>';
                })
                ->addColumn('role_info', function ($user) {
                    $roleName = $user->role ? ($user->role->display_name ?? $user->role->name) : 'No Role';
                    $badgeClass = $user->role ? 'primary' : 'secondary';
                    return '<span class="badge bg-' . $badgeClass . '">' . e($roleName) . '</span>';
                })
                ->addColumn('permissions_info', function ($user) {
                    if ($user->role) {
                        $permissions = $this->getDefaultRolePermissions($user->role->name);
                        $permissionBadges = [];
                        foreach (array_slice($permissions, 0, 3) as $permission) {
                            $permissionBadges[] = '<span class="badge bg-info badge-sm me-1">' . e($permission) . '</span>';
                        }
                        if (count($permissions) > 3) {
                            $permissionBadges[] = '<span class="badge bg-light text-dark badge-sm">+' . (count($permissions) - 3) . ' more</span>';
                        }
                        return implode('', $permissionBadges);
                    }
                    return '<span class="text-muted">No permissions</span>';
                })
                ->addColumn('action', function ($user) {
                    if ($user->role && in_array($user->role->name, ['admin', 'System Admin'])) {
                        return '<button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="bi bi-shield-lock"></i> Protected
                        </button>';
                    }
                    
                    return '<button class="btn btn-sm btn-primary edit-user-permissions" data-user-id="' . $user->id . '" data-user-name="' . e($user->firstname . ' ' . $user->lastname) . '">
                        <i class="bi bi-pencil"></i> Edit
                    </button>';
                })
                ->rawColumns(['user_info', 'role_info', 'permissions_info', 'action'])
                ->make(true);
                
        } catch (\Exception $e) {
            Log::error('PermissionController::getData - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }
    
    private function getDefaultRolePermissions($roleName)
    {
        // Define role-based permissions
        $rolePermissions = [
            'admin' => ['manage_users', 'view_dashboard', 'maintenance', 'view_system_logs', 'role_management'],
            'System Admin' => ['manage_users', 'view_dashboard', 'maintenance', 'view_system_logs', 'role_management'],
            'manager' => ['view_dashboard', 'access_pos', 'manage_inventory', 'manage_menu', 'view_sales', 'manage_scholars', 'view_scholar_reports'],
            'cashier' => ['view_dashboard', 'access_pos', 'view_sales'],
            'staff' => ['view_dashboard', 'access_pos']
        ];
        
        return $rolePermissions[$roleName] ?? [];
    }

    public function getUserPermissions($userId)
    {
        try {
            Log::info('Getting permissions for user ID: ' . $userId);
            
            // Eager-load relations but ensure we reference the relation via method to avoid attribute name collisions
            $user = User::with(['permissions', 'role.permissions'])->findOrFail($userId);
            
            Log::info('User found:', ['name' => $user->firstname, 'role_id' => $user->role_id]);
            
            $userPermissions = [];
            if ($user->permissions) {
                $userPermissions = $user->permissions->pluck('id')->toArray();
                Log::info('User permissions:', $userPermissions);
            }
            
            $rolePermissions = [];
            if ($user->role) {
                Log::info('User has role:', ['role_name' => $user->role->name, 'role_id' => $user->role->id]);
                
                // Try to get role permissions from role_permissions table
                if ($user->role->permissions) {
                    $rolePermissions = $user->role->permissions->pluck('id')->toArray();
                    Log::info('Role permissions from role_permissions table:', $rolePermissions);
                } else {
                    Log::warning('No permissions relation found for role');
                }
                
                // Check if role_permissions table has data
                $rolePermCount = DB::table('role_permissions')
                    ->where('role_id', $user->role->id)
                    ->count();
                Log::info('Role permissions count in DB:', ['count' => $rolePermCount]);
            } else {
                Log::warning('User has no role');
            }
            
            Log::info('Returning permissions:', [
                'userPermissions' => $userPermissions,
                'rolePermissions' => $rolePermissions
            ]);
            
            return response()->json([
                'userPermissions' => $userPermissions,
                'rolePermissions' => $rolePermissions
            ]);
        } catch (\Exception $e) {
            Log::error('Get user permissions error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'userPermissions' => [],
                'rolePermissions' => []
            ]);
        }
    }

    public function getRolePermissions($roleId)
    {
        try {
            $role = Role::with('permissions')->findOrFail($roleId);
            $permissions = $role->permissions ? $role->permissions->pluck('id')->toArray() : [];
            
            return response()->json([
                'success' => true,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            Log::error('Get role permissions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'permissions' => []
            ]);
        }
    }

    public function saveRolePermissions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $role = Role::findOrFail($request->role_id);
            
            // Protect admin role from permission changes
            if (in_array(strtolower($role->name), ['admin', 'system admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot modify admin role permissions. Admin role permissions are protected and cannot be changed."
                ], 403);
            }
            
            // Get old permissions before syncing
            $oldPermissions = $role->permissions ? $role->permissions->pluck('name')->toArray() : [];
            
            // Sync permissions to role
            $role->permissions()->sync($request->permissions ?? []);
            
            // Get new permissions after syncing
            $role->load('permissions');
            $newPermissions = $role->permissions ? $role->permissions->pluck('name')->toArray() : [];

            // Log permission changes
            $this->logService->logSettings(
                'update',
                "Updated permissions for role: {$role->display_name}",
                ['permissions' => $oldPermissions],
                ['permissions' => $newPermissions],
                'warning'
            );

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role permission update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating role permissions'
            ], 500);
        }
    }

    public function savePermissions(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'permissions' => 'array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Do not allow user to edit their own permissions
            if (auth()->id() === (int) $request->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => "You cannot edit your own permissions."
                ], 403);
            }

            DB::beginTransaction();

            $user = User::with('role.permissions')->findOrFail($request->user_id);
            
            // Do not allow editing permissions of admin users (their role permissions are immutable)
            if ($user->role && in_array(strtolower($user->role->name), ['admin', 'system admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot modify permissions for admin users. Admin role permissions are fixed and cannot be changed."
                ], 403);
            }

            // Get old permissions before syncing
            $oldPermissions = $user->permissions ? $user->permissions->pluck('name')->toArray() : [];

            // Role permissions are default/immutable at user level; ensure they are not modified via user_permissions
            $rolePermissionIds = [];
            if ($user->role && $user->role->permissions) {
                $rolePermissionIds = $user->role->permissions->pluck('id')->toArray();
            }
            $requestedIds = collect($request->permissions ?? [])->map(fn($id) => (int) $id)->unique();

            // Only user-assigned permissions (exclude any role-permission ids)
            $userAssignable = $requestedIds->reject(function ($id) use ($rolePermissionIds) {
                return in_array($id, $rolePermissionIds, true);
            })->values()->all();

            // Sync only user-level permissions
            $user->permissions()->sync($userAssignable);

            // Get new permissions after syncing
            $user->load('permissions');
            $newPermissions = $user->permissions ? $user->permissions->pluck('name')->toArray() : [];

            // Log permission changes
            $this->logService->logUserManagement(
                'update',
                "Updated permissions for user: {$user->firstname} {$user->lastname}",
                ['permissions' => $oldPermissions],
                ['permissions' => $newPermissions],
                $user->id,
                'info'
            );

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Permission update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating permissions'
            ], 500);
        }
    }
}
