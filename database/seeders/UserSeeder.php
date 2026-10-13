<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'role' => UserRole::Admin,
            ],
            [
                'name' => 'Alex Rivera',
                'email' => 'alex@example.com',
                'role' => UserRole::User,
            ],
            [
                'name' => 'Sam Chen',
                'email' => 'sam@example.com',
                'role' => UserRole::User,
            ],
            [
                'name' => 'Jordan Blake',
                'email' => 'jordan@example.com',
                'role' => UserRole::User,
            ],
            [
                'name' => 'Maya Patel',
                'email' => 'maya@example.com',
                'role' => UserRole::User,
            ],
            [
                'name' => 'Chris Nguyen',
                'email' => 'chris@example.com',
                'role' => UserRole::User,
            ],
        ];

        foreach ($users as $user) {
            $model = User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );

            $model->role = $user['role'];
            $model->save();
        }
    }
}
