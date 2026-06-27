<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\User\CreateUserData;
use App\DTOs\User\UpdateProfileData;
use App\DTOs\User\UpdateUserData;
use App\Enums\UserRole;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function create(CreateUserData $data): User
    {
        StoreUserRequest::validatePayload([
            'name' => $data->name,
            'username' => $data->username,
            'email' => $data->email,
            'password' => $data->password,
            'role' => $data->role->value,
        ]);

        return DB::transaction(function () use ($data): User {
            $user = $this->userRepository->create([
                'name' => $data->name,
                'username' => $data->username,
                'email' => $data->email,
                'password' => $data->password,
                'email_verified_at' => now(),
            ]);

            $user->assignRole($data->role->value);

            return $user;
        });
    }

    public function update(User $user, UpdateUserData $data): User
    {
        UpdateUserRequest::validatePayload([
            'name' => $data->name,
            'username' => $data->username,
            'email' => $data->email,
            'password' => $data->password,
            'role' => $data->role?->value,
            'user_id' => $user->id,
        ]);

        return DB::transaction(function () use ($user, $data): User {
            $attributes = [
                'name' => $data->name,
                'username' => $data->username,
                'email' => $data->email,
            ];

            if ($data->password !== null) {
                $attributes['password'] = $data->password;
            }

            $updatedUser = $this->userRepository->update($user, $attributes);

            if ($data->role !== null) {
                $updatedUser->syncRoles([$data->role->value]);
            }

            return $updatedUser;
        });
    }

    public function updateProfile(User $user, UpdateProfileData $data): User
    {
        UpdateProfileRequest::validatePayload([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        $attributes = [
            'name' => $data->name,
            'email' => $data->email,
        ];

        if ($data->password !== null) {
            $attributes['password'] = $data->password;
        }

        return $this->userRepository->update($user, $attributes);
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->syncRoles([]);
            $this->userRepository->delete($user);
        });
    }

    /**
     * @return list<UserRole>
     */
    public function assignableRolesFor(User $actor): array
    {
        if ($actor->hasRole(UserRole::SuperAdmin->value)) {
            return UserRole::cases();
        }

        if ($actor->hasRole(UserRole::Admin->value)) {
            return [UserRole::Admin, UserRole::User];
        }

        return [];
    }
}
