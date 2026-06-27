<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Concerns;

use App\Services\ReportService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

trait ManagesReportDeletionActions
{
    /**
     * @return array<int, mixed>
     */
    protected function getReportDeletionActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Laporan')
                ->modalHeading('Hapus Laporan')
                ->modalDescription('Laporan akan dipindahkan ke arsip. Anda masih bisa memulihkannya nanti.')
                ->successNotificationTitle('Laporan dihapus')
                ->action(function (): void {
                    app(ReportService::class)->delete($this->getRecord());

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            ForceDeleteAction::make()
                ->label('Hapus Permanen')
                ->modalHeading('Hapus Permanen')
                ->modalDescription('Laporan beserta foto dan lampiran akan dihapus permanen.')
                ->successNotificationTitle('Laporan dihapus permanen')
                ->action(function (): void {
                    app(ReportService::class)->forceDelete($this->getRecord());

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            RestoreAction::make()
                ->label('Pulihkan Laporan')
                ->successNotificationTitle('Laporan dipulihkan')
                ->action(function (): void {
                    $record = app(ReportService::class)->restore($this->getRecord());
                    $this->record = $record->fresh(['user']);

                    if (method_exists($this, 'refreshFormData')) {
                        $this->refreshFormData(['reported_at', 'latitude', 'longitude', 'location_name', 'notes']);
                    }
                }),
        ];
    }
}
