<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\UserService;
use App\Support\UserImportTemplate;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama')
                ->requiredMapping()
                ->example('Bripka Ahmad Wijaya')
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('username')
                ->label('NRP/NIP/No. Anggota')
                ->requiredMapping()
                ->example('77010123')
                ->castStateUsing(fn (?string $state): ?string => filled($state) ? strtoupper(trim($state)) : null)
                ->rules([
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[A-Za-z0-9._-]+$/',
                ]),
            ImportColumn::make('email')
                ->label('Email')
                ->example('ahmad.wijaya@laporanpol.test')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('password')
                ->label('Kata Sandi')
                ->requiredMapping()
                ->example('Password123!')
                ->rules(['required', 'string', Password::defaults()]),
            ImportColumn::make('role')
                ->label('Hak Akses')
                ->requiredMapping()
                ->example('user')
                ->examples(['user', 'admin'])
                ->fillRecordUsing(fn (): null => null)
                ->rules(['required', 'string', new Enum(UserRole::class)]),
        ];
    }

    public function resolveRecord(): ?User
    {
        return User::query()->firstOrNew([
            'username' => $this->data['username'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $failed = number_format($import->getFailedRowsCount());

        return "Import akun selesai. {$import->successful_rows} berhasil, {$failed} gagal.";
    }

    protected function beforeValidate(): void
    {
        $this->data['username'] = isset($this->data['username'])
            ? strtoupper(trim((string) $this->data['username']))
            : null;
    }

    protected function beforeFill(): void
    {
        $this->assertAssignableRole();
        $this->record->email_verified_at = now();
    }

    protected function afterSave(): void
    {
        if (filled($this->data['role'] ?? null)) {
            $this->record->syncRoles([(string) $this->data['role']]);
        }
    }

    public function getValidationRules(): array
    {
        $rules = parent::getValidationRules();

        $rules['username'][] = Rule::unique('users', 'username')->ignore($this->record);
        $rules['email'][] = Rule::unique('users', 'email')->ignore($this->record);

        return $rules;
    }

    private function assertAssignableRole(): void
    {
        $actor = Auth::user();

        if ($actor === null) {
            throw new RowImportFailedException('Pengguna tidak terautentikasi.');
        }

        $role = UserRole::from((string) ($this->data['role'] ?? ''));

        if (! in_array($role, app(UserService::class)->assignableRolesFor($actor), true)) {
            throw new RowImportFailedException("Hak akses [{$role->value}] tidak diizinkan untuk akun Anda.");
        }
    }
}
