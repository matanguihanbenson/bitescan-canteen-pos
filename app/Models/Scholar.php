<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Scholar extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'email',
        'contact_number',
        'program',
        'year_level',
        'daily_allowance',
        'current_balance',
        'last_allowance_date',
        'is_active'
    ];

    protected $casts = [
        'daily_allowance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'last_allowance_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function balanceHistory()
    {
        return $this->hasMany(ScholarBalanceHistory::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function canReceiveAllowance()
    {
        if (!$this->is_active) {
            return false;
        }
        
        return !$this->last_allowance_date || 
               $this->last_allowance_date->format('Y-m-d') !== today()->format('Y-m-d');
    }

    public function hasClaimedToday()
    {
        return $this->last_allowance_date && 
               $this->last_allowance_date->format('Y-m-d') === today()->format('Y-m-d');
    }

    public function hasSufficientBalance($amount)
    {
        return $this->current_balance >= $amount;
    }
}
