<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MayaPatelSeeder extends Seeder
{
    /**
     * Seed categories and expenses for Maya Patel.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'maya@example.com')->firstOrFail();

        $user->expenses()->delete();
        $user->categories()->delete();

        $subscriptions = $user->categories()->create([
            'name' => 'Subscriptions',
            'color' => '#8B5CF6',
            'description' => 'Software and media subscriptions',
        ]);

        $coffee = $user->categories()->create([
            'name' => 'Coffee',
            'color' => '#92400E',
            'description' => 'Cafes and coffee shops',
        ]);

        $fitness = $user->categories()->create([
            'name' => 'Fitness',
            'color' => '#059669',
            'description' => 'Gym, yoga, and sports',
        ]);

        $gifts = $user->categories()->create([
            'name' => 'Gifts',
            'color' => '#DB2777',
            'description' => 'Presents and celebrations',
        ]);

        $user->expenses()->createMany([
            [
                'category_id' => $subscriptions->id,
                'title' => 'Spotify Premium',
                'amount' => 10.99,
                'spent_at' => '2026-09-01',
                'notes' => 'Monthly family plan share',
            ],
            [
                'category_id' => $fitness->id,
                'title' => 'Yoga class pack',
                'amount' => 60.00,
                'spent_at' => '2026-09-04',
                'notes' => '8-class package',
            ],
            [
                'category_id' => $coffee->id,
                'title' => 'Morning latte',
                'amount' => 5.25,
                'spent_at' => '2026-09-05',
                'notes' => null,
            ],
            [
                'category_id' => $subscriptions->id,
                'title' => 'Figma Professional',
                'amount' => 15.00,
                'spent_at' => '2026-09-08',
                'notes' => 'Design tool subscription',
            ],
            [
                'category_id' => $coffee->id,
                'title' => 'Weekend brunch drinks',
                'amount' => 14.80,
                'spent_at' => '2026-09-13',
                'notes' => 'Two drinks with a friend',
            ],
            [
                'category_id' => $gifts->id,
                'title' => 'Birthday gift - scarf',
                'amount' => 35.00,
                'spent_at' => '2026-09-16',
                'notes' => 'For Mom',
            ],
            [
                'category_id' => $fitness->id,
                'title' => 'Running socks',
                'amount' => 18.50,
                'spent_at' => '2026-09-19',
                'notes' => null,
            ],
        ]);
    }
}
