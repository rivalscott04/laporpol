<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable|null
    {
        return config('branding.full_name');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Masuk ke akun Anda';
    }
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Email atau NRP/NIP')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'login' => trim((string) $data['login']),
            'password' => $data['password'],
        ];
    }
}
