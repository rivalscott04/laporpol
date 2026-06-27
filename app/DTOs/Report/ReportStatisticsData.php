<?php

declare(strict_types=1);

namespace App\DTOs\Report;

final readonly class ReportStatisticsData
{
    public function __construct(
        public int $total,
        public int $today,
        public int $thisWeek,
        public int $thisMonth,
        public bool $isGlobalScope,
    ) {}
}
