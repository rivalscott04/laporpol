<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Report\ReportRecapData;
use App\Enums\ReportRecapPeriod;
use App\Repositories\ReportRepository;
use Carbon\CarbonImmutable;

class ReportRecapService
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
    ) {}

    public function recap(ReportRecapPeriod $period, CarbonImmutable $referenceDate): ReportRecapData
    {
        [$start, $end] = $this->dateRange($period, $referenceDate);

        return new ReportRecapData(
            period: $period,
            start: $start,
            end: $end,
            total: $this->reportRepository->countBetweenDates(null, $start, $end),
            breakdown: $this->reportRepository->dailyCounts(null, $start, $end),
            periodLabel: $this->formatPeriodLabel($period, $start, $end),
        );
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    public function dateRange(ReportRecapPeriod $period, CarbonImmutable $referenceDate): array
    {
        return match ($period) {
            ReportRecapPeriod::Daily => [$referenceDate, $referenceDate],
            ReportRecapPeriod::Weekly => [
                $referenceDate->startOfWeek(),
                $referenceDate->endOfWeek(),
            ],
            ReportRecapPeriod::Monthly => [
                $referenceDate->startOfMonth(),
                $referenceDate->endOfMonth(),
            ],
        };
    }

    private function formatPeriodLabel(
        ReportRecapPeriod $period,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): string {
        return match ($period) {
            ReportRecapPeriod::Daily => $start->translatedFormat('d F Y'),
            ReportRecapPeriod::Weekly => sprintf(
                '%s – %s',
                $start->translatedFormat('d M Y'),
                $end->translatedFormat('d M Y'),
            ),
            ReportRecapPeriod::Monthly => $start->translatedFormat('F Y'),
        };
    }
}
