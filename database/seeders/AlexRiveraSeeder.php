<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AlexRiveraSeeder extends Seeder
{
    /**
     * Seed categories and expenses for Alex Rivera.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'alex@example.com')->firstOrFail();

        $user->expenses()->delete();
        $user->categories()->delete();

        $groceries = $user->categories()->create([
            'name' => 'Groceries',
            'color' => '#22C55E',
            'description' => 'Supermarket and daily food shopping',
        ]);

        $transport = $user->categories()->create([
            'name' => 'Transport',
            'color' => '#3B82F6',
            'description' => 'Bus, metro, fuel, and ride shares',
        ]);

        $rent = $user->categories()->create([
            'name' => 'Rent',
            'color' => '#F59E0B',
            'description' => 'Monthly housing rent',
        ]);

        $entertainment = $user->categories()->create([
            'name' => 'Entertainment',
            'color' => '#A855F7',
            'description' => 'Movies, streaming, and nights out',
        ]);

        $utilities = $user->categories()->create([
            'name' => 'Utilities',
            'color' => '#EF4444',
            'description' => 'Electricity, water, and internet',
        ]);

        $user->expenses()->createMany([
            [
                'category_id' => $rent->id,
                'title' => 'September apartment rent',
                'amount' => 850.00,
                'spent_at' => '2026-09-01',
                'notes' => 'Paid to landlord via bank transfer',
            ],
            [
                'category_id' => $utilities->id,
                'title' => 'Electricity bill',
                'amount' => 64.30,
                'spent_at' => '2026-09-03',
                'notes' => 'August usage',
            ],
            [
                'category_id' => $utilities->id,
                'title' => 'Home internet',
                'amount' => 39.99,
                'spent_at' => '2026-09-03',
                'notes' => 'Fiber 300 Mbps plan',
            ],
            [
                'category_id' => $groceries->id,
                'title' => 'Weekly groceries - FreshMart',
                'amount' => 78.45,
                'spent_at' => '2026-09-05',
                'notes' => 'Milk, eggs, vegetables, rice',
            ],
            [
                'category_id' => $transport->id,
                'title' => 'Monthly metro pass',
                'amount' => 55.00,
                'spent_at' => '2026-09-06',
                'notes' => null,
            ],
            [
                'category_id' => $entertainment->id,
                'title' => 'Cinema tickets',
                'amount' => 24.00,
                'spent_at' => '2026-09-12',
                'notes' => 'Two tickets for Saturday evening',
            ],
            [
                'category_id' => $groceries->id,
                'title' => 'Weekend groceries',
                'amount' => 52.10,
                'spent_at' => '2026-09-13',
                'notes' => 'Bread, fruit, coffee',
            ],
            [
                'category_id' => $transport->id,
                'title' => 'Airport taxi',
                'amount' => 28.50,
                'spent_at' => '2026-09-17',
                'notes' => 'Trip back from airport',
            ],
            [
                'category_id' => $entertainment->id,
                'title' => 'Netflix subscription',
                'amount' => 15.99,
                'spent_at' => '2026-09-18',
                'notes' => 'Standard plan',
            ],
        ]);
    }
}
