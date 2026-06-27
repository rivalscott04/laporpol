<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Enums\Permission;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;

trait CanExportReports
{
    protected function canExportReports(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can(Permission::ReportsExport->value);
    }

    protected function hasExportableReportRecords(): bool
    {
        if (! $this instanceof HasTable) {
            return false;
        }

        return $this->getTableQueryForExport()->exists();
    }

    protected function reportExportDisabledTooltip(): ?string
    {
        return $this->hasExportableReportRecords()
            ? null
            : 'Tidak ada data untuk diunduh.';
    }
}
