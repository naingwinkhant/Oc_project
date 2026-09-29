<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['username' => 'admin', 'name' => 'Alyssa Reyes', 'email' => 'admin@supermarket.test', 'role' => Role::Admin, 'phone' => '09 380 000 01'],
            ['username' => 'manager', 'name' => 'Marco Villanueva', 'email' => 'manager@supermarket.test', 'role' => Role::Manager, 'phone' => '09 380 000 02'],
            ['username' => 'staff', 'name' => 'Jenna Cruz', 'email' => 'staff@supermarket.test', 'role' => Role::Staff, 'phone' => '09 380 000 03'],
            ['username' => 'ricky', 'name' => 'Rico Bautista', 'email' => 'stock.clerk@supermarket.test', 'role' => Role::Staff, 'phone' => '09 380 000 04'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'phone' => $user['phone'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
