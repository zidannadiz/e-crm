<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@ecrm.com'],
            [
                'name' => 'Admin E-CRM',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('📧 Email: admin@ecrm.com');
        $this->command->info('🔑 Password: password123');
        $this->command->info('👤 Role: admin');
        $this->command->newLine();

        // Create Client User for testing
        $client = User::updateOrCreate(
            ['email' => 'client@ecrm.com'],
            [
                'name' => 'Client Test',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Client user created successfully!');
        $this->command->info('📧 Email: client@ecrm.com');
        $this->command->info('🔑 Password: password123');
        $this->command->info('👤 Role: client');
        $this->command->newLine();

        $this->command->info('🎉 Seeding completed! You can now login with above credentials.');
    }
}

