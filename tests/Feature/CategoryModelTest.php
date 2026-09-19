<?php

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;

it('belongs to a user and has many expenses', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $expenses = Expense::factory()
        ->count(2)
        ->for($user)
        ->for($category)
        ->create();

    expect($category->user->is($user))->toBeTrue()
        ->and($category->expenses)->toHaveCount(2)
        ->and($category->expenses->pluck('id')->sort()->values()->all())
        ->toEqual($expenses->pluck('id')->sort()->values()->all());
});

it('exposes category fields and loaded relationships in its api resource', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create([
        'name' => 'Groceries',
        'color' => '#22C55E',
        'description' => 'Food and household items',
    ]);
    Expense::factory()->count(3)->for($user)->for($category)->create();

    $category->load('user')->loadCount('expenses');

    $payload = CategoryResource::make($category)
        ->toArray(Request::create('/'));

    expect($payload)
        ->id->toBe($category->id)
        ->user_id->toBe($user->id)
        ->name->toBe('Groceries')
        ->color->toBe('#22C55E')
        ->description->toBe('Food and household items')
        ->expenses_count->toBe(3)
        ->user->id->toBe($user->id)
        ->not->toHaveKey('expenses');
});
