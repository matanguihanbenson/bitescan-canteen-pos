<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';   
    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'unit',
        'adjustment_type',
        'date',
        'reference',
        'remarks',
        'reason',
        'created_by'
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
