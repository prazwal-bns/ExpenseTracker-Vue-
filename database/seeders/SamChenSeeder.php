<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SamChenSeeder extends Seeder
{
    /**
     * Seed categories and expenses for Sam Chen.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'sam@example.com')->firstOrFail();

        $user->expenses()->delete();
        $user->categories()->delete();

        $food = $user->categories()->create([
            'name' => 'Food & Dining',
            'color' => '#F97316',
            'description' => 'Restaurants, cafes, and takeout',
        ]);

        $shopping = $user->categories()->create([
            'name' => 'Shopping',
            'color' => '#EC4899',
            'description' => 'Clothes and personal items',
        ]);

        $health = $user->categories()->create([
            'name' => 'Health',
            'color' => '#14B8A6',
            'description' => 'Pharmacy, clinic, and gym',
        ]);

        $work = $user->categories()->create([
            'name' => 'Work',
            'color' => '#6366F1',
            'description' => 'Office supplies and co-working',
        ]);

        $user->expenses()->createMany([
            [
                'category_id' => $health->id,
                'title' => 'Gym membership',
                'amount' => 45.00,
                'spent_at' => '2026-09-01',
                'notes' => 'Monthly membership renewal',
            ],
            [
                'category_id' => $food->id,
                'title' => 'Lunch at Noodle House',
                'amount' => 14.75,
                'spent_at' => '2026-09-04',
                'notes' => null,
            ],
            [
                'category_id' => $shopping->id,
                'title' => 'Running shoes',
                'amount' => 119.00,
                'spent_at' => '2026-09-07',
                'notes' => 'Bought from SportZone',
            ],
            [
                'category_id' => $work->id,
                'title' => 'USB-C hub',
                'amount' => 34.99,
                'spent_at' => '2026-09-09',
                'notes' => 'For laptop docking',
            ],
            [
                'category_id' => $food->id,
                'title' => 'Coffee with team',
                'amount' => 18.40,
                'spent_at' => '2026-09-11',
                'notes' => 'Three drinks at Bean & Brew',
            ],
            [
                'category_id' => $health->id,
                'title' => 'Pharmacy - cold medicine',
                'amount' => 12.25,
                'spent_at' => '2026-09-15',
                'notes' => null,
            ],
            [
                'category_id' => $food->id,
                'title' => 'Dinner delivery',
                'amount' => 27.80,
                'spent_at' => '2026-09-16',
                'notes' => 'Thai food from Green Leaf',
            ],
        ]);
    }
}
