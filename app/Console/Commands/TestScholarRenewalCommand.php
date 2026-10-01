<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scholar;
use App\Models\ScholarBalanceHistory;
use App\Services\SettingsService;
use App\Http\Middleware\ScholarBalanceRenewalMiddleware;
use Illuminate\Support\Facades\Cache;

class TestScholarRenewalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scholar-renewal {--reset-cache : Reset the cache to force renewal check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the automatic scholar balance renewal system';

    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        parent::__construct();
        $this->settingsService = $settingsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Scholar Balance Renewal System');
        $this->newLine();

        // Show current settings
        $settings = $this->settingsService->getScholarSettings();
        $this->info('Current Scholar Settings:');
        $this->table(['Setting', 'Value'], [
            ['Default Allowance', '₱' . number_format($settings['default_allowance'], 2)],
            ['Auto Grant Allowance', $settings['auto_grant_allowance'] ? 'Yes' : 'No'],
            ['Auto Reset', $settings['auto_reset'] ? 'Yes' : 'No'],
            ['Reset Time', $settings['reset_time']]
        ]);

        // Show scholars status before renewal
        $this->newLine();
        $this->info('Scholars Status:');
        $scholars = Scholar::where('is_active', true)->get();
        
        if ($scholars->isEmpty()) {
            $this->warn('No active scholars found. Please add some scholars first.');
            return;
        }

        $scholarData = [];
        foreach ($scholars as $scholar) {
            $scholarData[] = [
                $scholar->student_id,
                $scholar->first_name . ' ' . $scholar->last_name,
                '₱' . number_format($scholar->current_balance, 2),
                $scholar->last_allowance_date ? $scholar->last_allowance_date->format('Y-m-d') : 'Never',
                $scholar->daily_allowance ? '₱' . number_format($scholar->daily_allowance, 2) : 'Default'
            ];
        }

        $this->table(['Student ID', 'Name', 'Current Balance', 'Last Allowance Date', 'Daily Allowance'], $scholarData);

        // Clear cache if requested
        if ($this->option('reset-cache')) {
            $cacheKey = 'scholar_balance_check_' . now()->format('Y-m-d-H');
            Cache::forget($cacheKey);
            $this->info('Cache cleared to force renewal check.');
        }

        // Test the renewal system
        $this->newLine();
        $this->info('Testing renewal system...');

        try {
            // Create a mock request to test middleware
            $request = new \Illuminate\Http\Request();
            $middleware = app(ScholarBalanceRenewalMiddleware::class);
            
            $response = $middleware->handle($request, function () {
                return response('OK');
            });

            $this->info('Renewal system executed successfully.');

            // Show scholars status after renewal
            $this->newLine();
            $this->info('Scholars Status After Renewal:');
            
            $scholars->fresh();
            $scholarDataAfter = [];
            foreach ($scholars as $scholar) {
                $scholarDataAfter[] = [
                    $scholar->student_id,
                    $scholar->first_name . ' ' . $scholar->last_name,
                    '₱' . number_format($scholar->current_balance, 2),
                    $scholar->last_allowance_date ? $scholar->last_allowance_date->format('Y-m-d') : 'Never',
                    $scholar->daily_allowance ? '₱' . number_format($scholar->daily_allowance, 2) : 'Default'
                ];
            }

            $this->table(['Student ID', 'Name', 'Current Balance', 'Last Allowance Date', 'Daily Allowance'], $scholarDataAfter);

            // Show recent history
            $this->newLine();
            $this->info('Recent Balance History (last 5 transactions):');
            
            $recentHistory = ScholarBalanceHistory::with(['scholar', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            if ($recentHistory->isNotEmpty()) {
                $historyData = [];
                foreach ($recentHistory as $history) {
                    $historyData[] = [
                        $history->scholar ? $history->scholar->student_id : 'N/A',
                        $history->created_at->format('Y-m-d H:i:s'),
                        ucfirst($history->transaction_type),
                        '₱' . number_format($history->amount, 2),
                        '₱' . number_format($history->new_balance, 2),
                        $history->description,
                        $history->user ? $history->user->firstname : 'System'
                    ];
                }

                $this->table(['Student ID', 'Date/Time', 'Type', 'Amount', 'New Balance', 'Description', 'Processed By'], $historyData);
            } else {
                $this->warn('No recent history found.');
            }

        } catch (\Exception $e) {
            $this->error('Error testing renewal system: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }

        $this->newLine();
        $this->info('Test completed.');
    }
}