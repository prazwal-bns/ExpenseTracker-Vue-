<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('requires authentication for the profile endpoints', function (string $method, string $uri) {
    $this->json($method, $uri)->assertUnauthorized();
})->with([
    'show' => ['GET', '/api/profile'],
    'update' => ['PUT', '/api/profile'],
    'password' => ['PUT', '/api/profile/password'],
    'destroy' => ['DELETE', '/api/profile'],
]);

it('shows the authenticated user profile with activity counts', function () {
    $user = User::factory()->create(['name' => 'Alex Rivera']);
    $category = Category::factory()->for($user)->create();
    Expense::factory()->count(3)->for($user)->for($category)->create();

    $this->actingAs($user)
        ->getJson('/api/profile')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', 'Alex Rivera')
        ->assertJsonPath('data.role', 'user')
        ->assertJsonPath('data.categories_count', 1)
        ->assertJsonPath('data.expenses_count', 3);
});

it('updates the name and email', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson('/api/profile', [
            'name' => 'Alex Updated',
            'email' => 'alex.updated@example.com',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Alex Updated')
        ->assertJsonPath('data.email', 'alex.updated@example.com');

    expect($user->fresh())
        ->name->toBe('Alex Updated')
        ->email->toBe('alex.updated@example.com');
});

it('validates the profile update', function () {
    $user = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($user)
        ->putJson('/api/profile', [
            'name' => '',
            'email' => 'taken@example.com',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email' => 'The email has already been taken.']);
});

it('keeps the current email when it is submitted unchanged', function () {
    $user = User::factory()->create(['email' => 'alex@example.com']);

    $this->actingAs($user)
        ->putJson('/api/profile', [
            'name' => 'Alex',
            'email' => 'alex@example.com',
        ])
        ->assertOk();
});

it('changes the password and signs out other devices', function () {
    $user = User::factory()->create(['password' => 'old-password']);
    $currentToken = $user->createToken('current')->plainTextToken;
    $user->createToken('other-device');

    $this->withToken($currentToken)
        ->putJson('/api/profile/password', [
            'current_password' => 'old-password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])
        ->assertOk()
        ->assertJsonPath('message', 'Password updated successfully.');

    expect(Hash::check('new-secret-password', $user->fresh()->password))->toBeTrue()
        ->and($user->tokens()->pluck('name')->all())->toBe(['current']);
});

it('rejects a password change with the wrong current password', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    $this->actingAs($user)
        ->putJson('/api/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['current_password']);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

it('requires the new password to be confirmed and different', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    $this->actingAs($user)
        ->putJson('/api/profile/password', [
            'current_password' => 'old-password',
            'password' => 'old-password',
            'password_confirmation' => 'something-else',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('deletes the account with its categories and expenses', function () {
    $user = User::factory()->create(['password' => 'secret-password']);
    $category = Category::factory()->for($user)->create();
    $expense = Expense::factory()->for($user)->for($category)->create();

    $this->actingAs($user)
        ->deleteJson('/api/profile', ['password' => 'secret-password'])
        ->assertOk()
        ->assertJsonPath('message', 'Account deleted successfully.');

    $this->assertModelMissing($user);
    $this->assertModelMissing($category);
    $this->assertModelMissing($expense);
});

it('requires the correct password to delete the account', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    $this->actingAs($user)
        ->deleteJson('/api/profile', ['password' => 'wrong-password'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

    $this->assertModelExists($user);
});

it('prevents the only admin from deleting their account', function () {
    $admin = User::factory()->admin()->create(['password' => 'secret-password']);

    $this->actingAs($admin)
        ->deleteJson('/api/profile', ['password' => 'secret-password'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'You are the only admin. Make someone else an admin before deleting your account.');

    $this->assertModelExists($admin);
});

it('lets an admin delete their account when another admin exists', function () {
    $admin = User::factory()->admin()->create(['password' => 'secret-password']);
    User::factory()->admin()->create();

    $this->actingAs($admin)
        ->deleteJson('/api/profile', ['password' => 'secret-password'])
        ->assertOk();

    $this->assertModelMissing($admin);
});
