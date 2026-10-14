<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('requires authentication for the admin users endpoints', function () {
    $this->getJson('/api/admin/users')->assertUnauthorized();
});

it('forbids non-admin users from the admin users endpoints', function (string $method, string $uri) {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($user)
        ->json($method, str_replace('{user}', (string) $target->id, $uri), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ])
        ->assertForbidden();

    expect($target->fresh())->not->toBeNull()
        ->and($target->fresh()->role)->toBe(UserRole::User);
})->with([
    'index' => ['GET', '/api/admin/users'],
    'store' => ['POST', '/api/admin/users'],
    'update' => ['PUT', '/api/admin/users/{user}'],
    'destroy' => ['DELETE', '/api/admin/users/{user}'],
]);

it('lists every user with their role and activity counts', function () {
    $admin = User::factory()->admin()->create(['name' => 'Alex Admin']);
    $member = User::factory()->create(['name' => 'Sam Member']);
    $category = Category::factory()->for($member)->create();
    Expense::factory()->count(2)->for($member)->for($category)->create();

    $this->actingAs($admin)
        ->getJson('/api/admin/users')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'Alex Admin')
        ->assertJsonPath('data.0.role', 'admin')
        ->assertJsonPath('data.1.name', 'Sam Member')
        ->assertJsonPath('data.1.role', 'user')
        ->assertJsonPath('data.1.categories_count', 1)
        ->assertJsonPath('data.1.expenses_count', 2);
});

it('creates a user with the chosen role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->postJson('/api/admin/users', [
            'name' => 'Maya Patel',
            'email' => 'maya@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'role' => 'admin',
        ])
        ->assertCreated()
        ->assertJsonPath('data.email', 'maya@example.com')
        ->assertJsonPath('data.role', 'admin');

    $created = User::query()->where('email', 'maya@example.com')->firstOrFail();

    expect($created->role)->toBe(UserRole::Admin)
        ->and(Hash::check('secret-password', $created->password))->toBeTrue();
});

it('requires every field when creating a user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->postJson('/api/admin/users', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
});

it('rejects an unknown role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->postJson('/api/admin/users', [
            'name' => 'Maya Patel',
            'email' => 'maya@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'role' => 'superuser',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role' => 'The selected role is invalid.']);
});

it('rejects a duplicate email when creating a user', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin)
        ->postJson('/api/admin/users', [
            'name' => 'Duplicate',
            'email' => 'taken@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'role' => 'user',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'The email has already been taken.']);
});

it('updates a user and promotes them to admin', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create(['name' => 'Sam Chen']);

    $this->actingAs($admin)
        ->putJson("/api/admin/users/{$member->id}", [
            'name' => 'Sam Updated',
            'role' => 'admin',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Sam Updated')
        ->assertJsonPath('data.role', 'admin');

    expect($member->fresh())
        ->name->toBe('Sam Updated')
        ->role->toBe(UserRole::Admin);
});

it('keeps the current password when the update leaves it blank', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $originalHash = $member->password;

    $this->actingAs($admin)
        ->putJson("/api/admin/users/{$member->id}", [
            'name' => 'Renamed',
            'password' => null,
        ])
        ->assertOk();

    expect($member->fresh()->password)->toBe($originalHash);
});

it('prevents an admin from removing their own admin role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->putJson("/api/admin/users/{$admin->id}", ['role' => 'user'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role' => 'You cannot remove your own admin role.']);

    expect($admin->fresh()->role)->toBe(UserRole::Admin);
});

it('deletes a user together with their categories and expenses', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $category = Category::factory()->for($member)->create();
    $expense = Expense::factory()->for($member)->for($category)->create();

    $this->actingAs($admin)
        ->deleteJson("/api/admin/users/{$member->id}")
        ->assertOk()
        ->assertJsonPath('message', 'User deleted successfully.');

    $this->assertModelMissing($member);
    $this->assertModelMissing($category);
    $this->assertModelMissing($expense);
});

it('prevents an admin from deleting their own account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->deleteJson("/api/admin/users/{$admin->id}")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'You cannot delete your own account from the admin panel.');

    $this->assertModelExists($admin);
});

it('ignores a role sent during registration', function () {
    $this->postJson('/api/register', [
        'name' => 'Sneaky',
        'email' => 'sneaky@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ])
        ->assertCreated()
        ->assertJsonPath('user.role', 'user');

    expect(User::query()->where('email', 'sneaky@example.com')->firstOrFail()->role)
        ->toBe(UserRole::User);
});

it('ignores a role sent to the self-service profile update', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson('/api/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'admin',
        ])
        ->assertOk();

    expect($user->fresh()->role)->toBe(UserRole::User);
});
