<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SystemLogService
{
    /**
     * Log a system event
     */
    public function log(
        string $action,
        string $module,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $targetType = null,
        ?int $targetId = null,
        string $severity = 'info'
    ): SystemLog {
        return SystemLog::create([
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
            'severity' => $severity,
        ]);
    }

    /**
     * Log user authentication
     */
    public function logAuth(string $action, string $description, string $severity = 'info'): SystemLog
    {
        return $this->log(
            action: $action,
            module: 'Authentication',
            description: $description,
            severity: $severity
        );
    }

    /**
     * Log user management actions
     */
    public function logUserManagement(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        string $severity = 'info'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'User Management',
            description: $description,
            oldValues: $oldValues,
            newValues: $newValues,
            targetType: 'User',
            targetId: $userId,
            severity: $severity
        );
    }

    /**
     * Log product/inventory actions
     */
    public function logInventory(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $productId = null,
        string $severity = 'info'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'Inventory',
            description: $description,
            oldValues: $oldValues,
            newValues: $newValues,
            targetType: 'Product',
            targetId: $productId,
            severity: $severity
        );
    }

    /**
     * Log transaction actions
     */
    public function logTransaction(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $transactionId = null,
        string $severity = 'info'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'Transaction',
            description: $description,
            oldValues: $oldValues,
            newValues: $newValues,
            targetType: 'Transaction',
            targetId: $transactionId,
            severity: $severity
        );
    }

    /**
     * Log menu management actions
     */
    public function logMenu(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $menuId = null,
        string $severity = 'info'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'Menu Management',
            description: $description,
            oldValues: $oldValues,
            newValues: $newValues,
            targetType: 'Menu',
            targetId: $menuId,
            severity: $severity
        );
    }

    /**
     * Log scholar management actions
     */
    public function logScholar(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $scholarId = null,
        string $severity = 'info'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'Scholar Management',
            description: $description,
            oldValues: $oldValues,
            newValues: $newValues,
            targetType: 'Scholar',
            targetId: $scholarId,
            severity: $severity
        );
    }

    /**
     * Log system maintenance actions
     */
    public function logMaintenance(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = 'warning'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'System Maintenance',
            description: $description,
            oldValues: $oldValues,
            newValues: $newValues,
            severity: $severity
        );
    }

    /**
     * Log report generation
     */
    public function logReport(
        string $action,
        string $description,
        ?array $metadata = null,
        string $severity = 'info'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'Reports',
            description: $description,
            newValues: $metadata,
            severity: $severity
        );
    }

    /**
     * Log settings changes
     */
    public function logSettings(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = 'warning'
    ): SystemLog {
        return $this->log(
            action: $action,
            module: 'Settings',
            description: $description,
            oldValues: $oldValues,
            newValues: $newValues,
            severity: $severity
        );
    }

    /**
     * Get logs with filters
     */
    public function getLogs(array $filters = [], int $perPage = 50)
    {
        $query = SystemLog::with('user')->orderBy('created_at', 'desc');

        // Apply role-based access
        if (isset($filters['user'])) {
            $query->accessibleByRole($filters['user']);
        }

        // Apply filters
        if (!empty($filters['action'])) {
            $query->byAction($filters['action']);
        }

        if (!empty($filters['module'])) {
            $query->byModule($filters['module']);
        }

        if (!empty($filters['severity'])) {
            $query->bySeverity($filters['severity']);
        }

        if (!empty($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'] . ' 00:00:00',
                $filters['end_date'] . ' 23:59:59'
            ]);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('action', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('module', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get activity summary
     */
    public function getActivitySummary(int $days = 7): array
    {
        $logs = SystemLog::recent($days)->get();

        return [
            'total_activities' => $logs->count(),
            'by_action' => $logs->groupBy('action')->map->count(),
            'by_module' => $logs->groupBy('module')->map->count(),
            'by_severity' => $logs->groupBy('severity')->map->count(),
            'by_user' => $logs->groupBy('user_id')->map->count(),
            'recent_critical' => SystemLog::recent($days)
                ->bySeverity('critical')
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        $date = now()->subDays($daysToKeep);
        
        $count = SystemLog::where('created_at', '<', $date)->count();
        
        SystemLog::where('created_at', '<', $date)->delete();

        $this->logMaintenance(
            'delete',
            "Cleaned {$count} system logs older than {$daysToKeep} days",
            severity: 'info'
        );

        return $count;
    }
}