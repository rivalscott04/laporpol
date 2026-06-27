<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Enums\Permission;
use App\Models\Report;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class ReportExporter extends Exporter
{
    protected static ?string $model = Report::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reported_at')
                ->label('Tanggal'),
            ExportColumn::make('location_name')
                ->label('Lokasi'),
            ExportColumn::make('user.name')
                ->label('Petugas'),
            ExportColumn::make('user.username')
                ->label('NRP/NIP'),
            ExportColumn::make('latitude')
                ->label('Garis Lintang'),
            ExportColumn::make('longitude')
                ->label('Garis Bujur'),
            ExportColumn::make('notes')
                ->label('Catatan'),
            ExportColumn::make('attachment_path')
                ->label('Lampiran')
                ->formatStateUsing(fn (?string $state): string => $state !== null ? 'Ada' : '-'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user !== null && ! $user->can(Permission::UsersView->value)) {
            $query->where('user_id', $user->id);
        }

        return $query->with('user');
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Pengunduhan laporan selesai. '.Number::format($export->successful_rows).' baris berhasil disimpan.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' baris gagal.';
        }

        return $body;
    }
}
