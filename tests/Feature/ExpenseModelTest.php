<?php

use App\Http\Resources\ExpenseResource;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;

it('belongs to a user and a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $expense = Expense::factory()
        ->for($user)
        ->for($category)
        ->create([
            'title' => 'Weekly groceries',
            'amount' => 42.50,
            'spent_at' => '2026-09-15',
        ]);

    expect($expense->user->is($user))->toBeTrue()
        ->and($expense->category->is($category))->toBeTrue()
        ->and($user->expenses)->toHaveCount(1)
        ->and($user->categories)->toHaveCount(1)
        ->and((string) $expense->amount)->toBe('42.50')
        ->and($expense->spent_at->toDateString())->toBe('2026-09-15');
});

it('exposes expense fields and loaded relationships in its api resource', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create(['name' => 'Transport']);
    $expense = Expense::factory()
        ->for($user)
        ->for($category)
        ->create([
            'title' => 'Bus pass',
            'amount' => 15.00,
            'spent_at' => '2026-09-10',
            'notes' => 'Monthly pass',
        ]);

    $expense->load(['user', 'category']);

    $payload = ExpenseResource::make($expense)
        ->toArray(Request::create('/'));

    expect($payload)
        ->id->toBe($expense->id)
        ->user_id->toBe($user->id)
        ->category_id->toBe($category->id)
        ->title->toBe('Bus pass')
        ->amount->toBe('15.00')
        ->spent_at->toBe('2026-09-10')
        ->notes->toBe('Monthly pass')
        ->user->id->toBe($user->id)
        ->category->name->toBe('Transport');
});
