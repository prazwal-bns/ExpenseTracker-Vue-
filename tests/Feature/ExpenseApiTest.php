<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;

it('lists only the authenticated users expenses', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $category = Category::factory()->for($user)->create();
    Expense::factory()->for($user)->for($category)->count(2)->create();
    Expense::factory()->for($other)->count(3)->create();

    $response = $this->actingAs($user)->getJson('/api/expenses');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates an expense for the authenticated user', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $response = $this->actingAs($user)->postJson('/api/expenses', [
        'category_id' => $category->id,
        'title' => 'Weekly groceries',
        'amount' => 48.75,
        'spent_at' => '2026-09-19',
        'notes' => 'Milk and bread',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Weekly groceries')
        ->assertJsonPath('data.amount', '48.75')
        ->assertJsonPath('data.category_id', $category->id);

    $this->assertDatabaseHas('expenses', [
        'user_id' => $user->id,
        'title' => 'Weekly groceries',
        'amount' => 48.75,
    ]);
});

it('shows an owned expense', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $expense = Expense::factory()->for($user)->for($category)->create([
        'title' => 'Bus pass',
    ]);

    $response = $this->actingAs($user)->getJson("/api/expenses/{$expense->id}");

    $response->assertOk()
        ->assertJsonPath('data.title', 'Bus pass');
});

it('returns 404 when showing another users expense', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $expense = Expense::factory()->for($other)->create();

    $this->actingAs($user)
        ->getJson("/api/expenses/{$expense->id}")
        ->assertNotFound();
});

it('updates an owned expense', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    $expense = Expense::factory()->for($user)->for($category)->create([
        'title' => 'Old title',
        'amount' => 10.00,
    ]);

    $response = $this->actingAs($user)->putJson("/api/expenses/{$expense->id}", [
        'title' => 'New title',
        'amount' => 15.50,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'New title')
        ->assertJsonPath('data.amount', '15.50');
});

it('deletes an owned expense', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->for($user)->create();

    $this->actingAs($user)
        ->deleteJson("/api/expenses/{$expense->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Expense deleted successfully.');

    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
});

it('rejects creating an expense with another users category', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = Category::factory()->for($other)->create();

    $this->actingAs($user)->postJson('/api/expenses', [
        'category_id' => $category->id,
        'title' => 'Invalid',
        'amount' => 10.00,
        'spent_at' => '2026-09-19',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['category_id']);
});
