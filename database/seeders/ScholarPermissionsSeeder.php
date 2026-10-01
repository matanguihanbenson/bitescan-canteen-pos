<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class ScholarPermissionsSeeder extends Seeder
{
    
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'manage_scholars',
                'display_name' => 'Manage Scholars'
            ],
            [
                'name' => 'view_scholar_reports',
                'display_name' => 'View Scholar Reports'
            ],
            [
                'name' => 'process_scholar_transactions',
                'display_name' => 'Process Scholar Transactions'
            ],
            [
                'name' => 'distribute_allowances',
                'display_name' => 'Distribute Allowances'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Scholar permissions seeded successfully!');
    }
}
