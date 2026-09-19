<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $alex = User::query()->create([
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $sam = User::query()->create([
            'name' => 'Sam Chen',
            'email' => 'sam@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $jordan = User::query()->create([
            'name' => 'Jordan Blake',
            'email' => 'jordan@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $this->seedAlexExpenses($alex);
        $this->seedSamExpenses($sam);
        $this->seedJordanExpenses($jordan);
    }

    private function seedAlexExpenses(User $user): void
    {
        $groceries = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Groceries',
            'color' => '#22C55E',
            'description' => 'Supermarket and daily food shopping',
        ]);

        $transport = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Transport',
            'color' => '#3B82F6',
            'description' => 'Bus, metro, fuel, and ride shares',
        ]);

        $rent = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Rent',
            'color' => '#F59E0B',
            'description' => 'Monthly housing rent',
        ]);

        $entertainment = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Entertainment',
            'color' => '#A855F7',
            'description' => 'Movies, streaming, and nights out',
        ]);

        $utilities = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Utilities',
            'color' => '#EF4444',
            'description' => 'Electricity, water, and internet',
        ]);

        Expense::query()->insert([
            [
                'user_id' => $user->id,
                'category_id' => $rent->id,
                'title' => 'September apartment rent',
                'amount' => 850.00,
                'spent_at' => '2026-09-01',
                'notes' => 'Paid to landlord via bank transfer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $utilities->id,
                'title' => 'Electricity bill',
                'amount' => 64.30,
                'spent_at' => '2026-09-03',
                'notes' => 'August usage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $utilities->id,
                'title' => 'Home internet',
                'amount' => 39.99,
                'spent_at' => '2026-09-03',
                'notes' => 'Fiber 300 Mbps plan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $groceries->id,
                'title' => 'Weekly groceries - FreshMart',
                'amount' => 78.45,
                'spent_at' => '2026-09-05',
                'notes' => 'Milk, eggs, vegetables, rice',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $transport->id,
                'title' => 'Monthly metro pass',
                'amount' => 55.00,
                'spent_at' => '2026-09-06',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $entertainment->id,
                'title' => 'Cinema tickets',
                'amount' => 24.00,
                'spent_at' => '2026-09-12',
                'notes' => 'Two tickets for Saturday evening',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $groceries->id,
                'title' => 'Weekend groceries',
                'amount' => 52.10,
                'spent_at' => '2026-09-13',
                'notes' => 'Bread, fruit, coffee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $transport->id,
                'title' => 'Airport taxi',
                'amount' => 28.50,
                'spent_at' => '2026-09-17',
                'notes' => 'Trip back from airport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $entertainment->id,
                'title' => 'Netflix subscription',
                'amount' => 15.99,
                'spent_at' => '2026-09-18',
                'notes' => 'Standard plan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedSamExpenses(User $user): void
    {
        $food = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Food & Dining',
            'color' => '#F97316',
            'description' => 'Restaurants, cafes, and takeout',
        ]);

        $shopping = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Shopping',
            'color' => '#EC4899',
            'description' => 'Clothes and personal items',
        ]);

        $health = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Health',
            'color' => '#14B8A6',
            'description' => 'Pharmacy, clinic, and gym',
        ]);

        $work = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Work',
            'color' => '#6366F1',
            'description' => 'Office supplies and co-working',
        ]);

        Expense::query()->insert([
            [
                'user_id' => $user->id,
                'category_id' => $health->id,
                'title' => 'Gym membership',
                'amount' => 45.00,
                'spent_at' => '2026-09-01',
                'notes' => 'Monthly membership renewal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $food->id,
                'title' => 'Lunch at Noodle House',
                'amount' => 14.75,
                'spent_at' => '2026-09-04',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $shopping->id,
                'title' => 'Running shoes',
                'amount' => 119.00,
                'spent_at' => '2026-09-07',
                'notes' => 'Bought from SportZone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $work->id,
                'title' => 'USB-C hub',
                'amount' => 34.99,
                'spent_at' => '2026-09-09',
                'notes' => 'For laptop docking',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $food->id,
                'title' => 'Coffee with team',
                'amount' => 18.40,
                'spent_at' => '2026-09-11',
                'notes' => 'Three drinks at Bean & Brew',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $health->id,
                'title' => 'Pharmacy - cold medicine',
                'amount' => 12.25,
                'spent_at' => '2026-09-15',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $food->id,
                'title' => 'Dinner delivery',
                'amount' => 27.80,
                'spent_at' => '2026-09-16',
                'notes' => 'Thai food from Green Leaf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedJordanExpenses(User $user): void
    {
        $household = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Household',
            'color' => '#84CC16',
            'description' => 'Cleaning supplies and home goods',
        ]);

        $education = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Education',
            'color' => '#0EA5E9',
            'description' => 'Courses, books, and learning tools',
        ]);

        $travel = Category::query()->create([
            'user_id' => $user->id,
            'name' => 'Travel',
            'color' => '#EAB308',
            'description' => 'Trips, hotels, and tickets',
        ]);

        Expense::query()->insert([
            [
                'user_id' => $user->id,
                'category_id' => $education->id,
                'title' => 'Laravel course',
                'amount' => 49.00,
                'spent_at' => '2026-09-02',
                'notes' => 'Online course subscription',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $household->id,
                'title' => 'Cleaning supplies',
                'amount' => 23.60,
                'spent_at' => '2026-09-08',
                'notes' => 'Detergent, sponges, trash bags',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $travel->id,
                'title' => 'Train tickets - weekend trip',
                'amount' => 68.00,
                'spent_at' => '2026-09-10',
                'notes' => 'Round trip to the coast',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $education->id,
                'title' => 'Programming book',
                'amount' => 32.50,
                'spent_at' => '2026-09-14',
                'notes' => 'Vue.js in Action paperback',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'category_id' => $household->id,
                'title' => 'Kitchen utensils set',
                'amount' => 41.20,
                'spent_at' => '2026-09-18',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
