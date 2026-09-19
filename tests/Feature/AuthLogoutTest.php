<?php

use App\Models\User;

it('returns 401 json when logout is requested without a token', function () {
    $response = $this->postJson('/api/logout');

    $response->assertUnauthorized();
});

it('revokes the current sanctum token on logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('postman');

    expect($user->tokens()->count())->toBe(1);

    $response = $this->withToken($token->plainTextToken)
        ->postJson('/api/logout');

    $response->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');

    expect($user->fresh()->tokens()->count())->toBe(0);
});

it('rejects authenticated requests after logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('postman')->plainTextToken;

    $this->withToken($token)->postJson('/api/logout')->assertOk();

    // Simulate a new request lifecycle; the auth guard caches the user within a test.
    auth()->forgetGuards();

    $this->withToken($token)
        ->getJson('/api/expenses')
        ->assertUnauthorized();
});
