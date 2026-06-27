<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports;

use App\Enums\Permission;
use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Filament\Resources\Reports\Pages\EditReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Filament\Resources\Reports\Pages\ViewReport;
use App\Filament\Resources\Reports\Schemas\ReportForm;
use App\Filament\Resources\Reports\Schemas\ReportInfolist;
use App\Filament\Resources\Reports\Tables\ReportsTable;
use App\Models\Report;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Daftar Laporan';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $modelLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'location_name';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return ReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
            'create' => CreateReport::route('/create'),
            'view' => ViewReport::route('/{record}'),
            'edit' => EditReport::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<Report>
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with('user');

        $user = Auth::user();

        if ($user !== null && ! $user->can(Permission::UsersView->value)) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
