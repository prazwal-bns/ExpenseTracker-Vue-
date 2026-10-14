<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\DeleteProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile with activity counts.
     */
    public function show(Request $request): UserResource
    {
        return UserResource::make(
            $request->user()->loadCount(['categories', 'expenses'])
        );
    }

    /**
     * Update the authenticated user's name and email.
     */
    public function update(UpdateProfileRequest $request): UserResource
    {
        $user = $request->user();
        $user->update($request->validated());

        return UserResource::make($user->loadCount(['categories', 'expenses']));
    }

    /**
     * Change the password and sign out every other device.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update(['password' => $request->validated('password')]);

        $currentToken = $user->currentAccessToken();

        $user->tokens()
            ->when(
                $currentToken instanceof PersonalAccessToken,
                fn ($query) => $query->whereKeyNot($currentToken->getKey()),
            )
            ->delete();

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * Permanently delete the authenticated user's account and data.
     */
    public function destroy(DeleteProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isAdmin() && User::query()->where('role', UserRole::Admin)->count() === 1) {
            return response()->json([
                'message' => 'You are the only admin. Make someone else an admin before deleting your account.',
            ], 422);
        }

        DB::transaction(function () use ($user): void {
            $user->expenses()->delete();
            $user->categories()->delete();
            $user->tokens()->delete();
            $user->delete();
        });

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }
}
