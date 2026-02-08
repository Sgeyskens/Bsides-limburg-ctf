<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@friday13.local',
                'password' => 'admin',
                'avatar_url' => '/images/avatars/admin.png',
                'is_admin' => true,
            ],
            [
                'username' => 'jason_voorhees',
                'email' => 'jason@crystallake.com',
                'password' => 'password123',
                'avatar_url' => '/images/avatars/jason.png',
                'is_admin' => false,
            ],
            [
                'username' => 'tommy_jarvis',
                'email' => 'tommy@example.com',
                'password' => 'password123',
                'avatar_url' => '/images/avatars/tommy.png',
                'is_admin' => false,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']], // UNIQUE KEY
                [
                    'username' => $user['username'],
                    'avatar_url' => $user['avatar_url'],
                    'is_admin' => $user['is_admin'],
                    'password' => Hash::make($user['password']),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Users seeded successfully!');
    }
}
