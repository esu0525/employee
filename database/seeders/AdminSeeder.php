<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'markjamesp11770@gmail.com';
        $hashedEmail = hash('sha256', strtolower(trim($email)));

        // Create/Update admin account
        User::updateOrCreate(
            ['email_hash' => $hashedEmail],
            [
                'email'      => $email,
                'first_name' => 'Mark James',
                'last_name'  => 'P',
                'password'   => Hash::make('deped123'),
                'role'       => 'admin',
                'permissions' => ['view_employees', 'edit_employees', 'delete_employees', 'manage_documents', 'manage_requests', 'manage_accounts'],
            ]
        );

        $this->command->info('Admin account updated with split names: markjamesp11770@gmail.com / deped123');
    }
}
