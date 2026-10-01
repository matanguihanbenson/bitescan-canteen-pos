<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subcategories';
    protected $primaryKey = 'id';

    const STATUS_ACTIVE = 'active';
    const STATUS_DEACTIVATED = 'deactivated';

    protected $fillable = [
        'name',
        'category_id',
        'status'
    ];

    protected $casts = [
        'category_id' => 'integer',
        'status' => 'string'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product_subcategories()
    {
        return $this->hasMany(ProductSubcategory::class);
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            ProductSubcategory::class,
            'subcategory_id',
            'product_code',
            'id',
            'product_code'
        );
    }
}