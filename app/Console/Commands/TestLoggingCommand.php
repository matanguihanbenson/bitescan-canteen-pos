<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemLogService;
use App\Models\User;

class TestLoggingCommand extends Command
{
    protected $signature = 'logs:test';
    protected $description = 'Test the system logging functionality';

    public function handle(SystemLogService $logService)
    {
        $this->info('Testing System Logging...');

        // Get admin user
        $admin = User::find(5);
        if (!$admin) {
            $this->error('Admin user not found');
            return 1;
        }

        auth()->login($admin);

        // Test various logging scenarios
        $this->info('1. Testing authentication log...');
        $logService->logAuth('login', 'Test user logged in via console command');

        $this->info('2. Testing user management log...');
        $logService->logUserManagement(
            'update',
            'Updated user permissions for testing',
            ['permission' => 'old_value'],
            ['permission' => 'new_value'],
            $admin->id
        );

        $this->info('3. Testing inventory log...');
        $logService->logInventory(
            'update',
            'Stock adjustment test - added 10 units',
            ['quantity' => 50],
            ['quantity' => 60],
            1,
            'info'
        );

        $this->info('4. Testing transaction log...');
        $logService->logTransaction(
            'create',
            'Created test transaction #12345',
            null,
            ['total' => 150.00, 'items' => 3],
            1,
            'success'
        );

        $this->info('5. Testing critical error log...');
        $logService->log(
            'error',
            'System Test',
            'This is a test critical error for demonstration',
            severity: 'critical'
        );

        $this->info('6. Testing maintenance log...');
        $logService->logMaintenance(
            'update',
            'System maintenance mode toggled',
            ['maintenance_mode' => false],
            ['maintenance_mode' => true],
            'warning'
        );

        $this->info('7. Testing report log...');
        $logService->logReport(
            'export',
            'Generated sales report for testing',
            ['format' => 'CSV', 'rows' => 100]
        );

        $this->info('');
        $this->info('✅ Successfully created 7 test log entries!');
        $this->info('You can view them at: http://127.0.0.1:8000/system-logs');
        
        return 0;
    }
}