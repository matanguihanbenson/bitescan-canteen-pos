<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Scholar;
use App\Models\ScholarBalanceHistory;
use App\Services\SettingsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ScholarBalanceRenewalMiddleware
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only run once per hour to avoid excessive database queries
        $cacheKey = 'scholar_balance_check_' . now()->format('Y-m-d-H');
        
        if (!Cache::has($cacheKey)) {
            $this->performBalanceRenewal();
            Cache::put($cacheKey, true, 3600); // Cache for 1 hour
        }

        return $next($request);
    }

    private function performBalanceRenewal(): void
    {
        try {
            $settings = $this->settingsService->getScholarSettings();
            
            if (!$settings['auto_reset']) {
                return;
            }

            $resetTime = $settings['reset_time'] ?? '04:00';
            $today = Carbon::today();
            $resetDateTime = Carbon::createFromFormat('Y-m-d H:i', $today->format('Y-m-d') . ' ' . $resetTime);
            
            if (now()->greaterThanOrEqualTo($resetDateTime)) {
                $this->resetScholarBalances($settings['default_allowance'] ?? 70.00);
            }
        } catch (\Exception $e) {
            Log::error('Scholar balance renewal error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function resetScholarBalances(float $defaultAllowance): void
    {
        DB::beginTransaction();
        
        try {
            $today = now()->toDateString();
            
            // Get all active scholars who haven't received allowance today
            $scholarsToReset = Scholar::where('is_active', true)
                ->where(function ($query) use ($today) {
                    $query->whereNull('last_allowance_date')
                        ->orWhere('last_allowance_date', '<', $today);
                })
                ->get();

            if ($scholarsToReset->isEmpty()) {
                DB::commit();
                return;
            }

            Log::info('Resetting scholar balances', [
                'count' => $scholarsToReset->count(),
                'default_allowance' => $defaultAllowance
            ]);

            foreach ($scholarsToReset as $scholar) {
                $previousBalance = $scholar->current_balance;
                $newBalance = $scholar->daily_allowance ?? $defaultAllowance;

                // Update scholar balance (reset to daily allowance, no carryover)
                $scholar->update([
                    'current_balance' => $newBalance,
                    'last_allowance_date' => now()->toDateString()
                ]);

                // Record the balance renewal in history
                ScholarBalanceHistory::create([
                    'scholar_id' => $scholar->id,
                    'transaction_type' => 'credit',
                    'amount' => $newBalance,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $newBalance,
                    'description' => 'Auto-renewal: Daily allowance reset (no carryover)',
                    'transaction_date' => now()->toDateString(),
                    'user_id' => null // System action
                ]);
            }

            DB::commit();
            
            Log::info('Successfully reset scholar balances', [
                'scholars_updated' => $scholarsToReset->count()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset scholar balances: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}