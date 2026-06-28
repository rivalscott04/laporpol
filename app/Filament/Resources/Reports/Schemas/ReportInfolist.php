<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Schemas;

use App\Enums\Permission;
use App\Models\Report;
use App\Support\ReportMediaUrl;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Ringkasan Laporan')
                    ->description('Informasi pelapor, lokasi kejadian, dan catatan kegiatan.')
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2, 'lg' => 3])
                            ->schema([
                                TextEntry::make('reported_at')
                                    ->label('Tanggal Laporan')
                                    ->date('d M Y'),
                                TextEntry::make('user.name')
                                    ->label('Petugas')
                                    ->visible(fn (): bool => self::canViewOfficerDetails()),
                                TextEntry::make('user.username')
                                    ->label('NRP/NIP')
                                    ->visible(fn (): bool => self::canViewOfficerDetails()),
                            ]),
                        TextEntry::make('location_name')
                            ->label('Nama Lokasi')
                            ->icon(Heroicon::OutlinedMapPin),
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                TextEntry::make('latitude')
                                    ->label('Garis Lintang')
                                    ->copyable(),
                                TextEntry::make('longitude')
                                    ->label('Garis Bujur')
                                    ->copyable(),
                            ]),
                        TextEntry::make('google_maps')
                            ->label('Peta')
                            ->state('Buka di Google Maps')
                            ->url(fn (Report $record): string => sprintf(
                                'https://www.google.com/maps?q=%s,%s',
                                $record->latitude,
                                $record->longitude,
                            ))
                            ->openUrlInNewTab()
                            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                            ->color('primary'),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tidak ada catatan.'),
                    ]),
                Section::make('Bukti Laporan')
                    ->description('Pratinjau foto dan dokumen PDF laporan.')
                    ->schema([
                        TextEntry::make('media_empty')
                            ->hiddenLabel()
                            ->state('Belum ada foto atau lampiran PDF pada laporan ini.')
                            ->icon(Heroicon::OutlinedPaperClip)
                            ->color('gray')
                            ->visible(fn (Report $record): bool => blank($record->photo_path) && blank($record->attachment_path)),
                        Grid::make(['default' => 1, 'md' => 2])
                            ->visible(fn (Report $record): bool => filled($record->photo_path) || filled($record->attachment_path))
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        ImageEntry::make('photo_path')
                                            ->label('Foto')
                                            ->disk((string) config('reports.photo_disk', 'local'))
                                            ->imageHeight(180)
                                            ->visible(fn (Report $record): bool => filled($record->photo_path)),
                                        TextEntry::make('photo_empty')
                                            ->label('Foto')
                                            ->state('Belum diunggah')
                                            ->icon(Heroicon::OutlinedPhoto)
                                            ->color('gray')
                                            ->visible(fn (Report $record): bool => blank($record->photo_path)),
                                        Actions::make([
                                            self::previewPhotoAction(),
                                        ])
                                            ->visible(fn (Report $record): bool => filled($record->photo_path)),
                                    ]),
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('attachment_path')
                                            ->label('Lampiran PDF')
                                            ->formatStateUsing(fn (?string $state): string => filled($state)
                                                ? basename($state)
                                                : 'Belum diunggah')
                                            ->icon(fn (?string $state): Heroicon => filled($state)
                                                ? Heroicon::OutlinedDocumentText
                                                : Heroicon::OutlinedDocument)
                                            ->color(fn (?string $state): string => filled($state) ? 'primary' : 'gray'),
                                        Actions::make([
                                            self::previewAttachmentAction(),
                                            self::downloadAttachmentAction(),
                                        ])
                                            ->visible(fn (Report $record): bool => filled($record->attachment_path)),
                                    ]),
                            ]),
                    ]),
                Section::make('Riwayat Sistem')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dicatat pada')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Terakhir diubah')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('deleted_at')
                            ->label('Dihapus pada')
                            ->dateTime('d M Y H:i')
                            ->color('danger')
                            ->visible(fn (Report $record): bool => $record->trashed()),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    private static function previewPhotoAction(): Action
    {
        return Action::make('previewPhoto')
            ->label('Pratinjau Foto')
            ->icon(Heroicon::OutlinedPhoto)
            ->color('gray')
            ->modalHeading('Foto Laporan')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::FourExtraLarge)
            ->modalContent(fn (Report $record) => view('filament.reports.modals.photo-preview', [
                'url' => ReportMediaUrl::photo($record),
            ]));
    }

    private static function previewAttachmentAction(): Action
    {
        return Action::make('previewAttachment')
            ->label('Pratinjau PDF')
            ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
            ->color('gray')
            ->modalHeading(fn (Report $record): string => filled($record->attachment_path)
                ? basename($record->attachment_path)
                : 'Lampiran PDF')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalWidth(Width::Screen)
            ->extraModalWindowAttributes([
                'class' => 'fi-modal-pdf-preview',
            ])
            ->modalContent(fn (Report $record) => view('filament.reports.modals.pdf-preview', [
                'viewerUrl' => ReportMediaUrl::attachment($record),
                'openUrl' => ReportMediaUrl::attachment($record),
                'downloadUrl' => ReportMediaUrl::attachmentDownload($record),
            ]));
    }

    private static function downloadAttachmentAction(): Action
    {
        return Action::make('downloadAttachment')
            ->label('Unduh PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('gray')
            ->url(fn (Report $record): ?string => ReportMediaUrl::attachmentDownload($record))
            ->openUrlInNewTab();
    }

    private static function canViewOfficerDetails(): bool
    {
        $user = Auth::user();

        return $user?->can(Permission::UsersView->value) ?? false;
    }
}
