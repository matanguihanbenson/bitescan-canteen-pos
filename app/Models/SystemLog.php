<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'target_type',
        'target_id',
        'severity'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeAccessibleByRole($query, $user)
    {
        if (!$user || !$user->role) {
            return $query->where('id', 0);
        }

        $roleName = strtolower($user->role->name);

        switch ($roleName) {
            case 'admin':
                return $query;
            
            case 'manager':
                $subordinateIds = User::whereHas('role', function($q) {
                    $q->where('name', 'Cashier');
                })->pluck('id')->toArray();
                
                $subordinateIds[] = $user->id;
                
                return $query->whereIn('user_id', $subordinateIds);
            
            case 'staff':
            case 'cashier':
                return $query->where('user_id', $user->id);
            
            default:
                return $query->where('user_id', $user->id);
        }
    }

    public function getSeverityBadgeAttribute()
    {
        $badges = [
            'info' => 'bg-info',
            'success' => 'bg-success',
            'warning' => 'bg-warning',
            'error' => 'bg-danger',
            'critical' => 'bg-danger'
        ];

        return $badges[$this->severity] ?? 'bg-secondary';
    }

    public function getActionBadgeAttribute()
    {
        $badges = [
            'login' => 'bg-success',
            'logout' => 'bg-secondary',
            'create' => 'bg-primary',
            'update' => 'bg-info',
            'delete' => 'bg-danger',
            'restore' => 'bg-success',
            'view' => 'bg-light text-dark',
            'export' => 'bg-warning',
            'import' => 'bg-info'
        ];

        return $badges[$this->action] ?? 'bg-secondary';
    }
}