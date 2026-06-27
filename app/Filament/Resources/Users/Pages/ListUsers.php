<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Imports\UserImporter;
use App\Filament\Resources\Users\UserResource;
use App\Support\UserImportTemplate;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadImportTemplate')
                ->label('Unduh Template Impor')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn (): StreamedResponse => UserImportTemplate::download()),
            ImportAction::make()
                ->importer(UserImporter::class)
                ->label('Impor Akun Massal')
                ->modalHeading('Impor Akun Massal')
                ->modalDescription('Unduh template terlebih dahulu, isi data petugas, lalu unggah berkas CSV. Kolom wajib: nama, NRP/NIP, kata sandi, dan hak akses (user, admin, atau super_admin).'),
            CreateAction::make()
                ->label('Tambah Pengguna'),
        ];
    }
}
