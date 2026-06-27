<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Tables;

use App\Enums\Permission;
use App\Filament\Resources\Reports\ReportResource;
use App\Models\Report;
use App\Services\ReportService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        $canSearchFilter = self::canUseAdvancedFilters();

        return $table
            ->columns(self::columns($canSearchFilter))
            ->defaultSort('reported_at', 'desc')
            ->recordUrl(fn (Report $record): string => ReportResource::getUrl('view', ['record' => $record->getKey()]))
            ->filters(self::filters($canSearchFilter))
            ->recordActions(self::recordActions())
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function configureForRecap(Table $table): Table
    {
        return $table
            ->columns(self::recapColumns())
            ->defaultSort('reported_at', 'desc')
            ->recordUrl(fn (Report $record): string => ReportResource::getUrl('view', ['record' => $record->getKey()]))
            ->recordActions(self::recordActions())
            ->paginated([10, 25, 50]);
    }

    /**
     * @return array<int, mixed>
     */
    private static function columns(bool $canSearchFilter): array
    {
        $columns = [
            TextColumn::make('reported_at')
                ->label('Tanggal')
                ->date()
                ->sortable(),
            TextColumn::make('location_name')
                ->label('Lokasi')
                ->searchable()
                ->sortable(),
        ];

        if ($canSearchFilter) {
            $columns[] = TextColumn::make('notes')
                ->label('Catatan')
                ->searchable()
                ->limit(40)
                ->toggleable();
            $columns[] = TextColumn::make('user.name')
                ->label('Petugas')
                ->searchable()
                ->sortable();
        } else {
            $columns[] = TextColumn::make('user.name')
                ->label('Petugas')
                ->sortable()
                ->toggleable();
        }

        $columns = array_merge($columns, [
            ImageColumn::make('photo_path')
                ->label('Foto')
                ->disk((string) config('reports.photo_disk', 'local'))
                ->square()
                ->toggleable(),
            TextColumn::make('attachment_path')
                ->label('Lampiran')
                ->formatStateUsing(fn (?string $state): string => $state !== null ? 'Ada' : '-')
                ->badge()
                ->color(fn (?string $state): string => $state !== null ? 'success' : 'gray')
                ->toggleable(),
            TextColumn::make('latitude')
                ->label('Garis Lintang')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('longitude')
                ->label('Garis Bujur')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('created_at')
                ->label('Dicatat pada')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ]);

        return $columns;
    }

    /**
     * @return array<int, mixed>
     */
    private static function recapColumns(): array
    {
        return [
            TextColumn::make('reported_at')
                ->label('Tanggal')
                ->date()
                ->sortable(),
            TextColumn::make('location_name')
                ->label('Lokasi')
                ->searchable()
                ->sortable(),
            TextColumn::make('user.name')
                ->label('Petugas')
                ->sortable(),
            TextColumn::make('notes')
                ->label('Catatan')
                ->limit(50)
                ->toggleable(),
            TextColumn::make('attachment_path')
                ->label('Lampiran')
                ->formatStateUsing(fn (?string $state): string => $state !== null ? 'Ada' : '-')
                ->badge()
                ->color(fn (?string $state): string => $state !== null ? 'success' : 'gray'),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private static function recordActions(): array
    {
        return [
            ViewAction::make()
                ->label('Detail'),
            EditAction::make()
                ->label('Ubah')
                ->visible(fn (Report $record): bool => ReportResource::canEdit($record)),
            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Laporan')
                ->modalDescription('Laporan akan dipindahkan ke arsip. Anda masih bisa memulihkannya nanti.')
                ->successNotificationTitle('Laporan dihapus')
                ->visible(fn (Report $record): bool => ReportResource::canDelete($record))
                ->action(fn (Report $record): mixed => app(ReportService::class)->delete($record)),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private static function filters(bool $canSearchFilter): array
    {
        $filters = [
            TrashedFilter::make()
                ->label('Laporan terhapus'),
        ];

        if (! $canSearchFilter) {
            return $filters;
        }

        return [
            ...$filters,
            Filter::make('reported_at')
                ->label('Tanggal Laporan')
                ->schema([
                    DatePicker::make('reported_from')
                        ->label('Dari'),
                    DatePicker::make('reported_until')
                        ->label('Sampai'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['reported_from'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('reported_at', '>=', $date),
                        )
                        ->when(
                            $data['reported_until'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('reported_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['reported_from'] ?? null) {
                        $indicators[] = Indicator::make('Dari '.$data['reported_from'])
                            ->removeField('reported_from');
                    }

                    if ($data['reported_until'] ?? null) {
                        $indicators[] = Indicator::make('Sampai '.$data['reported_until'])
                            ->removeField('reported_until');
                    }

                    return $indicators;
                }),
            SelectFilter::make('user_id')
                ->label('Petugas')
                ->relationship('user', 'name')
                ->searchable()
                ->preload(),
            Filter::make('has_attachment')
                ->label('Memiliki Lampiran')
                ->toggle()
                ->query(fn (Builder $query): Builder => $query->whereNotNull('attachment_path')),
        ];
    }

    private static function canUseAdvancedFilters(): bool
    {
        $user = Auth::user();

        return $user?->can(Permission::ReportsSearchFilter->value) ?? false;
    }
}
