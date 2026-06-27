<?php

declare(strict_types=1);

namespace App\DTOs\Report;

use App\Enums\ReportRecapPeriod;
use Carbon\CarbonImmutable;

final readonly class ReportRecapData
{
    /**
     * @param  array<string, int>  $breakdown
     */
    public function __construct(
        public ReportRecapPeriod $period,
        public CarbonImmutable $start,
        public CarbonImmutable $end,
        public int $total,
        public array $breakdown,
        public string $periodLabel,
    ) {}
}
