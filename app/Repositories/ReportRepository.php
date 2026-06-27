<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Report;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class ReportRepository
{
    public function create(array $attributes): Report
    {
        return Report::query()->create($attributes);
    }

    public function update(Report $report, array $attributes): Report
    {
        $report->update($attributes);

        return $report->refresh();
    }

    public function delete(Report $report): void
    {
        $report->delete();
    }

    public function restore(Report $report): Report
    {
        $report->restore();

        return $report->refresh();
    }

    public function forceDelete(Report $report): void
    {
        $report->forceDelete();
    }

    public function countTotal(?int $userId = null): int
    {
        return $this->scopedQuery($userId)->count();
    }

    public function countForDate(?int $userId, CarbonImmutable $date): int
    {
        return $this->scopedQuery($userId)
            ->whereDate('reported_at', $date->toDateString())
            ->count();
    }

    public function countBetweenDates(?int $userId, CarbonImmutable $start, CarbonImmutable $end): int
    {
        return $this->scopedQuery($userId)
            ->whereDate('reported_at', '>=', $start->toDateString())
            ->whereDate('reported_at', '<=', $end->toDateString())
            ->count();
    }

    /**
     * @return array<string, int>
     */
    public function dailyCounts(?int $userId, CarbonImmutable $start, CarbonImmutable $end): array
    {
        $dateExpression = $this->reportDateSqlExpression();

        $grouped = $this->scopedQuery($userId)
            ->whereDate('reported_at', '>=', $start->toDateString())
            ->whereDate('reported_at', '<=', $end->toDateString())
            ->selectRaw("{$dateExpression} as report_date, COUNT(*) as aggregate")
            ->groupByRaw($dateExpression)
            ->pluck('aggregate', 'report_date')
            ->map(fn ($count): int => (int) $count)
            ->all();

        $counts = [];

        for ($date = $start; $date->lte($end); $date = $date->addDay()) {
            $key = $date->toDateString();
            $counts[$key] = $grouped[$key] ?? 0;
        }

        return $counts;
    }

    private function reportDateSqlExpression(): string
    {
        return match (Report::query()->getConnection()->getDriverName()) {
            'pgsql' => 'reported_at::date',
            default => 'DATE(reported_at)',
        };
    }

    /**
     * @return Builder<Report>
     */
    public function queryBetweenDates(?int $userId, CarbonImmutable $start, CarbonImmutable $end): Builder
    {
        return $this->scopedQuery($userId)
            ->with('user')
            ->whereDate('reported_at', '>=', $start->toDateString())
            ->whereDate('reported_at', '<=', $end->toDateString())
            ->orderByDesc('reported_at');
    }

    /**
     * @return Builder<Report>
     */
    private function scopedQuery(?int $userId): Builder
    {
        $query = Report::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query;
    }
}
