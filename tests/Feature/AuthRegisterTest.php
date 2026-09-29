<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('registers a user and returns a sanctum token', function () {
    config(['sanctum.expiration' => 720]);
    $this->travelTo('2026-01-01 08:00:00');

    $response = $this->postJson('/api/register', [
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'postman',
    ]);

    $response->assertCreated()
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('user.email', 'taylor@example.com')
        ->assertJsonPath('expires_at', '2026-01-01T20:00:00+00:00')
        ->assertJsonStructure(['token', 'token_type', 'expires_at', 'user' => ['id', 'name', 'email']]);

    $this->assertDatabaseHas('users', [
        'email' => 'taylor@example.com',
        'name' => 'Taylor Otwell',
    ]);

    $user = User::query()->where('email', 'taylor@example.com')->first();

    expect($user)->not->toBeNull()
        ->and(Hash::check('password', $user->password))->toBeTrue()
        ->and($user->tokens()->count())->toBe(1)
        ->and($user->tokens()->first()->expires_at->toDateTimeString())->toBe('2026-01-01 20:00:00');
});

it('rejects registration when the email is already taken', function () {
    User::factory()->create([
        'email' => 'taylor@example.com',
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects registration when password confirmation does not match', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.com',
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
