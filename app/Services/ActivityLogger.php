<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string $action,
        string $module,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $targetType = null,
        ?int $targetId = null,
        string $severity = 'info'
    ) {
        try {
            SystemLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'severity' => $severity
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    public static function login($username)
    {
        self::log(
            action: 'login',
            module: 'Authentication',
            description: "User '{$username}' logged in",
            severity: 'success'
        );
    }

    public static function logout($username)
    {
        self::log(
            action: 'logout',
            module: 'Authentication',
            description: "User '{$username}' logged out",
            severity: 'info'
        );
    }

    public static function createUser($userData)
    {
        self::log(
            action: 'create',
            module: 'User Management',
            description: "Created new user: {$userData['username']} ({$userData['firstname']} {$userData['lastname']})",
            newValues: $userData,
            targetType: 'User',
            targetId: $userData['id'] ?? null,
            severity: 'success'
        );
    }

    public static function updateUser($userId, $oldData, $newData, $username)
    {
        $changes = self::getChangedFields($oldData, $newData);
        
        self::log(
            action: 'update',
            module: 'User Management',
            description: "Updated user: {$username}. Changes: " . implode(', ', array_keys($changes)),
            oldValues: $oldData,
            newValues: $newData,
            targetType: 'User',
            targetId: $userId,
            severity: 'info'
        );
    }

    public static function deleteUser($userId, $username)
    {
        self::log(
            action: 'delete',
            module: 'User Management',
            description: "Deleted user: {$username}",
            targetType: 'User',
            targetId: $userId,
            severity: 'warning'
        );
    }

    public static function changeUserStatus($userId, $username, $oldStatus, $newStatus)
    {
        self::log(
            action: 'update',
            module: 'User Management',
            description: "Changed status for user '{$username}' from '{$oldStatus}' to '{$newStatus}'",
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus],
            targetType: 'User',
            targetId: $userId,
            severity: $newStatus === 'inactive' ? 'warning' : 'success'
        );
    }

    public static function createRole($roleData)
    {
        self::log(
            action: 'create',
            module: 'Role Management',
            description: "Created new role: {$roleData['name']}",
            newValues: $roleData,
            targetType: 'Role',
            targetId: $roleData['id'] ?? null,
            severity: 'success'
        );
    }

    public static function updateRole($roleId, $oldData, $newData, $roleName)
    {
        $changes = self::getChangedFields($oldData, $newData);
        
        self::log(
            action: 'update',
            module: 'Role Management',
            description: "Updated role: {$roleName}. Changes: " . implode(', ', array_keys($changes)),
            oldValues: $oldData,
            newValues: $newData,
            targetType: 'Role',
            targetId: $roleId,
            severity: 'warning'
        );
    }

    public static function deleteRole($roleId, $roleName)
    {
        self::log(
            action: 'delete',
            module: 'Role Management',
            description: "Deleted role: {$roleName}",
            targetType: 'Role',
            targetId: $roleId,
            severity: 'warning'
        );
    }

    public static function updatePermissions($roleId, $roleName, $oldPermissions, $newPermissions)
    {
        $added = array_diff($newPermissions, $oldPermissions);
        $removed = array_diff($oldPermissions, $newPermissions);
        
        $description = "Updated permissions for role: {$roleName}.";
        if (!empty($added)) {
            $description .= " Added: " . implode(', ', $added) . ".";
        }
        if (!empty($removed)) {
            $description .= " Removed: " . implode(', ', $removed) . ".";
        }
        
        self::log(
            action: 'update',
            module: 'Role Management',
            description: $description,
            oldValues: ['permissions' => $oldPermissions],
            newValues: ['permissions' => $newPermissions],
            targetType: 'Role',
            targetId: $roleId,
            severity: 'critical'
        );
    }

    public static function passwordChange($userId, $username, $isSelfChange = false)
    {
        $description = $isSelfChange
            ? "User '{$username}' changed their own password"
            : "Password changed for user '{$username}'";
        
        self::log(
            action: 'update',
            module: 'Security',
            description: $description,
            targetType: 'User',
            targetId: $userId,
            severity: 'warning'
        );
    }

    public static function passwordReset($userId, $username)
    {
        self::log(
            action: 'update',
            module: 'Security',
            description: "Password reset requested/completed for user: {$username}",
            targetType: 'User',
            targetId: $userId,
            severity: 'warning'
        );
    }

    public static function updateSettings($settingsType, $oldValues, $newValues)
    {
        $changes = self::getChangedFields($oldValues, $newValues);
        
        self::log(
            action: 'update',
            module: 'Settings',
            description: "Updated {$settingsType} settings. Changes: " . implode(', ', array_keys($changes)),
            oldValues: $oldValues,
            newValues: $newValues,
            severity: 'warning'
        );
    }

    public static function maintenanceMode($enabled)
    {
        self::log(
            action: 'update',
            module: 'System',
            description: $enabled ? 'Enabled maintenance mode' : 'Disabled maintenance mode',
            newValues: ['maintenance_mode' => $enabled],
            severity: 'critical'
        );
    }

    public static function exportData($module, $format)
    {
        self::log(
            action: 'export',
            module: $module,
            description: "Exported {$module} data in {$format} format",
            severity: 'info'
        );
    }

    public static function createScholar($scholarData)
    {
        self::log(
            action: 'create',
            module: 'Scholar Management',
            description: "Created new scholar: {$scholarData['student_id']} ({$scholarData['first_name']} {$scholarData['last_name']})",
            newValues: $scholarData,
            targetType: 'Scholar',
            targetId: $scholarData['id'] ?? null,
            severity: 'success'
        );
    }

    public static function updateScholar($scholarId, $oldData, $newData, $scholarName)
    {
        $changes = self::getChangedFields($oldData, $newData);
        
        self::log(
            action: 'update',
            module: 'Scholar Management',
            description: "Updated scholar: {$scholarName}. Changes: " . implode(', ', array_keys($changes)),
            oldValues: $oldData,
            newValues: $newData,
            targetType: 'Scholar',
            targetId: $scholarId,
            severity: 'info'
        );
    }

    public static function failedLoginAttempt($username, $reason)
    {
        self::log(
            action: 'login',
            module: 'Security',
            description: "Failed login attempt for username: {$username}. Reason: {$reason}",
            severity: 'error'
        );
    }

    public static function unauthorizedAccess($resource)
    {
        self::log(
            action: 'view',
            module: 'Security',
            description: "Unauthorized access attempt to: {$resource}",
            severity: 'error'
        );
    }

    private static function getChangedFields($old, $new)
    {
        $changes = [];
        
        foreach ($new as $key => $value) {
            if (!isset($old[$key]) || $old[$key] !== $value) {
                if ($key !== 'password' && $key !== 'remember_token' && $key !== 'updated_at') {
                    $changes[$key] = [
                        'old' => $old[$key] ?? null,
                        'new' => $value
                    ];
                }
            }
        }
        
        return $changes;
    }
}