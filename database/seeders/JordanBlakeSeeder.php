<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class JordanBlakeSeeder extends Seeder
{
    /**
     * Seed categories and expenses for Jordan Blake.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'jordan@example.com')->firstOrFail();

        $user->expenses()->delete();
        $user->categories()->delete();

        $household = $user->categories()->create([
            'name' => 'Household',
            'color' => '#84CC16',
            'description' => 'Cleaning supplies and home goods',
        ]);

        $education = $user->categories()->create([
            'name' => 'Education',
            'color' => '#0EA5E9',
            'description' => 'Courses, books, and learning tools',
        ]);

        $travel = $user->categories()->create([
            'name' => 'Travel',
            'color' => '#EAB308',
            'description' => 'Trips, hotels, and tickets',
        ]);

        $user->expenses()->createMany([
            [
                'category_id' => $education->id,
                'title' => 'Laravel course',
                'amount' => 49.00,
                'spent_at' => '2026-09-02',
                'notes' => 'Online course subscription',
            ],
            [
                'category_id' => $household->id,
                'title' => 'Cleaning supplies',
                'amount' => 23.60,
                'spent_at' => '2026-09-08',
                'notes' => 'Detergent, sponges, trash bags',
            ],
            [
                'category_id' => $travel->id,
                'title' => 'Train tickets - weekend trip',
                'amount' => 68.00,
                'spent_at' => '2026-09-10',
                'notes' => 'Round trip to the coast',
            ],
            [
                'category_id' => $education->id,
                'title' => 'Programming book',
                'amount' => 32.50,
                'spent_at' => '2026-09-14',
                'notes' => 'Vue.js in Action paperback',
            ],
            [
                'category_id' => $household->id,
                'title' => 'Kitchen utensils set',
                'amount' => 41.20,
                'spent_at' => '2026-09-18',
                'notes' => null,
            ],
        ]);
    }
}
