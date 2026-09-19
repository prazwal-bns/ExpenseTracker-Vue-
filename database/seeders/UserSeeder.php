<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * All demo accounts use password: "password"
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Alex Rivera',
                'email' => 'alex@example.com',
            ],
            [
                'name' => 'Sam Chen',
                'email' => 'sam@example.com',
            ],
            [
                'name' => 'Jordan Blake',
                'email' => 'jordan@example.com',
            ],
            [
                'name' => 'Maya Patel',
                'email' => 'maya@example.com',
            ],
            [
                'name' => 'Chris Nguyen',
                'email' => 'chris@example.com',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );
        }
    }
}
