<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::UsersView->value);
    }

    public function view(User $user, User $model): bool
    {
        return $user->can(Permission::UsersView->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::UsersCreate->value);
    }

    public function update(User $user, User $model): bool
    {
        if ($user->can(Permission::UsersUpdate->value)) {
            return true;
        }

        return $user->can(Permission::ProfileUpdate->value) && $user->is($model);
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->can(Permission::UsersDelete->value)) {
            return false;
        }

        return ! $user->is($model);
    }
}
