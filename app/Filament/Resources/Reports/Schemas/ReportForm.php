<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Schemas;

use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Services\SettingService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(['default' => 1, 'xl' => 2])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Informasi Laporan')
                            ->description('Tanggal, lokasi, dan catatan kegiatan.')
                            ->schema([
                                DatePicker::make('reported_at')
                                    ->label('Tanggal')
                                    ->required()
                                    ->native(false)
                                    ->default(now()->toDateString()),
                                TextInput::make('location_name')
                                    ->label('Nama Lokasi')
                                    ->placeholder('Contoh: Monas, Jakarta Pusat')
                                    ->helperText('Tulis nama tempat agar mudah dikenali saat dibaca laporan.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Section::make('Titik Lokasi')
                                    ->description('Koordinat diambil otomatis dari browser. Klik "Ambil Lokasi" jika belum terisi atau ingin memperbarui.')
                                    ->schema([
                                        View::make('filament.reports.components.geolocation-capture')
                                            ->columnSpanFull(),
                                        TextInput::make('latitude')
                                            ->label('Garis Lintang')
                                            ->placeholder('Menunggu lokasi…')
                                            ->helperText('Diisi otomatis setelah izin lokasi diberikan.')
                                            ->required()
                                            ->numeric()
                                            ->readOnly()
                                            ->step('0.0000001')
                                            ->minValue(-90)
                                            ->maxValue(90),
                                        TextInput::make('longitude')
                                            ->label('Garis Bujur')
                                            ->placeholder('Menunggu lokasi…')
                                            ->helperText('Diisi otomatis setelah izin lokasi diberikan.')
                                            ->required()
                                            ->numeric()
                                            ->readOnly()
                                            ->step('0.0000001')
                                            ->minValue(-180)
                                            ->maxValue(180),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                Textarea::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('Contoh: Patroli rutin area parkir, kondisi aman.')
                                    ->rows(5)
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Bukti Laporan')
                            ->description('Unggah foto lokasi dan dokumen PDF pendukung.')
                            ->schema([
                                FileUpload::make('photo')
                                    ->label('Foto')
                                    ->helperText(fn (): string => 'Unggah foto yang sudah diberi cap waktu dan lokasi dari ponsel Anda. Ukuran maksimal '
                                        .app(SettingService::class)->formatMaxSize(app(SettingService::class)->photoMaxKb()).'.')
                                    ->image()
                                    ->disk((string) config('reports.photo_disk', 'local'))
                                    ->directory((string) config('reports.photo_upload_directory', 'reports/photos/uploads'))
                                    ->required(fn ($livewire): bool => $livewire instanceof CreateReport)
                                    ->maxSize(fn (): int => app(SettingService::class)->photoMaxKb())
                                    ->imagePreviewHeight('220')
                                    ->columnSpanFull(),
                                FileUpload::make('attachment')
                                    ->label('Lampiran (PDF)')
                                    ->helperText(fn (): string => 'Wajib diunggah dalam format PDF. Ukuran maksimal '
                                        .app(SettingService::class)->formatMaxSize(app(SettingService::class)->attachmentMaxKb()).'.')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->disk((string) config('reports.attachment_disk', 'local'))
                                    ->directory((string) config('reports.attachment_upload_directory', 'reports/attachments/uploads'))
                                    ->required(fn ($livewire): bool => $livewire instanceof CreateReport)
                                    ->maxSize(fn (): int => app(SettingService::class)->attachmentMaxKb())
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
