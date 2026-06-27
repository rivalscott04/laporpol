<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Pages;

use App\DTOs\Report\UpdateReportData;
use App\Filament\Concerns\HasBackNavigation;
use App\Filament\Resources\Reports\Concerns\ManagesReportDeletionActions;
use App\Filament\Resources\Reports\ReportResource;
use App\Services\ReportService;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditReport extends EditRecord
{
    use HasBackNavigation;
    use ManagesReportDeletionActions;

    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->makeBackAction('Kembali ke Detail'),
            ViewAction::make()
                ->label('Lihat Detail'),
            ...$this->getReportDeletionActions(),
        ];
    }

    protected function getBackNavigationUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()->getKey()]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->getRecord()->photo_path !== null) {
            $data['photo'] = [$this->getRecord()->photo_path];
        }

        if ($this->getRecord()->attachment_path !== null) {
            $data['attachment'] = [$this->getRecord()->attachment_path];
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(ReportService::class)->update(
            $record,
            UpdateReportData::fromArray($data),
        );
    }
}
