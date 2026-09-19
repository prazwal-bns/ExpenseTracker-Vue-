<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 1, 5_000),
            'spent_at' => fake()->date(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Ensure the related category belongs to the same user as the expense.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Expense $expense): void {
            if ($expense->category_id === null || $expense->user_id === null) {
                return;
            }

            $category = Category::query()->find($expense->category_id);

            if ($category !== null && $category->user_id !== $expense->user_id) {
                $category->forceFill(['user_id' => $expense->user_id])->save();
            }
        });
    }
}
