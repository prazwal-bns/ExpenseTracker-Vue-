<?php

use App\Models\User;

it('returns only the authenticated user from the users index', function () {
    $user = User::factory()->create();
    User::factory()->count(2)->create();

    $this->actingAs($user)
        ->getJson('/api/users')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $user->id);
});

it('shows the authenticated user profile', function () {
    $user = User::factory()->create([
        'name' => 'Alex Rivera',
        'email' => 'alex@example.com',
    ]);

    $this->actingAs($user)
        ->getJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJsonPath('data.name', 'Alex Rivera')
        ->assertJsonPath('data.email', 'alex@example.com');
});

it('returns 404 when showing another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
        ->getJson("/api/users/{$other->id}")
        ->assertNotFound();
});

it('updates the authenticated user profile', function () {
    $user = User::factory()->create([
        'name' => 'Alex Rivera',
    ]);

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", [
            'name' => 'Alex Updated',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Alex Updated');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Alex Updated',
    ]);
});

it('rejects creating users through the users endpoint', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertMethodNotAllowed();
});

it('deletes the authenticated user account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJsonPath('message', 'User deleted successfully.');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
