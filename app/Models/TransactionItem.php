<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;
    protected $table = 'transaction_items';
    protected $fillable = [
        'transaction_id',
        'product_code',
        'product_name',
        'menu_code',
        'menu_name',
        'quantity',
        'unit_price',
        'subtotal',
        'unit',
        'type',
        'order_type'
    ];

    protected $attributes = [
        'product_code' => null,
        'menu_code' => null
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }
}
