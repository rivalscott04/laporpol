<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\LoginUserProvider;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        app()->setLocale('id');

        Auth::provider('login_user', function ($app, array $config): LoginUserProvider {
            return new LoginUserProvider($app['hash'], $config['model']);
        });

        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->hasRole(UserRole::SuperAdmin->value)) {
                return true;
            }

            return null;
        });
    }
}
