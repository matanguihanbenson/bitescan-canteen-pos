<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    protected $table = 'menu_groups';

    protected $fillable = [
        'name',
        'menucategory_id',
        'description',
        'status',
        'created_by',
        'updated_by',
        'is_archived',
        'is_displayed',
        'archive_reason',
    ];

    protected $casts = [
        'is_archived' => 'string',
        'is_displayed' => 'string',
        'status' => 'string'
    ];

    public function items()
    {
        return $this->hasMany(MenuGroupItem::class, 'menugroup_id');
    }
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menucategory_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
