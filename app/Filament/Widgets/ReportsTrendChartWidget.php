<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Permission;
use App\Services\ReportStatisticsService;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ReportsTrendChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Perkembangan Laporan';

    protected ?string $description = 'Jumlah laporan 7 hari terakhir';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->can(Permission::ReportsView->value) ?? false;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $dailyCounts = app(ReportStatisticsService::class)->dailyTrendForAuthenticatedUser();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Laporan',
                    'data' => array_values($dailyCounts),
                    'fill' => 'start',
                ],
            ],
            'labels' => array_map(
                static fn (string $date): string => CarbonImmutable::parse($date)->format('d M'),
                array_keys($dailyCounts),
            ),
        ];
    }
}
