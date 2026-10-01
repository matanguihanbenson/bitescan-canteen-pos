<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';
    protected $primaryKey = 'id';

    const STATUS_ACTIVE = 'active';
    const STATUS_DEACTIVATED = 'deactivated';

    protected $fillable = [
        'product_code',
        'base_price',
        'selling_price',
        'current_stock',
        'exp_date',
        'base_quantity',
        'min_stock',
        'unit',
        'units_per_group',
        'status'
    ];

    protected $casts = [
        'product_code' => 'integer',
        'base_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'current_stock' => 'integer',
        'exp_date' => 'date',
        'base_quantity' => 'integer',
        'min_stock' => 'integer',
        'units_per_group' => 'integer',
        'status' => 'string'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    public function lastMovement()
    {
        return $this->hasOne(StockMovement::class)->latest();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}