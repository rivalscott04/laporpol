<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\DTOs\User\UpdateUserData;
use App\Filament\Concerns\HasBackNavigation;
use App\Filament\Resources\Users\UserResource;
use App\Services\UserService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    use HasBackNavigation;
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makeBackAction('Kembali ke Daftar'),
            DeleteAction::make()
                ->action(function (): void {
                    app(UserService::class)->delete($this->getRecord());

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role'] = $this->getRecord()->roles->first()?->name;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UserService::class)->update(
            $record,
            UpdateUserData::fromArray($data),
        );
    }
}
