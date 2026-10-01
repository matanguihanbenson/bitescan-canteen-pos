<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarBalanceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholar_id',
        'transaction_type',
        'amount',
        'previous_balance',
        'new_balance',
        'description',
        'transaction_number',
        'transaction_date',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
        'transaction_date' => 'date'
    ];

    public function scholar()
    {
        return $this->belongsTo(Scholar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCredits($query)
    {
        return $query->where('transaction_type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('transaction_type', 'debit');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }
}
