<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Concerns\CanExportReports;
use App\Filament\Exports\ReportExporter;
use App\Filament\Resources\Reports\ReportResource;
use App\Services\ReportPdfExportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ListReports extends ListRecords
{
    use CanExportReports;

    protected static string $resource = ReportResource::class;

    protected static ?string $title = 'Daftar Laporan';

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Laporan';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Laporan'),
            ExportAction::make()
                ->label('Unduh Excel')
                ->icon(Heroicon::OutlinedTableCells)
                ->exporter(ReportExporter::class)
                ->visible(fn (): bool => $this->canExportReports()),
            Action::make('exportPdf')
                ->label('Unduh PDF')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->action(fn (): mixed => $this->exportReportsPdf())
                ->visible(fn (): bool => $this->canExportReports()),
        ];
    }

    public function exportReportsPdf(): mixed
    {
        abort_unless($this->canExportReports(), 403);

        return app(ReportPdfExportService::class)->download(
            $this->getTableQueryForExport(),
            'Daftar Laporan',
            'laporan-'.now()->format('Y-m-d-His').'.pdf',
        );
    }
}
