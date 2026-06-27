<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Concerns\HasBackNavigation;
use App\Filament\Resources\Reports\Concerns\ManagesReportDeletionActions;
use App\Filament\Resources\Reports\ReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewReport extends ViewRecord
{
    use HasBackNavigation;
    use ManagesReportDeletionActions;

    protected static string $resource = ReportResource::class;

    protected static ?string $title = 'Detail Laporan';

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();

        return sprintf(
            'Detail Laporan · %s · %s',
            $record->location_name,
            $record->reported_at?->format('d M Y') ?? '-',
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->makeBackAction('Kembali ke Daftar'),
            EditAction::make()
                ->label('Ubah Laporan')
                ->visible(fn (): bool => ReportResource::canEdit($this->getRecord())),
            ...$this->getReportDeletionActions(),
        ];
    }
}
