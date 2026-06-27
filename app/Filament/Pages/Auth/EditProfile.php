<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\DTOs\User\UpdateProfileData;
use App\Services\UserService;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends BaseEditProfile
{
    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('NRP/NIP/No. Anggota')
            ->disabled()
            ->dehydrated(false);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getUsernameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UserService::class)->updateProfile(
            $record,
            UpdateProfileData::fromArray($data),
        );
    }
}
