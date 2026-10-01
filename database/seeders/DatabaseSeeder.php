<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');
        $this->command->newLine();

        // Seed users, roles, and permissions first
        $this->command->info('📦 Seeding Users, Roles & Permissions...');
        $this->call(UsersRolesPermissionsSeeder::class);
        $this->command->newLine();

        // Seed additional scholar permissions (if any new ones)
        $this->command->info('📦 Seeding Additional Scholar Permissions...');
        $this->call(ScholarPermissionsSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Database seeding completed successfully!');
    }
}
