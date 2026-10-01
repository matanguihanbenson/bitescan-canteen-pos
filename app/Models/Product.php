<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id';

    const STATUS_ACTIVE = 'active';
    const STATUS_DEACTIVATED = 'deactivated';

    protected $fillable = [
        'product_code',
        'product_name',
        'product_description',
        'category_id',
        'status',
        'product_image',
        'current_stock',
        'base_quantity'
    ];

    protected $casts = [
        'product_code' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product_subcategories(): HasMany
    {
        return $this->hasMany(ProductSubcategory::class, 'product_code', 'product_code');
    }

    public function subcategories(): HasManyThrough
    {
        return $this->hasManyThrough(
            Subcategory::class,
            ProductSubcategory::class,
            'product_code',
            'id',
            'product_code',
            'subcategory_id'
        );
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class, 'product_code', 'product_code');
    }
}