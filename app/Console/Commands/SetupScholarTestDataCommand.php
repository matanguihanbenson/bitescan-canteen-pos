<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scholar;
use App\Models\AppSetting;
use App\Services\SettingsService;
use Illuminate\Support\Facades\DB;

class SetupScholarTestDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:scholar-test-data {--reset : Reset all existing data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup test data for the scholar management system';

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
        $this->info('Setting up Scholar Test Data');
        $this->newLine();

        if ($this->option('reset')) {
            if ($this->confirm('This will delete all existing scholar data. Are you sure?')) {
                $this->info('Resetting scholar data...');
                DB::table('scholar_balance_histories')->delete();
                DB::table('scholars')->delete();
                DB::table('app_settings')->where('key', 'like', 'scholar.%')->delete();
                $this->info('Scholar data reset.');
            } else {
                $this->info('Operation cancelled.');
                return;
            }
        }

        // Setup scholar settings
        $this->info('Setting up scholar settings...');
        
        $this->settingsService->updateScholarSettings([
            'default_allowance' => 100.00,
            'auto_grant_allowance' => true,
            'auto_reset' => true,
            'reset_time' => '06:00'
        ]);

        $this->info('Scholar settings configured:');
        $this->line('- Default allowance: ₱100.00');
        $this->line('- Auto grant allowance: Yes');
        $this->line('- Auto reset: Yes');
        $this->line('- Reset time: 06:00');

        // Create test scholars
        $this->newLine();
        $this->info('Creating test scholars...');

        $testScholars = [
            [
                'student_id' => 'STUD-001',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@example.com',
                'contact_number' => '09123456789',
                'program' => 'Computer Science',
                'year_level' => '3rd Year',
                'daily_allowance' => 120.00,
                'current_balance' => 50.00, // Low balance to test renewal
                'last_allowance_date' => now()->subDays(2), // Old date to trigger renewal
                'is_active' => true
            ],
            [
                'student_id' => 'STUD-002',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@example.com',
                'contact_number' => '09987654321',
                'program' => 'Information Technology',
                'year_level' => '2nd Year',
                'daily_allowance' => null, // Will use default allowance
                'current_balance' => 25.00,
                'last_allowance_date' => now()->subDay(), // Yesterday to trigger renewal
                'is_active' => true
            ],
            [
                'student_id' => 'STUD-003',
                'first_name' => 'Jose',
                'last_name' => 'Rizal',
                'email' => 'jose.rizal@example.com',
                'contact_number' => '09111222333',
                'program' => 'Engineering',
                'year_level' => '4th Year',
                'daily_allowance' => 150.00,
                'current_balance' => 150.00,
                'last_allowance_date' => now(), // Today - should not trigger renewal
                'is_active' => true
            ],
            [
                'student_id' => 'STUD-004',
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'email' => 'ana.garcia@example.com',
                'contact_number' => '09444555666',
                'program' => 'Business Administration',
                'year_level' => '1st Year',
                'daily_allowance' => 80.00,
                'current_balance' => 0.00,
                'last_allowance_date' => null, // Never received allowance
                'is_active' => true
            ],
            [
                'student_id' => 'STUD-005',
                'first_name' => 'Pedro',
                'last_name' => 'Inactive',
                'email' => 'pedro.inactive@example.com',
                'contact_number' => '09777888999',
                'program' => 'Liberal Arts',
                'year_level' => '2nd Year',
                'daily_allowance' => 90.00,
                'current_balance' => 45.00,
                'last_allowance_date' => now()->subDays(5),
                'is_active' => false // Inactive scholar
            ]
        ];

        foreach ($testScholars as $scholarData) {
            $scholar = Scholar::create($scholarData);
            
            $status = $scholarData['is_active'] ? 'Active' : 'Inactive';
            $this->line("✓ Created scholar: {$scholarData['student_id']} - {$scholarData['first_name']} {$scholarData['last_name']} ({$status})");
        }

        $this->newLine();
        $this->info('Test data setup completed!');
        
        $this->newLine();
        $this->info('Summary:');
        $this->line('- 4 active scholars created (3 need balance renewal, 1 current)');
        $this->line('- 1 inactive scholar created (should be ignored by renewal)');
        $this->line('- Scholar settings configured for automatic renewal at 06:00');
        $this->line('- Default allowance set to ₱100.00');
        
        $this->newLine();
        $this->info('You can now test the renewal system with:');
        $this->line('php artisan test:scholar-renewal --reset-cache');

        $this->newLine();
        $this->info('Or test the middleware by accessing any page in your application.');
    }
}