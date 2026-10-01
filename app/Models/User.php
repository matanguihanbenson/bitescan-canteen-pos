<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'firstname',
        'middlename',
        'lastname',
        'email',
        'password',
        'role_id',
        'status',
        'last_login',
        'login_count',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array<string>
     */
    protected $dates = [
        'last_login',
        'created_at',
        'updated_at'
    ];

    /**
     * Define default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'active',
        'login_count' => 0,
    ];

    /**
     * Get the role that the user belongs to.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the permissions that belong to the user.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withTimestamps();
    }

    /**
     * Get all permissions for the user (role + direct permissions).
     *
     * @return array
     */
    public function getAllPermissions()
    {
        // Use relation method to avoid collision with Role::$permissions attribute cast
        $rolePermissions = $this->role ?
            $this->role->permissions()->pluck('display_name')->toArray() :
            [];

        $userPermissions = $this->permissions ? $this->permissions->pluck('display_name')->toArray() : [];

        return array_values(array_unique(array_merge($rolePermissions, $userPermissions)));
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission($permissionName)
    {
        // Check direct user permissions
        if ($this->permissions && $this->permissions->contains('name', $permissionName)) {
            return true;
        }

        // Check role permissions via relation (avoid attribute collision)
        if ($this->role && $this->role->permissions()->where('name', $permissionName)->exists()) {
            return true;
        }

        if ($this->role && is_array($this->role->permissions)) {
            return in_array($permissionName, $this->role->permissions);
        }

        return false;
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim(($this->firstname ?? '') . ' ' . ($this->lastname ?? ''));
    }

    /**
     * Get the user's display name.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        $fullName = $this->getFullNameAttribute();
        return !empty($fullName) ? $fullName : $this->email;
    }
}
