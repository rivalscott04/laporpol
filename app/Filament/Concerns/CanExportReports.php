<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Enums\Permission;
use Illuminate\Support\Facades\Auth;

trait CanExportReports
{
    protected function canExportReports(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can(Permission::ReportsExport->value);
    }
}
