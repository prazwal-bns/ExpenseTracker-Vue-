<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * List every user with their activity counts.
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()
            ->withCount(['categories', 'expenses'])
            ->orderBy('name')
            ->get();

        return UserResource::collection($users);
    }

    /**
     * Create a user with the given role.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $user->role = UserRole::from($request->string('role')->toString());
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->loadCount(['categories', 'expenses']);

        return UserResource::make($user)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update a user's details or role.
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();

        $user->fill(array_filter(
            [
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'password' => $data['password'] ?? null,
            ],
            fn (?string $value): bool => $value !== null,
        ));

        if ($request->has('role')) {
            $user->role = UserRole::from($request->string('role')->toString());
        }

        $user->save();

        return UserResource::make($user->loadCount(['categories', 'expenses']));
    }

    /**
     * Delete a user together with their categories and expenses.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->is($user)) {
            return response()->json([
                'message' => 'You cannot delete your own account from the admin panel.',
            ], 422);
        }

        DB::transaction(function () use ($user): void {
            $user->expenses()->delete();
            $user->categories()->delete();
            $user->tokens()->delete();
            $user->delete();
        });

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }
}
