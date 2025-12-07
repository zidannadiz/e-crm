<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'cs@ecrm.com'],
            [
                'name' => 'Customer Service',
                'password' => Hash::make('password123'),
                'role' => 'cs',
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Customer Service user created successfully!\n";
        echo "📧 Email: cs@ecrm.com\n";
        echo "🔑 Password: password123\n";
        echo "👤 Role: cs\n";
        echo "\n🎉 Seeding completed! You can now login with above credentials.\n";
    }
}

