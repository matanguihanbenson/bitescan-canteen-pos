<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        // Use the explicit pivot table to match the existing schema
        return $this->belongsToMany(Permission::class, 'role_permissions')->withTimestamps();
    }

    public function hasPermission($permission)
    {
        // Check if using permissions relationship
        if ($this->permissions && $this->permissions->contains('name', $permission)) {
            return true;
        }

        // Check if using permissions array (backward compatibility)
        if (is_array($this->permissions)) {
            return in_array($permission, $this->permissions);
        }

        return false;
    }
}
