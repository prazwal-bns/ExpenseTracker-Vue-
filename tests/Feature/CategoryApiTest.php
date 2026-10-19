<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;

it('lists only the authenticated users categories', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Category::factory()->for($user)->count(2)->create();
    Category::factory()->for($other)->count(3)->create();

    $this->actingAs($user)
        ->getJson('/api/categories')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a category for the authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/categories', [
        'name' => 'Groceries',
        'color' => '#22C55E',
        'description' => 'Food shopping',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Groceries')
        ->assertJsonPath('data.color', '#22C55E');

    $this->assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Groceries',
    ]);
});

it('returns 404 when showing another users category', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = Category::factory()->for($other)->create();

    $this->actingAs($user)
        ->getJson("/api/categories/{$category->id}")
        ->assertNotFound();
});

it('updates an owned category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create([
        'name' => 'Food',
    ]);

    $this->actingAs($user)
        ->putJson("/api/categories/{$category->id}", [
            'name' => 'Groceries',
            'color' => '#16A34A',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Groceries')
        ->assertJsonPath('data.color', '#16A34A');
});

it('deletes an empty category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $this->actingAs($user)
        ->deleteJson("/api/categories/{$category->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Category deleted successfully.');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('rejects deleting a category that still has expenses', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();
    Expense::factory()->for($user)->for($category)->create();

    $this->actingAs($user)
        ->deleteJson("/api/categories/{$category->id}")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete a category that still has expenses.');

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

it('returns 304 when the category list has not changed', function () {
    $user = User::factory()->create();
    Category::factory()->for($user)->create();

    $etag = $this->actingAs($user)
        ->getJson('/api/categories')
        ->assertOk()
        ->assertHeader('ETag')
        ->headers->get('ETag');

    $this->actingAs($user)
        ->withHeaders(['If-None-Match' => $etag])
        ->getJson('/api/categories')
        ->assertStatus(304)
        ->assertHeader('ETag', $etag);
});

it('treats the category list as changed when an expense count changes', function () {
    $user = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $etag = $this->actingAs($user)->getJson('/api/categories')->headers->get('ETag');

    Expense::factory()->for($user)->for($category)->create();

    $this->actingAs($user)
        ->withHeaders(['If-None-Match' => $etag])
        ->getJson('/api/categories')
        ->assertOk()
        ->assertJsonPath('data.0.expenses_count', 1);
});
