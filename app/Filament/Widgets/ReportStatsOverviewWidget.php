<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Permission;
use App\Filament\Resources\Reports\ReportResource;
use App\Services\ReportStatisticsService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ReportStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan Laporan';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->can(Permission::ReportsView->value) ?? false;
    }

    protected function getStats(): array
    {
        $statistics = app(ReportStatisticsService::class)->forAuthenticatedUser();
        $scopeLabel = $statistics->isGlobalScope ? 'Semua laporan' : 'Laporan Anda';

        return [
            Stat::make('Total Laporan', $statistics->total)
                ->description($scopeLabel)
                ->descriptionIcon(Heroicon::OutlinedDocumentText)
                ->color('primary')
                ->url(ReportResource::getUrl('index')),
            Stat::make('Hari Ini', $statistics->today)
                ->description('Laporan hari ini')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('success'),
            Stat::make('Minggu Ini', $statistics->thisWeek)
                ->description('Minggu berjalan')
                ->descriptionIcon(Heroicon::OutlinedCalendar)
                ->color('info'),
            Stat::make('Bulan Ini', $statistics->thisMonth)
                ->description('Bulan berjalan')
                ->descriptionIcon(Heroicon::OutlinedChartBar)
                ->color('warning'),
        ];
    }
}
