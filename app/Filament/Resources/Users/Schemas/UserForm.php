<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Services\UserService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('username')
                    ->label('NRP/NIP/No. Anggota')
                    ->required()
                    ->maxLength(50)
                    ->alphaDash()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? strtoupper(trim($state)) : null)
                    ->helperText('Digunakan untuk login. Contoh: 76010123'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->required(fn ($livewire): bool => $livewire instanceof CreateUser)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255),
                Select::make('role')
                    ->label('Hak Akses')
                    ->options(fn (): array => self::roleOptions())
                    ->required(fn ($livewire): bool => $livewire instanceof CreateUser)
                    ->native(false)
                    ->searchable(),
            ]);
    }

    /**
     * @return array<string, string>
     */
    private static function roleOptions(): array
    {
        $actor = Auth::user();

        if ($actor === null) {
            return [];
        }

        return collect(app(UserService::class)->assignableRolesFor($actor))
            ->mapWithKeys(static fn (UserRole $role): array => [
                $role->value => $role->label(),
            ])
            ->all();
    }
}
