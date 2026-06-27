<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\DTOs\User\CreateUserData;
use App\Filament\Concerns\HasBackNavigation;
use App\Filament\Resources\Users\UserResource;
use App\Services\UserService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    use HasBackNavigation;
    protected static string $resource = UserResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Tambah Pengguna';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah Pengguna';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->makeBackAction('Kembali ke Daftar'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return app(UserService::class)->create(CreateUserData::fromArray($data));
    }
}
