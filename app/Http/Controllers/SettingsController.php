<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SettingsService;
use App\Services\SystemLogService;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function general(SettingsService $settingsService)
    {
        $settings = $settingsService->getGeneralSettings();
        return view('settings.general', compact('settings'));
    }

    public function updateGeneralSettings(Request $request, SettingsService $settingsService, SystemLogService $logService)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'app_name' => 'required|string|max:255',
                'company_name' => 'nullable|string|max:255',
                'receipt_footer' => 'nullable|string|max:500',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'currency' => 'required|string|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldSettings = $settingsService->getGeneralSettings();
            $settingsService->updateGeneralSettings($request->all());
            $newSettings = $settingsService->getGeneralSettings();

            // Log the settings change
            $logService->logSettings(
                'update',
                'Updated general settings',
                $oldSettings,
                $newSettings,
                'info'
            );

            return response()->json([
                'success' => true,
                'message' => 'General settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('General settings update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating settings.'
            ], 500);
        }
    }

    public function pos(SettingsService $settingsService)
    {
        // Check if user has POS access permission
        $user = Auth::user();
        $hasAccess = method_exists($user, 'hasPermission') && $user->hasPermission('access_pos');
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized. You need POS access permission.');
        }

        $settings = $settingsService->getPOSSettings();
        return view('settings.pos', compact('settings'));
    }

    public function updatePOSSettings(Request $request, SettingsService $settingsService, SystemLogService $logService)
    {
        try {
            $oldSettings = $settingsService->getPOSSettings();
            $settingsService->updatePOSSettings($request->all());
            $newSettings = $settingsService->getPOSSettings();

            // Log the settings change
            $changes = [];
            if (isset($request->departments)) {
                $changes[] = 'departments';
            }
            if (isset($request->discount_types)) {
                $changes[] = 'discount types';
            }
            if (isset($request->credit_user_types)) {
                $changes[] = 'credit user types';
            }
            
            $changesText = empty($changes) ? 'POS settings' : implode(', ', $changes);
            $logService->logSettings(
                'update',
                "Updated POS settings: {$changesText}",
                $oldSettings,
                $newSettings,
                'info'
            );

            return response()->json([
                'success' => true,
                'message' => 'POS settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('POS settings update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating settings.'
            ], 500);
        }
    }

    public function roles()
    {
        // Check if user has role_management permission
        $user = Auth::user();
        $hasPermission = method_exists($user, 'hasPermission') && $user->hasPermission('role_management');
        
        // Fallback to admin role check for backward compatibility
        if (!$hasPermission) {
            $roleName = $user->role->name ?? null;
            $displayName = $user->role->display_name ?? null;
            $isAdmin = in_array($roleName, ['admin'], true) || in_array($displayName, ['System Admin'], true);
            
            if (!$isAdmin) {
                abort(403, 'Unauthorized. You need role management permission to access this page.');
            }
        }

        $roles = \App\Models\Role::all();
        $permissions = \App\Models\Permission::all();
        return view('settings.roles', compact('roles', 'permissions'));
    }

    public function scholars(SettingsService $settingsService)
    {
        // Check if user has canteen manager role or appropriate permissions
        if (!$this->hasCanteenManagerAccess()) {
            abort(403, 'Unauthorized. Only canteen managers can access scholar settings.');
        }

        $settings = $settingsService->getScholarSettings();
        return view('settings.scholars', compact('settings'));
    }

    public function updateScholarSettings(Request $request, SettingsService $settingsService, SystemLogService $logService)
    {
        try {
            if (!$this->hasCanteenManagerAccess()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only managers can update scholar settings.'
                ], 403);
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'default_allowance' => 'required|numeric|min:0|max:9999.99',
                'auto_grant_allowance' => 'required|boolean',
                'auto_reset' => 'required|boolean',
                'reset_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            ], [
                'default_allowance.required' => 'Default allowance is required.',
                'default_allowance.numeric' => 'Default allowance must be a number.',
                'default_allowance.min' => 'Default allowance cannot be negative.',
                'default_allowance.max' => 'Default allowance cannot exceed ₱9,999.99.',
                'reset_time.required' => 'Reset time is required.',
                'reset_time.regex' => 'Reset time must be in HH:MM format (24-hour).'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldSettings = $settingsService->getScholarSettings();
            
            $settingsService->updateScholarSettings([
                'default_allowance' => (float)$request->default_allowance,
                'auto_grant_allowance' => (bool)$request->auto_grant_allowance,
                'auto_reset' => (bool)$request->auto_reset,
                'reset_time' => $request->reset_time,
            ]);

            $newSettings = $settingsService->getScholarSettings();
            
            // Log the settings change
            $logService->logSettings(
                'update',
                'Updated scholar settings',
                $oldSettings,
                $newSettings,
                'warning'
            );

            return response()->json([
                'success' => true,
                'message' => 'Scholar settings updated successfully! Changes will take effect on the next renewal cycle.',
                'settings' => $settingsService->getScholarSettings()
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid settings: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Scholar settings update error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating settings. Please try again.'
            ], 500);
        }
    }

    private function hasCanteenManagerAccess()
    {
        $user = Auth::user();
        
        // Allow via role name or display_name
        $roleName = $user->role->name ?? null;
        $displayName = $user->role->display_name ?? null;
        $hasRoleAccess = in_array($roleName, ['admin', 'manager', 'canteen_manager'], true)
            || in_array($displayName, ['System Admin', 'Manager'], true);

        // Allow via permission method if available
        $hasPermAccess = method_exists($user, 'hasPermission')
            ? ($user->hasPermission('manage_scholars') || $user->hasPermission('manage_users'))
            : false;

        return $hasRoleAccess || $hasPermAccess;
    }
}
