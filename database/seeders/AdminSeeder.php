<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin if none exists
        if (! User::where('role', 'admin')->exists()) {
            User::create([
                'name'     => 'System Administrator',
                'email'    => 'admin@deped.gov.ph',
                'password' => Hash::make('Admin@12345'),
                'role'     => 'admin',
                'permissions' => [],
            ]);

            $this->command->info('Default admin created: admin@deped.gov.ph / Admin@12345');
        } else {
            $this->command->info('Admin already exists, skipping seed.');
        }
    }
}
