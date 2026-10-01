<?php

namespace App\Services;

use App\Models\Scholar;
use App\Models\ScholarBalanceHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScholarService
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }
    
    public function addAllowance(Scholar $scholar, $amount = null, $description = 'Daily allowance')
    {
        $amount = $amount ?? $scholar->daily_allowance;
        
        if (!$scholar->canReceiveAllowance()) {
            throw new \Exception('Scholar already received allowance today or is inactive');
        }

        DB::beginTransaction();
        try {
            $previousBalance = $scholar->current_balance;
            $newBalance = $previousBalance + $amount;

            $scholar->update([
                'current_balance' => $newBalance,
                'last_allowance_date' => today()
            ]);

            ScholarBalanceHistory::create([
                'scholar_id' => $scholar->id,
                'transaction_type' => 'credit',
                'amount' => $amount,
                'previous_balance' => $previousBalance,
                'new_balance' => $newBalance,
                'description' => $description,
                'transaction_date' => today(),
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding allowance: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reset the scholar's balance to today's allowance without carrying over unused balance.
     * If last_allowance_date is not today and scholar is active, set current_balance to daily_allowance
     * and record a credit history entry for traceability.
     */
    public function resetDailyAllowanceIfNeeded(Scholar $scholar): bool
    {
        if (!$scholar->is_active) {
            return false;
        }

        if ($scholar->last_allowance_date && $scholar->last_allowance_date->isToday()) {
            return false;
        }

        DB::beginTransaction();
        try {
            $amountToGrant = $scholar->daily_allowance ?? 0;
            $previousBalance = $scholar->current_balance ?? 0;

            $scholar->update([
                'current_balance' => $amountToGrant,
                'last_allowance_date' => today()
            ]);

            ScholarBalanceHistory::create([
                'scholar_id' => $scholar->id,
                'transaction_type' => 'credit',
                'amount' => $amountToGrant,
                'previous_balance' => $previousBalance,
                'new_balance' => $amountToGrant,
                'description' => 'Daily allowance (reset, no carryover)',
                'transaction_date' => today(),
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting daily allowance: ' . $e->getMessage());
            return false;
        }
    }

    
    public function deductBalance(Scholar $scholar, $amount, $transactionNumber = null, $description = 'Purchase')
    {
        if (!$scholar->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient balance. Current balance: ₱' . number_format($scholar->current_balance, 2));
        }

        DB::beginTransaction();
        try {
            $previousBalance = $scholar->current_balance;
            $newBalance = $previousBalance - $amount;

            $scholar->update(['current_balance' => $newBalance]);

            ScholarBalanceHistory::create([
                'scholar_id' => $scholar->id,
                'transaction_type' => 'debit',
                'amount' => $amount,
                'previous_balance' => $previousBalance,
                'new_balance' => $newBalance,
                'description' => $description,
                'transaction_number' => $transactionNumber,
                'transaction_date' => today(),
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return $newBalance;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deducting balance: ' . $e->getMessage());
            throw $e;
        }
    }

    
    public function validateScholar($studentId)
    {
        $scholar = Scholar::where('student_id', $studentId)->first();
        
        if (!$scholar) {
            return [
                'valid' => false,
                'message' => 'Student ID not found'
            ];
        }

        if (!$scholar->is_active) {
            return [
                'valid' => false,
                'message' => 'Student account is inactive'
            ];
        }

        return [
            'valid' => true,
            'scholar' => $scholar,
            'message' => 'Valid scholar'
        ];
    }

    
    public function getSummaryStats()
    {
        $today = today();
        
        $totalScholars = Scholar::where('is_active', true)->count();
        $claimedToday = Scholar::where('is_active', true)
            ->where('last_allowance_date', $today)
            ->count();
        
        $percentageClaimed = $totalScholars > 0 ? ($claimedToday / $totalScholars) * 100 : 0;
        
        $totalAllowanceToday = ScholarBalanceHistory::where('transaction_type', 'credit')
            ->where('transaction_date', $today)
            ->sum('amount');
        
        $totalBalanceRemaining = Scholar::where('is_active', true)->sum('current_balance');

        return [
            'total_scholars' => $totalScholars,
            'claimed_today' => $claimedToday,
            'percentage_claimed' => round($percentageClaimed, 1),
            'total_allowance_today' => $totalAllowanceToday,
            'total_balance_remaining' => $totalBalanceRemaining
        ];
    }

    

    
    public function autoDistributeAllowance(Scholar $scholar)
    {
        if ($scholar->canReceiveAllowance()) {
            $settings = $this->settingsService->getScholarSettings();
            $amount = $scholar->daily_allowance ?? $settings['default_allowance'] ?? 70.00;
            $this->addAllowance($scholar, $amount, 'Daily allowance (auto-granted)');
            return true;
        }
        return false;
    }
}
