<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Report\ReportStatisticsData;
use App\Enums\Permission;
use App\Models\User;
use App\Repositories\ReportRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class ReportStatisticsService
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
    ) {}

    public function forAuthenticatedUser(): ReportStatisticsData
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->forUser($user);
    }

    public function forUser(User $user): ReportStatisticsData
    {
        $userId = $user->can(Permission::UsersView->value) ? null : $user->id;

        return $this->statistics($userId);
    }

    /**
     * @return array<string, int>
     */
    public function dailyTrendForAuthenticatedUser(int $days = 7): array
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->dailyTrendForUser($user, $days);
    }

    /**
     * @return array<string, int>
     */
    public function dailyTrendForUser(User $user, int $days = 7): array
    {
        $userId = $user->can(Permission::UsersView->value) ? null : $user->id;
        $end = CarbonImmutable::today();
        $start = $end->subDays(max($days, 1) - 1);

        return $this->reportRepository->dailyCounts($userId, $start, $end);
    }

    private function statistics(?int $userId): ReportStatisticsData
    {
        $today = CarbonImmutable::today();

        return new ReportStatisticsData(
            total: $this->reportRepository->countTotal($userId),
            today: $this->reportRepository->countForDate($userId, $today),
            thisWeek: $this->reportRepository->countBetweenDates(
                $userId,
                $today->startOfWeek(),
                $today->endOfWeek(),
            ),
            thisMonth: $this->reportRepository->countBetweenDates(
                $userId,
                $today->startOfMonth(),
                $today->endOfMonth(),
            ),
            isGlobalScope: $userId === null,
        );
    }
}
