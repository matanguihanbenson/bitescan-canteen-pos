<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'unit_price',
        'subtotal',
        'discount_amount',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'modifiers' => 'array',
        'customizations' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pending',
        'discount_amount' => 0,
        'notes' => ''
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    protected function getFormattedTotalAttribute()
    {
        return '₱' . number_format($this->total_amount, 2);
    }

    protected function getFormattedSubtotalAttribute()
    {
        return '₱' . number_format($this->subtotal, 2);
    }

    public function calculateTotal()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        $this->total_amount = $this->subtotal - $this->discount_amount;
        return $this->total_amount;
    }

    public function applyDiscount($amount)
    {
        $this->discount_amount = min($amount, $this->subtotal);
        $this->total_amount = $this->calculateTotal();
        return $this;
    }

    public function updateQuantity($quantity)
    {
        $this->quantity = max(1, $quantity);
        $this->calculateTotal();
        return $this;
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        return $this->save();
    }

    public function complete()
    {
        $this->status = 'completed';
        return $this->save();
    }
}
