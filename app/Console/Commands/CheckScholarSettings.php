<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AppSetting;
use App\Services\SettingsService;
use App\Models\ScholarBalanceHistory;
use App\Models\Scholar;

class CheckScholarSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:scholar-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check scholar settings and reports data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Scholar Settings and Reports');
        $this->newLine();

        // Check app_settings table
        $this->info('1. App Settings Table Contents:');
        $settings = DB::table('app_settings')
            ->where('key', 'like', 'scholar.%')
            ->get();
        
        if ($settings->isEmpty()) {
            $this->warn('No scholar settings found in app_settings table!');
        } else {
            foreach ($settings as $setting) {
                $this->line("  {$setting->key} = {$setting->value}");
            }
        }

        $this->newLine();
        
        // Check SettingsService
        $this->info('2. Settings Service Output:');
        try {
            $settingsService = app(SettingsService::class);
            $scholarSettings = $settingsService->getScholarSettings();
            foreach ($scholarSettings as $key => $value) {
                $this->line("  {$key} = " . json_encode($value));
            }
        } catch (\Exception $e) {
            $this->error('Error getting settings: ' . $e->getMessage());
        }

        $this->newLine();
        
        // Check scholar balance history for reports
        $this->info('3. Scholar Balance History Data:');
        $historyCount = ScholarBalanceHistory::count();
        $this->line("  Total history records: {$historyCount}");
        
        if ($historyCount > 0) {
            $recentHistory = ScholarBalanceHistory::with(['scholar', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $this->line('  Recent 5 records:');
            foreach ($recentHistory as $record) {
                $scholarName = $record->scholar ? $record->scholar->first_name . ' ' . $record->scholar->last_name : 'Unknown';
                $userName = $record->user ? $record->user->firstname : 'System';
                $this->line("    - {$record->created_at}: {$scholarName} - {$record->transaction_type} ₱{$record->amount} by {$userName}");
            }
        } else {
            $this->warn('  No balance history records found!');
        }

        $this->newLine();
        
        // Check scholars table
        $this->info('4. Scholar Data:');
        $scholarCount = Scholar::count();
        $activeCount = Scholar::where('is_active', true)->count();
        $this->line("  Total scholars: {$scholarCount}");
        $this->line("  Active scholars: {$activeCount}");
        
        if ($scholarCount > 0) {
            $scholars = Scholar::limit(3)->get();
            $this->line('  Sample scholars:');
            foreach ($scholars as $scholar) {
                $this->line("    - {$scholar->student_id}: {$scholar->first_name} {$scholar->last_name} (Balance: ₱{$scholar->current_balance})");
            }
        }

        $this->newLine();
        $this->info('Check completed.');
    }
}
