<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        DB::table('user_permissions')->truncate();
        DB::table('role_permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed Roles
        $roles = [
            [
                'id' => 1,
                'name' => 'admin',
                'display_name' => 'System Admin',
                'description' => 'Full system access with all permissions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Manages inventory, menu, and sales oversight',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'cashier',
                'display_name' => 'Cashier',
                'description' => 'Handles POS transactions and daily sales',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Basic access to dashboard and sales viewing',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);

        // Seed Permissions
        $permissions = [
            ['id' => 1, 'name' => 'manage_users', 'display_name' => 'User Management'],
            ['id' => 2, 'name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
            ['id' => 3, 'name' => 'access_pos', 'display_name' => 'Access POS'],
            ['id' => 4, 'name' => 'manage_inventory', 'display_name' => 'Inventory Management'],
            ['id' => 5, 'name' => 'manage_menu', 'display_name' => 'Menu Management'],
            ['id' => 6, 'name' => 'view_sales', 'display_name' => 'Sales Overview'],
            ['id' => 7, 'name' => 'maintenance', 'display_name' => 'System Maintenance'],
            ['id' => 8, 'name' => 'manage_scholars', 'display_name' => 'Manage Scholars'],
            ['id' => 9, 'name' => 'view_scholar_reports', 'display_name' => 'View Scholar Reports'],
            ['id' => 10, 'name' => 'view_system_logs', 'display_name' => 'View System Logs'],
            ['id' => 11, 'name' => 'role_management', 'display_name' => 'Role Management'],
        ];

        DB::table('permissions')->insert($permissions);

        // Seed Role Permissions
        $rolePermissions = [
            // Admin - Limited to specific admin permissions
            ['permission_id' => 1, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()],  // manage_users
            ['permission_id' => 2, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()],  // view_dashboard
            ['permission_id' => 7, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()],  // maintenance
            ['permission_id' => 10, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()], // view_system_logs
            ['permission_id' => 11, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()], // role_management

            // Manager
            ['permission_id' => 2, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // view_dashboard
            ['permission_id' => 3, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // access_pos
            ['permission_id' => 4, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // manage_inventory
            ['permission_id' => 5, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // manage_menu
            ['permission_id' => 6, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // view_sales
            ['permission_id' => 8, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // manage_scholars
            ['permission_id' => 9, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // view_scholar_reports
            ['permission_id' => 10, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()], // view_system_logs

            // Cashier
            ['permission_id' => 2, 'role_id' => 3, 'created_at' => now(), 'updated_at' => now()],  // view_dashboard
            ['permission_id' => 3, 'role_id' => 3, 'created_at' => now(), 'updated_at' => now()],  // access_pos
            ['permission_id' => 6, 'role_id' => 3, 'created_at' => now(), 'updated_at' => now()],  // view_sales
            ['permission_id' => 10, 'role_id' => 3, 'created_at' => now(), 'updated_at' => now()], // view_system_logs

            // Staff
            ['permission_id' => 2, 'role_id' => 4, 'created_at' => now(), 'updated_at' => now()],  // view_dashboard
            ['permission_id' => 6, 'role_id' => 4, 'created_at' => now(), 'updated_at' => now()],  // view_sales
            ['permission_id' => 10, 'role_id' => 4, 'created_at' => now(), 'updated_at' => now()], // view_system_logs
        ];

        DB::table('role_permissions')->insert($rolePermissions);

        // Seed Users
        $users = [
            [
                'id' => 5,
                'username' => 'admin',
                'firstname' => 'System',
                'middlename' => null,
                'lastname' => 'Admin',
                'status' => 'active',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'), // Default password
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'image' => null,
                'last_login' => null,
            ],
            [
                'id' => 6,
                'username' => 'cashier',
                'firstname' => 'Canteen',
                'middlename' => null,
                'lastname' => 'Cashier',
                'status' => 'active',
                'email' => 'cashier@gmail.com',
                'password' => Hash::make('password'), // Default password
                'role_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
                'image' => null,
                'last_login' => null,
            ],
            [
                'id' => 7,
                'username' => 'finance',
                'firstname' => 'Finance',
                'middlename' => null,
                'lastname' => 'Office',
                'status' => 'active',
                'email' => 'finance@gmail.com',
                'password' => Hash::make('password'), // Default password
                'role_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
                'image' => null,
                'last_login' => null,
            ],
            [
                'id' => 8,
                'username' => 'manager',
                'firstname' => 'Canteen',
                'middlename' => null,
                'lastname' => 'Manager',
                'status' => 'active',
                'email' => 'manager@gmail.com',
                'password' => Hash::make('password'), // Default password
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
                'image' => null,
                'last_login' => null,
            ],
        ];

        DB::table('users')->insert($users);

        // Seed User-specific Permissions (additional to role permissions)
        $userPermissions = [
            // Admin user gets specific admin permissions only
            ['user_id' => 5, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()],  // manage_users
            ['user_id' => 5, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()],  // view_dashboard
            ['user_id' => 5, 'permission_id' => 7, 'created_at' => now(), 'updated_at' => now()],  // maintenance
            ['user_id' => 5, 'permission_id' => 10, 'created_at' => now(), 'updated_at' => now()], // view_system_logs
            ['user_id' => 5, 'permission_id' => 11, 'created_at' => now(), 'updated_at' => now()], // role_management

            // Cashier user
            ['user_id' => 6, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 6, 'permission_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Finance user
            ['user_id' => 7, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 7, 'permission_id' => 6, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('user_permissions')->insert($userPermissions);

        $this->command->info('✅ Roles seeded: 4 roles (Admin, Manager, Cashier, Staff)');
        $this->command->info('✅ Permissions seeded: 11 permissions (including view_system_logs and role_management)');
        $this->command->info('✅ Role Permissions seeded: Admin role has all permissions including role_management');
        $this->command->info('✅ Users seeded: 4 users (admin, cashier, finance, manager)');
        $this->command->info('');
        $this->command->warn('⚠️  Default password for all users: password');
        $this->command->warn('⚠️  Please change passwords after first login!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('  Admin:    admin@gmail.com    / password');
        $this->command->info('  Cashier:  cashier@gmail.com  / password');
        $this->command->info('  Finance:  finance@gmail.com  / password');
        $this->command->info('  Manager:  manager@gmail.com  / password');
    }
}