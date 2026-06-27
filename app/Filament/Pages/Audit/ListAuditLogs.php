<?php

declare(strict_types=1);

namespace App\Filament\Pages\Audit;

use App\Enums\Permission;
use App\Models\Report;
use App\Models\User;
use App\Support\ActivityLogLabels;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ListAuditLogs extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Riwayat Aktivitas';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 98;

    protected static ?string $slug = 'audit-log';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can(Permission::AuditLogView->value);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Riwayat Aktivitas';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Catatan perubahan akun pengguna dan laporan di sistem.';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->auditLogQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('event')
                    ->label('Jenis Perubahan')
                    ->formatStateUsing(fn (?string $state): string => ActivityLogLabels::event($state))
                    ->badge()
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('Dilakukan Oleh')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('Bagian')
                    ->formatStateUsing(fn (?string $state): string => ActivityLogLabels::subjectType($state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label('Jenis Perubahan')
                    ->options([
                        'created' => 'Baru ditambahkan',
                        'updated' => 'Diubah',
                        'deleted' => 'Dihapus',
                    ]),
                SelectFilter::make('subject_type')
                    ->label('Bagian')
                    ->options([
                        User::class => 'Akun pengguna',
                        Report::class => 'Laporan',
                    ]),
            ])
            ->emptyStateHeading('Belum ada aktivitas tercatat')
            ->emptyStateDescription('Perubahan pada akun pengguna dan laporan akan muncul di sini.')
            ->paginated([25, 50, 100]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return $this->auditLogQuery();
    }

    private function auditLogQuery(): Builder
    {
        return Activity::query()
            ->with('causer')
            ->latest('created_at');
    }
}
