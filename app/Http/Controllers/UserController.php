<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\LoginLog;
use App\Services\SystemLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\DataTables\UserDataTable;
use Illuminate\Foundation\Validation\ValidatesRequests;

class UserController extends Controller
{
    use ValidatesRequests;
    
    protected $logService;

    public function __construct(SystemLogService $logService)
    {
        $this->logService = $logService;
    }
    
    const SYSTEM_ADMIN = 'System Admin';
    const SYSTEM_ADMIN_ROLE_ID = 1;

    public function index(UserDataTable $dataTable)
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_roles' => Role::count(),
            'unique_logins_today' => User::whereDate('last_login', today())->count(),
            'total_logins_today' => LoginLog::whereDate('login_at', today())->count()
        ];

        return $dataTable->render('user-management.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        try {
            Log::info('UserController::getData - Starting DataTables request');
            
            $users = User::with('role')
                ->select([
                    'users.id',
                    'users.username',
                    'users.firstname',
                    'users.lastname',
                    'users.middlename',
                    'users.email',
                    'users.role_id',
                    'users.status',
                    'users.created_at',
                    'users.last_login',
                    'users.image'
                ]);

            Log::info('UserController::getData - Query built, total users: ' . $users->count());

            return DataTables::of($users)
                ->addColumn('user_details', function ($user) {
                    $name = trim("{$user->firstname} {$user->middlename} {$user->lastname}");
                    return '<div class="d-flex align-items-center">
                        <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="bi bi-person text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-medium">' . e($name) . '</div>
                            <div class="text-muted small">' . e($user->email) . '</div>
                        </div>
                    </div>';
                })
                ->addColumn('role_badge', function ($user) {
                    $roleName = $user->role ? ($user->role->display_name ?? $user->role->name) : 'No Role';
                    return '<span class="badge bg-primary">' . e($roleName) . '</span>';
                })
                ->addColumn('status_badge', function ($user) {
                    $statusClass = strtolower($user->status) === 'active' ? 'success' : 'danger';
                    return '<span class="badge bg-' . $statusClass . '">' . ucfirst($user->status) . '</span>';
                })
                ->addColumn('last_login_formatted', function ($user) {
                    return $user->last_login
                        ? $user->last_login->format('M d, Y')
                        : '<span class="text-muted">Never</span>';
                })
                ->addColumn('action', function ($user) {
                    if ($user->role && in_array($user->role->name, ['admin', 'System Admin'])) {
                        return '<div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                <i class="bi bi-shield-lock"></i> Protected
                            </button>
                        </div>';
                    }

                    $statusBtn = strtolower($user->status) === 'active'
                        ? '<button class="btn btn-sm btn-warning toggle-status" data-id="' . $user->id . '" data-status="active" title="Deactivate">
                            <i class="bi bi-pause-circle"></i>
                        </button>'
                        : '<button class="btn btn-sm btn-success toggle-status" data-id="' . $user->id . '" data-status="inactive" title="Activate">
                            <i class="bi bi-play-circle"></i>
                        </button>';
                    
                    return '<div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary edit-user" data-id="' . $user->id . '" title="Edit User">
                            <i class="bi bi-pencil"></i>
                        </button>
                        ' . $statusBtn . '
                        <button class="btn btn-sm btn-danger delete-user" data-id="' . $user->id . '" title="Delete User">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['user_details', 'role_badge', 'status_badge', 'last_login_formatted', 'action'])
                ->make(true);
                
        } catch (\Exception $e) {
            Log::error('UserController::getData - Error: ' . $e->getMessage(), [
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

    public function getAllUsers()
    {
        try {
            Log::info('UserController::getAllUsers - Starting request');

            // Fetch all users with their roles
            $users = User::with('role')
                ->select('id', 'firstname', 'lastname', 'email', 'status', 'role_id', 'last_login', 'created_at', 'updated_at')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'firstname' => $user->firstname ?? '',
                        'lastname' => $user->lastname ?? '',
                        'email' => $user->email ?? '',
                        'status' => $user->status ?? 'active',
                        'role_id' => $user->role_id,
                        'role_name' => $user->role ? $user->role->name : 'User',
                        'last_login' => $user->last_login,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ];
                })
                ->values()
                ->all();

            Log::info('UserController::getAllUsers - Found users', ['count' => count($users)]);

            return response()->json([
                'success' => true,
                'users' => $users,
                'total' => count($users)
            ]);
        } catch (\Exception $e) {
            Log::error('UserController::getAllUsers - Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching users: ' . $e->getMessage(),
                'users' => [],
                'debug' => [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            ], 500);
        }
    }

    public function getRoles()
    {
        try {
            Log::info('UserController::getRoles - Starting request');

            // Create roles table if it doesn't exist
            if (!DB::getSchemaBuilder()->hasTable('roles')) {
                Log::info('Creating roles table...');
                DB::statement('CREATE TABLE IF NOT EXISTS roles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    display_name VARCHAR(255),
                    description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )');
            }

            $roles = DB::table('roles')->select('id', 'name', 'display_name')->get();

            if ($roles->isEmpty()) {
                Log::info('No roles found, creating defaults...');
                $defaultRoles = [
                    ['name' => 'admin', 'display_name' => 'System Admin'],
                    ['name' => 'manager', 'display_name' => 'Manager'],
                    ['name' => 'cashier', 'display_name' => 'Cashier'],
                    ['name' => 'staff', 'display_name' => 'Staff']
                ];

                foreach ($defaultRoles as $roleData) {
                    DB::table('roles')->insert($roleData);
                }

                $roles = DB::table('roles')->select('id', 'name', 'display_name')->get();
            }

            $rolesArray = $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->display_name ?? $role->name
                ];
            })->toArray();

            Log::info('UserController::getRoles - Found roles', ['count' => count($rolesArray)]);

            return response()->json($rolesArray);
        } catch (\Exception $e) {
            Log::error('UserController::getRoles - Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return fallback roles even on error
            return response()->json([
                ['id' => 1, 'name' => 'System Admin'],
                ['id' => 2, 'name' => 'Manager'],
                ['id' => 3, 'name' => 'Cashier'],
                ['id' => 4, 'name' => 'Staff']
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'role_id' => 'required|exists:roles,id',
                'password' => 'required|string|min:6'
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validated->errors()
                ], 422);
            }

            $validatedData = $validated->validated();
            
            // Auto-generate username from email (part before @)
            $username = explode('@', $validatedData['email'])[0];
            
            // Check if username already exists and make it unique
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            
            $user = User::create([
                'username' => $username,
                'firstname' => $validatedData['firstname'],
                'lastname' => $validatedData['lastname'],
                'email' => $validatedData['email'],
                'role_id' => $validatedData['role_id'],
                'password' => Hash::make($validatedData['password']),
                'status' => 'active'
            ]);

            // Log user creation
            $user->load('role');
            $this->logService->logUserManagement(
                'create',
                "Created new user: {$user->firstname} {$user->lastname} ({$user->username})",
                null,
                [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role ? $user->role->display_name : 'N/A'
                ],
                $user->id,
                'success'
            );

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('UserController::store - Error', [
                'message' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);

            // Normalise image path for frontend and provide absolute URL
            $img = $user->image ? str_replace('\\', '/', $user->image) : null;
            $imgUrl = $img ? asset('storage/' . $img) : null;

            return response()->json([
                'id' => $user->id,
                'firstname' => $user->firstname,
                'middlename' => $user->middlename,
                'lastname' => $user->lastname,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'role_id' => $user->role_id,
                'status' => $user->status,
                'image' => $img,
                'image_url' => $imgUrl,
                'role_name' => $user->role ? $user->role->name : 'User'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('UserController::update - Starting update', [
                'user_id' => $id,
                'request_data' => $request->except(['password', 'image']),
                'has_image' => $request->hasFile('image'),
                'has_role_id' => $request->has('role_id')
            ]);
            
            $user = User::findOrFail($id);

            // Determine context: profile update (no role_id) vs admin update (with role_id)
            $isProfileUpdate = !$request->has('role_id');

            $rules = [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6'
            ];

            if ($isProfileUpdate) {
                // Profile modal fields
                $rules['username'] = 'nullable|string|max:255|unique:users,username,' . $id;
                $rules['middlename'] = 'nullable|string|max:255';
                $rules['image'] = 'nullable|image|max:2048';
            } else {
                // Admin update fields
                $rules['role_id'] = 'nullable|exists:roles,id';
            }

            Log::info('UserController::update - Validation rules', ['rules' => $rules, 'is_profile' => $isProfileUpdate]);
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $validated = $validator->validated();
            Log::info('UserController::update - Validation passed', ['validated' => array_keys($validated)]);

            $updateData = [
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
            ];
            
            // Auto-generate username from email if email changed and username not provided
            if ($user->email !== $validated['email'] && !array_key_exists('username', $validated)) {
                $username = explode('@', $validated['email'])[0];
                
                // Check if username already exists and make it unique
                $baseUsername = $username;
                $counter = 1;
                while (User::where('username', $username)->where('id', '!=', $id)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }
                
                $updateData['username'] = $username;
            }

            if (array_key_exists('middlename', $validated)) {
                $updateData['middlename'] = $validated['middlename'];
            }
            if (array_key_exists('username', $validated)) {
                $updateData['username'] = $validated['username'];
            }
            if (array_key_exists('role_id', $validated)) {
                $updateData['role_id'] = $validated['role_id'];
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('image')) {
                Log::info('UserController::update - Processing image upload');
                $path = $request->file('image')->store('profiles', 'public');
                // Normalise path separators for web URL
                $path = str_replace('\\', '/', $path);
                $updateData['image'] = $path;
                Log::info('UserController::update - Image stored', ['path' => $path]);
            }

            Log::info('UserController::update - About to update user', ['update_data' => array_keys($updateData)]);
            
            // Capture old values for logging
            $oldValues = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'role_id' => $user->role_id
            ];
            
            $user->update($updateData);
            Log::info('UserController::update - User updated successfully');

            // Log user update
            $user->load('role');
            $newValues = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'role' => $user->role ? $user->role->display_name : 'N/A'
            ];
            
            $this->logService->logUserManagement(
                'update',
                "Updated user: {$user->firstname} {$user->lastname} ({$user->username})",
                $oldValues,
                $newValues,
                $user->id,
                'info'
            );

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('UserController::update - Error', [
                'message' => $e->getMessage(),
                'user_id' => $id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::with('role')->findOrFail($id);
            
            $userData = [
                'user_id' => $user->id,
                'username' => $user->username,
                'name' => $user->firstname . ' ' . $user->lastname,
                'email' => $user->email,
                'role' => $user->role ? $user->role->display_name : 'N/A'
            ];
            
            $userName = $user->firstname . ' ' . $user->lastname;
            $user->delete();

            // Log user deletion
            $this->logService->logUserManagement(
                'delete',
                "Deleted user: {$userName} ({$userData['username']})",
                $userData,
                null,
                null,
                'critical'
            );

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $oldStatus = $user->status;
            $newStatus = strtolower($user->status) === 'active' ? 'deactivated' : 'active';
            
            $user->status = $newStatus;
            $user->save();

            // Log status change
            $action = $newStatus === 'active' ? 'activated' : 'deactivated';
            $this->logService->logUserManagement(
                'update',
                ucfirst($action) . " user: {$user->firstname} {$user->lastname} ({$user->username})",
                ['status' => $oldStatus],
                ['status' => $newStatus],
                $user->id,
                'warning'
            );

            return response()->json(['message' => 'User status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating user status'], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'action' => 'required|in:activate,deactivate,delete'
        ]);

        try {
            switch ($request->action) {
                case 'activate':
                    User::whereIn('id', $request->ids)->update(['status' => 'active']);
                    break;
                case 'deactivate':
                    User::whereIn('id', $request->ids)->update(['status' => 'deactivated']);
                    break;
                case 'delete':
                    User::whereIn('id', $request->ids)->delete();
                    break;
            }
            return response()->json(['message' => 'Bulk action completed successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error performing bulk action'], 500);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();

            // Log password change
            $this->logService->logUserManagement(
                'update',
                "Changed password for user: {$user->firstname} {$user->lastname} ({$user->username})",
                null,
                ['action' => 'password_changed'],
                $user->id,
                'warning'
            );

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Password update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating password'
            ], 500);
        }
    }

    public function permissions()
    {
        // Get actual roles from database
        $roles = Role::all();

        // Sample permissions - replace with actual permissions from database
        $permissions = [
            (object) ['id' => 1, 'name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
            (object) ['id' => 2, 'name' => 'access_pos', 'display_name' => 'Access POS'],
            (object) ['id' => 3, 'name' => 'manage_inventory', 'display_name' => 'Manage Inventory'],
            (object) ['id' => 4, 'name' => 'manage_menu', 'display_name' => 'Manage Menu'],
            (object) ['id' => 5, 'name' => 'view_sales', 'display_name' => 'View Sales'],
            (object) ['id' => 6, 'name' => 'manage_users', 'display_name' => 'Manage Users'],
            (object) ['id' => 7, 'name' => 'manage_scholars', 'display_name' => 'Manage Scholars'],
            (object) ['id' => 8, 'name' => 'view_scholar_reports', 'display_name' => 'View Scholar Reports'],
            (object) ['id' => 9, 'name' => 'maintenance', 'display_name' => 'System Maintenance']
        ];

        return view('user-management.permissions', compact('roles', 'permissions'));
    }
}