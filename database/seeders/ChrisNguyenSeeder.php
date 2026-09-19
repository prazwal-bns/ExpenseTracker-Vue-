<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ChrisNguyenSeeder extends Seeder
{
    /**
     * Seed categories and expenses for Chris Nguyen.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'chris@example.com')->firstOrFail();

        $user->expenses()->delete();
        $user->categories()->delete();

        $fuel = $user->categories()->create([
            'name' => 'Fuel',
            'color' => '#DC2626',
            'description' => 'Petrol and charging',
        ]);

        $groceries = $user->categories()->create([
            'name' => 'Groceries',
            'color' => '#65A30D',
            'description' => 'Weekly supermarket trips',
        ]);

        $insurance = $user->categories()->create([
            'name' => 'Insurance',
            'color' => '#1D4ED8',
            'description' => 'Car, health, and home insurance',
        ]);

        $pets = $user->categories()->create([
            'name' => 'Pets',
            'color' => '#CA8A04',
            'description' => 'Pet food and vet visits',
        ]);

        $dining = $user->categories()->create([
            'name' => 'Dining Out',
            'color' => '#EA580C',
            'description' => 'Restaurants and takeaway',
        ]);

        $user->expenses()->createMany([
            [
                'category_id' => $insurance->id,
                'title' => 'Car insurance premium',
                'amount' => 112.00,
                'spent_at' => '2026-09-01',
                'notes' => 'Monthly installment',
            ],
            [
                'category_id' => $fuel->id,
                'title' => 'Petrol fill-up',
                'amount' => 48.60,
                'spent_at' => '2026-09-03',
                'notes' => 'Shell station',
            ],
            [
                'category_id' => $groceries->id,
                'title' => 'Costco run',
                'amount' => 96.40,
                'spent_at' => '2026-09-06',
                'notes' => 'Bulk household items',
            ],
            [
                'category_id' => $pets->id,
                'title' => 'Dog food - 10kg bag',
                'amount' => 42.00,
                'spent_at' => '2026-09-09',
                'notes' => null,
            ],
            [
                'category_id' => $dining->id,
                'title' => 'Sushi dinner',
                'amount' => 38.75,
                'spent_at' => '2026-09-12',
                'notes' => 'Date night',
            ],
            [
                'category_id' => $fuel->id,
                'title' => 'EV charging session',
                'amount' => 16.20,
                'spent_at' => '2026-09-14',
                'notes' => 'City parking garage charger',
            ],
            [
                'category_id' => $pets->id,
                'title' => 'Vet checkup',
                'amount' => 75.00,
                'spent_at' => '2026-09-17',
                'notes' => 'Annual vaccination',
            ],
            [
                'category_id' => $groceries->id,
                'title' => 'Midweek groceries',
                'amount' => 54.15,
                'spent_at' => '2026-09-19',
                'notes' => 'Fresh produce and snacks',
            ],
        ]);
    }
}
