<?php

use App\Models\Expense;
use App\Models\User;

it('returns 401 json when expenses are requested without a token', function () {
    $response = $this->getJson('/api/expenses');

    $response->assertUnauthorized();
});

it('returns a sanctum token when login credentials are valid', function () {
    config(['sanctum.expiration' => 720]);
    $this->travelTo('2026-01-01 08:00:00');
    $user = User::factory()->create([
        'email' => 'alex@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'alex@example.com',
        'password' => 'password',
        'device_name' => 'postman',
    ]);

    $response->assertOk()
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('user.email', 'alex@example.com')
        ->assertJsonPath('expires_at', '2026-01-01T20:00:00+00:00')
        ->assertJsonStructure(['token', 'token_type', 'expires_at', 'user' => ['id', 'name', 'email']]);

    expect($user->tokens()->count())->toBe(1)
        ->and($user->tokens()->first()->expires_at->toDateTimeString())->toBe('2026-01-01 20:00:00');
});

it('rejects the login token once it has expired', function () {
    config(['sanctum.expiration' => 720]);
    $this->travelTo('2026-01-01 08:00:00');
    User::factory()->create([
        'email' => 'alex@example.com',
        'password' => 'password',
    ]);
    $token = $this->postJson('/api/login', [
        'email' => 'alex@example.com',
        'password' => 'password',
    ])->json('token');
    $this->travelTo('2026-01-01 20:00:01');

    $response = $this->withToken($token)->getJson('/api/expenses');

    $response->assertUnauthorized();
});

it('rejects invalid login credentials', function () {
    User::factory()->create([
        'email' => 'alex@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'alex@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('lists expenses when authenticated with a sanctum token', function () {
    $user = User::factory()->create();
    Expense::factory()->for($user)->count(2)->create();

    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)->getJson('/api/expenses');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});
