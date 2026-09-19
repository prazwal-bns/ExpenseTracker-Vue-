<?php

use App\Models\Expense;
use App\Models\User;

it('returns 401 json when expenses are requested without a token', function () {
    $response = $this->getJson('/api/expenses');

    $response->assertUnauthorized();
});

it('returns a sanctum token when login credentials are valid', function () {
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
        ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'name', 'email']]);

    expect($user->tokens()->count())->toBe(1);
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
