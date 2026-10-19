<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;

it('returns 429 when login is attempted too many times for the same email', function () {
    $email = 'targeted@example.com';
    User::factory()->create([
        'email' => $email,
        'password' => 'password',
    ]);

    repeatJson('POST', '/api/login', [
        'email' => $email,
        'password' => 'wrong-password',
    ], (int) config('rate-limiting.auth'))
        ->assertUnprocessable();

    test()->postJson('/api/login', [
        'email' => $email,
        'password' => 'password',
    ])->assertTooManyRequests()
        ->assertJsonPath('message', 'Too Many Attempts.');
});

it('still checks a different email after one address is rate limited', function () {
    $limited = 'limited@example.com';
    User::factory()->create([
        'email' => $limited,
        'password' => 'password',
    ]);
    User::factory()->create([
        'email' => 'other@example.com',
        'password' => 'password',
    ]);

    repeatJson('POST', '/api/login', [
        'email' => $limited,
        'password' => 'wrong-password',
    ], (int) config('rate-limiting.auth') + 1)
        ->assertTooManyRequests();

    test()->postJson('/api/login', [
        'email' => 'other@example.com',
        'password' => 'wrong-password',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns 429 when registration is attempted too many times for the same email', function () {
    $payload = [
        'name' => 'Repeat',
        'email' => 'repeat@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    repeatJson('POST', '/api/register', $payload, (int) config('rate-limiting.auth'))
        ->assertUnprocessable();

    test()->postJson('/api/register', $payload)
        ->assertTooManyRequests()
        ->assertJsonPath('message', 'Too Many Attempts.');
});

it('returns 429 when a sensitive profile action is repeated', function (string $method, string $uri, array $payload) {
    $user = User::factory()->create(['password' => 'secret-password']);
    $limit = (int) config('rate-limiting.sensitive');

    repeatJson($method, $uri, $payload, $limit, $user)
        ->assertUnprocessable();

    test()->actingAs($user)->json($method, $uri, $payload)
        ->assertTooManyRequests()
        ->assertJsonPath('message', 'Too Many Attempts.');
})->with([
    'password change' => ['PUT', '/api/profile/password', [
        'current_password' => 'wrong-password',
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
    ]],
    'account deletion' => ['DELETE', '/api/profile', [
        'password' => 'wrong-password',
    ]],
]);

it('returns 429 when an authenticated user exceeds the api limit', function () {
    $user = User::factory()->create();
    $limit = (int) config('rate-limiting.api');

    repeatJson('GET', '/api/user', [], $limit, $user)->assertOk();

    test()->actingAs($user)->getJson('/api/user')->assertTooManyRequests();
});

/**
 * Send the same JSON request several times and return the last response.
 *
 * @param  array<string, string>  $payload
 */
function repeatJson(string $method, string $uri, array $payload, int $times, ?User $user = null): TestResponse
{
    $response = null;

    for ($attempt = 0; $attempt < $times; $attempt++) {
        $pending = $user === null ? test() : test()->actingAs($user);
        $response = $pending->json($method, $uri, $payload);
    }

    return $response;
}
