<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menus';
    protected $fillable = [
        'name',
        'menu_code',
        'price',
        'description',
        'status',
        'menucategory_id',
        'menusubcategory_id',
        'image_path',
        'is_archived'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'order_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_archived' => 'string'
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menucategory_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(MenuSubcategory::class, 'menusubcategory_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function getDisplayPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }
}
