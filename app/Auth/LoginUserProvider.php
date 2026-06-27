<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class LoginUserProvider extends EloquentUserProvider
{
    /**
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        if (blank($credentials['login'] ?? null)) {
            return null;
        }

        $login = (string) $credentials['login'];

        return User::query()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->first();
    }
}
