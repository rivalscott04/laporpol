<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ReportsView->value);
    }

    public function view(User $user, Report $report): bool
    {
        if (! $user->can(Permission::ReportsView->value)) {
            return false;
        }

        return $this->canManageAllReports($user) || $user->id === $report->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ReportsCreate->value);
    }

    public function update(User $user, Report $report): bool
    {
        if (! $user->can(Permission::ReportsUpdate->value)) {
            return false;
        }

        return $this->canManageAllReports($user) || $user->id === $report->user_id;
    }

    public function delete(User $user, Report $report): bool
    {
        if (! $user->can(Permission::ReportsDelete->value)) {
            return false;
        }

        return $this->canManageAllReports($user) || $user->id === $report->user_id;
    }

    public function restore(User $user, Report $report): bool
    {
        return $this->canManageAllReports($user);
    }

    public function forceDelete(User $user, Report $report): bool
    {
        return $this->canManageAllReports($user);
    }

    private function canManageAllReports(User $user): bool
    {
        return $user->can(Permission::UsersView->value);
    }
}
